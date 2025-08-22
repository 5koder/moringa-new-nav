<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace PrestaShop\Module\IqitProductFlags\Form;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlag;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Adapter\Shop\Context;

class IqitProductFlagFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Context
     */
    private $shopContext;

    /**
     * IqitProductFlagFormDataProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, Context $shopContext)
    {
        $this->entityManager = $entityManager;
        $this->shopContext = $shopContext;
    }

    /**
     * @param mixed $id
     * @return array
     */
    public function getData($id): array
    {
        /**
        * @var IqitProductFlag $entity 
        */

        $entity = $this->entityManager->getRepository(IqitProductFlag::class)->find((int) $id);

        $formData = [
            'enable' => $entity->getEnable(),
            'from_date' => $entity->getFromDate() ? $entity->getFromDate()->format('Y-m-d H:i:s') : null,
            'to_date' => $entity->getToDate() ? $entity->gettoDate()->format('Y-m-d H:i:s') : null,
            'categories' => $entity->getCategoryIds(),
            'hook' => $entity->getHook(),
            'shop_association' => [],
        ];

        foreach ($entity->getConfig() as $key => $config) {
            $formData['config_'.$key] = $config;
        }

        foreach ($entity->getShops() as $shop) {
            $formData['shop_association'] = $shop->getId();
        }

        foreach ($entity->getLangs() as $lang) {
            $formData['title'][$lang->getLang()->getId()] = $lang->getTitle();
            $formData['link'][$lang->getLang()->getId()] = $lang->getLink();
            $formData['description'][$lang->getLang()->getId()] = $lang->getDescription();
        }

        return $formData;
    }

    /**
     * @return array
     */
    public function getDefaultData(): array
    {
        return [
            'enable' => true,
            'title' => [],
            'description' => [],
            'link' => [],
            'config_txt_color' => '#ffffff',
            'shop_association' => $this->shopContext->getContextListShopID(),
        ];
    }
}

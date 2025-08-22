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

use DateTime;
use Db;
use PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlag;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlagLang;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\Repository\LangRepository;

class IqitProductFlagFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LangRepository
     */
    private $langRepository;

    public function __construct(EntityManagerInterface $entityManager, LangRepository $langRepository)
    {
        $this->entityManager = $entityManager;
        $this->langRepository = $langRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $entity = new IqitProductFlag();
        $this->populateEntityWithData($entity, $data);
        $this->addAssociatedCategories($entity, $data['categories'] ?? null);
    
        $entity->setPosition(
            (int) Db::getInstance()->getValue('SELECT MAX(position) AS max FROM ' . _DB_PREFIX_ . 'iqit_product_flag') + 1 ?: 1
        );
    
        $this->addAssociatedShops($entity, $data['shop_association'] ?? null);
    
        foreach ($data['title'] as $langId => $langContent) {
            $this->addOrUpdateEntityLang($entity, $langId, $langContent, $data);
        }
    
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    
        return $entity->getId();
    }
    
    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): int
    {
        $entity = $this->entityManager->getRepository(IqitProductFlag::class)->find($id);
        $entity->clearCategories();
        $this->entityManager->flush();
        $this->addAssociatedCategories($entity, $data['categories'] ?? null);
        $this->populateEntityWithData($entity, $data);
    
        foreach ($data['title'] as $langId => $langContent) {
            $this->addOrUpdateEntityLang($entity, $langId, $langContent, $data, true);
        }
    
        if (!is_array($data['shop_association'])) {
            $data['shop_association'] = [$data['shop_association']];
        }
        $this->addAssociatedShops($entity, $data['shop_association'] ?? null);


        
        $this->entityManager->flush();
    
        return $entity->getId();
    }
    
    /**
     * Populate the entity with common data.
     */
    private function populateEntityWithData(IqitProductFlag $entity, array $data): void
    {
        $entity->setEnable($data['enable']);
        $entity->setFromDate($this->createDateTimeOrNull($data['from_date'] ?? null));
        $entity->setToDate($this->createDateTimeOrNull($data['to_date'] ?? null));
        $entity->setHook($data['hook']);


        $config = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'config_') === 0) {
                $newKey = substr($key, strlen('config_'));
                $config[$newKey] = $value;
            }
        }

        $entity->setConfig($config);


    }
    
    /**
     * Create a DateTime object or return null on failure.
     */
    private function createDateTimeOrNull(?string $date): ?DateTime
    {
        try {
            return $date ? new DateTime($date) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
  
    /**
     * Add or update a language entity.
     *
     * @return void
     */
    private function addOrUpdateEntityLang(
        IqitProductFlag $entity,
        int $langId,
        $langContent,
        array $data,
        bool $update = false
    ): void {
        if ($update) {
            $entityLang = $entity->getLangByLangId($langId);
            if (null === $entityLang) {
                return;
            }
        } else {
            $entityLang = new IqitProductFlagLang();
            $lang = $this->langRepository->findOneById($langId);
            $entityLang->setLang($lang);
            $entity->addLang($entityLang);
        }
    
        $entityLang
            ->setTitle($langContent)
            ->setDescription($data['description'][$langId] ?? '')
            ->setLink($data['link'][$langId] ?? '');
    }


    /**
     * @param IqitProductFlag $entity
     * @param array|null $shopIdList
     */
    private function addAssociatedShops(IqitProductFlag &$entity, array $shopIdList = null): void
    {
        $entity->clearShops();

        if (empty($shopIdList)) {
            return;
        }

        foreach ($shopIdList as $shopId) {
            $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);
            $entity->addShop($shop);
        }
    }


    /**
     * @param IqitProductFlag $entity
     * @param array|null $shopIdList
     */
    private function addAssociatedCategories(IqitProductFlag &$entity, array $categoriesIdList = null): void
    {
        if (empty($categoriesIdList)) {
            return;
        }
    
        foreach ($categoriesIdList as $categoryId) {
            $entity->addCategory((int) $categoryId);
        }
    }
}



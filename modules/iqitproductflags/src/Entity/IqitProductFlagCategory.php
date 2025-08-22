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

namespace PrestaShop\Module\IqitProductFlags\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class IqitProductFlagCategory
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShop\Module\IqitProductFlags\Entity\IqitProductFlag", inversedBy="categories")
     * @ORM\JoinColumn(name="id_iqit_product_flag", referencedColumnName="id_iqit_product_flag", onDelete="CASCADE")
     */
    private IqitProductFlag $flag;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id_category")
     */
    private int $categoryId;

    public function getFlag(): IqitProductFlag
    {
        return $this->flag;
    }

    public function setFlag(IqitProductFlag $flag): self
    {
        $this->flag = $flag;
        return $this;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }
}
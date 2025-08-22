<?php

namespace PrestaShop\Module\IqitProductVariants\Factory;

use PrestaShop\Module\IqitProductVariants\Entity\ProductVariant;


class ProductVariantsFactory{

    public static function create(
        int $idProduct,
        ?int $idLang = null,
        ?int $idShop = null
    ):  ProductVariant {
        return  ProductVariant::getInstanceByProductId(
            $idProduct,
            $idLang,
            $idShop
        );
    }
}

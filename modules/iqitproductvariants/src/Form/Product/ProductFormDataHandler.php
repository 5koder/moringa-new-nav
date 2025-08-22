<?php

namespace PrestaShop\Module\IqitProductVariants\Form\Product;

use PrestaShop\Module\IqitProductVariants\Form\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;

use Exception;
use PrestaShop\Module\IqitProductVariants\Entity\ProductVariant;
use PrestaShop\Module\IqitProductVariants\Factory\ProductVariantsFactory;

final class ProductFormDataHandler implements FormDataHandlerInterface
{

    public function save(array $data): bool
    {

        $idProduct = (int) $data['id_product'];
        $this->clearVariants($idProduct);

        if (!isset($data['variants']) || empty($data['variants'])) {
            return true;
        }

        $variantProductsIds = $data['variants'];
        $variantProductsIds[] = $idProduct;


        foreach ($variantProductsIds as $index => $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$index]);
            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }

    public function getData(array $params): array
    {
        $productVariant = ProductVariantsFactory::create(
            (int)$params['id_product'],
            $params['id_lang'] ?? null,
            $params['id_shop'] ?? null
        );

        return [
            'id' => $productVariant->id,
            'id_product' => $productVariant->id_product,
            'variants' => ['data' => array_filter(explode(',', $productVariant->variants))]
        ];
    }

    public function duplicate(array $data): bool
    {
        $idProduct = (int) $data['id_product_old'];
        $idProductNew = (int) $data['id_product'];
        $productVariant = ProductVariantsFactory::create(
            $idProduct
        );

        if (!$productVariant) {
            return true;
        }

        $this->clearVariants($idProduct);

        $variantProductsIds = [];
        foreach (array_filter(explode(',', $productVariant->variants)) as $variantsProduct) {
            $variantProductsIds[$variantsProduct] = $variantsProduct;
        }

        $variantProductsIds[$idProduct] = $idProduct;
        $variantProductsIds[$idProductNew] = $idProductNew;

        foreach ($variantProductsIds as $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$variantsProduct]);
            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }

    public function delete(array $data): bool
    {
        $idProduct = (int) $data['id_product'];
        $productVariant = ProductVariantsFactory::create(
            $idProduct
        );

        if (!$productVariant) {
            return true;
        }

        $this->clearVariants($idProduct);

        $variantProductsIds = [];
        foreach (array_filter(explode(',', $productVariant->variants)) as $variantsProduct) {
            $variantProductsIds[$variantsProduct] = $variantsProduct;
        }

        foreach ($variantProductsIds as $variantsProduct) {
            $variantProductsIdsNew = $variantProductsIds;
            unset($variantProductsIdsNew[$variantsProduct]);
            $this->saveVariant($variantsProduct, $variantProductsIdsNew);
        }

        return true;
    }


    private function saveVariant(int $idProduct, array $variantProductsIds): bool
    {
        $productVariant = ProductVariantsFactory::create($idProduct);
        $productVariant->id_product = $idProduct;
        $productVariant->variants =  implode(',', $variantProductsIds);

        try {
            if ($productVariant->save()) {
                return true;
            }
        } catch (Exception $e) {
            throw new ModuleErrorException($e->getMessage());
        }
    }

    private function removeVariant(ProductVariant $productVariant): bool
    {
        try {
            if ($productVariant->delete()) {
                return true;
            }
        } catch (Exception $e) {
            throw new ModuleErrorException($e->getMessage());
        }
    }

    private function clearVariants(int $idProduct): bool
    {
        $productVariant = ProductVariantsFactory::create($idProduct);
        $this->removeVariant($productVariant);

        $idsVariantsToRemove = array_filter(explode(',', $productVariant->variants));

        foreach ($idsVariantsToRemove as $idVariantProduct) {
            $productVariantLoop = ProductVariantsFactory::create($idVariantProduct);
            $this->removeVariant($productVariantLoop);
        }

        return true;
    }
}

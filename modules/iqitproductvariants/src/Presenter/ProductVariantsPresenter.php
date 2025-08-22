<?php

namespace PrestaShop\Module\IqitProductVariants\Presenter;

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use Product;
use ProductPresenterFactory;

class ProductVariantsPresenter
{

    private $context;


    public function __construct(
        $context
    ) {
        $this->context = $context->getContext();
    }

    public function present(array $products): array
    {
        if (!is_array($products)) {
            return [];
        }
        if (empty($products)) {
            return [];
        }

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();

        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        if (is_array($products)) {
            foreach ($products as &$product) {
                $product = $presenter->present(
                    $presentationSettings,
                    Product::getProductProperties($this->context->language->id, $product, $this->context),
                    $this->context->language
                );
            }
            unset($product);
        }
        return $products;
    }


    public function presentBackend(array $products): array
    {
        if (!is_array($products)) {
            return [];
        }
        if (empty($products)) {
            return [];
        }

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();

        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        if (is_array($products)) {
            foreach ($products as &$product) {
                $product = $presenter->present(
                    $presentationSettings,
                    Product::getProductProperties($this->context->language->id, $product, $this->context),
                    $this->context->language
                );
            }
            unset($product);
        }

        $selectedVariants = [];
        foreach ($products as $product) {
      
            $selectedVariants[] = [
                'id' => $product['id_product'],
                'name' => $product['name'] . ' (ref: '.  $product['reference'] . ')',
                'image' => $product['cover']['medium']['url'],
            ];
            
        }
        return  $selectedVariants;
    }

}

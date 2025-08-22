<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);


use PrestaShop\Module\IqitProductVariants\Form\Modifier\ProductFormModifier;

use PrestaShop\Module\IqitProductVariants\Install\Installer;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\Templating\EngineInterface;


if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class IqitProductVariants extends Module implements WidgetInterface
{

    public function __construct()
    {
        $this->name = 'iqitproductvariants';
        $this->author = 'iqit-commerce.com';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7.8', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->trans('IQITPRODUCTVARIANTS', [], 'Modules.Iqitproductvariants.Config');
        $this->description = $this->trans('Show associated product as variants', [], 'Modules.Iqitproductvariants.Config');
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Installer();

        return $installer->install($this);
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $installer = new Installer();

        return $installer->uninstall($this);
    }


    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    public function clearCache(int $idProduct = 0): void
    {
        $cacheId = $this->name . '|' . $idProduct ;
        $templateFile = 'module:' . $this->name . '/views/templates/hook/front.tpl';

        if ($idProduct) {
            $this->_clearCache($templateFile, $cacheId);
        } else {
            $this->_clearCache($templateFile);
        }
    }


    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        $idProduct = (int) $configuration['smarty']->tpl_vars['product']->value['id_product'];

        if (!$idProduct) {
            return null;
        }

        $cacheId = $this->name . '|' . $idProduct;
        $templateFile = 'module:' . $this->name . '/views/templates/hook/front.tpl';

        if (!$this->isCached($templateFile, $this->getCacheId($cacheId))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }
        return $this->fetch($templateFile, $this->getCacheId($cacheId));
    }


    public function getWidgetVariables($hookName = null, array $configuration = []): array
    {
        $idProduct = (int) $configuration['smarty']->tpl_vars['product']->value['id_product'];

        $repository = $this->get('PrestaShop\Module\IqitProductVariants\Repository\ProductVariantsRepository');
        $presenter = $this->get('PrestaShop\Module\IqitProductVariants\Presenter\ProductVariantsPresenter');

        $variants = $repository->getByIdProduct($idProduct);
        $presentedVariants = $presenter->present($variants);

        return array(
            'products' =>  $presentedVariants,
            'currentId' => $idProduct
        );
    }

    public function hookActionProductFormBuilderModifier(array $params): void
    {
        /** @var ProductFormModifier $productFormModifier */
        $productFormModifier = $this->get(ProductFormModifier::class);
        $productId = (int) $params['id'];
        $data = [];

        $presentedVariants = [];

        $repository = $this->get('PrestaShop\Module\IqitProductVariants\Repository\ProductVariantsRepository');
        $presenter = $this->get('PrestaShop\Module\IqitProductVariants\Presenter\ProductVariantsPresenter');

        $variants = $repository->getByIdProductForBackend($productId);
        $presentedVariants = $presenter->presentBackend($variants);

        $productFormModifier->modify($productId, $params['form_builder'], $presentedVariants);
    }

    /**
     * Hook called after form is submitted and combination is updated, custom data is updated here.
     *
     * @param array $params
     */
    public function hookActionAfterUpdateProductFormHandler(array $params): void
    {   

        $data['id_product'] = $params['form_data']['id'];
        $data['variants'] = [];

        foreach($params['form_data']['description']['variants'] as $variant){
            $data['variants'][] = $variant['id'];
        }

        
        /** @var ProductFormDataHandler $productFormDataHandlerr */
        $productFormDataHandler = $this->get('PrestaShop\Module\IqitProductVariants\Form\Product\ProductFormDataHandler');
        $productFormDataHandler->save($data);
        $this->clearCache();

    }


    public function hookActionProductAdd($params)
    {
        if (isset($params['id_product_old'])) {
            $data['id_product_old'] = (int) $params['id_product_old'];
            $data['id_product'] = (int) $params['id_product'];

            /** @var ProductFormDataHandler $productFormDataHandler */
            $productFormDataHandler = $this->get('PrestaShop\Module\IqitProductVariants\Form\Product\ProductFormDataHandler');
            $productFormDataHandler->duplicate($data);
            $this->clearCache();
        }
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        if (!isset($params['object']->id)) {
            return;
        }
        $data['id_product'] = (int) $params['object']->id;
        /** @var ProductFormDataHandler $productFormDataHandler */
        $productFormDataHandler = $this->get('PrestaShop\Module\IqitProductVariants\Form\Product\ProductFormDataHandler');

        $productFormDataHandler->delete($data);

        $this->clearCache();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller->controller_name == 'AdminProducts') {
            $requestStack = $this->get('request_stack')->getCurrentRequest();
            if ($requestStack && $requestStack->attributes->get('_route') == 'admin_products_edit') {
                $this->context->controller->addCSS($this->_path . 'views/dist/back.css');
                $this->context->controller->addJS($this->_path . 'views/dist/back.js');
            }
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            $this->name . '-front-css',
            'modules/' . $this->name . '/views/dist/front.css'
        );
        $this->context->controller->registerJavascript(
            $this->name . '-front-js',
            'modules/' . $this->name . '/views/dist/front.js'
        );
    }
}

<?php
/**
 * Mailchimp Pro - Newsletter sync and eCommerce Automation
 *
 * @author    BusinessTech.fr - https://www.businesstech.fr
 * @copyright Business Tech 2021 - https://www.businesstech.fr
 * @license   Commercial
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

class BTMailchimpEcommerceVoucherModuleFrontController extends ModuleFrontController
{
    /**
     * init() method init module front controller
     */
    public function init()
    {
        // exec parent
        parent::init();

        // include main module class
        require_once($this->module->getLocalPath() . 'btmailchimpecommerce.php');
        require_once(_MCE_PATH_CONF . 'hook.php');
        require_once(_MCE_PATH_LIB_HOOK . 'HookCtrl.php');
    }

    /**
     * initContent() method init module front controller content
     *
     * @return bool
     */
    public function initContent()
    {
        // exec parent
        parent::initContent();

        Media::getJqueryPluginPath('fancybox');

        // instantiate
        $oModule = new BTMailchimpEcommerce();

        // Own Module's front controller
        $oHook = new \MCE\HookCtrl('display', 'voucher');

        // displays good block content
        $aContent = $oHook->run();

        // set module name
        $aContent['assign']['sMceModuleName'] = Tools::strtolower(_MCE_MODULE_NAME);

        foreach ($aContent['assign'] as $sKey => $mValue) {
            $this->context->smarty->assign($sKey, $mValue);
        }

        // get FancyBox plugin
        $aJsCss = \Media::getJqueryPluginPath('fancybox');

        // add fancybox plugin
        if (!empty($aJsCss['js']) && !empty($aJsCss['css'])) {
            \Context::getContext()->controller->addJqueryPlugin('fancybox');
        }

        Context::getContext()->controller->addCSS(_MCE_URL_CSS . 'front.css');

        // use case  - PS 1.5 should load bootstrap
        if (empty(BTMailchimpEcommerce::$bCompare16)) {
            Context::getContext()->controller->addCSS(_MCE_URL_CSS . 'bootstrap.min.css');
            Context::getContext()->controller->addJS(_MCE_URL_JS . 'bootstrap.min.js');
        }

        $this->setTemplate($aContent['tpl']);
    }
}

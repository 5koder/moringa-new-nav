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

class BTMailchimpEcommerceCronModuleFrontController extends ModuleFrontController
{
    /**
     * method manage post data
     *
     * @throws Exception
     * @return bool
     */
    public function postProcess()
    {
        $sGetKey = Tools::getValue('bt_token');
        $sSecureKey = BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN'];
        $sResponse = false;

        if ($sGetKey == $sSecureKey) {
            // use case - export recently updated products to MC
            $_POST['sAction'] = Tools::getIsset('sAction') ? Tools::getValue('sAction') : 'generate';
            $_POST['sType'] = Tools::getIsset('sType') ? Tools::getValue('sType') : 'cron';
        
            if ($this->module->getContent()) {
                $sResponse = true;
            }
        } else {
            $sResponse = $this->module->l('Internal server error! (security error)', 'cron');
        }

        die(Tools::jsonEncode($sResponse));
    }
}

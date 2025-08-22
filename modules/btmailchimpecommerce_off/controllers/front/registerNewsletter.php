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

class BTMailchimpEcommerceRegisterNewsletterModuleFrontController extends ModuleFrontController
{
    /**
     * method manage post data
     *
     * @throws Exception
     * @return bool
     */
    public function postProcess()
    {
        $sAction = Tools::getValue('sAction');
        $sType = Tools::getValue('sType');
        $sToken = Tools::getValue('bt_token');
        $sEmail = Tools::getValue('bt_nl_email');
        $bDisplaySignup = Tools::getValue('bt_signup_display');
        $sResponse = false;

        // Use case for synch with the block newsletter from PS
        if ($sAction == 'register' && $sType == 'newsletter' && !empty($sToken) && !empty($sEmail)) {
            if ($this->module->hookActionRegisterNewsletter(array_merge($_POST, array('action' => 'registernewsletter')))) {
                $sResponse = true;
            }
        }

        // Use case for not displayed the popup again
        if ($sAction == 'signup' && $sType == 'notdisplay' && !empty($sToken) && !empty($bDisplaySignup)) {
            if ($this->module->hookActionNotDisplaySignup(array_merge($_POST, array('action' => 'notdisplaysignup')))) {
                $sResponse = true;
            }
        }

        die(Tools::jsonEncode($sResponse));
    }
}

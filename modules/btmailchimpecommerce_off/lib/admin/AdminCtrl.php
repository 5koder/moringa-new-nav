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

namespace MCE;

class AdminCtrl extends BaseCtrl
{
    /**
     * Magic Method __construct
     *
     * @param array $aParams
     */
    public function __construct(array $aParams = null)
    {
        // defines type to execute
        // use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
        // use case : key sAction sent in POST mode (form or ajax query posted ).
        $sAction = (!\Tools::getIsset('sAction') || (\Tools::getIsset('sAction') && 'display' == \Tools::getValue('sAction'))) ? (\Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'display') : \Tools::getValue('sAction');

        // set action
        $this->setAction($sAction);

        // set type
        $this->setType();
    }

    /**
     * run() method execute abstract derived admin object
     *
     * @param array $aRequest : request
     * @return array $aDisplay : empty => false / not empty => true
     */
    public function run($aRequest)
    {
        // set
        $aDisplay = array();
        $aParams = array();

        // include interface
        require_once(_MCE_PATH_LIB_ADMIN . 'Admin.php');

        // set js msg translation
        \MCE\Tools::translateJsMsg();

        // set params
        $aParams['oJsTranslatedMsg'] = \MCE\Tools::jsonEncode($GLOBALS['MCE_JS_MSG']);

        switch (self::$sAction) {
            case 'display' :
                // include admin display object
                require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

                $oAdminType = \MCE\AdminDisplay::create();

                // update new module keys
                \MCE\Tools::updateConfiguration();

                // get configuration options
                \MCE\Tools::getConfig();

                // use case - type not define => first page requested
                if (empty(self::$sType)) {
                    // update module if necessary
                    $aParams['aUpdateErrors'] = \BTMailchimpEcommerce::$oModule->updateModule();
                }

                break;
            case 'update'   :
                // include admin update object
                require_once(_MCE_PATH_LIB_ADMIN . 'AdminUpdate.php');

                $oAdminType = \MCE\AdminUpdate::create();
                break;
            case 'delete'   :
                // include admin delete object
                require_once(_MCE_PATH_LIB_ADMIN . 'AdminDelete.php');

                $oAdminType = \MCE\AdminDelete::create();
                break;
            case 'generate'   :
                // include admin generate object
                require_once(_MCE_PATH_LIB_ADMIN . 'AdminGenerate.php');

                $oAdminType = \MCE\AdminGenerate::create();
                break;
            default :
                $oAdminType = false;
                break;
        }

        // process data to use in view (tpl)
        if (!empty($oAdminType)) {
            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oAdminType->run(parent::$sType, $aRequest);

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], $aParams, array('bAddJsCss' => true));
            }
        }

        return $aDisplay;
    }
}

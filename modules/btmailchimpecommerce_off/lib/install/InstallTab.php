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

class BT_InstallTab implements BT_IInstall
{
    /**
     * install of module
     *
     * @param mixed $mParam
     * @return bool $bReturn : true => validate install, false => invalidate install
     */
    public static function install($mParam = null)
    {
        // declare return
        $bReturn = true;

        static $oTab;

        // instantiate
        if (null === $oTab) {
            $oTab = new Tab();
        }

        // log jam to debug appli
        if (defined('_MCE_LOG_JAM_CONFIG') && _MCE_LOG_JAM_CONFIG) {
            $bReturn = _MCE_LOG_JAM_CONFIG;
        } else {
            // set variables
            $aTmpLang = array();

            // get available languages
            $aLangs = Language::getLanguages(true);

            // loop on each admin tab
            foreach ($GLOBALS['MCE_TABS'] as $sAdminClassName => $aTab) {
                foreach ($aLangs as $aLang) {
                    $aTmpLang[$aLang['id_lang']] = array_key_exists($aLang['iso_code'],
                        $aTab['lang']) ? $aTab['lang'][$aLang['iso_code']] : $aTab['lang']['en'];
                }
                $oTab->name = $aTmpLang;
                $oTab->class_name = $sAdminClassName;
                $oTab->module = \BTMailchimpEcommerce::$oModule->name;
                $oTab->id_parent = Tab::getIdFromClassName($aTab['parent']);

                // save admin tab
                if (false == $oTab->save()) {
                    $bReturn = false;
                }
            }
        }

        return $bReturn;
    }

    /**
     * uninstall of module
     *
     * @param mixed $mParam
     * @return bool $bReturn : true => validate uninstall, false => invalidate uninstall
     */
    public static function uninstall($mParam = null)
    {
        // set return execution
        $bReturn = true;

        // loop on each admin tab
        foreach ($GLOBALS['MCE_TABS'] as $sAdminClassName => $aTab) {
            // get ID
            $iTabId = Tab::getIdFromClassName($sAdminClassName);

            if (!empty($iTabId)) {
                // instantiate
                $oTab = new Tab($iTabId);

                // use case - check delete
                if (false == $oTab->delete()) {
                    $bReturn = false;
                }
            }
        }

        return $bReturn;
    }
}

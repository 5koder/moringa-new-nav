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

class ModuleUpdate
{
    /**
     * @var $aErrors : store errors
     */
    public $aErrors = array();


    /**
     * execute required function
     *
     * @param string $sType
     * @param array $aParam
     */
    public function run($sType, array $aParam = null)
    {
        // get type
        $sType = empty($sType) ? 'tables' : $sType;

        switch ($sType) {
            case 'tables' : // use case - update tables
            case 'fields' : // use case - update fields
            case 'hooks' : // use case - update hooks
            case 'templates' : // use case - update templates
            case 'moduleAdminTab' : // use case - update old module admin tab version
            case 'version2' : // use case - update module v1 to v2
                // execute match function
                call_user_func_array(array($this, 'update' . ucfirst($sType)), array($aParam));
                break;
            default :
                break;
        }
    }


    /**
     * update tables if required
     *
     * @param array $aParam
     */
    private function updateTables(array $aParam = null)
    {
        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        if (!empty($GLOBALS['MCE_SQL_UPDATE']['table'])) {
            // loop on each elt to update SQL
            foreach ($GLOBALS['MCE_SQL_UPDATE']['table'] as $sTable => $mSqlFile) {
                // execute query
                $bResult = \Db::getInstance()->ExecuteS('SHOW TABLES LIKE "' . _DB_PREFIX_ .'mce_' . $sTable . '"');

                // if empty - update
                if (empty($bResult)) {
                    require_once(_MCE_PATH_CONF . 'install.php');
                    require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

                    $sSqlFile = isset($mSqlFile['file']) ? $mSqlFile['file'] : $mSqlFile;

                    // use case - KO update
                    if (!\BT_InstallCtrl::run('install', 'sql', _MCE_PATH_SQL . $sSqlFile)) {
                        $this->aErrors[] = array('table' => $sTable, 'file' => $sSqlFile);
                    }

                    // check if we want to install the new tables
                    if (!empty($mSqlFile['install_new_tables'])) {
                        if (!\BT_InstallCtrl::run('install', 'sql', _MCE_PATH_SQL . _MCE_INSTALL_SQL_FILE)) {
                            $this->aErrors[] = array('table' => $sTable, 'file' => $sSqlFile);
                        }
                    }
                }
            }
        }

        if (empty($this->aErrors)) {
            \Db::getInstance()->Execute('COMMIT');
        } else {
            \Db::getInstance()->Execute('ROLLBACK');
        }
    }


    /**
     * update fields if required
     *
     * @param array $aParam
     */
    private function updateFields(array $aParam = null)
    {
        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        if (!empty($GLOBALS['MCE_SQL_UPDATE']['field'])) {
            // loop on each elt to update SQL
            foreach ($GLOBALS['MCE_SQL_UPDATE']['field'] as $aOption) {
                // execute query
                $bResult = \Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' . _DB_PREFIX_ .'mce_' . $aOption['table'] . ' LIKE "' . $aOption['field'] . '"');

                if (!empty($bResult[0])
                    && isset($aOption['options'])
                    && is_array($aOption['options'])
                ) {
                    $bMatching = true;
                    foreach ($aOption['options'] as $sType => $sDefintion) {
                        if (array_key_exists($sType, $bResult[0])
                            && $sDefintion != $bResult[0][$sType]
                        ) {
                            $bMatching = false;
                        }
                    }
                    if (!$bMatching) {
                        $bResult = false;
                    }
                }

                // if empty - update
                if (empty($bResult)) {
                    require_once(_MCE_PATH_CONF . 'install.php');
                    require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

                    // use case - KO update
                    if (!\BT_InstallCtrl::run('install', 'sql', _MCE_PATH_SQL . $aOption['file'])) {
                        $aErrors[] = array(
                            'field' => $aOption['field'],
                            'linked' => $aOption['table'],
                            'file' => $aOption['file']
                        );
                    }
                }
            }
        }

        if (empty($this->aErrors)) {
            \Db::getInstance()->Execute('COMMIT');
        } else {
            \Db::getInstance()->Execute('ROLLBACK');
        }
    }

    /**
     * update hooks if required
     *
     * @param array $aParam
     */
    private function updateHooks(array $aParam = null)
    {
        require_once(_MCE_PATH_CONF . 'install.php');
        require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

        // use case - hook register ko
        if (!\BT_InstallCtrl::run('install', 'config', array('bHookOnly' => true))) {
            $this->aErrors[] = array(
                'table' => 'ps_hook_module',
                'file' => \BTMailchimpEcommerce::$oModule->l('register hooks KO')
            );
        }
    }


    /**
     * update templates if required
     *
     * @param array $aParam
     */
    private function updateTemplates(array $aParam = null)
    {
        require_once(_MCE_PATH_LIB_COMMON . 'DirReader.php');

        // get templates files
        $aTplFiles = \MCE\DirReader::create()->run(array(
            'path' => _MCE_PATH_TPL,
            'recursive' => true,
            'extension' => 'tpl',
            'subpath' => true
        ));

        if (!empty($aTplFiles)) {

            $smarty = \Context::getContext()->smarty;

            if (method_exists($smarty, 'clearCompiledTemplate')) {
                $smarty->clearCompiledTemplate();
            } elseif (method_exists($smarty, 'clear_compiled_tpl')) {
                foreach ($aTplFiles as $aFile) {
                    $smarty->clear_compiled_tpl($aFile['filename']);
                }
            }
        }
    }


    /**
     * update module admin tab in case of an update
     *
     * @param array $aParam
     */
    private function updateModuleAdminTab(array $aParam = null)
    {
        foreach ($GLOBALS['MCE_TABS'] as $sModuleTabName => $aTab) {
            if (isset($aTab['oldName'])) {
                if (\Tab::getIdFromClassName($aTab['oldName']) != false) {
                    // include install ctrl class
                    require_once(_MCE_PATH_CONF . 'install.php');
                    require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

                    // use case - if uninstall succeeded
                    if (\BT_InstallCtrl::run('uninstall', 'tab', array('name' => $aTab['oldName']))) {
                        // install new admin tab
                        \BT_InstallCtrl::run('install', 'tab', array('name' => $sModuleTabName));
                    }
                }
            }
        }
    }


    /**
     * update module v1 to v2
     *
     * @param array $aParam
     */
    private function updateVersion2(array $aParam = null)
    {
        if (\Configuration::get('MCE_OLD_CONFIG') == false
            && \Configuration::get('MCE_MODULE_VERSION') != \BTMailchimpEcommerce::$oModule->version
            && \BTMailchimpEcommerce::$oModule->version == '2.0.0'
        ) {
            // include DAO
            require_once(_MCE_PATH_LIB . 'Dao.php');

            $aCurrentLists = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId);

            if (!empty($aCurrentLists)) {
                $aLists = [];
                foreach ($aCurrentLists as $aList) {
                    if (!empty($aList['active'])) {
                        $aLists[] = ['id' => $aList['id'], 'name' => $aList['name'], 'lang_id' => $aList['lang_id']];

                        // register the old configured lists
                        \Configuration::updateValue('MCE_OLD_CONFIG', serialize($aLists));
                    }
                }
            }
        }
    }


    /**
     * returns errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    /**
     * manages singleton
     *
     * @return array
     */
    public static function create()
    {
        static $oModuleUpdate;

        if (null === $oModuleUpdate) {
            $oModuleUpdate = new \MCE\ModuleUpdate();
        }
        return $oModuleUpdate;
    }
}
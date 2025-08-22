<?php
/**
 * Mailchimp Pro - Newsletter sync and eCommerce Automation
 *
 * @author    BusinessTech.fr - https://www.businesstech.fr
 * @copyright Business Tech 2021 - https://www.businesstech.fr
 * @license   Commercial
 * @version 2.0.6
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

if (!defined('_PS_VERSION_')) {
    exit(1);
}

class BTMailchimpEcommerce extends Module
{
    /**
     * @var array $conf : array of set configuration
     */
    public static $conf = array();

    /**
     * @var int $iCurrentLang : store id of default lang
     */
    public static $iCurrentLang = null;

    /**
     * @var int $sCurrentLang : store iso of default lang
     */
    public static $sCurrentLang = null;

    /**
     * @var obj $oCookie : store cookie obj
     */
    public static $oCookie = null;

    /**
     * @var obj $oModule : obj module itself
     */
    public static $oModule = array();

    /**
     * @var string $sQueryMode : query mode - detect XHR
     */
    public static $sQueryMode = null;

    /**
     * @var string $sBASE_URI : base of URI in prestashop
     */
    public static $sBASE_URI = null;

    /**
     * @var array $aErrors : array get error
     */
    public $aErrors = null;

    /**
     * @var int $iShopId : shop id used for 1.5 and for multi shop
     */
    public static $iShopId = 1;

    /**
     * @var bool $bCompare16 : get compare version for PS 1.6
     */
    public static $bCompare16 = false;

    /**
     * @var bool $bCompare1610 : get compare version for PS 1.6.1.0
     */
    public static $bCompare1610 = false;

    /**
     * @var bool $bCompare17 : get compare version for PS 1.7
     */
    public static $bCompare17 = false;

    /**
     * Magic Method __construct assigns few information about module and instantiate parent class
     */
    public function __construct()
    {
        require_once(dirname(__FILE__) . '/conf/common.php');
        require_once(_MCE_PATH_LIB . 'Warning.php');
        require_once(_MCE_PATH_LIB . 'Tools.php');

        $this->name = 'btmailchimpecommerce';
        $this->module_key = 'daf1d525357e1610bb3722302efc75e9';
        $this->tab = 'advertising_marketing';
        $this->version = '2.0.6';
        $this->author = 'Business Tech';
        $this->need_instance = 0;
        $this->controllers = array('voucher', 'signup');
        $this->bootstrap = true;
        $this->ps_versions_compliancy['min'] = '1.7.0.0';

        parent::__construct();

        $this->displayName = $this->l('Newsletter & Marketing automation with MailChimp');
        $this->description = $this->l('Synchronize your products and customer base with Mailchimp to build a quality automated customer relationship while enjoying a maximum deliverability for your newsletters');
        $this->confirmUninstall = $this->l('Are you sure you want to remove it ? Your Mailchimp Pro module will no longer work. Be careful, all your configuration and your data will be lost locally but not in Mailchimp.');

        // get shop id
        self::$iShopId = $this->context->shop->id;

        // get current  lang id
        self::$iCurrentLang = $this->context->cookie->id_lang;

        // get current lang iso
        self::$sCurrentLang = \MCE\Tools::getLangIso();

        // get cookie obj
        self::$oCookie = $this->context->cookie;

        // compare PS version
        self::$bCompare16 = version_compare(_PS_VERSION_, '1.6', '>=');
        self::$bCompare1610 = version_compare(_PS_VERSION_, '1.6.1.0', '>=');
        self::$bCompare17 = version_compare(_PS_VERSION_, '1.7', '>=');

        // stock itself obj
        self::$oModule = $this;

        // update module version
        $GLOBALS['MCE_CONFIGURATION']['MCE_MODULE_VERSION'] = $this->version;

        // set base of URI
        self::$sBASE_URI = $this->_path;

        // get configuration options
        \MCE\Tools::getConfig();

        // get call mode - Ajax or dynamic - used for clean headers and footer in ajax request
        self::$sQueryMode = Tools::getValue('sMode');
    }

    /**
     * installs all mandatory structure (DB or Files) => sql queries and update values and hooks registered
     *
     * @return bool
     */
    public function install()
    {
        require_once(_MCE_PATH_CONF . 'install.php');
        require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

        // set return
        $bReturn = true;

        if (
            !parent::install()
            || !BT_InstallCtrl::run('install', 'sql', _MCE_PATH_SQL . _MCE_INSTALL_SQL_FILE)
            || !BT_InstallCtrl::run('install', 'config')
        ) {
            $bReturn = false;
        }

        return $bReturn;
    }

    /**
     * uninstalls all mandatory structure (DB or Files)
     *
     * @return bool
     */
    public function uninstall()
    {
        require_once(_MCE_PATH_CONF . 'install.php');
        require_once(_MCE_PATH_LIB_INSTALL . 'InstallCtrl.php');

        // set return
        $bReturn = true;

        if (
            !parent::uninstall()
            || !BT_InstallCtrl::run('uninstall', 'sql', _MCE_PATH_SQL . _MCE_UNINSTALL_SQL_FILE)
            || !BT_InstallCtrl::run('uninstall', 'config')
        ) {
            $bReturn = false;
        }

        return $bReturn;
    }

    /**
     * manages all data in Back Office
     *
     * @return string
     */
    public function getContent()
    {
        require_once(_MCE_PATH_CONF . 'admin.php');
        require_once(_MCE_PATH_LIB_ADMIN . 'BaseCtrl.php');
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminCtrl.php');

        // set
        $aUpdateModule = array();

        try {
            // get controller type
            $sControllerType = (!Tools::getIsset(_MCE_PARAM_CTRL_NAME) || (Tools::getIsset(_MCE_PARAM_CTRL_NAME) && 'admin' == Tools::getValue(_MCE_PARAM_CTRL_NAME))) ? (Tools::getIsset(_MCE_PARAM_CTRL_NAME) ? Tools::getValue(_MCE_PARAM_CTRL_NAME) : 'admin') : Tools::getValue(_MCE_PARAM_CTRL_NAME);

            // instantiate matched controller object
            $oCtrl = \MCE\BaseCtrl::get($sControllerType);

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oCtrl->run(array_merge($_GET, $_POST));

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
                    'aUpdateErrors' => $aUpdateModule,
                    'oJsTranslatedMsg' => \MCE\Tools::jsonEncode($GLOBALS['MCE_JS_MSG']),
                    'bAddJsCss' => true
                ));

                // get content
                $sContent = $this->displayModule($aDisplay['tpl'], $aDisplay['assign']);

                if (!empty(self::$sQueryMode)) {
                    echo $sContent;
                } else {
                    return $sContent;
                }
            } else {
                throw new Exception('action returns empty content', 110);
            }
        } catch (Exception $e) {
            $this->aErrors[] = array('msg' => $e->getMessage(), 'code' => $e->getCode());

            // get content
            $sContent = $this->displayErrorModule(_MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

            if (!empty(self::$sQueryMode)) {
                echo $sContent;
            } else {
                return $sContent;
            }
        }
        // exit clean with XHR mode
        if (!empty(self::$sQueryMode)) {
            exit(0);
        }
    }


    /**
     * displays customized module content on header
     *
     * @return string
     */
    public function hookDisplayHeader()
    {
        return $this->execHook('display', 'header');
    }


    /**
     * actionFrontControllerSetMedia() method handle to set media to the controller
     *
     * @return string
     */
    public function hookActionFrontControllerSetMedia()
    {
        return $this->execHook('action', 'setMedia');
    }

    /**
     * add a new product
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionProductAdd($aParams = null)
    {
        return $this->execHook('action', 'productAdd', $aParams);
    }


    /**
     * delete a product
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionProductDelete($aParams = null)
    {
        return $this->execHook('action', 'productDelete', $aParams);
    }

    /**
     * update a product
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionProductUpdate($aParams = null)
    {
        return $this->execHook('action', 'productUpdate', $aParams);
    }


    /**
     * update a product's combination
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionProductAttributeUpdate($aParams = null)
    {
        return $this->execHook('action', 'combinationUpdate', $aParams);
    }

    /**
     * hookActionProductAttributeDelete() method delete a product's combination
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionProductAttributeDelete($aParams = null)
    {
        return $this->execHook('action', 'combinationDelete', $aParams);
    }


    /**
     * add a customer
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionCustomerAccountAdd($aParams = null)
    {
        return $this->execHook('action', 'customerAccountAdd', $aParams);
    }


    /**
     * update a customer
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionCustomerAccountUpdate($aParams = null)
    {
        return $this->execHook('action', 'customerAccountUpdate', $aParams);
    }

    /**
     * update a customer
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionObjectUpdateAfter($aParams = null)
    {
        // detect if the controller Address is executed to update the customer and his address
        if (
            isset(Context::getContext()->controller->php_self)
            && Context::getContext()->controller->php_self == 'address'
            && Tools::getIsset('address1')
        ) {
            $oTmpAddress = is_a($aParams['object'], 'address') ? $aParams['object'] : null;
            $aParams = array();
            $aParams['customer'] = new Customer(self::$oCookie->id_customer);

            if ($oTmpAddress !== null) {
                $aParams['address'] = (array) $oTmpAddress;
            }
        }

        return $this->execHook('action', 'customerAccountUpdate', $aParams);
    }


    /**
     * update a customer via the address form
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionValidateCustomerAddressForm($aParams = null)
    {

        $iCustomerId = $aParams['cookie']->id_customer;

        $aParams = array();
        $aParams['customer'] = new Customer($iCustomerId);
        $aParams['address'] = array(
            'firstname' =>    Tools::getValue('firstname'),
            'lastname'  =>  Tools::getValue('lastname'),
            'address1' => Tools::getValue('address1'),
            'address2' => Tools::getValue('address2'),
            'city' => Tools::getValue('city'),
            'postcode' => Tools::getValue('postcode'),
            'id_state' => Tools::getValue('id_state'),
            'id_country' => Tools::getValue('id_country'),
            'phone' => Tools::getValue('phone'),
        );

        return $this->execHook('action', 'customerAccountUpdate', $aParams);
    }


    /**
     * save cart
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionCartSave($aParams = null)
    {
        return $this->execHook('action', 'cartSave', $aParams);
    }


    /**
     * executes validate of new order
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionValidateOrder(array $aParams)
    {
        return $this->execHook('action', 'validateOrder', $aParams);
    }

    /**
     * executes validate of new order
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionOrderStatusUpdate(array $aParams)
    {
        return $this->execHook('action', 'updateOrderStatus', $aParams);
    }

    /**
     * process a batch webhook
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionBatchWebhookProcess($aParams)
    {
        return $this->execHook('action', 'batchWebhook', $aParams);
    }

    /**
     * process a list webhook
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionListWebhookProcess($aParams)
    {
        return $this->execHook('action', 'listWebhook', $aParams);
    }

    /**
     * define if the visitor doesn't wnat the signup popup displayed
     *
     * @param array $aParams
     * @return string
     */
    public function hookActionNotDisplaySignup($aParams)
    {
        return $this->execHook('action', 'signupDisplay', $aParams);
    }


    /**
     * define if the visitor has subscribed to the newsletter
     *
     * @param string $sEmail
     * @return string
     */
    public function hookActionRegisterNewsletter($aParams)
    {
        return $this->execHook('action', 'registeremail', $aParams);
    }


    /**
     * displays or execute selected hook content
     *
     * @param string $sHookType
     * @param array $aParams
     * @return string
     */
    private function execHook($sHookType, $sAction, array $aParams = null)
    {
        // include
        require_once(_MCE_PATH_CONF . 'hook.php');
        require_once(_MCE_PATH_LIB_HOOK . 'HookCtrl.php');

        // set
        $aDisplay = array();

        try {
            // use cache or not
            if (
                !empty($aParams['cache'])
                && !empty($aParams['template'])
                && !empty($aParams['cacheId'])
            ) {
                $bUseCache = !$this->isCached(
                    $aParams['template'],
                    $this->getCacheId($aParams['cacheId'])
                ) ? false : true;

                if ($bUseCache) {
                    $aDisplay['tpl'] = $aParams['template'];
                    $aDisplay['assign'] = array();
                }
            } else {
                $bUseCache = false;
            }

            // detect cache or not
            if (!$bUseCache) {
                // define which hook class is executed in order to display good content in good zone in shop
                $oHook = new \MCE\HookCtrl($sHookType, $sAction);

                // displays good block content
                $aDisplay = $oHook->run($aParams);
            }

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            if (!empty($aDisplay)) {
                return $this->displayModule(
                    $aDisplay['tpl'],
                    $aDisplay['assign'],
                    $bUseCache,
                    (!empty($aParams['cacheId']) ? $aParams['cacheId'] : null)
                );
            } else {
                throw new Exception('Chosen hook returned empty content', 110);
            }
        } catch (Exception $e) {
            $this->aErrors[] = array('msg' => $e->getMessage(), 'code' => $e->getCode());

            return $this->displayErrorModule(_MCE_TPL_HOOK_PATH . _MCE_TPL_ERROR);
        }
    }


    /**
     * displays views
     *
     * @throws Exception
     * @param string $sTplName
     * @param array $assign
     * @param bool $bUseCache
     * @param int $iICacheId
     * @return string html
     */
    public function displayModule($sTplName, $assign, $bUseCache = false, $iICacheId = null)
    {
        if (file_exists(_MCE_PATH_TPL . $sTplName) && is_file(_MCE_PATH_TPL . $sTplName)) {
            $assign = array_merge(
                $assign,
                array('sModuleName' => Tools::strtolower(_MCE_MODULE_NAME), 'bDebug' => _MCE_DEBUG)
            );

            // use cache
            if (!empty($bUseCache) && !empty($iICacheId)) {
                return $this->display(__FILE__, $sTplName, $this->getCacheId($iICacheId));
            } else {
                $this->context->smarty->assign($assign);
                return $this->display(__FILE__, _MCE_PATH_TPL_NAME . $sTplName);
            }
        } else {
            throw new Exception('Template "' . $sTplName . '" doesn\'t exists', 120);
        }
    }

    /**
     * displays view with error
     *
     * @param string $sTplName
     * @return string html
     */
    public function displayErrorModule($sTplName)
    {
        $this->context->smarty->assign(
            array(
                'sHomeURI' => \MCE\Tools::truncateUri(),
                'aErrors' => $this->aErrors,
                'sModuleName' => Tools::strtolower(_MCE_MODULE_NAME),
                'bDebug' => _MCE_DEBUG,
            )
        );

        return $this->display(__FILE__, _MCE_PATH_TPL_NAME . $sTplName);
    }

    /**
     * updates module as necessary
     * @return array
     */
    public function updateModule()
    {
        require(_MCE_PATH_LIB . 'ModuleUpdate.php');

        // check if we upgrade from v1 to v2
        \MCE\ModuleUpdate::create()->run('version2');

        // check if update tables
        \MCE\ModuleUpdate::create()->run('tables');

        // check if update fields
        \MCE\ModuleUpdate::create()->run('fields');

        // check if update hooks
        \MCE\ModuleUpdate::create()->run('hooks');

        \Configuration::updateValue('MCE_MODULE_VERSION', $this->version);

        return \MCE\ModuleUpdate::create()->aErrors;
    }

    /**
     * module function MC loader
     * @param
     */
    public static function getMailchimpLoader($loader_type = '')
    {
        $GLOBALS['loader_type'] = $loader_type;
        require_once(_MCE_PATH_LIB_MC . 'autoload.php');
    }
}

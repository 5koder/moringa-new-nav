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

class AdminDisplay implements \BT_IAdmin
{
    /**
     * @var array $aFlagIds : array for all flag ids used in option translation
     */
    protected $aFlagIds = array();

    /**
     * Display all configured data admin tabs
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     * @return array
     */
    public function run($sType, array $aParam = array())
    {
        // set variables
        $aDisplayInfo = array();

        if (empty($sType)) {
            $sType = 'tabs';
        }

        // include DAO
        require_once(_MCE_PATH_LIB . 'Dao.php');

        // get loader
        \BTMailchimpEcommerce::getMailchimpLoader();

        switch ($sType) {
            case 'tabs' : // use case - display first page with all tabs
            case 'test' : // use case - display test cUrl popup
            case 'mailchimp' : // use case - display mailchimp settings page
            case 'exclusion' : // use case - display exclusion settings page
            case 'userList' : // use case - display user list page
            case 'syncStatus' : // use case - display sync history and diagnostic tool page
            case 'searchPopup' : // use case - display search popup for synching
            case 'newsletterConfig' : // use case - display newsletter config and sync
            case 'signupForm' : // use case - display sign-up form settings
            case 'ecommerce' : // use case - display ecommerce config and sync
            case 'vouchers' : // use case - display vouchers config
            case 'voucherForm' : // use case - display voucher form
            case 'syncForm' : // use case - display synch form popup
                // execute match function
                $aDisplayInfo = call_user_func_array(array($this, 'display' . ucfirst($sType)), array($aParam));

                // check if 1.5 and multishop active and if group is selected
                $aDisplayInfo['assign']['bMultiShop'] = \MCE\Tools::checkGroupMultiShop();
                break;
            default :
                break;
        }
        // use case - generic assign
        if (!empty($aDisplayInfo)) {
            $aDisplayInfo['assign'] = array_merge($aDisplayInfo['assign'], $this->assign());
        }

        return $aDisplayInfo;
    }

    /**
     * Assigns transverse data
     *
     * @return array
     */
    private function assign()
    {
        // set smarty variables
        $assign = array(
            'sURI' => \MCE\Tools::truncateUri(array('&iPage', '&sAction')),
            'sCtrlParamName' => _MCE_PARAM_CTRL_NAME,
            'sController' => _MCE_ADMIN_CTRL,
            'sTpl' => \Tools::getValue('sTpl'),
            'sSubTpl' => \Tools::getValue('sSubTpl'),
            'aQueryParams' => $GLOBALS['MCE_REQUEST_PARAMS'],
            'iCurrentLang' => intval(\BTMailchimpEcommerce::$iCurrentLang),
            'sCurrentLang' => \BTMailchimpEcommerce::$sCurrentLang,
            'sLoader' => _MCE_URL_IMG . _MCE_LOADER_GIF,
            'sLoaderLarge' => _MCE_URL_IMG . _MCE_LOADER_GIF_BIG,
            'iCurlTest' => \BTMailchimpEcommerce::$conf['MCE_CURL_TEST'],
            'sApiKey' => \BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'],
            'bNewsletterConf' => \BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'],
            'bEcommerceConf' => \BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_ACTIVE'],
            'sHeaderInclude' => \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_HEADER),
            'sTopInclude' => \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_TOP),
            'sErrorInclude' => \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR),
            'sConfirmInclude' => \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_CONFIRM),
        );

        return $assign;
    }

    /**
     * Displays admin's first page with all tabs
     *
     * @param array $aPost
     * @return array
     */
    private function displayTabs(array $aPost)
    {
        // set smarty variables
        $assign = array(
            'sDocUri' => _MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/',
            'sDocName' => 'readme_' . ((\BTMailchimpEcommerce::$sCurrentLang == 'fr') ? 'fr' : 'en') . '.pdf',
            'sContactUs' => _MCE_SUPPORT_BT ? _MCE_SUPPORT_URL . ((\BTMailchimpEcommerce::$sCurrentLang == 'fr') ? 'fr/contactez-nous' : 'en/contact-us') : _MCE_SUPPORT_URL . ((\BTMailchimpEcommerce::$sCurrentLang == 'fr') ? 'fr/ecrire-au-developpeur?id_product=' . _MCE_SUPPORT_ID : 'en/write-to-developper?id_product=' . _MCE_SUPPORT_ID),
            'sRateUrl' => _MCE_SUPPORT_BT ? _MCE_SUPPORT_URL . ((\BTMailchimpEcommerce::$sCurrentLang == 'fr') ? 'fr/modules-prestashop-reseaux-sociaux-facebook/50-module-prestashop-publicites-de-produits-facebook-pixel-facebook-0656272916497.html' : 'en/prestashop-modules-social-networks-facebook/50-prestashop-addon-facebook-product-ads-facebook-pixel-0656272916497.html') : _MCE_SUPPORT_URL . ((\BTMailchimpEcommerce::$sCurrentLang == 'fr') ? '/fr/ratings.php' : '/en/ratings.php'),
            'bHideConfiguration' => \MCE\Warning::create()->bStopExecution,
        );

        // use case - get display mailchimp settings
        $aData = $this->displayMailchimp($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display exclusion settings
        $aData = $this->displayExclusion($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display user list settings
        $aData = $this->displayUserList($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display sync status settings
        $aData = $this->displaySyncStatus($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display NL config & sync settings
        $aData = $this->displayNewsletterConfig($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display MC Sign-up form settings
        $aData = $this->displaySignupForm($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display Ecommerce settings
        $aData = $this->displayEcommerce($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // use case - get display vouchers settings
        $aData = $this->displayVouchers($aPost);
        $assign = array_merge($assign, $aData['assign']);

        // assign all included templates files
        $assign['sMailchimpInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_GENERAL_MC);
        $assign['sExclusionInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_EXCLUSION);
        $assign['sUserListInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_USER_LIST);
        $assign['sSyncStatusInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . 'tools/'. _MCE_TPL_BODY);
        $assign['sNewsletterConfigInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_NL_CONFIG_SYNC);
        $assign['sNewsletterSignupInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . 'newsletter/signup/'. _MCE_TPL_BODY);
        $assign['sEcommerceInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . 'ecommerce/'. _MCE_TPL_BODY);
        $assign['sVoucherInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_VOUCHERS);
        $assign['sModuleVersion'] = \BTMailchimpEcommerce::$oModule->version;

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_BODY,
            'assign' => $assign,
        );
    }


    /**
     * Display the cURL test result
     *
     * @param array $aPost
     * @return array
     */
    private function displayTest(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        //set
        $assign = array();

        // init curl connexion
        $ch = curl_init('https://google.fr');

        // transfer test
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // exec curl
        curl_exec($ch);

        // error test and set error message
        if (curl_errno($ch) == 1) {
            $assign['bCurlSslCheck'] = false;
            \Configuration::updateValue('MCE_CURL_TEST', 2);
        } else {
            $assign['bCurlSslCheck'] = true;
            \Configuration::updateValue('MCE_CURL_TEST', 1);
        }

        // close curl connexion
        curl_close($ch);

        // force xhr mode activated
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_CURL_TEST,
            'assign' => $assign,
        );
    }

    /**
     * Displays mailchimp settings
     *
     * @param array $aPost
     * @return array
     */
    private function displayMailchimp(array $aPost = null)
    {
        $assign = array(
            'sApiKey' => \BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'],
            'iCookieTime' => (\BTMailchimpEcommerce::$conf['MCE_COOKIE_TTL'] / 60 / 60),
        );

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_GENERAL_MC,
            'assign' => $assign,
        );
    }


    /**
     * Displays exclusion settings
     *
     * @param array $aPost
     * @return array
     */
    private function displayExclusion(array $aPost = null)
    {
        $assign = array(
            'aEmailExclusions' => \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'],
            'sExclusionListInclude' => \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_EXCLUSION_LIST),
        );

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_EXCLUSION,
            'assign' => $assign,
        );
    }


    /**
     * Displays user list settings
     *
     * @param array $aPost
     * @return array
     */
    private function displayUserList(array $aPost = null)
    {
        $assign = array('aCurrentList' => []);

        if (!empty(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'])) {
            try {
                $assign['sApiKey'] = \BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'];
                $assign['aListsAndStores'] = \MCE\Chimp\Facade::getLists();
                $assign['aRootInfo'] = \MCE\Chimp\Facade::getRootInfo();

                // instantiate the MC's controller
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                if (!empty($assign['aListsAndStores'])) {
                    foreach ($assign['aListsAndStores'] as $index => $aList) {
                        if (empty($aList['active_ml'])) {
                            \MCE\Dao::deleteList(\BTMailchimpEcommerce::$iShopId, $aList['id']);
                            unset($assign['aListsAndStores'][$index]);
                        } elseif (!empty($aList['active'])) {
                            $assign['aCurrentList'] = $aList;

                            // create default merge_fields required by the module
                            $oMcCtrl->mergeFields->setId($aList['id']);
                            $aResult = $oMcCtrl->mergeFields->get(null, [], [], 50);

                            $aCreateMergeFields = array();

                            if (!empty($aResult['merge_fields'])) {
                                foreach ($GLOBALS['MCE_MERGE_FIELDS'] as $default_merge_field) {
                                    $is_exist = false;
                                    foreach ($aResult['merge_fields'] as $merge_field) {
                                        if ($merge_field['tag'] == $default_merge_field['tag']) {
                                            $is_exist = true;
                                        }
                                    }
                                    // check if exists, to create it or not
                                    if (!$is_exist) {
                                        $aCreateMergeFields[] = $default_merge_field;
                                    }
                                }
                            }

                            // if not empty merge field to create
                            if (!empty($aCreateMergeFields)) {
                                foreach ($aCreateMergeFields as $merge_field) {
                                    $opts = array('tag' => $merge_field['tag']);
                                    if (!empty($merge_field['options'])) {
                                        $opts['options'] = $merge_field['options'];
                                    }
                                    $aResult = \MCE\Chimp\Facade::addMergeField($aList['id'], $merge_field['name'], $merge_field['type'], $opts);
                                }
                            }
                        }
                    }
                }

                // get the default language
                $oLanguage = new \Language(\Configuration::get('PS_LANG_DEFAULT'));
                $assign['current_language'] = (array)$oLanguage;

                // shop info
                $assign['shop_id'] = \Context::getContext()->shop->id;
                $assign['shop_name'] = \Context::getContext()->shop->name;
                $assign['shop_email'] = \Configuration::get('PS_SHOP_EMAIL');
                $assign['shop_subject'] = \BTMailchimpEcommerce::$oModule->l('Our products on', 'AdminDisplay') .' '. $assign['shop_name'];
                $assign['double_optin'] = \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'];
                $assign['gdpr'] = \BTMailchimpEcommerce::$conf['MCE_GDPR'];
            } catch (MCEChimpMailchimpException $e) {
                $assign['aListStoreErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
            }
        }

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_USER_LIST,
            'assign' => $assign,
        );
    }


    /**
     * Displays synching status / tools
     *
     * @param array $aPost
     * @return array
     */
    private function displaySyncStatus(array $aPost = null)
    {
        $assign = array();
        $assign['bActiveNewsletter'] = \BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'];
        $assign['bActiveEcommerce'] = \BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_ACTIVE'];
        $assign['aLanguages'] = \Language::getLanguages(true);
        $assign['aSelectedLanguages'] = !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'])? \BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] : array(\Configuration::get('PS_LANG_DEFAULT'));
        $assign['sHistoryInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_DASHBOARD);
        $assign['sDashboardTableInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_DASHBOARD_TABLE);
        $assign['sDiagnosticInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_DIAGNOSTIC_TOOL);

        // check if a list is already activated and used
        $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
        $assign['aListStatus'] = !empty($aActiveList[0])? $aActiveList[0] : false;

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH .'tools/'. _MCE_TPL_BODY,
            'assign' => $assign,
        );
    }


    /**
     * Display the search popup to synch the data again
     *
     * @throws Exception
     * @param array $aPost
     * @return array
     */
    private function displaySearchPopup(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        //set
        $assign = array();
        $aDataToSync = null;

        try {
            // check if a list is already activated and used
            $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
            $assign['aListSync'] = !empty($aActiveList[0])? $aActiveList[0] : false;

            // get form elts
            $sDataType = \Tools::getValue('bt_data_type');
            $sEltId = \Tools::getValue('bt_elt_id');
            $iLangId = \Tools::getValue('bt_elt_lang_id');

            // use case - test the list ID
            if (empty($assign['aListSync']['id'])) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('There ins\'t valid list', 'AdminDisplay') . '.', 113);
            }
            // use case - test the type of data to check
            if (!$sDataType) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The type of the data you want to verify is not valid', 'AdminDisplay') . '.', 115);
            }
            // use case - test the element ID
            if (!$sEltId) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The element ID of the data you want to verify is not valid, it is mandatory', 'AdminDisplay') . '.', 116);
            }

            $assign['sListId'] = $assign['aListSync']['id'];
            $assign['sListName'] = $assign['aListSync']['name'];
            $assign['sStoreId'] = $assign['aListSync']['store_id'];
            $assign['sDataType'] = $sDataType;
            $assign['sEltId'] = $sEltId;
            $assign['iLangId'] = $iLangId;
            $assign['sLangName'] = (new \Language($iLangId))->name;

            // define the search type
            $sType = ($sDataType == 'member' || $sDataType == 'mergefield') ? 'list' : 'store';

            // define the type of data we need to check and sync
            switch ($sType) {
                case 'list':
                    // member
                    if ($sDataType == 'member') {
                        $iCustomerId = \Customer::customerExists($sEltId, true);

                        if (!empty($iCustomerId)) {
                            $oCustomer = new \Customer($iCustomerId);
                            $aDataToSync = (new \MCE\Chimp\Format\Member($oCustomer, $oCustomer->id_lang, \BTMailchimpEcommerce::$conf['MCE_CUST_TYPE_EXPORT'], \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN']))->format();
                        }
                    }
                    break;
                case 'store':
                    if (!empty($assign['sStoreId'])) {
                        switch ($sDataType) {
                            case 'product':
                                // get currency code and get the conversion rate
                                $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($assign['sStoreId'], 'currency');
                                $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                                // define the MC store currency to the context in order to calculate the product price according to the MC currency
                                $oCurrentCurrency = \Context::getContext()->currency;
                                \Context::getContext()->currency = new \Currency($iMCCurrencyId);

                                // set the product data format to sync them
                                $aDataToSync = (new \MCE\Chimp\Format\Product(
                                    $sEltId,
                                    $iLangId,
                                    \BTMailchimpEcommerce::$conf['MCE_CAT_LABEL_FORMAT'] == 'short' ? true : false,
                                    \BTMailchimpEcommerce::$conf['MCE_PROD_VENDOR_TYPE'],
                                    !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_format',
                                    \BTMailchimpEcommerce::$conf['MCE_PROD_DESC_TYPE'],
                                    '',
                                    false
                                ))->format();

                                // set the previous currency
                                \Context::getContext()->currency = $oCurrentCurrency;
                                break;
                            case 'variant':
                                if (strstr($sEltId, 'C')) {
                                    list($iProdId, $iAttributeId) = explode('C', $sEltId);

                                    $iAttributeId = 1;

                                    // get currency code and get the conversion rate
                                    $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($assign['sStoreId'], 'currency');
                                    $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                                    // define the MC store currency to the context in order to calculate the product price according to the MC currency
                                    $oCurrentCurrency = \Context::getContext()->currency;
                                    \Context::getContext()->currency = new \Currency($iMCCurrencyId);

                                    // set the product combination required
                                    $aDataToSync = (new \MCE\Chimp\Format\Combination(
                                        $iProdId,
                                        $iAttributeId,
                                        $iLangId,
                                        !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_format'
                                    ))->format();

                                    // set the previous currency
                                    \Context::getContext()->currency = $oCurrentCurrency;
                                } else {
                                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected the \'store\' type + product combination and so the product combination reference you have filled is not formatted well, it should be like product ID + V + product attribute id, then you have to write it like 1V1 for example.', 'AdminDisplay');
                                    $aResult['code'] = 404;
                                }
                                break;
                            case 'cart':
                                $oCart = new \Cart($sEltId);

                                if (\Validate::isLoadedObject($oCart)) {
                                    $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($assign['sStoreId'], 'currency');
                                    $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                                    // format cart data for MC
                                    $aDataToSync = (new \MCE\Chimp\Format\Cart($oCart->id, $iLangId, $iMCCurrencyId, $oCart->id_customer))->format();
                                }
                                break;
                            case 'order':
                                $oOrder = new \Order($sEltId);

                                if (\Validate::isLoadedObject($oOrder)) {
                                    $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($assign['sStoreId'], 'currency');

                                    $aOptions = array(
                                        'bDirectFormat' => true,
                                    );

                                    // format order data for MC
                                    $aDataToSync = (new \MCE\Chimp\Format\Order($oOrder->id, \Configuration::get('PS_LANG_DEFAULT'), $aOptions['iMCCurrencyId'], false, $aOptions))->format();
                                }
                                break;
                            case 'customer':
                                $oCustomer = new \Customer($sEltId);

                                if (\Validate::isLoadedObject($oCustomer)) {
                                    // detect the currency code
                                    $iMcCurrencyId = \Currency::getIdByIsoCode(\MCE\Chimp\Facade::getExplodeStoreId($assign['sStoreId'], 'currency'), \BTMailchimpEcommerce::$iShopId);

                                    // format the customer data
                                    $aDataToSync = (new \MCE\Chimp\Format\Customer($oCustomer, $oCustomer->id_lang, $iMcCurrencyId))->format();
                                }
                                break;
                            default:
                                break;
                        }
                    } else {
                        $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have activated the e-commerce option for the current list!', 'AdminDisplay');
                        $aResult['code'] = 404;
                    }
                    break;
                default:
                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have activated the e-commerce option for the current list!', 'AdminDisplay');
                    $aResult['code'] = 404;
                    break;
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }
        $assign['aDataToSync'] = $aDataToSync;

        // force xhr mode activated
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_SEARCH_POPUP,
            'assign' => $assign,
        );
    }

    /**
     * Displays NL config and sync settings
     *
     * @throws Exception
     * @param array $aPost
     * @return array
     */
    private function displayNewsletterConfig(array $aPost = null)
    {
        $assign = array();
        $assign['bActiveNewsletter'] = \BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'];
        $assign['iNewsletterModuleLang'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_LANG'];
        $assign['sExportCustType'] = \BTMailchimpEcommerce::$conf['MCE_CUST_TYPE_EXPORT'];
        $assign['aLanguages'] = \Language::getLanguages(true);
        $assign['aOldLists'] = \BTMailchimpEcommerce::$conf['MCE_OLD_CONFIG'];
        $assign['bOldSyncFlag'] = \BTMailchimpEcommerce::$conf['MCE_SYNC_OLD_LISTS_FLAG'];

        // check if a list is already activated and used
        $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
        $assign['aListNewsletter'] = !empty($aActiveList[0])? $aActiveList[0] : false;

        if (!empty($assign['aListNewsletter'])) {
            // set local for review date
            $sLocale = setlocale(LC_ALL, \BTMailchimpEcommerce::$sCurrentLang);

            // get the newsletter sync status
            $aSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $assign['aListNewsletter']['id'], 'newsletter');

            // check the customer sync status
            if (!empty($aSyncStatus)) {
                $assign['aListNewsletter']['active_catalog'] = $aSyncStatus['sync'];
                $assign['aListNewsletter']['sync_date'] = \MCE\Tools::formatTimestamp($aSyncStatus['date_add'], null, $sLocale);
                $assign['aListNewsletter']['sync_date_last'] = \MCE\Tools::formatTimestamp($aSyncStatus['date_upd'], null, $sLocale);

                // use case - get batches' status when the synchronization is in progress
                if ($aSyncStatus['sync'] == 2) {
                    // get the batches to check if the synchronization is over for the current shop / store / list
                    $assign['aListNewsletter']['batches'] = $this->getBatches($assign['aListNewsletter']['id'], \BTMailchimpEcommerce::$iShopId, 'newsletter');
                }
            } else {
                $assign['aListNewsletter']['active_catalog'] = 0;
            }
        }

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_NL_CONFIG_SYNC,
            'assign' => $assign,
        );
    }


    /**
     * Displays MC Sign-up form settings
     *
     * @throws Exception
     * @param array $aPost
     * @return array
     */
    private function displaySignupForm(array $aPost = null)
    {
        $assign = array();

        // check if a list is already activated and used
        $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
        $assign['aListSignup'] = !empty($aActiveList[0])? $aActiveList[0] : false;
        $assign['bActiveNewsletter'] = \BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'];
        $assign['bNewsletterModule'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE'];
        $assign['bNewsletterAjax'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_AJAX'];
        $assign['sSelectedModule'] = \BTMailchimpEcommerce::$conf['MCE_NL_SELECT_MODULE'];
        $assign['sModuleSubmit'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_SUBMIT'];
        $assign['sModuleFieldEmail'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_EMAIL_FIELD'];
        $assign['sModuleHtmlSelector'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_SELECTOR'];
        $assign['bActiveSignup'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP'];
        $assign['aMcSignupForm'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'];
        $assign['sSignupDisplay'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_DISPLAY'];
        $assign['aSignupLinkLabel'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_LINK_LABEL'];
        $assign['iPopupTimes'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TIMES'];
        $assign['aPopupPages'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_PAGES'];
        $assign['bPopupNotDisplayButton'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_NOT_DISPLAY'];
        $assign['aPopupText'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT'];
        $assign['iPopupWidth'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_WIDTH'];
        $assign['iPopupHeight'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_HEIGHT'];
        $assign['sTextValign'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_VALIGN'];
        $assign['sTextHalign'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_HALIGN'];
        $assign['sPopupTextVCenterCustom'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_VALIGN_CUSTOM'];
        $assign['sPopupTextHCenterCustom'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_HALIGN_CUSTOM'];
        $assign['bPopupImage'] = \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_IMAGE'];
        $assign['aPixelValues'] = $GLOBALS['MCE_POPUP_PIXEL_VALUES'];
        $assign['aPopupImages'] = [];
        $assign['aLanguages'] = \Language::getLanguages(true);
        $assign['sBaseAdminDir'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $iso = \Context::getContext()->language->iso_code;
        $assign['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $assign['sShortCode'] = '{$render_html nofilter}';

        \Context::getContext()->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        \Context::getContext()->controller->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        \Context::getContext()->controller->addJqueryPlugin('autosize');

        // load signup popup images if exist
        $assign['aPopupImages'] = \MCE\Tools::loadImage('signup-popup', \BTMailchimpEcommerce::$iShopId, true, $assign['aLanguages']);
        $assign['sNewsletterModuleInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_MODULE_SIGNUP_FORM);
        $assign['sMcFormInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_MC_SIGNUP_MC_FORM);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . 'newsletter/signup/'. _MCE_TPL_BODY,
            'assign' => $assign,
        );
    }



    /**
     * Displays ecommerce config / synch
     *
     * @throws Exception
     * @param array $aPost
     * @return array
     */
    private function displayEcommerce(array $aPost = null)
    {
        \MCE\Tools::translateCatLabelFormat();

        $assign = array();
        $assign['bActiveNewsletter'] = \BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'];
        $assign['bActiveEcommerce'] = \BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_ACTIVE'];
        $assign['aLanguages'] = \Language::getLanguages(true);
        $assign['aSelectedLanguages'] = !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'])? \BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] : array(\Configuration::get('PS_LANG_DEFAULT'));
        $assign['sCatLabelFormat'] = \BTMailchimpEcommerce::$conf['MCE_CAT_LABEL_FORMAT'];
        $assign['aCatLabelFormat'] = $GLOBALS['MCE_LABEL_FORMAT'];
        $assign['iDescType'] = \BTMailchimpEcommerce::$conf['MCE_PROD_DESC_TYPE'];
        $assign['sProdImgFormat'] = \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'];
        $assign['aDescriptionType'] = \MCE\Tools::getDescriptionType();
        $assign['aImageTypes'] = \ImageType::getImagesTypes('products');
        $assign['aCartMaxProd'] = $GLOBALS['MCE_CART_MAX_PROD'];
        $assign['iCartMaxProd'] = \BTMailchimpEcommerce::$conf['MCE_CART_MAX_PROD'];
        $assign['bUseCron'] = \MCE\Tools::isCronModeUsed();
        $assign['iItemsCronCycle'] = \BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_CRON_CYCLE'];
        $assign['sCronUrl'] = \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_CRON, array('bt_token' => \BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN'], 'id_shop' => \BTMailchimpEcommerce::$iShopId));
        $assign['sBatchDeleteUrl'] = \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_CRON, array('bt_token' => \BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN'], 'id_shop' => \BTMailchimpEcommerce::$iShopId, 'sType' => 'batchDelete'));
        $assign['sEcommerceConfigInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ECOMMERCE_CONFIG);
        $assign['sEcommerceSyncInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ECOMMERCE_SYNC);
        $assign['sToday'] = date('Y-m-d H:i:s', time());
        $assign['sPastYear'] = date('Y-m-d H:i:s', (time() - 31536000));
        $assign['aOrderStatuses'] = \MCE\Dao::getOrderStatus();
        $assign['sMemberExportMode'] = \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE'];
        $assign['sProductExportMode'] = \BTMailchimpEcommerce::$conf['MCE_PRODUCT_SYNC_MODE'];
        $assign['sCustomerExportMode'] = \BTMailchimpEcommerce::$conf['MCE_CUSTOMER_SYNC_MODE'];
        $assign['sCartExportMode'] = \BTMailchimpEcommerce::$conf['MCE_CART_SYNC_MODE'];
        $assign['sOrderExportMode'] = \BTMailchimpEcommerce::$conf['MCE_ORDER_SYNC_MODE'];
        $assign['bProductTax'] = \BTMailchimpEcommerce::$conf['MCE_PRODUCT_TAX'];
        $assign['sVendorType'] = \BTMailchimpEcommerce::$conf['MCE_PROD_VENDOR_TYPE'];

        // get pre-selection
        $aSelection = !empty(\BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION']) ? \BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION'] : array(2,3,4,5,7);
        $assign['aStatusSelection'] = $aSelection;

        // check if a list is already activated and used
        $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
        $assign['aListEcommerce'] = !empty($aActiveList[0])? $aActiveList[0] : false;
        $assign['aSynchronizedData'] = array('customer' => array('title' => \BTMailchimpEcommerce::$oModule->l('Customer list', 'AdminDisplay'), 'data' => array()), 'product' => array('title' => \BTMailchimpEcommerce::$oModule->l('Product catalog', 'AdminDisplay'), 'data' => array()), 'order' => array('title' => \BTMailchimpEcommerce::$oModule->l('Past orders', 'AdminDisplay'), 'data' => array()));

        // set local for review date
        $sLocale = setlocale(LC_ALL, \BTMailchimpEcommerce::$sCurrentLang);

        foreach ($assign['aSynchronizedData'] as $type => &$data) {
            // get the newsletter sync status
            $aSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $assign['aListEcommerce']['id'], $type);

            // check the customer sync status
            if (!empty($aSyncStatus)) {
                $data['data']['active_catalog'] = $aSyncStatus['sync'];
                $data['data']['sync_date'] = \MCE\Tools::formatTimestamp($aSyncStatus['date_add'], null, $sLocale);
                $data['data']['sync_date_last'] = \MCE\Tools::formatTimestamp($aSyncStatus['date_upd'], null, $sLocale);

                // use case - get batches' status when the synchronization is in progress
                if ($aSyncStatus['sync'] == 2) {
                    // get the batches to check if the synchronization is over for the current shop / store / list
                    $data['data']['batches'] = $this->getBatches($assign['aListEcommerce']['id'], \BTMailchimpEcommerce::$iShopId, $type);
                }
            }
        }

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH .'ecommerce/'. _MCE_TPL_BODY,
            'assign' => $assign,
        );
    }


    /**
     * Displays vouchers settings
     *
     * @param array $aPost
     * @return array
     */
    private function displayVouchers(array $aPost = null)
    {
        $assign = array();

        if (!empty(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'])) {
            try {
                \MCE\Tools::translateAutomationVouchers();
                $assign['sMCApiKey'] = true;
                $assign['aAutomations'] = $GLOBALS['MCE_AUTOMATION'];
                $assign['aVouchers'] = \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'];

                if (!empty($assign['aVouchers'])) {
                    foreach ($assign['aVouchers'] as &$aVoucher) {
                        foreach (\Language::getLanguages() as $aLang) {
                            $aVoucher['aModuleLangLinks'][] = array(
                                'id_lang' => $aLang['id_lang'],
                                'lang' => $aLang['name'],
                                'link' => \Context::getContext()->link->getModuleLink(
                                    _MCE_MODULE_SET_NAME,
                                    'voucher',
                                    array('type' => $aVoucher['type']),
                                    null,
                                    $aLang['id_lang']
                                )
                            );
                        }
                    }
                }

                $assign['sModuleVoucherCtrl'] = \Context::getContext()->link->getModuleLink(
                    _MCE_MODULE_SET_NAME,
                    'voucher',
                    array('test' => true),
                    null
                );
            } catch (\Exception $e) {
                $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
            }
        } else {
            $assign['sMCApiKey'] = false;
        }

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_VOUCHERS,
            'assign' => $assign,
        );
    }


    /**
     * Displays vouchers settings
     *
     * @param array $aPost
     * @return array
     */
    private function displayVoucherForm(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $assign = array();

        try {
            \MCE\Tools::translateAutomationVouchers();
            $assign['sMCApiKey'] = true;
            $assign['aAutomations'] = $GLOBALS['MCE_AUTOMATION'];
            $assign['aVouchers'] = \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'];
            $assign['aLangs'] = \Language::getLanguages();
            $assign['aCurrencies'] = \Currency::getCurrencies();

            // use case - detect if we are on update mode
            $sUpdateVoucher = \Tools::getValue('bt_voucher');

            if (!empty($sUpdateVoucher)
                && (isset($assign['aVouchers'][$sUpdateVoucher])
                    && !empty($assign['aVouchers'][$sUpdateVoucher]))
            ) {
                $assign['aCurrentVoucher'] = $assign['aVouchers'][$sUpdateVoucher];
                if (!isset($assign['aCurrentVoucher']['type'])) {
                    $assign['aCurrentVoucher']['type'] = $sUpdateVoucher;
                }
                // use case - check if the update voucher is of type "other" as custom automation
                $assign['aCurrentVoucher']['other'] = !array_key_exists($assign['aCurrentVoucher']['type'], $assign['aAutomations']) ? true : false;
            }

            // get available categories and manufacturers
            $aSelectedCategories = !empty($assign['aCurrentVoucher']['categories']) ? $assign['aCurrentVoucher']['categories'] : array();
            $aCategories = \Category::getCategories(intval(\BTMailchimpEcommerce::$iCurrentLang), false);
            $assign['aFormatCat'] = \MCE\Tools::recursiveCategoryTree($aCategories, $aSelectedCategories, current(current($aCategories)), 1, null, true);
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_VOUCHER_FORM,
            'assign' => $assign,
        );
    }


    /**
     * Displays customers / products / orders / users sync form
     *
     * @param array $aPost
     * @return array
     */
    private function displaySyncForm(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        try {
            $assign = array();

            $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
            $assign['aListSyncForm'] = !empty($aActiveList[0])? $aActiveList[0] : false;
            $assign['sSyncType'] = \Tools::getValue('type');
            $assign['iShopId'] = \BTMailchimpEcommerce::$iShopId;
            $assign['iProcessed'] = \Tools::getValue('processed') == false ? 0 : \Tools::getValue('processed');
            $assign['bBatch'] = \Tools::getValue('batch');
            $assign['redo'] = \Tools::getValue('redo');
            $assign['sLoader'] = _MCE_URL_IMG . _MCE_LOADER_GIF;
            $assign['iRefreshWaitingTime'] = _MCE_BT_REFRESH_WAITING_TIME;

            switch ($assign['sSyncType']) {
                case 'product' :
                    $assign['sTemplate'] = _MCE_TPL_PROD_SYNC_FORM;
                    $assign['iItemCycle'] = (int)\BTMailchimpEcommerce::$conf['MCE_PROD_PER_CYCLE'];
                    $assign['iTotal'] = \MCE\Dao::countProducts(\BTMailchimpEcommerce::$iShopId);
                    $assign['iTotalAll'] = \MCE\Dao::countProducts(\BTMailchimpEcommerce::$iShopId, true);
                    break;
                case 'customer' :
                    $assign['sTemplate'] = _MCE_TPL_CUST_SYNC_FORM;
                    $assign['iItemCycle'] = (int)\BTMailchimpEcommerce::$conf['MCE_CUST_PER_CYCLE'];
                    $assign['iTotal'] = \MCE\Dao::getCustomerData(true, \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']);
                    break;
                case 'newsletter' :
                    $assign['bOldSync'] = \tools::getValue('oldsync');
                    $assign['aOldLists'] = \BTMailchimpEcommerce::$conf['MCE_OLD_CONFIG'];

                    // use case - migration of old list members
                    if (!empty($assign['bOldSync'])
                        && !empty($assign['aOldLists'])
                    ) {
                        // instantiate the MC's controller
                        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                        $total = 0;

                        foreach ($assign['aOldLists'] as &$aList) {
                            // set the current list Id
                            $oMcCtrl->members->setId($aList['id']);

                            // get total members
                            $aResult = $oMcCtrl->members->get(null, [], [], 1);

                            if (isset($aResult['total_items'])) {
                                $aList['total'] = $aResult['total_items'];

                                $total += $aResult['total_items'];
                            }
                            // define the ISO language
                            $aList['language'] =  (new \Language($aList['lang_id']))->name;
                        }
                    } else {
                        $total = \MCE\Dao::getUsers(\BTMailchimpEcommerce::$iShopId, true, \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'], \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_LANG']);
                    }

                    $assign['sTemplate'] = _MCE_TPL_NL_SYNC_FORM;
                    $assign['iItemCycle'] = (int)\BTMailchimpEcommerce::$conf['MCE_MEMBER_PER_CYCLE'];
                    $assign['iTotal'] = $total;
                    break;
                case 'order' :
                    $assign['sTemplate'] = _MCE_TPL_ORDER_SYNC_FORM;
                    $assign['iItemCycle'] = (int)\BTMailchimpEcommerce::$conf['MCE_ORDER_PER_CYCLE'];
                    $sDateFrom = \Tools::getValue('sDateFrom');
                    $sDateTo = \Tools::getValue('sDateTo');

                    if (!empty($sDateFrom)) {
                        $iImportDateFrom = \MCE\Tools::getTimeStamp($sDateFrom, 'db');
                        // check if the date_to is set
                        if (!empty($sDateTo)) {
                            $iImportDateTo = \MCE\Tools::getTimeStamp($sDateTo, 'db');
                        } else {
                            $iImportDateTo = time();
                            $sDateTo = date('Y-m-d H:i:s', $iImportDateTo);
                        }

                        if ($iImportDateFrom < $iImportDateTo) {
                            // get pre-selection
                            $aSelection = !empty(\BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION']) ? \BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION'] : array(2, 3, 4, 5, 7);
                            $aOrders = \MCE\Dao::getOrdersIdByDate($sDateFrom, $sDateTo, \Configuration::get('PS_LANG_DEFAULT'), null, 'date_add', $aSelection);
                            $assign['aOrderStatuses'] = \MCE\Dao::getOrderStatus();
                            $assign['aStatusSelection'] = $aSelection;
                            $assign['iTotal'] = count($aOrders);
                            $assign['sDateFrom'] = $sDateFrom;
                            $assign['iDateFromTimestamp'] = $iImportDateFrom;
                            $assign['sDateTo'] = $sDateTo;
                            $assign['iDateToTimestamp'] = $iImportDateTo;
                            $assign['sReloadUrlParams'] = 'sAction=display&sType=syncForm&type=' . $assign['sSyncType'] . '&processed=' . $assign['iProcessed'] . '&sDateFrom=' . $sDateFrom . '&sDateTo=' . $sDateTo;

                        } else {
                            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The orders selection date start should be set as a previous date from the date end', 'AdminDisplay'), 110);
                        }
                    } else {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The orders selection date start is not valid', 'AdminDisplay'), 111);
                    }
                    break;
                default:
                    break;
            }

            // get the customers / products / orders status
            $aSyncStatus = \MCE\Dao::getSyncStatus($assign['iShopId'], $assign['aListSyncForm']['id'], $assign['sSyncType']);

            if (!empty($aSyncStatus)) {
                $assign['bAlreadySync'] = $aSyncStatus['sync'] == 1 ? true : false;
                $assign['sDateCreated'] = date('Y/m/d H:i:s', $aSyncStatus['date_add']);
                $assign['sDateUpdated'] = date('Y/m/d H:i:s', $aSyncStatus['date_upd']);
            }

            // get the batches to check if the synchronization is over for the current shop / store / list
            $assign['aBatches'] = $this->getBatches($assign['aListSyncForm']['id'], $assign['iShopId'], $assign['sSyncType']);

            // detect if we have refreshed the fancybox to display the number of the batches created and the number of products synchronized
            if (\Tools::getIsset('processed')
                || !empty($assign['bBatch'])
            ) {
                $assign['bDisplayDetails'] = true;
                $bCanClose = true;
                if (!empty($assign['aBatches'])) {
                    foreach ($assign['aBatches'] as $aBatch) {
                        if ($aBatch['status'] != 'finished') {
                            $bCanClose = false;
                        }
                    }
                }
                $assign['bCanClose'] = $bCanClose;

                if (empty($assign['bCanClose'])
                    && empty($assign['sReloadUrlParams'])
                ) {
                    $assign['sReloadUrlParams'] = 'sAction=display&sType=syncForm&type='. $assign['sSyncType'] .'&processed='. $assign['iProcessed'];
                }
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode activated
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';
        $assign['bAjaxMode'] = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . $assign['sTemplate'],
            'assign' => $assign,
        );
    }


    /**
     * Returns list of batch
     *
     * @throws Exception
     * @param string $sId
     * @param int $iShopId
     * @param string $sType
     * @param string $sMode
     * @return array
     */
    private function getBatches($sId, $iShopId, $sType = 'product', $sMode = 'manual')
    {
        $aOutputBatches = array();

        try {
            // get the batches to check if the synchronization is over for the current shop / list  / store
            $aBatches = \MCE\Dao::getBatches($iShopId, array('id' => $sId, 'type' => $sType, 'mode' => $sMode));

            if (!empty($aBatches)) {
                // get the MC matching object
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                foreach ($aBatches as &$aBatch) {
                    $aResult = $oMcCtrl->batches->get($aBatch['batch_id']);

                    if (!empty($aResult)) {
                        $aOutputBatches[] = array(
                            'id' => $aResult['id'],
                            'status' => $aResult['status'],
                            'total' => $aResult['total_operations'],
                            'finished' => $aResult['finished_operations'],
                            'errored' => $aResult['errored_operations'],
                            'floor' => $aBatch['floor'],
                            'step' => $aBatch['step'],
                            'next' => $aBatch['floor'] + $aBatch['step'],
                        );
                    }
                }
            }
        } catch (\Exception $e) {

        }

        return $aOutputBatches;
    }


    /**
     * Set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oDisplay;

        if (null === $oDisplay) {
            $oDisplay = new \MCE\AdminDisplay();
        }
        return $oDisplay;
    }
}

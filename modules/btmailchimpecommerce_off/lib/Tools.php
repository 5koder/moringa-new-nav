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

class Tools
{
    /**
     * returns current page type
     */
    public static function detectCurrentPage()
    {
        $sCurrentTypePage = '';

        // use case - home page
        if (\Tools::getValue('controller') == 'index') {
            $sCurrentTypePage = 'home';

            // use case - search results page
        } elseif (\Tools::getValue('controller') == 'search' && empty(\Context::getContext()->controller->module)) {
            $sCurrentTypePage = 'search';

            // use case - order page
        } elseif ((\Tools::getValue('controller') == 'order'
            || \Tools::getValue('controller') == 'orderopc')
            && \Tools::getValue('step') == false
        ) {
            $sCurrentTypePage = 'cart';

            // use case - category page
        } elseif (\Tools::getvalue('id_category')) {
            $sCurrentTypePage = 'category';

            // use case - product page
        } elseif (\Tools::getvalue('id_product')) {
            $sCurrentTypePage = 'product';

        } elseif (\Tools::getValue('controller') == 'manufacturer') {
            $sCurrentTypePage = 'manufacturer';

        } elseif (\Tools::getValue('controller') == 'pricesdrop') {
            $sCurrentTypePage = 'promotion';

        } elseif (\Tools::getValue('controller') == 'newproducts') {
            $sCurrentTypePage = 'newproducts';

        } elseif (\Tools::getValue('controller') == 'bestsales') {
            $sCurrentTypePage = 'bestsales';

        } elseif (\Tools::getValue('controller') == 'cart') {
            $sCurrentTypePage = 'cart';

        } elseif (\Tools::getValue('controller') == 'contact') {
            $sCurrentTypePage = 'contactus';

        } elseif (\Tools::getValue('controller') == 'stores') {
            $sCurrentTypePage = 'stores';

        } elseif (\Tools::getValue('controller') == 'cms') {
            $sCurrentTypePage = 'cms';

        } elseif (\Tools::getValue('controller') == 'authentication') {
            $sCurrentTypePage = 'authentication';

            // other
        } else {
            $sCurrentTypePage = 'other';
        }

        return $sCurrentTypePage;
    }

    /**
     * encode to secure the communication between 2 platforms as mailchimp and PS
     *
     * @param string $sHash
     * @param int $mElement1
     * @param int $mElement2
     * @return string
     */
    public static function setSecureKey($sHash, $mElement1, $mElement2)
    {
        return md5($mElement1 . $sHash . $mElement2);
    }

    /**
     * convert the amount in the good currency
     *
     * @param numeric $nAmount
     * @param int $iOrderCurrencyId
     * @param int $iMCCurrencyId
     * @return numeric
     */
    public static function convertAmount($nAmount, $iOrderCurrencyId, $iMCCurrencyId)
    {
        // detect if we have to calculate from the currency conversion rate to the default currency conversion rate
        if ($iOrderCurrencyId != $iMCCurrencyId) {
            $fConversionRate = self::getCurrency('conversion_rate', $iOrderCurrencyId);
            $nAmount = (float)($nAmount / $fConversionRate);
            $fConversionRate = self::getCurrency('conversion_rate', $iMCCurrencyId);
            $nAmount = (float)($nAmount / $fConversionRate);
        } else {
            $nAmount = (float)$nAmount;
        }

        return $nAmount;
    }


    /**
     * count the OK and KO element per sync type
     *
     * @param int $iSync
     * @param array $aSyncDetail
     * @return array
     */
    public static function countSyncDetail($sType, array $aSyncDetail)
    {
        $aReturn = array('ok' => 0, 'ko' => 0, 'details' => array('ok' => array(), 'ko' => array()));

        if (!empty($aSyncDetail)) {
            foreach ($aSyncDetail as &$aDetail) {
                switch ($sType) {
                    case 'customer':
                        $oCustomer = new \Customer($aDetail['id']);
                        $aDetail['name'] = \Tools::ucfirst($oCustomer->firstname) . ' ' . \Tools::ucfirst($oCustomer->lastname);
                        $aDetail['link'] = \Context::getContext()->link->getAdminLink('AdminCustomers') . '&id_customer=' . $aDetail['id'] . '&viewcustomer';
                        break;
                    case 'product':
                        $oProduct = new \Product($aDetail['id'], \BTMailchimpEcommerce::$iCurrentLang);
                        $aDetail['name'] = \Tools::ucfirst($oProduct->name);
                        $aDetail['link'] = \Context::getContext()->link->getAdminLink('AdminProducts') . '&id_product=' . $aDetail['id'] . '&viewproduct';
                        break;
                    case 'order':
                        $aDetail['link'] = \Context::getContext()->link->getAdminLink('AdminOrders') . '&id_order=' . $aDetail['id'] . '&vieworder';
                        break;
                    default:
                        break;
                }
                if (strstr($aDetail['detail'], 'ok')) {
                    ++$aReturn['ok'];
                    $aReturn['details']['ok'][] = $aDetail;
                } else {
                    ++$aReturn['ko'];
                    $aReturn['details']['ko'][] = $aDetail;
                }
            }
        }

        return $aReturn;
    }

    /**
     * format the product name with combination
     *
     * @param string $sProductName
     * @param int $iAttrId
     * @param int $iCurrentLang
     * @param int $iShopId
     * @return string
     */
    public static function getProductCombinationName($sProductName, $iAttrId, $iCurrentLang, $iShopId)
    {
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $aCombinations = \MCE\Dao::getCombinationAttributeNames($iAttrId, $iCurrentLang, $iShopId);

        if (!empty($aCombinations)) {
            $sExtraName = '';
            foreach ($aCombinations as $c) {
                $sExtraName .= ' ' . \Tools::stripslashes($c['name']);
            }
            $sProductName .= $sExtraName;
        }

        return $sProductName;
    }


    /**
     * returns the product's combination link
     *
     * @param int $iShopId
     * @param int $iProdId
     * @param int $iProdAttributeId
     * @param int $iLangId
     * @return mixed
     */
    public static function getCombinationLink($iShopId, $iProdId, $iProdAttributeId, $iLangId)
    {
        if (!empty(\BTMailchimpEcommerce::$bCompare17)) {
            $sBaseLink = \Context::getContext()->link->getProductLink((int)$iProdId, null, null, null, $iLangId, null, (int)$iProdAttributeId);
        } else {
            $sBaseLink = \Context::getContext()->link->getProductLink($iProdId, null, null, null, $iLangId);
            $sQuery = 'SELECT distinct(al.`name`), agl.`name` as group_name, a.`id_attribute`'
                . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas'
                . ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pas.`id_product_attribute`'
                . ' LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON pac.`id_attribute` = a.`id_attribute`'
                . ' LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute`)'
                . ' LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON a.id_attribute_group = agl.id_attribute_group'
                . ' WHERE pac.`id_product_attribute` = ' . (int)$iProdAttributeId
                . ' AND al.`id_lang` = ' . (int)$iLangId
                . ' AND agl.`id_lang` = ' . (int)$iLangId
                . ' AND pas.id_shop = ' . (int)$iShopId
                . ' ORDER BY al.`name`';

            $aResult = \Db::getInstance()->ExecuteS($sQuery);

            if (!empty($aResult)) {
                $sBaseLink .= '#/';

                foreach ($aResult as $id => $aRow) {
                    $sBaseLink .= (version_compare(_PS_VERSION_, '1.6.0.13', '>=') ? $aRow['id_attribute'] . \Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') : '');
                    $sBaseLink .= str_replace(\Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', \Tools::link_rewrite($aRow['group_name']));
                    $sBaseLink .= \Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') . str_replace(\Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', \Tools::link_rewrite($aRow['name'])) . ((isset($aResult[$id + 1])) ? '/' : '');
                }
            }
        }

        return $sBaseLink;
    }


    /**
     * returns good translated errors
     */
    public static function translateJsMsg()
    {
        $GLOBALS['MCE_JS_MSG']['apikey'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp API key', 'Tools');
        $GLOBALS['MCE_JS_MSG']['cookie'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the cookie lifetime', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listName'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp list name', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listReminder'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t define the GDPR fields option for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listCompanyName'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp company name for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listCompanyAddress'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp company address for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listCompanyCity'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp company city for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listCompanyZip'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp company zip code for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listCompanyCountry'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp company country for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listFromName'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp sender\'s name for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listFromEmail'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp sender\'s e-mail address for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['listSubject'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the MailChimp e-mails default subject for the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['storeListId'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select any list to associate with', 'Tools');
        $GLOBALS['MCE_JS_MSG']['storeCurrency'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select any currency to associate with', 'Tools');
        $GLOBALS['MCE_JS_MSG']['statuses'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select any status in the list', 'Tools');
        $GLOBALS['MCE_JS_MSG']['delay'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select any value for the number of days to return', 'Tools');
        $GLOBALS['MCE_JS_MSG']['automation'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill any e-mails campaign type', 'Tools');
        $GLOBALS['MCE_JS_MSG']['customAutomation'] = \BTMailchimpEcommerce::$oModule->l('You have selected "Other" as e-mails campaign type but you didn\'t fill any name for this campaign type', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherCode'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the prefix of the voucher code', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherDiscountType'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select any discount type', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherAmount'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the amount value', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherPercent'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill the percentage value', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherMinimum'] = \BTMailchimpEcommerce::$oModule->l('The value you filled for the minimum is not a numeric value', 'Tools');
        $GLOBALS['MCE_JS_MSG']['voucherValidity'] = \BTMailchimpEcommerce::$oModule->l('The value you filled for the validity is not a numeric value or is set to 0', 'Tools');
        $GLOBALS['MCE_JS_MSG']['mcFormCode'] = \BTMailchimpEcommerce::$oModule->l('You have activated the use of a block newsletter module but you didn\'t fill any HTML code in the field', 'Tools');
        $GLOBALS['MCE_JS_MSG']['signupDisplay'] = \BTMailchimpEcommerce::$oModule->l('You have activated the MailChimp sign-up form but you didn\'t select the way to display it', 'Tools');
        $GLOBALS['MCE_JS_MSG']['signupPopupTextAlign'] = \BTMailchimpEcommerce::$oModule->l('You didn\'t fill a NUMERIC value between 1 and 100', 'Tools');

        foreach (\Language::getLanguages() as $aLang) {
            $GLOBALS['MCE_JS_MSG']['voucherName'][$aLang['id_lang']] = \BTMailchimpEcommerce::$oModule->l('You have several languages activated on your shop and you didn\'t fill in the voucher name for each language.', 'Tools')
                . ' "' . $aLang['name'] . '". ' . \BTMailchimpEcommerce::$oModule->l('Click on the languages drop-down list in order to fill in the field for each language.', 'Tools');
        }
    }


    /**
     * sets display category label format's titles
     */
    public static function translateCatLabelFormat()
    {
        $GLOBALS['MCE_LABEL_FORMAT']['short'] = \BTMailchimpEcommerce::$oModule->l('Standard current category name (short)', 'Tools');
        $GLOBALS['MCE_LABEL_FORMAT']['long'] = \BTMailchimpEcommerce::$oModule->l('Full breadcrumb category name (long)', 'Tools');
    }

    /**
     * sets display automation voucher titles
     */
    public static function translateAutomationVouchers()
    {
        $GLOBALS['MCE_AUTOMATION']['best'] = \BTMailchimpEcommerce::$oModule->l('Best Customers', 'Tools');
        $GLOBALS['MCE_AUTOMATION']['reengagement'] = \BTMailchimpEcommerce::$oModule->l('Win back lapsed customers', 'Tools');
    }

    /**
     * update new keys in new module version
     */
    public static function updateConfiguration()
    {
        // check to update new module version
        foreach ($GLOBALS['MCE_CONFIGURATION'] as $sKey => $mVal) {
            // use case - not exists
            if (\Configuration::get($sKey) === false) {
                // update key/ value
                \Configuration::updateValue($sKey, $mVal);
            }
        }
    }

    /**
     * set all constant module in ps_configuration
     *
     * @param array $aOptionListToUnserialize
     * @param int $iShopId
     */
    public static function getConfig(array $aOptionListToUnserialize = null, $iShopId = null)
    {
        // get configuration options
        if (null !== $iShopId && is_numeric($iShopId)) {
            \BTMailchimpEcommerce::$conf = \Configuration::getMultiple(array_keys($GLOBALS['MCE_CONFIGURATION']), null, null, $iShopId);
        } else {
            \BTMailchimpEcommerce::$conf = \Configuration::getMultiple(array_keys($GLOBALS['MCE_CONFIGURATION']));
        }

        if (!empty($GLOBALS['MCE_CONF_SERIALIZED_LIST'])) {
            if ($aOptionListToUnserialize !== null && is_array($aOptionListToUnserialize)) {
                $aOptionListToUnserialize = array_merge($GLOBALS['MCE_CONF_SERIALIZED_LIST'], $aOptionListToUnserialize);
            } else {
                $aOptionListToUnserialize = $GLOBALS['MCE_CONF_SERIALIZED_LIST'];
            }
        }

        if (!empty($aOptionListToUnserialize)
            && is_array($aOptionListToUnserialize)
        ) {
            foreach ($aOptionListToUnserialize as $sOption) {
                if (!empty(\BTMailchimpEcommerce::$conf[strtoupper($sOption)])
                    && is_string(\BTMailchimpEcommerce::$conf[strtoupper($sOption)])
                    && !is_numeric(\BTMailchimpEcommerce::$conf[strtoupper($sOption)])
                ) {
                    \BTMailchimpEcommerce::$conf[strtoupper($sOption)] = unserialize(\BTMailchimpEcommerce::$conf[strtoupper($sOption)]);
                }
            }
        }
    }

    /**
     * check if multi-shop is activated and if the group or global context is used
     *
     * @return bool
     */
    public static function checkGroupMultiShop()
    {
        return (
            \Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
            && (strpos(\BTMailchimpEcommerce::$oCookie->shopContext, 'g-') !== false
            || empty(\BTMailchimpEcommerce::$oCookie->shopContext))
        );
    }


    /**
     * defines if the language is active
     *
     * @param mixed $mLang
     * @return bool
     */
    public static function isActiveLang($mLang)
    {
        if (is_numeric($mLang)) {
            $sField = 'id_lang';
        } else {
            $sField = 'iso_code';
            $mLang = strtolower($mLang);
        }

        $mResult = \Db::getInstance()->getValue('SELECT count(*) FROM `' . _DB_PREFIX_ . 'lang` WHERE active = 1 AND `' . $sField . '` = "' . pSQL($mLang) . '"');

        return !empty($mResult) ? true : false;
    }

    /**
     * set good iso lang
     *
     * @return string
     */
    public static function getLangIso($iLangId = null)
    {
        if (null === $iLangId) {
            $iLangId = \BTMailchimpEcommerce::$iCurrentLang;
        }

        // get iso lang
        $sIsoLang = \Language::getIsoById($iLangId);

        if (false === $sIsoLang) {
            $sIsoLang = 'en';
        }
        return $sIsoLang;
    }

    /**
     * return Lang id from iso code
     *
     * @param string $sIsoCode
     * @return int
     */
    public static function getLangId($sIsoCode, $iDefaultId = null)
    {
        // get iso lang
        $iLangId = \Language::getIdByIso($sIsoCode);

        if (empty($iLangId) && $iDefaultId !== null) {
            $iLangId = $iDefaultId;
        }
        return $iLangId;
    }

    /**
     * returns current currency sign or id
     *
     * @param string $sField : field name has to be returned
     * @param string $iCurrencyId : currency id
     * @return mixed : string or array
     */
    public static function getCurrency($sField = null, $iCurrencyId = null)
    {
        // set
        $mCurrency = null;

        // get currency id
        if (null === $iCurrencyId) {
            $iCurrencyId = \Configuration::get('PS_CURRENCY_DEFAULT');
        }

        $aCurrency = \Currency::getCurrency($iCurrencyId);

        if ($sField !== null) {
            switch ($sField) {
                case 'id_currency' :
                    $mCurrency = $aCurrency['id_currency'];
                    break;
                case 'name' :
                    $mCurrency = $aCurrency['name'];
                    break;
                case 'iso_code' :
                    $mCurrency = $aCurrency['iso_code'];
                    break;
                case 'iso_code_num' :
                    $mCurrency = $aCurrency['iso_code_num'];
                    break;
                case 'sign' :
                    if (empty($aCurrency['sign'])) {
                        $oCurrency = new \Currency($iCurrencyId);
                        if (!empty($oCurrency)) {
                            $mCurrency = $oCurrency->getSign();
                        }
                    } else {
                        $mCurrency = $aCurrency['sign'];
                    }
                    break;
                case 'conversion_rate' :
                    $mCurrency = $aCurrency['conversion_rate'];
                    break;
                case 'format' :
                    if (empty($aCurrency['sign'])) {
                        $oCurrency = new \Currency($iCurrencyId);
                        if (!empty($oCurrency)) {
                            $mCurrency = $oCurrency->format;
                        }
                    } else {
                        $mCurrency = $aCurrency['format'];
                    }
                    break;
                default:
                    $mCurrency = $aCurrency;
                    break;
            }
        }

        return $mCurrency;
    }

    /**
     * returns timestamp
     *
     * @param string $sDate
     * @param string $sType
     * @return mixed : bool or int
     */
    public static function getTimeStamp($sDate, $sType = 'en')
    {
        // set variable
        $iTimeStamp = false;

        // get date
        $aTmpDate = explode(' ', str_replace(array('-', '/', ':'), ' ', $sDate));

        if (count($aTmpDate) > 1) {
            $iHour = isset($aTmpDate[3]) ? $aTmpDate[3] : 0;
            $iMin = isset($aTmpDate[4]) ? $aTmpDate[4] : 0;
            $iSec = isset($aTmpDate[5]) ? $aTmpDate[5] : 0;

            if ($sType == 'en') {
                $iTimeStamp = mktime($iHour, $iMin, $iSec, $aTmpDate[0], $aTmpDate[1], $aTmpDate[2]);
            } elseif ($sType == 'db') {
                $iTimeStamp = mktime($iHour, $iMin, $iSec, $aTmpDate[1], $aTmpDate[2], $aTmpDate[0]);
            } else {
                $iTimeStamp = mktime($iHour, $iMin, $iSec, $aTmpDate[1], $aTmpDate[0], $aTmpDate[2]);
            }
        }

        return $iTimeStamp;
    }

    /**
     * returns valid ISO format date
     *
     * @param string $sDate
     * @param string $sType
     * @return string
     */
    public static function getUntilDate($sDate, $sType = 'en')
    {
        // set
        $sUntilDate = '';

        // get timestamp
        $iTimestamp = self::getTimeStamp($sDate, $sType);

        if ($iTimestamp && $iTimestamp > time()) {
            $sUntilDate = date('Y-m-d', $iTimestamp);
        }

        return $sUntilDate;
    }


    /**
     * returns a formatted date
     *
     * @param int $iTimestamp
     * @param mixed $mLocale
     * @param string $sLangIso
     * @return string
     */
    public static function formatTimestamp($iTimestamp, $sTemplate = null, $mLocale = false, $sLangIso = null)
    {
        // set
        $sDate = '';

        if ($mLocale !== false) {
            if (null === $sTemplate) {
                $sTemplate = '%d %h. %Y';
            }
            // set date with locale format
            $sDate = strftime($sTemplate, $iTimestamp);
        } else {
            // get Lang ISO
            $sLangIso = ($sLangIso !== null) ? $sLangIso : \BTMailchimpEcommerce::$sCurrentLang;

            switch ($sTemplate) {
                case 'snippet' :
                    $sDate = date('d', $iTimestamp)
                        . ' '
                        . (!empty($GLOBALS['MCE_MONTH'][$sLangIso]) ? $GLOBALS['MCE_MONTH'][$sLangIso]['long'][date('n',
                            $iTimestamp)] : date('M', $iTimestamp))
                        . ' '
                        . date('Y', $iTimestamp);
                    break;
                default:
                    // set date with matching month or with default language
                    $sDate = date('d', $iTimestamp)
                        . ' '
                        . (!empty($GLOBALS['MCE_MONTH'][$sLangIso]) ? $GLOBALS['MCE_MONTH'][$sLangIso]['short'][date('n',
                            $iTimestamp)] : date('M', $iTimestamp))
                        . ' '
                        . date('Y', $iTimestamp);
                    break;
            }
        }
        return $sDate;
    }


    /**
     * returns formatted URI for page name type
     *
     * @return mixed
     */
    public static function getPageName()
    {
        $sScriptName = '';

        // use case - script name filled
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $sScriptName = $_SERVER['SCRIPT_NAME'];
        } // use case - php_self filled
        elseif ($_SERVER['PHP_SELF']) {
            $sScriptName = $_SERVER['PHP_SELF'];
        } // use case - default script name
        else {
            $sScriptName = 'index.php';
        }
        return substr(basename($sScriptName), 0, strpos(basename($sScriptName), '.'));
    }


    /**
     * returns template path
     *
     * @param string $sTemplate
     * @return string
     */
    public static function getTemplatePath($sTemplate)
    {
        // set
        $mTemplatePath = null;

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $mTemplatePath = \BTMailchimpEcommerce::$oModule->getTemplatePath($sTemplate);
        } else {
            if (file_exists(_PS_THEME_DIR_ . 'modules/' . \BTMailchimpEcommerce::$oModule->name . '/' . $sTemplate)) {
                $mTemplatePath = _PS_THEME_DIR_ . 'modules/' . \BTMailchimpEcommerce::$oModule->name . '/' . $sTemplate;
            } elseif (file_exists(_PS_MODULE_DIR_ . \BTMailchimpEcommerce::$oModule->name . '/' . $sTemplate)) {
                $mTemplatePath = _PS_MODULE_DIR_ . \BTMailchimpEcommerce::$oModule->name . '/' . $sTemplate;
            }
        }

        return $mTemplatePath;
    }


    /**
     * returns link object
     *
     * @param string $sURI : relative URI
     * @return obj
     */
    public static function getLoginLink($sURI)
    {
        // for 1.5
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $sLoginURI = \Context::getContext()->link->getPageLink('authentication',
                    true) . (\Configuration::get('PS_REWRITING_SETTINGS') ? '?' : '&') . 'back=' . urlencode(self::detectHttpUri($sURI));
        } // over 1.4
        elseif (version_compare(_PS_VERSION_, '1.4', '>')) {
            $sLoginURI = '/authentication.php?back=' . urlencode($sURI);
        } // under 1.4
        else {
            $sURI = substr($sURI, 1, strlen($sURI));
            $sLoginURI = '/authentication.php?back=' . urlencode($sURI);
        }

        return $sLoginURI;
    }

    /**
     * returns product image
     *
     * @param obj $oProduct
     * @param string $sImageType
     * @param array $aForceImage
     * @param string $sForceDomainName
     * @param int $iLangId
     * @return mixed: string or false
     */
    public static function getProductImage(
        $oProduct,
        $sImageType = null,
        $aForceImage = false,
        $sForceDomainName = null,
        $iLangId = null
    ) {
        $sImgUrl = '';

        if (\Validate::isLoadedObject($oProduct)) {
            // use case - get Image
            $aImage = $aForceImage !== false ? $aForceImage : \Image::getCover($oProduct->id);

            // empty cover
            if (empty($aImage)) {
                if (empty($iLangId)) {
                    $iLangId = \BTMailchimpEcommerce::$iCurrentLang;
                }
                $aImages = $oProduct->getImages($iLangId);

                if (!empty($aImages[0])) {
                    $aImage = array('id_image' => $aImages[0]['id_image']);
                }
            }
            // not empty image
            if (!empty($aImage['id_image'])) {
                // get image url
                if ($sImageType !== null) {
                    $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image'], $sImageType);
                } else {
                    $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image']);
                }
            }

            if (!empty($sImgUrl)) {
                // use case - get valid IMG URI before  Prestashop 1.4
                $sImgUrl = self::detectHttpUri($sImgUrl, $sForceDomainName);
            }
        }

        return !empty($sImgUrl) ? $sImgUrl : false;
    }

    /**
     * detects and returns available URI - resolve Prestashop compatibility
     *
     * @param string $sURI
     * @param string $sForceDomainName
     * @return mixed
     */
    public static function detectHttpUri($sURI, $sForceDomainName = null)
    {
        // use case - only with relative URI
        if (!strstr($sURI, 'http')) {
            $sHost = $sForceDomainName !== null ? $sForceDomainName : $_SERVER['HTTP_HOST'];
            $sURI = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '') . '://' . $sHost . $sURI;
        } elseif ($sForceDomainName !== null
            && !strstr($sURI, $sForceDomainName)
            && strstr($sURI, $_SERVER['HTTP_HOST'])
        ) {
            $sTmpDomainName = str_replace('//', '', substr($sForceDomainName, strpos($sForceDomainName, '//'), strlen($sForceDomainName)));
            $sURI = str_replace($_SERVER['HTTP_HOST'], $sTmpDomainName, $sURI);
        }
        return $sURI;
    }

    /**
     * truncate current request_uri in order to delete params : sAction and sType
     *
     * @param mixed: string or array $mNeedle
     * @return mixed
     */
    public static function truncateUri($mNeedle = '&sAction', $sURI = '')
    {
        // set tmp
        $aQuery = is_array($mNeedle) ? $mNeedle : array($mNeedle);

        // get URI
        $sURI = !empty($sURI) ? $sURI : $_SERVER['REQUEST_URI'];

        foreach ($aQuery as $sNeedle) {
            $sURI = strstr($sURI, $sNeedle) ? substr($sURI, 0, strpos($sURI, $sNeedle)) : $sURI;
        }
        return $sURI;
    }

    /**
     * detects available method and apply json encode
     *
     * @return string
     */
    public static function jsonEncode($aData)
    {
        if (method_exists('\Tools', 'jsonEncode')) {
            $aData = \Tools::jsonEncode($aData);
        } elseif (function_exists('json_encode')) {
            $aData = json_encode($aData);
        } else {
            if (is_null($aData)) {
                return 'null';
            }
            if ($aData === false) {
                return 'false';
            }
            if ($aData === true) {
                return 'true';
            }
            if (is_scalar($aData)) {
                $aData = addslashes($aData);
                $aData = str_replace("\n", '\n', $aData);
                $aData = str_replace("\r", '\r', $aData);
                $aData = preg_replace('{(</)(script)}i', "$1'+'$2", $aData);
                return "'$aData'";
            }
            $isList = true;
            for ($i = 0, reset($aData); $i < count($aData); $i++, next($aData)) {
                if (key($aData) !== $i) {
                    $isList = false;
                    break;
                }
            }
            $result = array();

            if ($isList) {
                foreach ($aData as $v) {
                    $result[] = self::json_encode($v);
                }
                $aData = '[ ' . join(', ', $result) . ' ]';
            } else {
                foreach ($aData as $k => $v) {
                    $result[] = self::json_encode($k) . ': ' . self::json_encode($v);
                }
                $aData = '{ ' . join(', ', $result) . ' }';
            }
        }

        return $aData;
    }

    /**
     * detects available method and apply json decode
     *
     * @return mixed
     */
    public static function jsonDecode($aData)
    {
        if (method_exists('\Tools', 'jsonDecode')) {
            $aData = \Tools::jsonDecode($aData);
        } elseif (function_exists('json_decode')) {
            $aData = json_decode($aData);
        }
        return $aData;
    }

    /**
     * check if specific module and module's vars are available
     *
     * @param int $sModuleName
     * @param array $aCheckedVars
     * @param bool $bObjReturn
     * @param bool $bOnlyInstalled
     * @return mixed : true or false or obj
     */
    public static function isInstalled(
        $sModuleName,
        array $aCheckedVars = array(),
        $bObjReturn = false,
        $bOnlyInstalled = false
    ) {
        $mReturn = false;

        // use case - check module is installed in DB
        if (\Module::isInstalled($sModuleName)) {
            if (!$bOnlyInstalled) {
                $oModule = \Module::getInstanceByName($sModuleName);

                if (!empty($oModule)) {
                    // check if module is activated
                    $aActivated = \Db::getInstance()->ExecuteS('SELECT id_module as id, active FROM ' . _DB_PREFIX_ . 'module WHERE name = "' . pSQL($sModuleName) . '" AND active = 1');

                    if (!empty($aActivated[0]['active'])) {
                        $mReturn = true;

                        if (version_compare(_PS_VERSION_, '1.5', '>')) {
                            $aActivated = \Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . (int)$aActivated[0]['id'] . ' AND id_shop = ' . (int)\Context::getContext()->shop->id);

                            if (empty($aActivated)) {
                                $mReturn = false;
                            }
                        }

                        if ($mReturn) {
                            if (!empty($aCheckedVars)) {
                                foreach ($aCheckedVars as $sVarName) {
                                    $mVar = \Configuration::get($sVarName);

                                    if (empty($mVar)) {
                                        $mReturn = false;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($mReturn && $bObjReturn) {
                    $mReturn = $oModule;
                }
            } else {
                $mReturn = true;
            }
        }
        return $mReturn;
    }

    /**
     * check if the product is a valid obj
     *
     * @param int $iProdId
     * @param int $iLangId
     * @param bool $bObjReturn
     * @param bool $bAllProperties
     * @return mixed : true or false
     */
    public static function isProductObj($iProdId, $iLangId, $bObjReturn = false, $bAllProperties = false)
    {
        // set
        $bReturn = false;

        $oProduct = new \Product($iProdId, $bAllProperties, $iLangId);

        if (\Validate::isLoadedObject($oProduct)) {
            $bReturn = true;
        }

        return !empty($bObjReturn) && $bReturn ? $oProduct : $bReturn;
    }

    /**
     * write breadcrumbs of product for category
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param bool $bCatNameOnly
     * @param string $sPath
     * @param string $sForcePipe
     * @param bool $bEncoding
     * @return string
     */
    public static function getProductPath(
        $iCatId,
        $iLangId,
        $bCatNameOnly = false,
        $sPath = '',
        $sForcePipe = '',
        $bEncoding = true
    ) {
        $sLabel = '';

        $oCategory = new \Category($iCatId, $iLangId);

        if ($bCatNameOnly) {
            $sLabel = $oCategory->name;
        } else {
            $sLabel = \Validate::isLoadedObject($oCategory) ? strip_tags(self::getPath((int)$oCategory->id, (int)$iLangId, $sPath, $sForcePipe, $bEncoding)) : '';
        }

        return $sLabel;
    }

    /**
     * write breadcrumbs of product for category
     *
     * Forced to redo the function from Tools here as it works with cookie
     * for language, not a passed parameter in the function
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param string $sPath
     * @param string $sForcePipe = ''
     * @param bool $bHtml
     * @return string
     */
    public static function getPath($iCatId, $iLangId, $sPath = '', $sForcePipe = '', $bHtml = true)
    {
        $mReturn = '';

        if ($iCatId == 1) {
            $mReturn = $sPath;
        } else {
            // get pipe
            if (empty($sForcePipe)) {
                $sPipe = \Configuration::get('PS_NAVIGATION_PIPE');

                if (empty($sPipe)) {
                    $sPipe = '>';
                }
            } else {
                $sPipe = $sForcePipe;
            }

            $sFullPath = '';

            $aInterval = \Category::getInterval($iCatId);
            $aIntervalRoot = \Category::getInterval(\Context::getContext()->shop->getCategory());

            if (!empty($aInterval) && !empty($aIntervalRoot)) {
                $sQuery = 'SELECT c.id_category, cl.name, cl.link_rewrite'
                    . ' FROM ' . _DB_PREFIX_ . 'category c'
                    . \Shop::addSqlAssociation('category', 'c', false)
                    . ' LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . \Shop::addSqlRestrictionOnLang('cl') . ')'
                    . 'WHERE c.nleft <= ' . $aInterval['nleft']
                    . ' AND c.nright >= ' . $aInterval['nright']
                    . ' AND c.nleft >= ' . $aIntervalRoot['nleft']
                    . ' AND c.nright <= ' . $aIntervalRoot['nright']
                    . ' AND cl.id_lang = ' . (int)$iLangId
                    . ' AND c.level_depth > ' . (int)$aIntervalRoot['level_depth']
                    . ' ORDER BY c.level_depth ASC';

                $aCategories = \Db::getInstance()->executeS($sQuery);

                $iCount = 1;
                $nCategories = count($aCategories);

                foreach ($aCategories as $aCategory) {
                    if ($bHtml) {
                        $sFullPath .= htmlentities($aCategory['name'], ENT_NOQUOTES, 'UTF-8')
                            . (($iCount++ != $nCategories OR !empty($sPath)) ? '<span class="navigation-pipe">' . $sPipe . '</span>' : '');
                    } else {
                        $sFullPath .= $aCategory['name'] . (($iCount++ != $nCategories OR !empty($sPath)) ? $sPipe : '');
                    }
                }
                $mReturn = $sFullPath . $sPath;
            }
        }

        return $mReturn;
    }


    /**
     * returns all available description
     */
    public static function getDescriptionType()
    {
        return array(
            1 => \BTMailchimpEcommerce::$oModule->l('Short description', 'Tools'),
            2 => \BTMailchimpEcommerce::$oModule->l('Long description', 'Tools'),
            3 => \BTMailchimpEcommerce::$oModule->l('Both', 'Tools'),
            4 => \BTMailchimpEcommerce::$oModule->l('Meta-description', 'Tools')
        );
    }


    /**
     * returns a cleaned desc string
     *
     * @param int $iType
     * @param string $sShortDesc
     * @param string $sLongDesc
     * @param string $sMetaDesc
     * @return string
     */
    public static function getProductDesc($iType, $sShortDesc, $sLongDesc, $sMetaDesc)
    {
        // set product description
        switch ($iType) {
            case 1:
                $sDesc = $sShortDesc;
                break;
            case 2:
                $sDesc = $sLongDesc;
                break;
            case 3:
                $sDesc = $sShortDesc . '<br />' . $sLongDesc;
                break;
            case 4:
                $sDesc = $sMetaDesc;
                break;
            default:
                $sDesc = (!empty($sShortDesc) ? $sShortDesc : (!empty($sLongDesc) ? $sLongDesc : $sMetaDesc));
                break;
        }
        return $sDesc;
    }


    /**
     * process categories to generate tree of them
     *
     * @param array $aCategories
     * @param array $aIndexedCat
     * @param array $aCurrentCat
     * @param int $iCurrentIndex
     * @param int $iDefaultId
     * @return array
     */
    public static function recursiveCategoryTree(
        array $aCategories,
        array $aIndexedCat,
        $aCurrentCat,
        $iCurrentIndex = 1,
        $iDefaultId = null
    ) {
        // set variables
        static $_aTmpCat;
        static $_aFormatCat;

        if ($iCurrentIndex == 1) {
            $_aTmpCat = null;
            $_aFormatCat = null;
        }

        if (!isset($_aTmpCat[$aCurrentCat['infos']['id_parent']])) {
            $_aTmpCat[$aCurrentCat['infos']['id_parent']] = 0;
        }
        $_aTmpCat[$aCurrentCat['infos']['id_parent']] += 1;

        // calculate new level
        $aCurrentCat['infos']['iNewLevel'] = $aCurrentCat['infos']['level_depth'] + 1;

        // calculate type of gif to display - displays tree in good
        $aCurrentCat['infos']['sGifType'] = (count($aCategories[$aCurrentCat['infos']['id_parent']]) == $_aTmpCat[$aCurrentCat['infos']['id_parent']] ? 'f' : 'b');

        // calculate if checked
        if (in_array($iCurrentIndex, $aIndexedCat)) {
            $aCurrentCat['infos']['bCurrent'] = true;
        } else {
            $aCurrentCat['infos']['bCurrent'] = false;
        }

        // define classname with default cat id
        $aCurrentCat['infos']['mDefaultCat'] = ($iDefaultId === null) ? 'default' : $iCurrentIndex;

        $_aFormatCat[] = $aCurrentCat['infos'];

        if (isset($aCategories[$iCurrentIndex])) {
            foreach ($aCategories[$iCurrentIndex] as $iCatId => $aCat) {
                if ($iCatId != 'infos') {
                    self::recursiveCategoryTree($aCategories, $aIndexedCat, $aCategories[$iCurrentIndex][$iCatId],
                        $iCatId);
                }
            }
        }
        return $_aFormatCat;
    }


    /**
     * round on numeric
     *
     * @param float $fVal
     * @param int $iPrecision
     * @return float
     */
    public static function round($fVal, $iPrecision = 2)
    {
        if (method_exists('\Tools', 'ps_round')) {
            $fVal = \Tools::ps_round($fVal, $iPrecision);
        } else {
            $fVal = round($fVal, $iPrecision);
        }

        return $fVal;
    }


    /**
     *  check uploaded image and copy it
     *
     * @param string $sFileName
     * @param int $iMaxSize
     * @param string $sDestination
     * @return string
     */
    public static function checkAndUploadImage($sFileName, $iMaxSize, $sDestination)
    {
        $sError = '';

        if (!empty($_FILES[$sFileName])
            && !empty($_FILES[$sFileName]['tmp_name'])
        ) {
            $sError = \ImageManager::validateUpload($_FILES[$sFileName], $iMaxSize);

            if (!$sError) {
                $sTmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

                // check the tmp directory
                if (!$sTmpName
                    || !move_uploaded_file($_FILES[$sFileName]['tmp_name'], $sTmpName)
                ) {
                    $sError = \BTMailchimpEcommerce::$oModule->l('An error occurred during the image upload', 'Tools');
                }
                // image resize and copy
                elseif (!\ImageManager::resize($sTmpName, $sDestination)) {
                    $sError = \BTMailchimpEcommerce::$oModule->l('An error occurred during the image upload', 'Tool');
                }
                unlink($sTmpName);
            }
        }
        return $sError;
    }


    /**
     *  load images if exist
     *
     * @param string $sFileName
     * @param int $iShopId
     * @param bool $bLanguage
     * @param array $aLanguages
     * @return array
     */
    public static function loadImage($sFileName, $iShopId, $bLanguage = false, $aLanguages = [])
    {
        $images = [];
        if (!empty($aLanguages) && is_array($aLanguages)) {
            foreach ($aLanguages as $aLanguage) {
                $sFilename = $sFileName .'_'. $aLanguage['id_lang'] .'_shop_'. $iShopId .'.jpg';
                if (file_exists(_MCE_PATH_ROOT . _MCE_PATH_VIEWS . _MCE_PATH_IMG . $sFilename)) {
                    $images[$aLanguage['id_lang']] = _MCE_URL_IMG . $sFilename;
                }
            }
        } else {
            $sFilename = $sFileName .'_shop_'. $iShopId. '.jpg';
            if (file_exists(_MCE_PATH_ROOT . _MCE_PATH_VIEWS . _MCE_PATH_IMG . $sFilename)) {
                $images[] = _MCE_URL_IMG . $sFilename;
            }
        }

        return $images;
    }


    /**
     *  check if one data synch mode uses the cron mode
     *
     * @return bool
     */
    public static function isCronModeUsed()
    {
        $used = false;

        foreach($GLOBALS['MCE_DATA_TYPE'] as $type) {
            if (isset(\BTMailchimpEcommerce::$conf['MCE_'. \Tools::strtoupper($type) .'_SYNC_MODE'])
                && \BTMailchimpEcommerce::$conf['MCE_'. \Tools::strtoupper($type) .'_SYNC_MODE'] == 'cron'
            ) {
                $used = true;
            }
        }

        return $used;
    }
}

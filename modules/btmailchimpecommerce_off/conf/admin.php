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

require_once(dirname(__FILE__) . '/common.php');

/* defines modules support product id */
define('_MCE_SUPPORT_ID', '24712');

/* defines activate the BT support if false we use the ADDONS support url */
define('_MCE_SUPPORT_BT', false);

/* defines activate the BT support if false we use the ADDONS support url */
define('_MCE_SUPPORT_URL', 'https://addons.prestashop.com/');
//define('_MCE_SUPPORT_URL', 'http://www.businesstech.fr/');

/* defines admin library path */
define('_MCE_PATH_LIB_ADMIN', _MCE_PATH_LIB . 'admin/');

/* defines admin tpl path */
define('_MCE_TPL_ADMIN_PATH', 'admin/');

/* defines header tpl */
define('_MCE_TPL_HEADER', 'header.tpl');

/* defines top bar tpl */
define('_MCE_TPL_TOP', 'top.tpl');

/* defines body tpl */
define('_MCE_TPL_BODY', 'body.tpl');

/* defines mailchimp settings tpl */
define('_MCE_TPL_GENERAL_MC', 'general/mailchimp-settings.tpl');

/* defines exclusion settings tpl */
define('_MCE_TPL_EXCLUSION', 'general/exclusion-settings.tpl');

/* defines exclusion list tpl */
define('_MCE_TPL_EXCLUSION_LIST', 'general/exclusion-list.tpl');

/* defines user list tpl */
define('_MCE_TPL_USER_LIST', 'general/user-list.tpl');

/* defines dashboard  settings tpl */
define('_MCE_TPL_DASHBOARD', 'tools/dashboard.tpl');

/* defines dashboard table tpl */
define('_MCE_TPL_DASHBOARD_TABLE', 'tools/dashboard-table.tpl');

/* defines diagnostic tool settings tpl */
define('_MCE_TPL_DIAGNOSTIC_TOOL', 'tools/diagnostic-tool.tpl');

/* defines search update tpl */
define('_MCE_TPL_SEARCH_UPDATE', 'tools/search-update.tpl');

/* defines search popup tpl */
define('_MCE_TPL_SEARCH_POPUP', 'tools/search-popup.tpl');

/* defines newsletter config and sync settings tpl */
define('_MCE_TPL_NL_CONFIG_SYNC', 'newsletter/config-sync.tpl');

/* defines newsletter sync form tpl */
define('_MCE_TPL_NL_SYNC_FORM', 'newsletter/newsletter-sync-form.tpl');

/* defines MC sign-up form tpl */
define('_MCE_TPL_MC_SIGNUP_MC_FORM', 'newsletter/signup/mailchimp-form.tpl');

/* defines module sign-up form tpl */
define('_MCE_TPL_MODULE_SIGNUP_FORM', 'newsletter/signup/nl-module-form.tpl');

/* defines ecommerce config settings tpl */
define('_MCE_TPL_ECOMMERCE_CONFIG', 'ecommerce/config.tpl');

/* defines ecommerce synching settings tpl */
define('_MCE_TPL_ECOMMERCE_SYNC', 'ecommerce/sync.tpl');

/* defines products sync form tpl */
define('_MCE_TPL_PROD_SYNC_FORM', 'ecommerce/product-sync-form.tpl');

/* defines customers sync form tpl */
define('_MCE_TPL_CUST_SYNC_FORM', 'ecommerce/customer-sync-form.tpl');

/* defines orders sync form tpl */
define('_MCE_TPL_ORDER_SYNC_FORM', 'ecommerce/order-sync-form.tpl');

/* defines incentive vouchers settings tpl */
define('_MCE_TPL_VOUCHERS', 'voucher/vouchers.tpl');

/* defines incentive vouchers settings tpl */
define('_MCE_TPL_VOUCHER_FORM', 'voucher/voucher-form.tpl');

/* defines incentive voucher form update tpl */
define('_MCE_TPL_VOUCHER_UPD', 'voucher/voucher-update.tpl');

/* defines products / customers synchronization tpl */
define('_MCE_TPL_ITEM_SYNCHRO', 'item-synchronization.tpl');

/* defines sync update tpl */
define('_MCE_TPL_SYNC_UPD', 'sync-update.tpl');

/* defines merge field update tpl */
define('_MCE_TPL_MERGEFIELD_UPDATE', 'custom-fields/mergefield-update.tpl');

/* defines curl test tpl */
define('_MCE_TPL_CURL_TEST', 'curl-test.tpl');

/* defines constant for external BT FAQ URL */
define('_MCE_BT_FAQ_MAIN_URL', 'https://faq.businesstech.fr/');

/* defines constant for the refresh waiting time */
define('_MCE_BT_REFRESH_WAITING_TIME', 30);

/* defines constant for the delay time to get past sync data */
define('_MCE_BT_DATA_DELAY', 2592000);

/* defines constant for BT FAQ product ID */
define('_MCE_FAQ_PROD_ID', 74);

/* defines loader gif name */
define('_MCE_LOADER_GIF', 'loader.gif');

/* defines loader large gif name */
define('_MCE_LOADER_GIF_BIG', 'loader-lg.gif');

/* defines csv file name prefix */
define('_MCE_CSV_NAME_PREFIX', 'subscribers-list');

/* defines update v2 sql file */
define('_MCE_UPDATE_V2_FILE', 'update_v2.sql');

/* defines variable for sql update */
$GLOBALS['MCE_SQL_UPDATE'] = array(
    'table' => array(
        'sync_data' => array('file' => _MCE_UPDATE_V2_FILE, 'install_new_tables' => true),
    ),
    'field' => array(
//        array(
//            'table' => 'sync_detail',
//            'field' => 'type',
//            'file' => _MCE_SYNC_DETAIL_SQL_FILE,
//            'options' => array(
//                'Type' => "enum('product','variant','customer','cart','cartLine','order','orderLine','member')"
//            )
//        ),
    )
);

/* defines variable for setting all request params : use for ajax request in to admin context */
$GLOBALS['MCE_REQUEST_PARAMS'] = array(
    'curl' => array('action' => 'display', 'type' => 'test'),
    'curlUpd' => array('action' => 'update', 'type' => 'curl'),
    'mailchimp' => array('action' => 'update', 'type' => 'mailchimpSettings'),
    'exclusion' => array('action' => 'update', 'type' => 'exclusion'),
    'exclusionEmail' => array('action' => 'update', 'type' => 'mailExclusion'),
    'exclusionDelete' => array('action' => 'delete', 'type' => 'excludedDomain'),
    'userList' => array('action' => 'update', 'type' => 'userList'),
    'synchingHistory' => array('action' => 'update', 'type' => 'synchingHistory'),
    'diagnosticTool' => array('action' => 'update', 'type' => 'diagnosticTool'),
    'search' => array('action' => 'update', 'type' => 'search'),
    'searchPopup' => array('action' => 'display', 'type' => 'searchPopup'),
    'newsletterExport' => array('action' => 'update', 'type' => 'newsletterExportSettings'),
    'newsletterSync' => array('action' => 'update', 'type' => 'newsletterSynching'),
    'dataToSynch' => array('action' => 'update', 'type' => 'syncData'),
    'batch' => array('action' => 'update', 'type' => 'syncStatus'),
    'signupModule' => array('action' => 'update', 'type' => 'signupFormModule'),
    'signupMailchimp' => array('action' => 'update', 'type' => 'signupFormMailchimp'),
    'ecommerce' => array('action' => 'update', 'type' => 'ecommerce'),
    'customerSync' => array('action' => 'update', 'type' => 'customerSynching'),
    'productSync' => array('action' => 'update', 'type' => 'productSynching'),
    'orderSync' => array('action' => 'update', 'type' => 'orderSynching'),
    'voucherForm' => array('action' => 'display', 'type' => 'voucherForm'),
    'voucherUpd' => array('action' => 'update', 'type' => 'voucher'),
);

/* defines variable for available number of products in the cart */
$GLOBALS['MCE_CART_MAX_PROD'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
$GLOBALS['MCE_POPUP_PIXEL_VALUES'] = array(100,125,150,175,200,225,250,275,300,325,350,375,400,425,450,475,500,525,550,575,600,625,650,675,700,725,750,775,800,825,850,875,900,925,950,975,1000,1025,1050,1075,1100,1125,1150,1175,1200,1225,1250,1275,1300,1325,1350,1375,1400,1425,1450,1475,1500,1525,1550,1575,1600,1625,1650,1675,1700,1725,1750,1775,1800,1825,1850,1875,1900,1925,1950,1975);

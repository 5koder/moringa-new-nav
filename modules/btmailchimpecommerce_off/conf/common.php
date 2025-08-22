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

/* defines constant of module name */
define('_MCE_MODULE_NAME', 'MCE');

/* defines module name */
define('_MCE_MODULE_SET_NAME', 'btmailchimpecommerce');

/* defines root path of module */
define('_MCE_PATH_ROOT', _PS_MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/');

/* defines conf path */
define('_MCE_PATH_CONF', _MCE_PATH_ROOT . 'conf/');

/* defines library path */
define('_MCE_PATH_LIB', _MCE_PATH_ROOT . 'lib/');

/* defines library of MailChimp classes path */
define('_MCE_PATH_LIB_MC', _MCE_PATH_LIB . 'mailchimp/');

/* defines common library path */
define('_MCE_PATH_LIB_COMMON', _MCE_PATH_LIB . 'common/');

/* defines sql path */
define('_MCE_PATH_SQL', _MCE_PATH_ROOT . 'sql/');

/* defines views folder */
define('_MCE_PATH_VIEWS', 'views/');

/* defines mails path */
define('_MCE_PATH_MAILS', _MCE_PATH_ROOT . 'mails/');

/* defines js URL */
define('_MCE_URL_JS', _MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/' . _MCE_PATH_VIEWS . 'js/');

/* defines css URL */
define('_MCE_URL_CSS', _MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/' . _MCE_PATH_VIEWS . 'css/');

/* defines MODULE URL */
define('_MCE_MODULE_URL', _MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/');

/* defines img path */
define('_MCE_PATH_IMG', 'img/');

/* defines img URL */
define('_MCE_URL_IMG', _MODULE_DIR_ . _MCE_MODULE_SET_NAME . '/' . _MCE_PATH_VIEWS . _MCE_PATH_IMG);

/* defines front tpl path */
define('_MCE_TPL_FRONT_PATH', 'front/');

/* defines tpl path name */
define('_MCE_PATH_TPL_NAME', _MCE_PATH_VIEWS . 'templates/');

/* defines tpl path */
define('_MCE_PATH_TPL', _MCE_PATH_ROOT . _MCE_PATH_TPL_NAME);

/* defines constant of error tpl */
define('_MCE_TPL_ERROR', 'error.tpl');

/* defines confirm tpl */
define('_MCE_TPL_CONFIRM', 'confirm.tpl');

/* defines empty tpl */
define('_MCE_TPL_EMPTY', 'empty.tpl');

/* defines activate / deactivate debug mode */
define('_MCE_DEBUG', false);

/* defines constant to use or not js on submit action */
define('_MCE_USE_JS', true);

/* defines variable for admin ctrl name */
define('_MCE_PARAM_CTRL_NAME', 'sController');

/* defines variable for admin ctrl name */
define('_MCE_ADMIN_CTRL', 'admin');

/* defines variable for stats ctrl name */
define('_MCE_ADMIN_STATS_CTRL', 'stats');

/* defines variable for the secure hash key */
define('_MCE_SECURE_HASH', 'BTMYMODULEMAILCHIMP');

/* defines variable for the cookie name */
define('_MCE_COOKIE', 'btmc');

/* defines variable for the cookie signup form */
define('_MCE_COOKIE_SIGNUP', 'btmc_signup');

/* defines image max size */
define('_MCE_IMG_MAX_SIZE', 512000);

/* defines variable for the cookie signup expire */
define('_MCE_COOKIE_SIGNUP_EXPIRE', 31536000);

/* set the token to securize all the XHR requests */
define('_MCE_TOKEN', 9163485270);

/* set the batch resync limit */
define('_MCE_BATCH_RESYNC_LIMIT', 3);

/* set the batch delay to use when the delete cron is executed in secs */
define('_MCE_BATCH_DELAY', 86400);

/* defines variable for front module controller of block newsletter handle */
define('_MCE_FRONT_CTRL_NEWSLETTER', 'registerNewsletter');

/* defines variable for front module controller for list webhook */
define('_MCE_FRONT_LIST_WEBHOOK', 'list');

/* defines variable for front module controller for list webhook */
define('_MCE_FRONT_BATCH_WEBHOOK', 'batch');

/* defines variable for front module controller for the cron */
define('_MCE_FRONT_CRON', 'cron');

/* defines variables to configuration settings */
$GLOBALS['MCE_CONFIGURATION'] = array(
    'MCE_MODULE_VERSION' => '1.0.0',
    'MCE_MODULE_BO_URL' => '',
    'MCE_CURL_TEST' => 0,
    'MCE_MC_API_KEY' => '',
    'MCE_COOKIE_TTL' => 259200,
    'MCE_NL_ACTIVE' => 0,
    'MCE_ECOMMERCE_ACTIVE' => 0,
    'MCE_DOUBLE_OPTIN' => 0,
    'MCE_GDPR' => 0,
    'MCE_NL_MODULE' => 0,
    'MCE_CUST_TYPE_EXPORT' => 'optin',
    'MCE_NL_MODULE_LANG' => 1,
    'MCE_NL_SELECT_MODULE' => 'native',
    'MCE_NL_MODULE_SUBMIT' => 'submitNewsletter',
    'MCE_NL_MODULE_EMAIL_FIELD' => 'email',
    'MCE_NL_MODULE_AJAX' => 0,
    'MCE_NL_MODULE_SELECTOR' => (version_compare(_PS_VERSION_, '1.7', '>=')? '.block_newsletter' : '#newsletter_block_left'),
    'MCE_SIGNUP' => 0,
    'MCE_SIGNUP_HTML' => '',
    'MCE_SIGNUP_DISPLAY' => '',
    'MCE_SIGNUP_LINK_LABEL' => '',
    'MCE_SIGNUP_POPUP_TIMES' => 1,
    'MCE_SIGNUP_POPUP_PAGES' => '',
    'MCE_SIGNUP_POPUP_NOT_DISPLAY' => 1,
    'MCE_SIGNUP_POPUP_TEXT' => '',
    'MCE_SIGNUP_POPUP_WIDTH' => 600,
    'MCE_SIGNUP_POPUP_HEIGHT' => 800,
    'MCE_SIGNUP_POPUP_TEXT_VALIGN' => 'middle',
    'MCE_SIGNUP_POPUP_TEXT_HALIGN' => 'left',
    'MCE_SIGNUP_POPUP_TEXT_VALIGN_CUSTOM' => '',
    'MCE_SIGNUP_POPUP_TEXT_HALIGN_CUSTOM' => '',
    'MCE_SIGNUP_POPUP_IMAGE' => 0,
    'MCE_PROD_LANG' => '',
    'MCE_PROD_DESC_TYPE' => 0,
    'MCE_PROD_IMG_FORMAT' => '',
    'MCE_CART_MAX_PROD' => 3,
    'MCE_ECOMMERCE_CRON' => 0,
    'MCE_ECOMMERCE_CRON_CYCLE' => 200,
    'MCE_PROD_VENDOR_TYPE' => 'brand',
    'MCE_CAT_LABEL_FORMAT' => 'short',
    'MCE_STATUS_SELECTION' => '',
    'MCE_MAIL_EXCLUSION' => '',
    'MCE_VOUCHERS' => '',
    'MCE_CRON_TOKEN' => md5(rand(1000, 1000000) . time()),
    'MCE_MEMBER_PER_CYCLE' => 200,
    'MCE_CUST_PER_CYCLE' => 200,
    'MCE_PROD_PER_CYCLE' => 200,
    'MCE_ORDER_PER_CYCLE' => 200,
    'MCE_MEMBER_SYNC_MODE' => 'regular',
    'MCE_PRODUCT_SYNC_MODE' => 'regular',
    'MCE_CUSTOMER_SYNC_MODE' => 'regular',
    'MCE_CART_SYNC_MODE' => 'regular',
    'MCE_ORDER_SYNC_MODE' => 'regular',
    'MCE_OLD_CONFIG' => '',
    'MCE_SYNC_OLD_LISTS_FLAG' => 0,
    'MCE_PRODUCT_TAX' => 1,
);

/* defines variables for configuration settings  to be serialized */
$GLOBALS['MCE_CONF_SERIALIZED_LIST'] = array('MCE_MAIL_EXCLUSION', 'MCE_VOUCHERS', 'MCE_SIGNUP_HTML', 'MCE_SIGNUP_LINK_LABEL', 'MCE_SIGNUP_POPUP_PAGES', 'MCE_SIGNUP_POPUP_TEXT', 'MCE_STATUS_SELECTION', 'MCE_PROD_LANG','MCE_OLD_CONFIG');

/* defines variable to hooks settings */
$GLOBALS['MCE_HOOKS'] = array(
    array('name' => 'actionProductAdd', 'use' => false, 'title' => 'Product add'),
    array('name' => 'actionProductDelete', 'use' => false, 'title' => 'Product delete'),
    array('name' => 'actionProductUpdate', 'use' => false, 'title' => 'Product update'),
    array('name' => 'actionProductAttributeUpdate', 'use' => false, 'title' => 'Combination update'),
    array('name' => 'actionProductAttributeDelete', 'use' => false, 'title' => 'Combination delete'),
    array('name' => 'actionCustomerAccountAdd', 'use' => false, 'title' => 'Customer Account Add'),
    array('name' => 'actionCartSave', 'use' => false, 'title' => 'Cart save'),
    array('name' => 'actionValidateOrder', 'use' => false, 'title' => 'Order validate'),
    array('name' => 'actionOrderStatusUpdate', 'use' => false, 'title' => 'Order status update'),
    array('name' => 'displayHeader', 'use' => false, 'title' => 'Display header'),
);
// use case - hooks for PS 1.7
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    $GLOBALS['MCE_HOOKS'][] = array(
        'name' => 'actionCustomerAccountUpdate',
        'use' => false,
        'title' => 'Update customer info'
    );
    $GLOBALS['MCE_HOOKS'][] = array(
        'name' => 'actionValidateCustomerAddressForm',
        'use' => false,
        'title' => 'Update customer info'
    );
    $GLOBALS['MCE_HOOKS'][] = array(
        'name' => 'actionFrontControllerSetMedia',
        'use' => false,
        'title' => 'Add external media'
    );
} // use case - hooks for PS 1.6
else {
    $GLOBALS['MCE_HOOKS'][] = array(
        'name' => 'actionObjectUpdateAfter',
        'use' => false,
        'title' => 'Update any object'
    );
}

/* defines variable to assign Admin Tab titles */
$GLOBALS['MCE_TABS'] = array(
    'BTMailchimpEcommerce' => array(
        'lang' => array(
            'en' => 'MailChimp Stats',
            'fr' => 'Stats MailChimp',
            'de' => 'MailChimp Stats',
            'it' => 'MailChimp Stats',
            'es' => 'MailChimp Stats',
        ),
        'parent' => 'AdminParentModules', // name of parent tab
//		'oldName'=> 'AdminModuleTemplate'
    ),
);

/* defines variable for available MC API objects */
$GLOBALS['MCE_APP_TYPES'] = array(
    'apiRoot' => 'ApiRoot',
    'store' => 'Store',
    'cart' => 'Cart',
    'cartLine' => 'CartLine',
    'customer' => 'Customer',
    'order' => 'Order',
    'product' => 'Product',
    'combination' => 'Combination',
    'connectedSites' => 'ConnectedSites',
    'lists' => 'Lists',
    'members' => 'Members',
    'mergeFields' => 'MergeFields',
    'batches' => 'Batches',
    'batchWebhook' => 'BatchWebhook',
    'listWebhook' => 'ListWebhook',
);

/* defines variable for available MC API objects */
$GLOBALS['MCE_MERGE_FIELDS'] = array(
    array(
        'tag' => 'BIRTHDAY',
        'name' => 'Birthday',
        'type' => 'birthday',
        'options' => array('date_format' => 'mm/dd'),
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setBirthday'), 'params' => array('birthday'))
    ),
    array(
        'tag' => 'CUST_GROUP',
        'name' => 'Customer group',
        'type' => 'text',
        'options' => array('size' => 50),
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setCustomerGroup'), 'params' => array('id_default_group', 'id_lang'))
    ),
    array(
        'tag' => 'GENDER',
        'name' => 'Gender',
        'type' => 'text',
        'options' => array('size' => 50),
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setCustomerGender'), 'params' => array('id_gender', 'id_lang'))
    ),
    array(
        'tag' => 'FNAME',
        'name' => 'First Name',
        'type' => 'text',
        'options' => array('size' => 50),
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setFirstname'), 'params' => array('firstname'))
    ),
    array(
        'tag' => 'LNAME',
        'name' => 'Last Name',
        'type' => 'text',
        'options' => array('size' => 50),
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setLastname'), 'params' => array('lastname'))
    ),
    array(
        'tag' => 'ADDRESS',
        'name' => 'Address',
        'type' => 'address',
        'callback' => array('function' => array('\MCE\Chimp\Format\Formatter', 'setAddress'), 'params' => array('id'))
    ),
);

/* defines variable a list of modules that make the pending order confirmation for MC notif */
$GLOBALS['MCE_ORDER_VALIDATE_PENDING_MODULES'] = array('cheque', 'ps_checkpayment', 'bankwire', 'ps_wirepayment');

/* defines a list of data type that the module send to the MC API */
$GLOBALS['MCE_DATA_TYPE'] = array('customer', 'product', 'cart', 'order', 'member');

/* defines variable to translate js msg */
$GLOBALS['MCE_JS_MSG'] = array();

/* defines variable to set request parameters */
$GLOBALS['MCE_MONTH'] = array(
    'en' => array(
        'short' => array(
            '',
            'Jan.',
            'Feb.',
            'March',
            'Apr.',
            'May',
            'June',
            'July',
            'Aug.',
            'Sept.',
            'Oct.',
            'Nov.',
            'Dec.'
        ),
        'long' => array(
            '',
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ),
    ),
    'fr' => array(
        'short' => array(
            '',
            'Jan.',
            'Fév.',
            'Mars',
            'Avr.',
            'Mai',
            'Juin',
            'Juil.',
            'Aout',
            'Sept.',
            'Oct.',
            'Nov.',
            'Déc.'
        ),
        'long' => array(
            '',
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Aout',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre'
        ),
    ),
    'de' => array(
        'short' => array(
            '',
            'Jan.',
            'Feb.',
            'Márz',
            'Apr.',
            'Mai',
            'Juni',
            'Juli',
            'Aug.',
            'Sept.',
            'Okt.',
            'Nov.',
            'Dez.'
        ),
        'long' => array(
            '',
            'Januar',
            'Februar',
            'Márz',
            'April',
            'Mai',
            'Juni',
            'Juli',
            'August',
            'September',
            'Oktober',
            'November',
            'Dezember'
        ),
    ),
    'it' => array(
        'short' => array(
            '',
            'Gen.',
            'Feb.',
            'Marzo',
            'Apr.',
            'Mag.',
            'Giu.',
            'Lug.',
            'Ago.',
            'Sett.',
            'Ott.',
            'Nov.',
            'Dic.'
        ),
        'long' => array(
            '',
            'Gennaio',
            'Febbraio',
            'Marzo',
            'Aprile',
            'Maggio',
            'Giugno',
            'Luglio',
            'Agosto',
            'Settembre',
            'Ottobre',
            'Novembre',
            'Dicembre'
        ),
    ),
    'es' => array(
        'short' => array(
            '',
            'Ene.',
            'Feb.',
            'Marzo',
            'Abr.',
            'Mayo',
            'Junio',
            'Jul.',
            'Ago.',
            'Sept.',
            'Oct.',
            'Nov.',
            'Dic.'
        ),
        'long' => array(
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ),
    ),
);

/* defines label format to set  */
$GLOBALS['MCE_LABEL_FORMAT'] = array('short' => '', 'long' => '');

/* defines variable for setting all automations we can associate with voucher */
$GLOBALS['MCE_AUTOMATION'] = array(
    'best' => '',
    'reengagement' => '',
);

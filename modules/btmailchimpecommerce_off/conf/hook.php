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

/* defines hook library path */
define('_MCE_PATH_LIB_HOOK', _MCE_PATH_LIB . 'hook/');

/* defines hook tpl path */
define('_MCE_TPL_HOOK_PATH', 'hook/');

/* defines header tpl */
define('_MCE_TPL_FRONT_HEADER', 'header.tpl');

/* defines footer tpl */
define('_MCE_TPL_FOOTER', 'footer.tpl');

/* defines MC newsletter form tpl */
define('_MCE_TPL_MC_NL_FORM', 'mc-newsletter-form.tpl');

/* defines voucher front module controller tpl */
define('_MCE_TPL_VOUCHER_CTRL', 'voucher.tpl');

/* defines signup front module controller tpl */
define('_MCE_TPL_SIGNUP_CTRL', 'signup.tpl');

/* defines variable for setting all request params */
$GLOBALS['MCE_REQUEST_PARAMS'] = array(
    'basics' => array('action' => 'update', 'type' => 'basics'),
);

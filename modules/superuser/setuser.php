<?php
/**
* Super User Module
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2016 idnovate
*  @license   See above
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$param_secure_key = Tools::getValue('secure_key');
$use_last_cart = Tools::getValue('use_last_cart');
$customer = new Customer((int)Tools::getValue('id_customer'));
$customer_secure_key = $customer->passwd;

if ($customer_secure_key === $param_secure_key || isBoLogged()) {
    setSUCookie($customer, $use_last_cart);
} else {
    Tools::redirect('index.php');
}

setSUCookie($customer, $use_last_cart, Tools::getValue('redir'));

function setSUCookie($customer, $use_last_cart = '1', $redir = '')
{
    if (version_compare(_PS_VERSION_, '1.5', '<')) {
        global $cookie;

        $cookie = new Cookie('ps');
        if (Tools::getValue('id_customer')) {
            if ($cookie->logged) {
                $cookie->logout();
            }
            $cookie = new Cookie('ps');
            Tools::setCookieLanguage();
            Tools::switchLanguage();

            $cookie->id_customer = (int)$customer->id;
            $cookie->customer_lastname = $customer->lastname;
            $cookie->customer_firstname = $customer->firstname;
            $cookie->logged = 1;
            $cookie->passwd = $customer->passwd;
            $cookie->email = $customer->email;
            if (Configuration::get('PS_CART_FOLLOWING') && (empty($cookie->id_cart) || Cart::getNbProducts($cookie->id_cart) == 0)) {
                $cookie->id_cart = Cart::lastNoneOrderedCart($customer->id);
            }
            if ($use_last_cart == '1') {
                $cookie->id_cart = $customer->getLastCart();
            }

        } else {
            die('Incorrect customer');
        }
    }
    $context = Context::getContext();
    if ($redir == 'cart') {
        Tools::redirect($context->link->getPageLink('order'));
    } elseif ($redir == 'myaccount') {
        Tools::redirect($context->link->getPageLink('myaccount'));
    }
    Tools::redirect('index.php');
}

function isBoLogged()
{
    $cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
    $employee = new Employee((int)$cookie->id_employee);
    if (Validate::isLoadedObject($employee) &&
        $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd) &&
        (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))
    ) {
        return true;
    } else {
        return false;
    }
}

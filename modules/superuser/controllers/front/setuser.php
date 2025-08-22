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

class SuperUserSetuserModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $param_secure_key = Tools::getValue('secure_key');
        $use_last_cart = Tools::getValue('use_last_cart');
        $id_customer = 0;

        if (Tools::getValue('id_customer')) {
            $id_customer = Tools::getValue('id_customer');
        } elseif (Tools::getValue('id_order')) {
            $order = new Order((int)Tools::getValue('id_order'));
            $id_customer = $order->id_customer;
        }

        $customer = new Customer((int)$id_customer);
        $customer_secure_key = $customer->passwd;
        $shop = new Shop((int)$customer->id_shop);

        if ($customer_secure_key === $param_secure_key || $this->isBoLogged()) {
            $this->setSUCookie($customer, $shop, $use_last_cart, Tools::getValue('redir'));
        } else {
            Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__);
        }
    }

    private function setSUCookie($customer, $shop, $use_last_cart = '1', $redir = '')
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $cookie = new Cookie('ps');
            if ( $cookie->logged ) {
                $cookie->logout();
            }
            $cookie = new Cookie( 'ps' );
            Tools::setCookieLanguage();
            Tools::switchLanguage();
            $cookie->id_customer        = (int) $customer->id;
            $cookie->customer_lastname  = $customer->lastname;
            $cookie->customer_firstname = $customer->firstname;
            $cookie->logged             = 1;
            $cookie->passwd             = $customer->passwd;
            $cookie->email              = $customer->email;
            if ( Configuration::get( 'PS_CART_FOLLOWING' ) && ( empty( $cookie->id_cart ) || Cart::getNbProducts( $cookie->id_cart ) == 0 ) ) {
                $cookie->id_cart = Cart::lastNoneOrderedCart( $customer->id );
            }
            if ( $use_last_cart == '1' ) {
                $cookie->id_cart = $customer->getLastCart();
            }
            $this->success = true;
            Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__);
        } else {
            $cookie_lifetime = (int)defined('_PS_ADMIN_DIR_' ? Configuration::get('PS_COOKIE_LIFETIME_BO') : Configuration::get('PS_COOKIE_LIFETIME_FO'));
            $cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);
            if ($shop->getGroup()->share_order) {
                $cookie = new Cookie('ps-sg'.$shop->getGroup()->id, '', $cookie_lifetime, $shop->getUrlsSharedCart());
            } else {
                $domains = null;
                if ($shop->domain != $shop->domain_ssl) {
                    $domains = array($shop->domain_ssl, $shop->domain);
                }
                $cookie = new Cookie('ps-s'.$shop->id, '', $cookie_lifetime, $domains);
            }
            if ($cookie->logged) {
                $cookie->logout();
            }
            Tools::setCookieLanguage();
            Tools::switchLanguage();
            $cookie->id_customer = (int)$customer->id;
            $cookie->customer_lastname = $customer->lastname;
            $cookie->customer_firstname = $customer->firstname;
            $cookie->logged = 1;
            $cookie->passwd = $customer->passwd;
            $cookie->email = $customer->email;
            if (Configuration::get('PS_CART_FOLLOWING') && (empty($cookie->id_cart) || Cart::getNbProducts($cookie->id_cart) == 0)) {
                $cookie->id_cart = (int)Cart::lastNoneOrderedCart($customer->id);
            }
            if ($use_last_cart == '1') {
                if (version_compare(_PS_VERSION_, '1.5', '<')) {
                    $cookie->id_cart = (int)$customer->getLastCart();
                } else {
                    $cookie->id_cart = (int)Cart::lastNoneOrderedCart($customer->id);
                }
            }
            $this->success = true;
            if ($redir == 'cart') {
                Tools::redirect($this->context->link->getPageLink('order'));
            } elseif ($redir == 'myaccount') {
                Tools::redirect($this->context->link->getPageLink('myaccount'));
            }
            Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__);
        }
    }

    private function isBoLogged()
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
}

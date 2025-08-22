<?php
/**
 * Overrides carrier shipping with Table Rate Shipping
 *
 * Table Rate Shipping by Kahanit(https://www.kahanit.com/) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at https://www.kahanit.com/.
 * Permissions beyond the scope of this license may be available at https://www.kahanit.com/.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2016 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/libraries/kahanit/Helpers.php');
require_once(dirname(__FILE__) . '/libraries/TRSUnitConvertor.php');
require_once(dirname(__FILE__) . '/libraries/TRSCarrierTableRate.php');
require_once(dirname(__FILE__) . '/libraries/autoload.php');

/**
 * Class TableRateShipping
 */
class TableRateShipping extends CarrierModule
{
    public $id_carrier = '';

    public function __construct()
    {
        $this->name = 'tablerateshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.6';
        $this->author = 'Kahanit';
        $this->need_instance = 0;
        $this->module_key = '256ca2bbccdf580776087af47b9e66ae';

        parent::__construct();

        $this->displayName = $this->l('Table Rate Shipping - PrestaShop Shipping by Zip Code Module - Kahanit');
        $this->description = $this->l('Setup shipping charges based on zone, country, state/region, city, zip/post code, total weight, total price, total pretax price, total quantity and total volume.');
        $this->confirmUninstall = $this->l('Uninstalling the module will delete all data?');
    }

    public function install()
    {
        Db::getInstance()->execute('
			DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'carrier_table_rate`;
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'carrier_table_rate` (
              `id_carrier_table_rate`   BIGINT(20)  UNSIGNED    NOT NULL AUTO_INCREMENT,
              `id_shop`                 INT(11)     UNSIGNED    NOT NULL DEFAULT \'0\',
              `id_carrier`              INT(10)     UNSIGNED    NOT NULL DEFAULT \'0\',
              `id_zone`                 INT(10)     UNSIGNED    NOT NULL DEFAULT \'0\',
              `id_group`                BIGINT(20)  UNSIGNED    NOT NULL DEFAULT \'0\',
              `id_country`              INT(10)     UNSIGNED    NOT NULL DEFAULT \'0\',
              `id_state`                INT(10)     UNSIGNED    NOT NULL DEFAULT \'0\',
              `dest_city`               VARCHAR(64)             NOT NULL DEFAULT \'\',
              `dest_zip`                VARCHAR(25)             NOT NULL DEFAULT \'\',
              `condition_weight_from`   DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_weight_to`     DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_price_from`    DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_price_to`      DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_ptprice_from`  DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_ptprice_to`    DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_quantity_from` DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_quantity_to`   DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_volume_from`   DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `condition_volume_to`     DECIMAL(12, 6)          NOT NULL DEFAULT \'0.000000\',
              `price`                   TEXT                    NOT NULL,
              `comment`                 TEXT                    NOT NULL,
              `active`                  TINYINT(1)  UNSIGNED    NOT NULL DEFAULT \'1\',
              `order`                   BIGINT(20)  UNSIGNED    NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id_carrier_table_rate`)
            )
              ENGINE = ' . _MYSQL_ENGINE_ . '
              DEFAULT CHARSET = utf8;
		');

        return parent::install()
            && $this->registerHook('updateCarrier')
            && KIHelpers::installModuleTab('Table Rate Shipping', 'AdminTableRateShipping', $this->name, 'AdminParentShipping', true);
    }

    public function uninstall()
    {
        TRSCarrierTableRate::clearAllCarriersAsTableRate();
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'carrier_table_rate`;');

        return parent::uninstall()
            && $this->unregisterHook('updateCarrier')
            && KIHelpers::uninstallModuleTab('AdminTableRateShipping');
    }

    public function getContent()
    {
        $link = $this->context->link->getAdminLink('AdminTableRateShipping');
        Tools::redirectAdmin($link);
        die();
    }

    public function hookUpdateCarrier($params)
    {
        if (TRSCarrierTableRate::isCarrierTableRate($params['id_carrier'])) {
            TRSCarrierTableRate::updateIdCarrier((int)$params['id_carrier'], (int)$params['carrier']->id);
            TRSCarrierTableRate::clearCarrierAsTableRate((int)$params['id_carrier']);
            TRSCarrierTableRate::setCarrierAsTableRate((int)$params['carrier']->id);
        }
    }

    public function getPackageShippingCost($cart, $shipping_cost, $products)
    {
        return $this->calculateShippingPrice($cart, $shipping_cost, $products);
    }

    public function getOrderShippingCost($cart, $shipping_cost)
    {
        return $this->calculateShippingPrice($cart, $shipping_cost, $cart->getProducts());
    }

    public function getOrderShippingCostExternal($cart)
    {
        return $this->calculateShippingPrice($cart, 0, $cart->getProducts());
    }

    private function calculateShippingPrice($cart, $shipping_cost = 0, $products = array())
    {
        $address = new Address($cart->id_address_delivery, $this->context->language->id);
        $id_shop = $this->context->shop->id;
        $id_zone = Address::getZoneById($cart->id_address_delivery);
        $id_country = $address->id_country;
        $id_state = $address->id_state;
        $dest_city = $address->city;
        $dest_zip = $address->postcode;

        $vars = array(
            'tw'   => 0,
            'tp'   => 0,
            'tptp' => 0,
            'tq'   => 0,
            'tv'   => 0,
            'skip' => -1,
            'stop' => false
        );

        foreach ($products as $product) {
            $width = $product['width'];
            $height = $product['height'];
            $depth = $product['depth'];
            $quantity = $product['quantity'];
            $volume = $width * $height * $depth * $quantity;

            $vars = array(
                'tw'   => $vars['tw'] + $product['weight'] * $quantity,
                'tp'   => $vars['tp'] + $product['price_wt'] / $this->context->currency->conversion_rate * $quantity,
                'tptp' => $vars['tptp'] + $product['price'] / $this->context->currency->conversion_rate * $quantity,
                'tq'   => $vars['tq'] + $product['quantity'],
                'tv'   => $vars['tv'] + $volume
            );
        }

        $dimension_unit = Configuration::get('PS_DIMENSION_UNIT');
        $volume_unit = Configuration::get('PS_VOLUME_UNIT');

        try {
            $unit_convertor = new TRSUnitConvertor($vars['tv'], Tools::strtolower($dimension_unit) . '3');
            $vars['tv'] = $unit_convertor->to(Tools::strtolower($volume_unit));
        } catch (Exception $e) {
            // do nothing
        }

        $shipping_price = TRSCarrierTableRate::getShippingPrice(
            $this->id_carrier,
            $id_zone,
            $id_country,
            $id_state,
            $dest_city,
            $dest_zip,
            $vars,
            $id_shop
        );

        if ($shipping_price === false) {
            return false;
        } else {
            return (float)$shipping_cost + (float)$shipping_price * (float)$this->context->currency->conversion_rate;
        }
    }
}

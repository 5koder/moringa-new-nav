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

require_once dirname(__FILE__) . '/../libraries/kahanit/Helpers.php';
require_once dirname(__FILE__) . '/../libraries/TRSCarrierTableRate.php';

function upgrade_module_1_0_0()
{
    // get data from table by carrier
    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('carrier_table_rate');
    $results = Db::getInstance()->executeS($sql);
    $records = array();

    foreach ($results as $result) {
        // combine zip from and to and unset zip from and to
        if ($result['dest_zip_to'] == '') {
            $result['dest_zip'] = $result['dest_zip_from'];
        } elseif ($result['dest_zip_from'] == '') {
            $result['dest_zip'] = $result['dest_zip_to'];
        } else {
            $result['dest_zip'] = $result['dest_zip_from'] . '-' . $result['dest_zip_to'];
        }
        unset($result['dest_zip_from']);
        unset($result['dest_zip_to']);

        // based on condition name set condition type from and to and price formula and unset condition name, value from and value to
        switch ($result['condition_name']) {
            case 'weight':
                // update condition
                $result['condition_weight_from'] = $result['condition_value_from'];
                $result['condition_weight_to'] = $result['condition_value_to'];
                // update formula
                $result['price'] = preg_replace('/\$cv(f|t|i)/', '$ctw$1', $result['price']);
                $result['price'] = preg_replace('/\$cv/', '$tw', $result['price']);
                break;
            case 'price':
                // update condition
                $pretax = Configuration::get('TABLERATESHIPPING_USE_PRE_TAX_PRICE');
                if ($pretax == 'no') {
                    $result['condition_price_from'] = $result['condition_value_from'];
                    $result['condition_price_to'] = $result['condition_value_to'];
                    // update formula
                    $result['price'] = preg_replace('/\$cv(f|t|i)/', '$ctp$1', $result['price']);
                    $result['price'] = preg_replace('/\$cv/', '$tp', $result['price']);
                } else {
                    $result['condition_ptprice_from'] = $result['condition_value_from'];
                    $result['condition_ptprice_to'] = $result['condition_value_to'];
                    // update formula
                    $result['price'] = preg_replace('/\$cv(f|t|i)/', '$ctptp$1', $result['price']);
                    $result['price'] = preg_replace('/\$cv/', '$tptp', $result['price']);
                }
                break;
            case 'quantity':
                // update condition
                $result['condition_quantity_from'] = $result['condition_value_from'];
                $result['condition_quantity_to'] = $result['condition_value_to'];
                // update formula
                $result['price'] = preg_replace('/\$cv(f|t|i)/', '$ctq$1', $result['price']);
                $result['price'] = preg_replace('/\$cv/', '$tq', $result['price']);
                break;
            case 'volume':
                // update condition
                $result['condition_volume_from'] = $result['condition_value_from'];
                $result['condition_volume_to'] = $result['condition_value_to'];
                // update formula
                $result['price'] = preg_replace('/\$cv(f|t|i)/', '$ctv$1', $result['price']);
                $result['price'] = preg_replace('/\$cv/', '$tv', $result['price']);
                break;
        }
        unset($result['condition_name']);
        unset($result['condition_value_from']);
        unset($result['condition_value_to']);

        // set active to 1
        $result['active'] = 1;

        // unset cost
        unset($result['cost']);

        // set records
        if (!isset($records[$result['id_shop'] . '-' . $result['id_carrier']])
            || !is_array($records[$result['id_shop'] . '-' . $result['id_carrier']])
        ) {
            $records[$result['id_shop'] . '-' . $result['id_carrier']] = array();
        }
        $records[$result['id_shop'] . '-' . $result['id_carrier']][] = $result;
    }

    // alter table to latest version
    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'carrier_table_rate`
            CHANGE      `id_shop`     `id_shop`    int(11)      unsigned    NOT NULL DEFAULT 0        after `id_carrier_table_rate`,
            CHANGE      `id_carrier`  `id_carrier` int(10)      unsigned    NOT NULL DEFAULT 0        after `id_shop`,
            CHANGE      `id_zone`     `id_zone`    int(10)      unsigned    NOT NULL DEFAULT 0        after `id_carrier`,
            ADD COLUMN  `dest_zip`                 varchar(25)              NOT NULL DEFAULT \'\'     after `dest_city`,
            ADD COLUMN  `condition_weight_from`    decimal(12,6)            NOT NULL DEFAULT 0.000000 after `dest_zip`,
            ADD COLUMN  `condition_weight_to`      decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_weight_from`,
            ADD COLUMN  `condition_price_from`     decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_weight_to`,
            ADD COLUMN  `condition_price_to`       decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_price_from`,
            ADD COLUMN  `condition_ptprice_from`   decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_price_to`,
            ADD COLUMN  `condition_ptprice_to`     decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_ptprice_from`,
            ADD COLUMN  `condition_quantity_from`  decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_ptprice_to`,
            ADD COLUMN  `condition_quantity_to`    decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_quantity_from`,
            ADD COLUMN  `condition_volume_from`    decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_quantity_to`,
            ADD COLUMN  `condition_volume_to`      decimal(12,6)            NOT NULL DEFAULT 0.000000 after `condition_volume_from`,
            CHANGE      `price`       `price`      text                     NOT NULL                  after `condition_volume_to`,
            CHANGE      `comment`     `comment`    text                     NOT NULL                  after `price`,
            ADD COLUMN  `active`                   tinyint(1)   unsigned    NOT NULL DEFAULT 1        after `comment`,
            ADD COLUMN  `order`                    bigint(20)   unsigned    NOT NULL DEFAULT 0        after `active`,
            DROP COLUMN `cost`,
            DROP COLUMN `dest_zip_from`,
            DROP COLUMN `dest_zip_to`,
            DROP COLUMN `condition_name`,
            DROP COLUMN `condition_value_from`,
            DROP COLUMN `condition_value_to`;
        TRUNCATE TABLE  `' . _DB_PREFIX_ . 'carrier_table_rate`;
	');

    // save rules by shop and carrier
    foreach ($records as $id_carrier_shop => $crecords) {
        $id_carrier_shop_explode = explode('-', $id_carrier_shop);
        if (isset($id_carrier_shop_explode[0]) && is_numeric($id_carrier_shop_explode[0]) && $id_carrier_shop_explode[0] != 0) {
            $id_shop = $id_carrier_shop_explode[0];
        } else {
            $id_shop = null;
        }
        if (isset($id_carrier_shop_explode[1]) && is_numeric($id_carrier_shop_explode[1])) {
            TRSCarrierTableRate::saveRules('add', $id_carrier_shop_explode[1], $crecords, null, $id_shop);
        }
    }

    // delete configuration variables
    Configuration::deleteByName('TABLERATESHIPPING_CARRIER_ID');
    Configuration::deleteByName('TABLERATESHIPPING_CONDITION_NAME');
    Configuration::deleteByName('TABLERATESHIPPING_USE_PRE_TAX_PRICE');
    Configuration::deleteByName('TABLERATESHIPPING_CSV_SEPARATOR');

    // create module tab under shipping
    KIHelpers::installModuleTab('Table Rate Shipping', 'AdminTableRateShipping', 'tablerateshipping', 'AdminParentShipping', true);

    return true;
}

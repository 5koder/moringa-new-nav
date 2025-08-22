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

function upgrade_module_1_0_3()
{
    // alter table to add id_group column
    Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'carrier_table_rate`
        ADD COLUMN `id_group` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `id_zone`;
    ');

    // get data from table by carrier
    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('carrier_table_rate');
    $results = Db::getInstance()->executeS($sql);
    Db::getInstance()->delete('carrier_table_rate');
    $records = array();

    foreach ($results as &$result) {
        // strip slashes
        $result = KIHelpers::stripslashes($result);

        // set records
        if (!isset($records[$result['id_shop'] . '-' . $result['id_carrier']])
            || !is_array($records[$result['id_shop'] . '-' . $result['id_carrier']])
        ) {
            $records[$result['id_shop'] . '-' . $result['id_carrier']] = array();
        }
        $records[$result['id_shop'] . '-' . $result['id_carrier']][] = $result;
    }

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

    return true;
}

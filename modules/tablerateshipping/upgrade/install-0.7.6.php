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

function upgrade_module_0_7_6()
{
    Configuration::updateValue('TABLERATESHIPPING_USE_PRE_TAX_PRICE', 'no');

    Db::getInstance()->execute('
		ALTER TABLE `' . _DB_PREFIX_ . 'carrier_table_rate` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci
	');

    return true;
}

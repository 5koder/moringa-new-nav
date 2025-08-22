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

function upgrade_module_0_7_2()
{
    Db::getInstance()->execute('
		ALTER TABLE `' . _DB_PREFIX_ . 'carrier_table_rate`
		CHANGE `id_carrier_table_rate` `id_carrier_table_rate` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		CHANGE `id_shop` `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
		CHANGE `dest_city` `dest_city` VARCHAR(64) NOT NULL DEFAULT \'\',
		CHANGE `dest_zip_from` `dest_zip_from` VARCHAR(12) NOT NULL DEFAULT \'\',
		CHANGE `dest_zip_to` `dest_zip_to` VARCHAR(12) NOT NULL DEFAULT \'\';
	');

    return true;
}

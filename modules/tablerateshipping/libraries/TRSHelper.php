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

/**
 * Class TRSHelper
 *
 * @package Kahanit\TableRateShipping\Helper
 */
class TRSHelper
{
    public static function checkZipCode($zip_code, $zip_code_format, $iso_code)
    {
        $iso_code_wildcard = preg_replace('/(.)/i', '([$1_%*]|)', $iso_code);

        $zip_regexp = '/^' . $zip_code_format . '$/ui';
        $zip_regexp = str_replace(' ', '( |)', $zip_regexp);
        $zip_regexp = str_replace('-', '(-|)', $zip_regexp);
        $zip_regexp = str_replace('N', '([0-9_%*]|)', $zip_regexp);
        $zip_regexp = str_replace('L', '([a-zA-Z_%*]|)', $zip_regexp);
        $zip_regexp = str_replace('C', $iso_code_wildcard, $zip_regexp);

        return (bool)preg_match($zip_regexp, $zip_code);
    }
}

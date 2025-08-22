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

interface BT_IAdmin
{
    /**
     * process display or updating or etc ... admin
     *
     * @param string $sType => defines which method to execute
     * @param mixed $aParam => $_GET or $_POST
     * @return bool
     */
    public function run($sType, array $aParam = array());
}
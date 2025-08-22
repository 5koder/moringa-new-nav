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

$aClassList = array(
    'format' => array('format/Base','format/Address','format/Cart','format/Combination','format/Customer','format/Member','format/Order','format/Product'),
    'root' => array('Api','Facade', 'Detail'),
);

if (isset($GLOBALS['loader_type'])
    && !empty($GLOBALS['loader_type'])
) {
    switch ($GLOBALS['loader_type']) {
        case 'format':
        case 'root':
             $list = $aClassList[$GLOBALS['loader_type']];
            break;
        default:
            break;
    }
} else {
    $list = array_merge($aClassList['format'], $aClassList['root']);
}

foreach ($list as $class) {
    require_once(_MCE_PATH_LIB_MC . $class .'.php');
}

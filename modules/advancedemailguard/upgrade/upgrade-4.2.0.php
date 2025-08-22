<?php
/**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to v4.2.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_4_2_0($module)
{
    unset($module);
    if (Shop::isFeatureActive()) {
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    // Create new configs.
    $configs = array(
        'ADVEG_REC_LOAD_ON_DEMAND' => 0,
    );
    foreach ($configs as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    return true;
}

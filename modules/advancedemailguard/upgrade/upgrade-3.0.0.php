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
 * Upgrade to v3.0.0
 *
 * @param \Advancedemailguard $module
 * @return bool
 */
function upgrade_module_3_0_0($module)
{
    // Create new configs.
    $configs = array(
        'ADVEG_REC_V3_KEY' => null,
        'ADVEG_REC_V3_SECRET' => null,
        'ADVEG_REC_THRESHOLD' => '0.5',
    );

    foreach ($configs as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    // Migrate the database.
    if (! $sql = $module->getMigrationQueriesArray('upgradesql/upgrade-3.0.0.sql')) {
        return false;
    }

    foreach ($sql as $query) {
        if (! $module->db->execute($query)) {
            return false;
        }
    }

    return true;
}

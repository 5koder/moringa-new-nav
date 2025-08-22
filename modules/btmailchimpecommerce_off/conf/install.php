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

require_once(dirname(__FILE__) . '/common.php');

/* defines install library path */
define('_MCE_PATH_LIB_INSTALL', _MCE_PATH_LIB . 'install/');

/* defines installation sql file */
define('_MCE_INSTALL_SQL_FILE', 'install.sql'); // comment if not use SQL

/* defines uninstallation sql file */
define('_MCE_UNINSTALL_SQL_FILE', 'uninstall.sql'); // comment if not use SQL

/* defines constant for plug SQL install/uninstall debug */
define('_MCE_LOG_JAM_SQL', false); // comment if not use SQL

/* defines constant for plug CONFIG install/uninstall debug */
define('_MCE_LOG_JAM_CONFIG', false);

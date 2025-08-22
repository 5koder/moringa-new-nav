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

namespace MCE;

class Warning
{
    /**
     * var $bStopExecution : defines if execution has to be stopped
     */
    public $bStopExecution = false;

    /**
     * detect warnings and display them
     *
     * @param string $sType
     * @param mixed $mValue
     * @param array $aParams
     * @param bool $bStop
     */
    public function run($sType, $mValue, array $aParams = array(), $bStop = false)
    {
        $bWarning = false;

        switch ($sType) {
            case 'configuration' :
                if (!\Configuration::get($mValue)) {
                    $bWarning = true;
                }
                break;
            case 'directive' :
                if (!ini_get($mValue)) {
                    $bWarning = true;
                }
                break;
            case 'module' :
                // get module's vars to check
                $aModuleVars = (!empty($aParams['vars']) && is_array($aParams['vars'])) ? $aParams['vars'] : array();

                // if only activated
                $bActivatedOnly = !empty($aParams['installed']) ? $aParams['installed'] : false;

                $mReturn = \MCE\Tools::isInstalled($mValue, $aModuleVars, false, $bActivatedOnly);

                if (!empty($aParams['hasToNotEmpty'])) {
                    if (!$mReturn) {
                        $bWarning = true;
                    }
                } else {
                    if ($mReturn) {
                        $bWarning = true;
                    }
                }
                break;
            case 'function' :
                if (!function_exists($mValue)) {
                    $bWarning = true;
                }
                break;
            case 'callback' :
                $mReturn = call_user_func_array($mValue, array($aParams));

                if (!empty($aParams['hasToNotEmpty'])) {
                    if (empty($mReturn)) {
                        $bWarning = true;
                    }
                } else {
                    if (!empty($mReturn)) {
                        $bWarning = true;
                    }
                }
                break;
            case 'file-permission' :
                // use case - check file permission
                if (!is_writable($mValue)) {
                    $bWarning = true;
                }
                break;
            default:
                $bWarning = false;
                break;
        }

        if ($bWarning && $bStop) {
            $this->bStopExecution = true;
        }

        return $bWarning;
    }

    /**
     * manages singleton
     *
     * @param
     * @return array
     */
    public static function create()
    {
        static $oWarning;

        if (null === $oWarning) {
            $oWarning = new \MCE\Warning();
        }
        return $oWarning;
    }
}

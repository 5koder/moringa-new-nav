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

class HookCtrl
{
    /**
     * @var obj $_oHook : defines hook object to display
     */
    private $oHook = null;

    /**
     * instantiate the matching hook class
     *
     * @param string $sType : type of interface to execute
     * @param string $sAction
     */
    public function __construct($sType, $sAction)
    {
        // include interface of hook executing
        require_once(_MCE_PATH_LIB_HOOK . 'HookBase.php');

        // check if file exists
        if (!file_exists(_MCE_PATH_LIB_HOOK . 'Hook' . ucfirst($sType) . '.php')) {
            throw new \Exception("no valid file", 130);
        } else {
            // include matched hook object
            require_once(_MCE_PATH_LIB_HOOK . 'Hook' . ucfirst($sType) . '.php');

            if (!class_exists('\MCE\Hook' . ucfirst($sType))
                && !method_exists('\MCE\Hook' . ucfirst($sType), 'run')
            ) {
                throw new \Exception("no valid class and method", 131);
            } else {
                // set class name
                $sClassName = '\MCE\Hook' . ucfirst($sType);

                // instantiate
                $this->oHook = new $sClassName($sAction);
            }
        }
    }

    /**
     * execute hook
     *
     * @category hook collection
     * @param array $aParams
     * @return array $aDisplay : empty => false / not empty => true
     */
    public function run(array $aParams = null)
    {
        return $this->oHook->run($aParams);
    }
}

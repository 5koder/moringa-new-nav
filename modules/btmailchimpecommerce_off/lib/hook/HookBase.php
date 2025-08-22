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

abstract class HookBase
{
    /**
     * @var string $sHook : define hook display or action
     */
    protected $sHook = null;

    /**
     * assigns few information about hook
     *
     * @param string $sHook
     */
    public function __construct($sHook)
    {
        // set hook
        $this->sHook = $sHook;
    }

    /**
     * execute hook
     *
     * @category hook collection
     * @uses
     *
     * @param array $aParams
     * @return array
     */
    abstract public function run(array $aParams = null);


    /**
     * get the MC JS if the store exists and connect to the current PS shop
     *
     * @return array
     */
    protected function getMailchimpJS()
    {
        // set vars
        $data = array();

        try {
            if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                $data = \MCE\Chimp\Facade::get('site_script');
            }
        } catch (\Exception $e) {}

        return $data;
    }

}

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

abstract class BaseCtrl
{
    /**
     * @var string $sAction : defines action
     */
    protected static $sAction = null;

    /**
     * @var string $sType : defines type
     */
    protected static $sType = null;

    /**
     * get params keys
     *
     * @param array $aParams
     */
    private function __construct(array $aParams = null)
    {
        // defines type to execute
        // use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
        // use case : key sAction sent in POST mode (form or ajax query posted ).
        $sAction = (!Tools::getIsset('sAction') ||  (\Tools::getIsset('sAction') && 'display' == \Tools::getValue('sAction'))) ?  (\Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'display') : \Tools::getValue('sAction');

        // set action
        $this->setAction($sAction);

        // set type
        $this->setType();
    }

    /**
     * set value to a property of object
     *
     * @param array $aRequest
     * @return array
     */
    abstract public function run($aRequest);

    /**
     * set type of method each controller has to execute
     *
     * @param string $sType
     */
    public static function setType($sType = null)
    {
        self::$sType = $sType !== null ? $sType : \Tools::getValue('sType');
    }

    /**
     * set action and select which controller
     *
     * @param string $sAction
     */
    public static function setAction($sAction = null)
    {
        self::$sAction = $sAction !== null ? $sAction : \Tools::getValue('sAction');
    }


    /**
     * instantiate matched ctrl object
     *
     * @throws Exception
     * @param string $sCtrlType
     * @param array $aParams
     * @return obj ctrl type
     */
    public static function get($sCtrlType, array $aParams = null)
    {
        $sCtrlType = strtolower($sCtrlType);

        // if valid controller
        if (file_exists(_MCE_PATH_LIB_ADMIN . ucfirst($sCtrlType) . 'Ctrl.php')) {
            // require
            require_once(ucfirst($sCtrlType) . 'Ctrl.php');

            // set class name
            $sClassName = 'MCE\\'. ucfirst($sCtrlType) . 'Ctrl';

            try {
                $oReflection = new \ReflectionClass($sClassName);

                if ($oReflection->isInstantiable()) {
                    return $oReflection->newInstance($aParams);
                } else {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('Internal server error => controller isn\'t instantiable', 'BaseCtrl'), 510);
                }
            } catch (ReflectionException $e) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('Internal server error => invalid controller', 'BaseCtrl'), 511);
            }
        } else {
            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('Internal server error => the controller file doesn\'t exist', 'BaseCtrl'), 512);
        }
    }
}

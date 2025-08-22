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

namespace MCE\Chimp\Command;

abstract class BaseCommand
{
    /**
     * @var obj $app : record the dynamic current obj according to the type of MailChimp resources we handle through
     */
    protected $app;

    /**
     * @var mixed $iCurrentId : record the current Id to handle current store / list / batch etc...
     */
    protected $iCurrentId;

    /**
     * assigns api obj
     *
     * @param MailChimp $oApp
     *
     */
    public function __construct($oApp)
    {
        $this->app = $oApp;
    }

    /**
     * define the current ID of the main app object as store / list/ batch etc...
     *
     * @param mixed $iCurrentId
     * @return bool
     */
    public function setId($iCurrentId)
    {
        $bReturn = false;
        if (is_string($iCurrentId)) {
            $this->iCurrentId = $iCurrentId;
            $bReturn = true;
        }

        return $bReturn;
    }

    /**
     * define the current ID of the main app object as store / list / batch etc...
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->iCurrentId;
    }
}

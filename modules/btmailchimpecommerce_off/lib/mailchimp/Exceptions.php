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

namespace MCE\Chimp;

class MailchimpException extends \Exception
{
    /**
     * @var array $errors : stock errors
     */
    protected $errors;

    /**
     * @var string $title : error's title
     */
    protected $title;

    /**
     * @var string $details : error's details
     */
    protected $details;

    /**
     * assigns few information about the class error
     *
     * @param string $url
     * @param string $title
     * @param string $details
     * @param string $type
     * @param array $errors
     */
    public function __construct($url = '', $title = '', $details = '', $type = '', array $errors = null)
    {
        $sCompleteTitle = $title . ' ' . \BTMailchimpEcommerce::$oModule->l('MailChimp API:', 'exceptions') . ' ' . $url;

        // instantiate
        parent::__construct($sCompleteTitle . (!empty($details) ? ' - ' . $details : '') . ' (code: ' . $this->getCode() . (!empty($type) ? ', type: "' . $type . '"' : '') . ')');

        // set obj variables
        $this->title = $sCompleteTitle;
        $this->details = $details;
        $this->errors = $errors;
    }

    /**
     * returns a friendly message
     *
     * @return string
     */
    public function getFriendlyMessage()
    {
        $sDetails = '';

        if (!empty($this->errors)) {
            foreach ($this->errors as $aError) {
                if (array_key_exists('message', $aError)) {
                    $sDetails .= ($sDetails != '' ? ' / ' : '');
                    if (array_key_exists('field', $aError)) {
                        $sDetails .= '"'. $aError['field'] .'" : ';
                    }
                    $sDetails .= $aError['message'];
                }
            }
        }

        return (
            ($sDetails != '' ? $this->title . ' : ' . $sDetails : $this->getMessage()) . ' (not-formatted: ' . $this->getDetails() . ')'
        );
    }

    /**
     * returns the error's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * returns the error's details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * returns the list of errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

class MailchimpHttpException extends MailChimpException
{

}

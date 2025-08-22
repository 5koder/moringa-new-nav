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

require_once(dirname(__FILE__) . '/Exceptions.php');


class Api
{
    /**
     * @cont string POST : defines the POST action
     */
    const POST = 'POST';

    /**
     * @cont string GET : defines the GET action
     */
    const GET = 'GET';

    /**
     * @cont string PATCH : defines the PATCH action
     */
    const PATCH = 'PATCH';

    /**
     * @cont string DELETE : defines the DELETE action
     */
    const DELETE = 'DELETE';

    /**
     * @cont string PUT : defines the PUT action
     */
    const PUT = 'PUT';

    /**
     * @var string $sRootUrl : the main URL of MC to call API
     */
    public $sRootUrl = 'https://api.mailchimp.com/3.0';

    /**
     * @var resource $ch : store the current curl object
     */
    public $ch = null;

    /**
     * @var string $sApiKey : store the API key
     */
    public $sApiKey = false;

    /**
     * @var bool $debug : activate the debug mode
     */
    public $debug = false;

    /**
     * @var string $sName : store the name of the current API object
     */
    public $sName = false;


    /**
     * assigns api key and options
     *
     * @throws \Exception
     * @param string $sApiKey
     * @param array $aOptions
     *
     */
    public function __construct($sApiKey, array $aOptions = array())
    {
        if (empty($sApiKey)) {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('You must provide a MailChimp API key', 'controller_class'));
        }

        // set the API key
        $this->sApiKey = $sApiKey;

        // set the good URL
        $this->setApiUrl();

        // handle options
        $this->setOptions($aOptions);
    }


    /**
     * allow to check value assign to property
     *
     * @param string $sName
     * @param mixed $mValue
     */
    public function __set($sName, $mValue)
    {
        $this->{$sName} = is_object($mValue) ? $mValue : false;
    }


    /**
     * _returns allowed properties
     *
     * @param string $sName
     * @return bool
     */
    public function __get($sName)
    {
        // check if the current asked property is matching to the known MC API object
        if (array_key_exists($sName, $GLOBALS['MCE_APP_TYPES'])) {
            if (!isset($this->{$sName})
                || (isset($this->{$sName}) && $this->{$sName} === false)
            ) {
                $this->{$sName} = $this->getApp($sName);
            }
            return $this->{$sName};
        }
        return false;
    }


    /**
     * defines which is the real API Url to call
     *
     * @throws \Exception
     */
    public function setApiUrl()
    {
        if (strstr($this->sApiKey, '-')) {
            list($sKey, $sDataCenter) = explode('-', $this->sApiKey, 2);

            if (empty($sDataCenter)) {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('You don\'t have a valid MailChimp API key to detect the data center used by MailChimp for you', 'controller_class'));
            }
        }
        $this->sRootUrl = str_replace('https://api', 'https://' . $sDataCenter . '.api', $this->sRootUrl);
        $this->sRootUrl = rtrim($this->sRootUrl, '/') . '/';
    }


    /**
     * handle different options
     *
     * @param array $aOptions
     */
    public function setOptions(array $aOptions = array())
    {
        // cURL init
        $this->ch = curl_init();

        if (!isset($aOptions['timeout'])
            || (isset($aOptions['timeout'])
            && !is_numeric($aOptions['timeout']))
        ) {
            $aOptions['timeout'] = 600;
        }
        if (isset($aOptions['debug'])) {
            $this->debug = true;
        }
        if (!isset($aOptions['label'])) {
            $aOptions['label'] = 'noname';
        }
        if (isset($aOptions['CURLOPT_FOLLOWLOCATION'])
            && $aOptions['CURLOPT_FOLLOWLOCATION'] === true
        ) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'BusinessTech-MailChimp-PHP/3.0.0');
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $aOptions['timeout']);
        curl_setopt($this->ch, CURLOPT_USERPWD, $aOptions['label'] . ':' . $this->sApiKey);
    }


    /**
     * instantiate matched app object
     *
     * @throws MailchimpException
     * @param string $sAppType
     * @param array $aParams
     * @return obj app type abstract type
     */
    public function getApp($sAppType)
    {
        // if valid connector
        if (array_key_exists($sAppType, $GLOBALS['MCE_APP_TYPES'])) {
            // include
            require_once(dirname(__FILE__) .'/command/Command.php');
            require_once(dirname(__FILE__) .'/command/'. $GLOBALS['MCE_APP_TYPES'][$sAppType] . '.php');

            // set class name
            $sClassName = '\MCE\Chimp\Command\\'. ucfirst($sAppType);

            // get tags type name
            $this->sName = $sAppType;

            $obj = new $sClassName($this);

            return $obj;
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => invalid MailChimp API type', 'controller_class'), 1501);
        }
    }


    /**
     * do calls to the MC API
     *
     * @throws \Exception
     * @param string $sUrl
     * @param mixed $mParams
     * @param mixed $sMethod
     * @param bool $bEncodeJson
     * @return array
     */
    public function call($sUrl, $mParams = null, $sMethod = \MCE\Chimp\Api::GET, $bEncodeJson = true)
    {
        // detect if we have to json encode the params
        if (!empty($mParams)
            && $bEncodeJson
            && $sMethod != \MCE\Chimp\Api::GET
        ) {
            $mParams = \Tools::jsonEncode($mParams);
        }

        // get the current CURL resource
        $ch = $this->ch;

        // use case - all actions different from GET
        if (!empty($mParams)) {
            if ($sMethod != \MCE\Chimp\Api::GET) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $mParams);
            } else {
                $sUrl .= '?' . http_build_query($mParams);
            }
        }

        $header = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        // set the last cURL options
        curl_setopt($ch, CURLOPT_URL, $this->sRootUrl . $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $sMethod);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // execute cURL and get the response
        $mResponse = curl_exec($ch);
        $aInfo = curl_getinfo($ch);

        // get the result
        $aResult = \Tools::jsonDecode($mResponse, true);

        if (curl_error($ch)) {
            throw new \MCE\Chimp\MailchimpHttpException(\BTMailchimpEcommerce::$oModule->l('API call failed to: ', 'controller_class') . $sUrl . ' / ' . curl_error($ch));
        }

        if (isset($aInfo['http_code'])
            && floor($aInfo['http_code'] / 100) >= 4
        ) {
            $aErrors = (isset($aResult['errors'])) ? $aResult['errors'] : array();
            throw new \MCE\Chimp\MailchimpException($sUrl, $aResult['title'], $aResult['detail'], $aResult['type'], $aErrors);
        }

        return $aResult;
    }

    /**
     * set singleton
     *
     * @throws \Exception
     * @param string $sApiKey
     * @param array $aOptions
     * @return obj
     */
    public static function get($sApiKey, array $aOptions = array())
    {
        return new \MCE\Chimp\Api($sApiKey, $aOptions);
    }
}

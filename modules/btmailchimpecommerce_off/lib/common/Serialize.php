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

class Serialize
{
    /**
     * @var object $obj
     */
    public static $obj = null;

    /**
     * @var array $_aSerialized
     */
    private $_aSerialized = array();

    /**
     * set serialize data
     * @param array $aParams
     * @return mixed : false or string
     */
    public function set($mData)
    {
        // serialize all php variable except resource
        if (is_resource($mData)) {
            return false;
        }
        return serialize($mData);
    }

    /**
     * get specific serialized data
     * @param array $sData
     * @param string $sKey
     * @return mixed
     */
    public function get($sData, $sKey = null)
    {
        // check if string - unserialize only serialized string
        if (is_string($sData)) {
            $mData = unserialize($sData);

            if (false !== $mData) {
                if (null !== $sKey) {
                    if (is_object($mData) && property_exists($mData, $sKey)) {
                        return $mData->$sKey;
                    } elseif (is_array($mData) && isset($mData[$sKey])) {
                        return $mData[$sKey];
                    }
                }
                return $mData;
            }
        }
        // use case - string declared or unserialize doesn't works
        return false;
    }

    /**
     * format error
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errLine
     * @param array $errcontext
     * @return string
     */
    public function setErrorHandler($errno, $errstr, $errfile, $errLine, $errcontext)
    {
        if (E_STRICT != $errno && E_NOTICE != $errno) {
            throw new \Exception($errstr . ' (line: ' . $errLine . ', file:' . $errfile . ')', $errno);
        }
    }

    /**
     * create instance of object
     * @example
     * @param    mixed $mParams
     * @return  object    $obj
     */
    public static function create($mParams = null)
    {
        if (null === self::$obj) {
            self::$obj = new \MCE\Serialize($mParams);
        }

        return self::$obj;
    }
}

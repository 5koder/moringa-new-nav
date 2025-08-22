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

class Cookie
{
    /**
     * @var int
     */
    protected static $expire = 0;

    /**
     * @var string
     */
    protected static $path = '/';

    /**
     * set a value
     *
     * @param string $sName
     * @param string $sKey
     * @param mixed $mValue
     * @param int $iExpire
     * @param string $iExpire
     * @return mixed : bool
     */
    public static function set($sName, $sKey, $mValue, $iExpire, $path = '/')
    {
        setcookie($sName . '['.$sKey.']', $mValue, $iExpire, '/');
        self::$expire = $iExpire;
        self::$path = $path;
    }


    /**
     * detects if a cookie exist and try to return the required key as argument
     *
     * @param string $sName
     * @param string $sRequestedKey
     * @return mixed : string or false
     */
    public static function get($sName, $sRequestedKey)
    {
        $mReturn = false;

        if (self::exist($sName, $sRequestedKey)) {
            $mReturn = htmlspecialchars($_COOKIE[$sName][$sRequestedKey]);
        }
        return $mReturn;
    }


    /**
     * update a value
     *
     * @param string $sName
     * @param string $sKey
     * @param mixed $mValue
     * @return mixed : bool
     */
    public static function update($sName, $sKey, $mValue)
    {
        $bUpdate = false;

        if (self::exist($sName, $sKey)) {
            setcookie($sName . '['.$sKey.']', $mValue, self::$expire, self::$path);
            $bUpdate = true;
        }

        return $bUpdate;
    }

    /**
     * check if the key exist
     *
     * @param string $sName
     * @param string $sRequestedKey
     * @return mixed : bool
     */
    public static function exist($sName, $sRequestedKey = null)
    {
        $mReturn = false;

        if (isset($_COOKIE[$sName])) {
            $mReturn = true;

            if ($sRequestedKey !== null) {
                if (!isset($_COOKIE[$sName][$sRequestedKey])) {
                    $mReturn = false;
                }
            }
        }

        return $mReturn;
    }

    /**
     * delete the requested cookie or reset a key of the cookie
     *
     * @param string $sName
     * @param string $sKey
     * @return bool
     */
    public static function delete($sName, $sKey)
    {
        $bReturn = false;

        if (self::exist($sName)) {
            $value = self::get($sName, $sKey);
            self::set($sName, $sKey, $value, (time()-60));
            $bReturn = true;
        }

        return $bReturn;
    }
}

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

namespace MCE\Chimp\Format;

abstract class Formatter
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var bool
     */
    public $valid = false;

    /**
     * @var null
     */
    public $lang_id = null;

    /**
     * @var null
     */
    public $nested = false;

    /**
     * format product ID
     *
     * @param $iProductId
     * @param $iLangId
     * @param $iCombinationId
     * @return string
     */
    public static function setProductID($iProductId, $iLangId, $iCombinationId = 0)
    {
        return $iProductId .'L'. $iLangId . (!empty($iCombinationId)? 'C'. $iCombinationId : '');
    }

    /**
     * Get a real Product object
     *
     * @param $mProductId
     * @return Product
     */
    protected function getProduct($mProductId)
    {
        return (is_object($mProductId) ? $mProductId : new \Product($mProductId, false, $this->lang_id));
    }


    /**
     * @param obj $object
     * @return bool
     */
    protected function isValid($object)
    {
        $this->valid = \Validate::isLoadedObject($object)? true : false;
        return $this->valid;
    }


    /**
     * @return array
     */
    protected function isNested()
    {
        if (!empty($this->options)) {
            if ($this->nested) {
                $this->data = array_merge($this->data, array('opts' => $this->options));
            } else {
                $this->data = array_merge($this->data, $this->options);
            }
        }

        return $this->data;
    }

    /**
     * sanitize language field
     *
     * @param string $string
     * @param int lang_id
     * @return string
     */
    public static function sanitizeLanguageField($string, $lang_id)
    {
        $value = '';
        if (is_array($string)) {
            if (isset($string[$lang_id])) {
                $value = $string[$lang_id];
            } else {
                $value = reset($string);
            }
        } else {
            $value = $string;
        }
        return (string)$value;
    }


    /**
     * format the birthday string
     *
     * @param string $sBirthday
     * @return string
     */
    public static function setBirthday($date)
    {
        $sBirthday = '';

        // default format
        $sDateFormat = 'mm/dd';

        if (!empty($date)
            && $date != '0000-00-00'
        ) {
            // get the dd and mm
            list($sYear, $sMonth, $sDay) = explode('-', $date);

            if (!empty($sMonth)
                && !empty($sDay)
                && $sDay != '00'
                && $sMonth != '00'
            ) {
                // use case - french format
                $sBirthday = ($sDateFormat == 'dd/mm') ? $sDay . '/' . $sMonth : $sMonth . '/' . $sDay;
            }
        }

        return $sBirthday;
    }

    /**
     * format the customer group name
     *
     * @param int $id_default_group
     * @param int $id_lang
     * @return string
     */
    public static function setCustomerGroup($id_default_group, $id_lang)
    {
        $oGroup = new \Group($id_default_group, $id_lang);
        $group_name = $oGroup->name;

        return !empty($group_name)? $group_name : '';
    }


    /**
     * format the customer gender
     *
     * @param int $id_gender
     * @param int $id_lang
     * @return string
     */
    public static function setCustomerGender($id_gender, $id_lang)
    {
        $oGender = new \Gender($id_gender, $id_lang);
        $gender = $oGender->name;

        return !empty($gender)? $gender : '';
    }

    /**
     * format the first name
     *
     * @param int $firstname
     * @return string
     */
    public static function setFirstname($firstname)
    {
        $value = '';

        if (!empty($firstname)) {
            $value = $firstname;
        }

        return $value;
    }

    /**
     * format the last name
     *
     * @param int $lastname
     * @return string
     */
    public static function setLastname($lastname)
    {
        $value = '';

        if (!empty($lastname)) {
            $value = $lastname;
        }

        return $value;
    }

    /**
     * format the customer address for merge_fields
     *
     * @param int $id_customer
     * @return array
     */
    public static function setAddress($id_customer)
    {
        $address = [];

        if (!empty($id_customer)) {
            $customer = new \Customer($id_customer);
            $aAddress = $customer->getAddresses($customer->id_lang);
            if (!empty($aAddress[0])) {
                $format_address = (new \MCE\Chimp\Format\Address($aAddress[0]['id_address'], $customer->id_lang))->format();

                $address['addr1'] = !empty($format_address['address1']) ? $format_address['address1'] : '';
                $address['addr2'] = !empty($format_address['address2']) ? $format_address['address2'] : '';
                $address['city'] = !empty($format_address['city']) ? $format_address['city'] : '';
                $address['zip'] = !empty($format_address['postal_code']) ? $format_address['postal_code'] : '';
                $address['state'] = !empty($format_address['province']) ? $format_address['province'] : '';
                $address['country'] = !empty($format_address['country_code']) ? $format_address['country_code'] : '';
            }
        }

        return $address;
    }
}

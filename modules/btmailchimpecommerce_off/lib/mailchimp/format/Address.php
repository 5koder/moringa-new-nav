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


class Address extends Formatter
{
    /**
     * @var obj
     */
    public $address = null;

    /**
     * @var string
     */
    public $province = '';

    /**
     * @var string
     */
    public $province_code = '';

    /**
     * @var string
     */
    public $country = '';

    /**
     * @var string
     */
    public $country_code = '';

    /**
     * Address constructor.
     * @param $mAddress
     * @param $iLangId
     */
    public function __construct($mAddress, $iLangId)
    {
        $this->lang_id = $iLangId;
        $this->address = $this->getAddress($mAddress);
    }


    /**
     * Format address data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->address)) {

            // get country and state
            $this->getProvince();
            $this->getCountry();

            $this->data = array(
                'address1' => $this->address->address1,
                'address2' => $this->address->address2,
                'city' => $this->address->city,
                'postal_code' => $this->address->postcode,
                'country' => (string) $this->country,
                'country_code' => (string) $this->country_code,
                'province' => (string) $this->province,
                'province_code' => (string) $this->province_code,
            );
            // check if the company name exist
            if (!empty($this->address->company)) {
                $this->data['company'] = $this->address->company;
            }
        }

        return $this->data;
    }

    /**
     * get the customer address object
     *
     * @param mixed
     * @return array
     */
    private function getAddress($mAddress)
    {
        return (is_object($mAddress) ? $mAddress : new \Address($mAddress, $this->lang_id));
    }

    /**
     * get the customer address province
     *
     * @return array
     */
    private function getProvince()
    {
        if (!empty($this->address->id_state)) {
            $oState = new \State($this->address->id_state, $this->lang_id);
            $this->province = $oState->name;
            $this->province_code = $oState->iso_code;
        }
    }

    /**
     * get the customer address country
     *
     * @return array
     */
    private function getCountry()
    {
        if (!empty($this->address->id_country)) {
            $oCountry = new \Country($this->address->id_country, $this->lang_id);
            $this->country = $oCountry->name;
            $this->country_code = $oCountry->iso_code;
        }
    }
}

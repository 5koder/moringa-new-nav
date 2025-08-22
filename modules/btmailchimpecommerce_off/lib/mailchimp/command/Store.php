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

class Store extends BaseCommand
{
    /**
     * @const API_STORE_URL
     */
    const API_STORE_URL = 'ecommerce/stores';

    /**
     * add a new store in the MC account
     *
     * @param string $sId
     * @param string $sListId
     * @param string $sName
     * @param string $sCurrencyIso
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add($sId, $sListId, $sName, $sCurrencyIso, array $aOpts = array())
    {
        // required values
        $aParams = array(
            'id' => $sId,
            'list_id' => $sListId,
            'name' => $sName,
            'currency_code' => $sCurrencyIso,
        );

        // optionals values - platform
        if (isset($aOpts['platform'])) {
            $aParams['platform'] = $aOpts['platform'];
        }
        // optionals - domain
        if (isset($aOpts['domain'])) {
            $aParams['domain'] = $aOpts['domain'];
        }
        // optionals - email address
        if (isset($aOpts['email_address'])) {
            $aParams['email_address'] = $aOpts['email_address'];
        }
        // optionals - money format
        if (isset($aOpts['money_format'])) {
            $aParams['money_format'] = $aOpts['money_format'];
        }
        // optionals - primary locale (language currency
        if (isset($aOpts['primary_locale'])) {
            $aParams['primary_locale'] = $aOpts['primary_locale'];
        }
        // optionals - timezone
        if (isset($aOpts['timezone'])) {
            $aParams['timezone'] = $aOpts['timezone'];
        }
        // optionals - phone
        if (isset($aOpts['phone'])) {
            $aParams['phone'] = $aOpts['phone'];
        }
        // optionals - check address option
        if (isset($aOpts['address'])) {
            $aAddress = array();

            // optionals - address 1
            if (isset($aOpts['address']['address1'])) {
                $aAddress['address1'] = $aOpts['address']['address1'];
            }
            // optionals - address 2
            if (isset($aOpts['address']['address2'])) {
                $aAddress['address2'] = $aOpts['address']['address2'];
            }
            // optionals - city
            if (isset($aOpts['address']['city'])) {
                $aAddress['city'] = $aOpts['address']['city'];
            }
            // optionals - province
            if (isset($aOpts['address']['province'])) {
                $aAddress['province'] = $aOpts['address']['province'];
            }
            // optionals - province code
            if (isset($aOpts['address']['province_code'])) {
                $aAddress['province_code'] = $aOpts['address']['province_code'];
            }
            // optionals - postal code
            if (isset($aOpts['address']['postal_code'])) {
                $aAddress['postal_code'] = $aOpts['address']['postal_code'];
            }
            // optionals - country code
            if (isset($aOpts['address']['country'])) {
                $aAddress['country'] = $aOpts['address']['country'];
            }
            // optionals - country code
            if (isset($aOpts['address']['country_code'])) {
                $aAddress['country_code'] = $aOpts['address']['country_code'];
            }
            // optionals - longitude
            if (isset($aOpts['address']['longitude'])) {
                $aAddress['longitude'] = $aOpts['address']['longitude'];
            }
            // optionals - latitude
            if (isset($aOpts['address']['latitude'])) {
                $aAddress['latitude'] = $aOpts['address']['latitude'];
            }
            // transform it into object as required in the MC's API
            if (!empty($aAddress)) {
                $aParams['address'] = $aAddress;
            }
        }

        return $this->app->call(self::API_STORE_URL, $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get store's information
     *
     * @param string $sId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $sId = null,
        array $aFields = array(),
        array $aExcludeFields = array(),
        $iCount = null,
        $iOffset = null
    ) {
        // optional values
        $aParams = array();

        // optionals - fields
        if (!empty($aFields)) {
            $aParams['fields'] = $aFields;
        }
        // optionals - exclude fields
        if (!empty($aExcludeFields)) {
            $aParams['exclude_fields'] = $aExcludeFields;
        }
        // optionals - number of record to return
        if ($iCount !== null) {
            $aParams['count'] = $iCount;
        }
        // optionals - offset
        if ($iOffset !== null) {
            $aParams['offset'] = $iOffset;
        }

        return $this->app->call(self::API_STORE_URL . (!empty($sId) ? '/' . $sId : ''), $aParams, \MCE\Chimp\Api::GET);
    }


    /**
     * update store's information
     *
     * @param string $sId : store ID
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update($sId, array $aOpts = array())
    {
        // optional values
        $aParams = array();

        // optionals - name
        if (isset($aOpts['name'])) {
            $aParams['name'] = $aOpts['name'];
        }

        // optionals - platform
        if (isset($aOpts['platform'])) {
            $aParams['platform'] = $aOpts['platform'];
        }
        // optionals - domain
        if (isset($aOpts['domain'])) {
            $aParams['domain'] = $aOpts['domain'];
        }
        // optionals - email address
        if (isset($aOpts['email_address'])) {
            $aParams['email_address'] = $aOpts['email_address'];
        }
        // optionals - money format
        if (isset($aOpts['money_format'])) {
            $aParams['money_format'] = $aOpts['money_format'];
        }
        // optionals - primary locale (language currency
        if (isset($aOpts['primary_locale'])) {
            $aParams['primary_locale'] = $aOpts['primary_locale'];
        }
        // optionals - timezone
        if (isset($aOpts['timezone'])) {
            $aParams['timezone'] = $aOpts['timezone'];
        }
        // optionals - phone
        if (isset($aOpts['phone'])) {
            $aParams['phone'] = $aOpts['phone'];
        }
        // optionals - check address option
        if (isset($aOpts['address'])) {
            $aAddress = array();

            // optionals - address 1
            if (isset($aOpts['address']['address1'])) {
                $aAddress['address1'] = $aOpts['address']['address1'];
            }
            // optionals - address 2
            if (isset($aOpts['address']['address2'])) {
                $aAddress['address2'] = $aOpts['address']['address2'];
            }
            // optionals - city
            if (isset($aOpts['address']['city'])) {
                $aAddress['city'] = $aOpts['address']['city'];
            }
            // optionals - province
            if (isset($aOpts['address']['province'])) {
                $aAddress['province'] = $aOpts['address']['province'];
            }
            // optionals - province code
            if (isset($aOpts['address']['province_code'])) {
                $aAddress['province_code'] = $aOpts['address']['province_code'];
            }
            // optionals - postal code
            if (isset($aOpts['address']['postal_code'])) {
                $aAddress['postal_code'] = $aOpts['address']['postal_code'];
            }
            // optionals - country code
            if (isset($aOpts['address']['country'])) {
                $aAddress['country'] = $aOpts['address']['country'];
            }
            // optionals - country code
            if (isset($aOpts['address']['country_code'])) {
                $aAddress['country_code'] = $aOpts['address']['country_code'];
            }
            // optionals - longitude
            if (isset($aOpts['address']['longitude'])) {
                $aAddress['longitude'] = $aOpts['address']['longitude'];
            }
            // optionals - latitude
            if (isset($aOpts['address']['latitude'])) {
                $aAddress['latitude'] = $aOpts['address']['latitude'];
            }
            // transform it into object as required in the MC's API
            if (!empty($aAddress)) {
                $aParams['address'] = $aAddress;
            }
        }

        return $this->app->call(self::API_STORE_URL . '/' . $sId, $aParams, \MCE\Chimp\Api::PATCH);
    }


    /**
     * delete store
     *
     * @param string $sId : store ID
     * @return mixed : result of the API call
     */
    public function delete($sId)
    {
        return $this->app->call(self::API_STORE_URL . '/' . $sId, null, \MCE\Chimp\Api::DELETE);
    }
}

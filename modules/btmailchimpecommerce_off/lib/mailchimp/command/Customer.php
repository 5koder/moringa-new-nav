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

class Customer extends BaseCommand
{
    /**
     * @const API_CUSTOMER_URL
     */
    const API_CUSTOMER_URL = 'ecommerce/stores/';

    /**
     * add a new customer in the MC account
     *
     * @param string $iCustomerId
     * @param string $sEmail
     * @param bool $bOptinStatus
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add($iCustomerId, $sEmail, $bOptinStatus, array $aOpts = array())
    {
        // required values
        $aParams = array(
            'id' => $iCustomerId,
            'email_address' => $sEmail,
            'opt_in_status' => (bool)$bOptinStatus,
        );

        // optionals values - company
        if (isset($aOpts['company'])) {
            $aParams['company'] = $aOpts['company'];
        }
        // optionals - first_name
        if (isset($aOpts['first_name'])) {
            $aParams['first_name'] = $aOpts['first_name'];
        }
        // optionals - last_name
        if (isset($aOpts['last_name'])) {
            $aParams['last_name'] = $aOpts['last_name'];
        }
        // optionals - orders_count
        if (isset($aOpts['orders_count'])) {
            $aParams['orders_count'] = $aOpts['orders_count'];
        }
        // optionals - total_spent
        if (isset($aOpts['total_spent'])) {
            $aParams['total_spent'] = $aOpts['total_spent'];
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
            // transform it into object as required in the MC's API
            if (!empty($aAddress)) {
                $aParams['address'] = $aAddress;
            }
        }

        return $this->app->call(self::API_CUSTOMER_URL . '/' . $this->getId() . '/customers', $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get customer' information
     *
     * @param int $iCustomerId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iCustomerId = null,
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

        return $this->app->call(
            self::API_CUSTOMER_URL . '/' . $this->getId() . '/customers' . (!empty($iCustomerId) ? '/' . $iCustomerId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update customers' information
     *
     * @param int $iCustomerId
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update($iCustomerId, array $aOpts = array())
    {
        $aParams = array();

        // optionals - opt-in
        if (isset($aOpts['opt_in_status'])) {
            $aParams['opt_in_status'] = (bool)$aOpts['opt_in_status'];
        }
        // optionals - company
        if (isset($aOpts['company'])) {
            $aParams['company'] = $aOpts['company'];
        }
        // optionals - first_name
        if (isset($aOpts['first_name'])) {
            $aParams['first_name'] = $aOpts['first_name'];
        }
        // optionals - last_name
        if (isset($aOpts['last_name'])) {
            $aParams['last_name'] = $aOpts['last_name'];
        }
        // optionals - orders_count
        if (isset($aOpts['orders_count'])) {
            $aParams['orders_count'] = $aOpts['orders_count'];
        }
        // optionals - total_spent
        if (isset($aOpts['total_spent'])) {
            $aParams['total_spent'] = $aOpts['total_spent'];
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

        return $this->app->call(
            self::API_CUSTOMER_URL . '/' . $this->getId() . '/customers' . (!empty($iCustomerId) ? '/' . $iCustomerId : ''),
            $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete customer
     *
     * @param string $iCustomerId : customer ID
     * @return mixed : result of the API call
     */
    public function delete($iCustomerId)
    {
        return $this->app->call(
            self::API_CUSTOMER_URL . '/' . $this->getId() . '/customers/' . $iCustomerId, null,
            \MCE\Chimp\Api::DELETE
        );
    }
}

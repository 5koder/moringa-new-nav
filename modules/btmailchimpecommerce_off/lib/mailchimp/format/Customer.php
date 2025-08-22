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

use \MCE\Chimp\Format\Address;

class Customer extends Formatter
{
    /**
     * @var null
     */
    public $customer = null;


    /**
     * @var int
     */
    public $currency_id = null;

    /**
     * @var int
     */
    public $default_address_id = 0;

    /**
     * @var null
     */
    public $address_id = null;

    /**
     * @var array
     */
    public $address = [];

    /**
     * @var bool
     */
    public $optin = 1;

    /**
     * @var int
     */
    public $total_orders = 0;

    /**
     * @var float
     */
    public $total_spent = 0.00;

    /**
     * Customer constructor.
     * @param $mCustomer
     * @param $iLangId
     * @param $iCurrencyId
     * @param $aForceAddress
     */
    public function __construct($mCustomer, $iLangId, $iCurrencyId, $iForceAddressId = 0)
    {
        $this->lang_id = $iLangId;
        $this->customer = $this->getCustomer($mCustomer);
        $this->currency_id = $iCurrencyId;
        $this->default_address_id = $iForceAddressId;
    }


    /**
     * Format customer data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->customer)) {
            // get total orders
            $this->total_orders = $this->getTotalOrders();

            // total spent
            $this->total_spent = $this->getTotalSpent();

            // get the optin status
            $this->getOptin();

            // get a valid address
            $this->getAddress();

            $this->data = array(
                'id' => (string) $this->customer->id,
                'email_address' => $this->customer->email,
                'opt_in_status' => (bool) $this->optin,
                'first_name' => $this->customer->firstname,
                'last_name' => $this->customer->lastname,
                'orders_count' => (int) $this->total_orders,
                'total_spent' => $this->total_spent,
                'birthday' => (isset($this->customer->birthday) ? $this->customer->birthday : '00-00-00'),
            );

            // check address
            if (!empty($this->address_id)) {

                $this->data['address'] = (new \MCE\Chimp\Format\Address($this->address_id, $this->lang_id))->format();

                if (!empty($this->data['address']['company'])) {
                    $this->data['company'] = $this->data['address']['company'];
                }
            }
        }

        return $this->data;
    }

    /**
     * Get a real customer object
     *
     * @param $mCustomer
     * @return Customer
     */
    private function getCustomer($mCustomer)
    {
        return (is_object($mCustomer) ? $mCustomer : new \Customer($mCustomer, $this->lang_id));
    }


    /**
     * define if the customer optin or not
     * @return bool
     */
    private function getOptin()
    {
        if (
            \BTMailchimpEcommerce::$conf['MCE_CUST_TYPE_EXPORT'] == 'optin'
            && isset($this->customer->newsletter)
            && $this->customer->newsletter == false
        ) {
            $this->optin = 0;
        }

        return $this->optin;
    }


    /**
     * define the total of orders
     * @return int
     */
    private function getTotalOrders()
    {
        $this->total_orders = \Order::getCustomerNbOrders($this->customer->id);
        return $this->total_orders;
    }


    /**
     * define the total spent
     * @return int
     */
    private function getTotalSpent()
    {
        if (!empty($this->total_orders)) {
            require_once(_MCE_PATH_LIB . 'Dao.php');

            // get the past orders
            $aOrders = \MCE\Dao::getCustomerOrders($this->customer->id);

            if (!empty($aOrders)) {
                foreach ($aOrders as $aOrder) {
                    $this->total_spent = (float) ($this->total_spent + \MCE\Tools::convertAmount($aOrder['total_paid'], $aOrder['id_currency'], $this->currency_id));
                }
                $this->total_spent = (float) number_format(\MCE\Tools::round($this->total_spent), 2, '.', '');

                // get the invoice address from the last order
                $this->address_id = $aOrders[0]['id_address_invoice'];
            }
        }
        return $this->total_spent;
    }

    /**
     * get the customer address
     * @return array
     */
    private function getAddress()
    {
        if (!empty($this->default_address_id)) {
            $this->address_id = $this->default_address_id;
        } elseif (empty($this->address_id)) {
            $aAddress = $this->customer->getAddresses($this->customer->id_lang);
            if (!empty($aAddress[0])) {
                $this->address_id = $aAddress[0]['id_address'];
            }
        }

        return $this->address_id;
    }
}

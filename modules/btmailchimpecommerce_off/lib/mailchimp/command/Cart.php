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

class Cart extends BaseCommand
{
    /**
     * @const API_CART_URL
     */
    const API_CART_URL = 'ecommerce/stores/';

    /**
     * add a new cart in the MC store
     *
     * @throws \Exception
     * @param int $iCartId
     * @param array $aCartData
     * @return mixed : result of the API call
     */
    public function add($iCartId, array $aCartData)
    {
        $aParams = array();

        // check the good information of customer and cart's lines
        if (!empty($iCartId)
            && is_array($aCartData)
            && !empty($aCartData['customer'])
            && !empty($aCartData['lines'])
            && !empty($aCartData['currency_code'])
            && !empty($aCartData['order_total'])
        ) {
            $aParams['id'] = (string)$iCartId;
            // use case - the cart amount and currency ISO
            $aParams['order_total'] = (float)$aCartData['order_total'];
            $aParams['currency_code'] = $aCartData['currency_code'];

            // check if the customer ID is well defined
            if (!empty($aCartData['customer']['id'])) {
                $aCustomer['id'] = (string)$aCartData['customer']['id'];

                // optionals values - email
                if (!empty($aCartData['customer']['email_address'])) {
                    $aCustomer['email_address'] = $aCartData['customer']['email_address'];
                }
                // optionals values - opt-in
                if (isset($aCartData['customer']['opt_in_status'])) {
                    $aCustomer['opt_in_status'] = (bool)$aCartData['customer']['opt_in_status'];
                }
                // optionals values - company
                if (!empty($aCartData['customer']['company'])) {
                    $aCustomer['company'] = $aCartData['customer']['company'];
                }
                // optionals values - firstname
                if (!empty($aCartData['customer']['first_name'])) {
                    $aCustomer['first_name'] = $aCartData['customer']['first_name'];
                }
                // optionals values - lastname
                if (!empty($aCartData['customer']['last_name'])) {
                    $aCustomer['last_name'] = $aCartData['customer']['last_name'];
                }
                // optionals values - orders_count
                if (!empty($aCartData['customer']['orders_count'])) {
                    $aCustomer['orders_count'] = $aCartData['customer']['orders_count'];
                }
                // optionals values - total_spent
                if (!empty($aCartData['customer']['total_spent'])) {
                    $aCustomer['total_spent'] = $aCartData['customer']['total_spent'];
                }
                // set the customer object
                $aParams['customer'] = $aCustomer;

                // optionals - check address option
                if (!empty($aCartData['customer']['address'])) {
                    $aAddress = array();

                    // optionals - address 1
                    if (!empty($aCartData['customer']['address']['address1'])) {
                        $aAddress['address1'] = $aCartData['customer']['address']['address1'];
                    }
                    // optionals - address 2
                    if (!empty($aCartData['customer']['address']['address2'])) {
                        $aAddress['address2'] = $aCartData['customer']['address']['address2'];
                    }
                    // optionals - city
                    if (!empty($aCartData['customer']['address']['city'])) {
                        $aAddress['city'] = $aCartData['customer']['address']['city'];
                    }
                    // optionals - province
                    if (!empty($aCartData['customer']['address']['province'])) {
                        $aAddress['province'] = $aCartData['customer']['address']['province'];
                    }
                    // optionals - province code
                    if (!empty($aCartData['customer']['address']['province_code'])) {
                        $aAddress['province_code'] = $aCartData['customer']['address']['province_code'];
                    }
                    // optionals - postal code
                    if (!empty($aCartData['customer']['address']['postal_code'])) {
                        $aAddress['postal_code'] = $aCartData['customer']['address']['postal_code'];
                    }
                    // optionals - country code
                    if (!empty($aCartData['customer']['address']['country'])) {
                        $aAddress['country'] = $aCartData['customer']['address']['country'];
                    }
                    // optionals - country code
                    if (!empty($aCartData['customer']['address']['country_code'])) {
                        $aAddress['country_code'] = $aCartData['customer']['address']['country_code'];
                    }
                    // optionals - longitude
                    if (!empty($aCartData['customer']['address']['longitude'])) {
                        $aAddress['longitude'] = $aCartData['customer']['address']['longitude'];
                    }
                    // optionals - latitude
                    if (!empty($aCartData['customer']['address']['latitude'])) {
                        $aAddress['latitude'] = $aCartData['customer']['address']['latitude'];
                    }
                    // transform it into object as required in the MC's API
                    if (!empty($aAddress)) {
                        $aParams['customer']['address'] = $aAddress;
                    }
                }
                // use case - cart lines
                $aCartLines = array();
                foreach ($aCartData['lines'] as $aCartLine) {
                    if (!empty($aCartLine['id'])
                        && !empty($aCartLine['product_id'])
                        && !empty($aCartLine['product_variant_id'])
                        && !empty($aCartLine['quantity'])
                        && isset($aCartLine['price'])
                    ) {
                        $aCartLines[] = array(
                            'id' => (string)$aCartLine['id'],
                            'product_id' => (string)$aCartLine['product_id'],
                            'product_variant_id' => (string)$aCartLine['product_variant_id'],
                            'quantity' => (int)$aCartLine['quantity'],
                            'price' => $aCartLine['price'],
                        );
                    } else {
                        throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => cart lines are not valid and do not include the required data, cart ID:', 'ecommerce-cart_class') . ' ' . $aCartLine['id'], 1520);
                    }
                }
                $aParams['lines'] = $aCartLines;

                // optionals - campaign_id
                if (!empty($aCartData['campaign_id'])) {
                    $aParams['campaign_id'] = $aCartData['campaign_id'];
                }
                // optionals - checkout_url
                if (!empty($aCartData['checkout_url'])) {
                    $aParams['checkout_url'] = $aCartData['checkout_url'];
                }
                // optionals - tax_total
                if (!empty($aCartData['tax_total'])) {
                    $aParams['tax_total'] = $aCartData['tax_total'];
                }
            } else {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the customer ID is not valid', 'ecommerce-cart_class'), 1521);
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => customer or cart\'s lines are empty data', 'ecommerce-cart_class'), 1522);
        }

        return $this->app->call(self::API_CART_URL . '/' . $this->getId() . '/carts', $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get carts' information
     *
     * @param int $iCartId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iCartId = null,
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
        if (!empty($iCount)) {
            $aParams['count'] = $iCount;
        }
        // optionals - offset
        if ($iOffset !== null) {
            $aParams['offset'] = $iOffset;
        }

        return $this->app->call(
            self::API_CART_URL . '/' . $this->getId() . '/carts' . (!empty($iCartId) ? '/' . $iCartId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update carts' information
     *
     * @throws \Exception
     * @throws Exception
     * @param int $iCartId
     * @param array $aCartData
     * @return mixed : result of the API call
     */
    public function update($iCartId, array $aCartData)
    {
        $aParams = array();

        // check the good information of customer and cart's lines
        if (!empty($iCartId)
            && is_array($aCartData)
        ) {
            // use case - the cart amount and currency ISO
            if (isset($aCartData['order_total'])) {
                $aParams['order_total'] = (float)$aCartData['order_total'];
            }
            if (isset($aCartData['currency_code'])) {
                $aParams['currency_code'] = $aCartData['currency_code'];
            }

            // use case - cart lines
            if (!empty($aCartData['lines'])) {
                $aCartLines = array();
                foreach ($aCartData['lines'] as $aCartLine) {
                    if (!empty($aCartLine['id'])
                        && !empty($aCartLine['product_id'])
                        && !empty($aCartLine['product_variant_id'])
                        && !empty($aCartLine['quantity'])
                        && isset($aCartLine['price'])
                    ) {
                        $aCartLines[] = array(
                            'id' => (string)$aCartLine['id'],
                            'product_id' => (string)$aCartLine['product_id'],
                            'product_variant_id' => (string)$aCartLine['product_variant_id'],
                            'quantity' => (int)$aCartLine['quantity'],
                            'price' => $aCartLine['price'],
                        );
                    } else {
                        throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => cart lines are not valid and do not include the required data, cart ID:', 'ecommerce-cart_class') . ' ' . $aCartLine['id'], 1523);
                    }
                }
                $aParams['lines'] = $aCartLines;
            }

            // check if the customer ID is well defined
            if (!empty($aCartData['customer']['id'])) {
                $aCustomer['id'] = (string)$aCartData['customer']['id'];

                // optionals values - email
                if (!empty($aCartData['customer']['email_address'])) {
                    $aCustomer['email_address'] = $aCartData['customer']['email_address'];
                }
                // optionals values - opt-in
                if (!empty($aCartData['customer']['opt_in_status'])) {
                    $aCustomer['opt_in_status'] = (bool)$aCartData['customer']['opt_in_status'];
                }
                // optionals values - company
                if (!empty($aCartData['customer']['company'])) {
                    $aCustomer['company'] = $aCartData['customer']['company'];
                }
                // optionals values - firstname
                if (!empty($aCartData['customer']['first_name'])) {
                    $aCustomer['first_name'] = $aCartData['customer']['first_name'];
                }
                // optionals values - lastname
                if (!empty($aCartData['customer']['last_name'])) {
                    $aCustomer['last_name'] = $aCartData['customer']['last_name'];
                }
                // optionals values - orders_count
                if (!empty($aCartData['customer']['orders_count'])) {
                    $aCustomer['orders_count'] = $aCartData['customer']['orders_count'];
                }
                // optionals values - total_spent
                if (!empty($aCartData['customer']['total_spent'])) {
                    $aCustomer['total_spent'] = $aCartData['customer']['total_spent'];
                }
                // set the customer object
                $aParams['customer'] = $aCustomer;

                // optionals - check address option
                if (!empty($aCartData['customer']['address'])) {
                    $aAddress = array();

                    // optionals - address 1
                    if (!empty($aCartData['customer']['address']['address1'])) {
                        $aAddress['address1'] = $aCartData['customer']['address']['address1'];
                    }
                    // optionals - address 2
                    if (!empty($aCartData['customer']['address']['address2'])) {
                        $aAddress['address2'] = $aCartData['customer']['address']['address2'];
                    }
                    // optionals - city
                    if (!empty($aCartData['customer']['address']['city'])) {
                        $aAddress['city'] = $aCartData['customer']['address']['city'];
                    }
                    // optionals - province
                    if (!empty($aCartData['customer']['address']['province'])) {
                        $aAddress['province'] = $aCartData['customer']['address']['province'];
                    }
                    // optionals - province code
                    if (!empty($aCartData['customer']['address']['province_code'])) {
                        $aAddress['province_code'] = $aCartData['customer']['address']['province_code'];
                    }
                    // optionals - postal code
                    if (!empty($aCartData['customer']['address']['postal_code'])) {
                        $aAddress['postal_code'] = $aCartData['customer']['address']['postal_code'];
                    }
                    // optionals - country code
                    if (!empty($aCartData['customer']['address']['country'])) {
                        $aAddress['country'] = $aCartData['customer']['address']['country'];
                    }
                    // optionals - country code
                    if (!empty($aCartData['customer']['address']['country_code'])) {
                        $aAddress['country_code'] = $aCartData['customer']['address']['country_code'];
                    }
                    // optionals - longitude
                    if (!empty($aCartData['customer']['address']['longitude'])) {
                        $aAddress['longitude'] = $aCartData['customer']['address']['longitude'];
                    }
                    // optionals - latitude
                    if (!empty($aCartData['customer']['address']['latitude'])) {
                        $aAddress['latitude'] = $aCartData['customer']['address']['latitude'];
                    }
                    // transform it into object as required in the MC's API
                    if (!empty($aAddress)) {
                        $aParams['customer']['address'] = $aAddress;
                    }
                }
            }

            // optionals - campaign_id
            if (!empty($aCartData['campaign_id'])) {
                $aParams['campaign_id'] = $aCartData['campaign_id'];
            }
            // optionals - checkout_url
            if (!empty($aCartData['checkout_url'])) {
                $aParams['checkout_url'] = $aCartData['checkout_url'];
            }
            // optionals - tax_total
            if (!empty($aCartData['tax_total'])) {
                $aParams['tax_total'] = $aCartData['tax_total'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => customer or cart\'s lines are empty data', 'ecommerce-cart_class'), 1524);
        }

        return $this->app->call(
            self::API_CART_URL . '/' . $this->getId() . '/carts' . (!empty($iCartId) ? '/' . $iCartId : ''),
            $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete cart
     *
     * @param string $iCartId : cart ID
     * @return mixed : result of the API call
     */
    public function delete($iCartId)
    {
        return $this->app->call(self::API_CART_URL . '/' . $this->getId() . '/carts/' . $iCartId, null, \MCE\Chimp\Api::DELETE);
    }
}

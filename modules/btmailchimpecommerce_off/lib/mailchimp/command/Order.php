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

class Order extends BaseCommand
{
    /**
     * @const API_ORDER_URL
     */
    const API_ORDER_URL = 'ecommerce/stores/';

    /**
     * add a new order in the MC store
     *
     * @throws \Exception
     * @param int $iOrderId
     * @param array $aOrderData
     * @return mixed : result of the API call
     */
    public function add($iOrderId, array $aOrderData)
    {
        $aParams = array();

        // check the good information of customer and order's lines
        if (!empty($iOrderId)
            && is_array($aOrderData)
            && !empty($aOrderData['customer'])
            && !empty($aOrderData['lines'])
            && !empty($aOrderData['currency_code'])
            && isset($aOrderData['order_total'])
        ) {
            $aParams['id'] = (string)$iOrderId;
            // use case - the order amount and currency ISO
            $aParams['order_total'] = (float)$aOrderData['order_total'];
            $aParams['currency_code'] = $aOrderData['currency_code'];

            // test if we have the financial status and fulfillment status for order notifications
            if (!empty($aOrderData['financial_status'])) {
                $aParams['financial_status'] = $aOrderData['financial_status'];
            }
            if (!empty($aOrderData['fulfillment_status'])) {
                $aParams['fulfillment_status'] = $aOrderData['fulfillment_status'];
            }

            // check if the customer ID is well defined
            if (!empty($aOrderData['customer']['id'])) {
                $aCustomer['id'] = (string)$aOrderData['customer']['id'];

                // optionals values - email
                if (!empty($aOrderData['customer']['email']) || !empty($aOrderData['customer']['email_address'])) {
                    $aCustomer['email_address'] = !empty($aOrderData['customer']['email_address'])? $aOrderData['customer']['email_address'] : $aOrderData['customer']['email'];
                }
                // optionals values - opt-in
                if (isset($aOrderData['customer']['opt_in_status'])) {
                    $aCustomer['opt_in_status'] = (bool)$aOrderData['customer']['opt_in_status'];
                }
                // optionals values - company
                if (!empty($aOrderData['customer']['company'])) {
                    $aCustomer['company'] = $aOrderData['customer']['company'];
                }
                // optionals values - firstname
                if (!empty($aOrderData['customer']['first_name'])) {
                    $aCustomer['first_name'] = $aOrderData['customer']['first_name'];
                }
                // optionals values - lastname
                if (!empty($aOrderData['customer']['last_name'])) {
                    $aCustomer['last_name'] = $aOrderData['customer']['last_name'];
                }
                // optionals values - orders_count
                if (!empty($aOrderData['customer']['orders_count'])) {
                    $aCustomer['orders_count'] = $aOrderData['customer']['orders_count'];
                }
                // optionals values - total_spent
                if (!empty($aOrderData['customer']['total_spent'])) {
                    $aCustomer['total_spent'] = $aOrderData['customer']['total_spent'];
                }
                // set the customer object
                $aParams['customer'] = $aCustomer;

                // optionals - check address option
                if (!empty($aOrderData['customer']['address'])) {
                    $aAddress = array();

                    // optionals - address 1
                    if (!empty($aOrderData['customer']['address']['address1'])) {
                        $aAddress['address1'] = $aOrderData['customer']['address']['address1'];
                    }
                    // optionals - address 2
                    if (!empty($aOrderData['customer']['address']['address2'])) {
                        $aAddress['address2'] = $aOrderData['customer']['address']['address2'];
                    }
                    // optionals - city
                    if (!empty($aOrderData['customer']['address']['city'])) {
                        $aAddress['city'] = $aOrderData['customer']['address']['city'];
                    }
                    // optionals - province
                    if (!empty($aOrderData['customer']['address']['province'])) {
                        $aAddress['province'] = $aOrderData['customer']['address']['province'];
                    }
                    // optionals - province code
                    if (!empty($aOrderData['customer']['address']['province_code'])) {
                        $aAddress['province_code'] = $aOrderData['customer']['address']['province_code'];
                    }
                    // optionals - postal code
                    if (!empty($aOrderData['customer']['address']['postal_code'])) {
                        $aAddress['postal_code'] = $aOrderData['customer']['address']['postal_code'];
                    }
                    // optionals - country code
                    if (!empty($aOrderData['customer']['address']['country'])) {
                        $aAddress['country'] = $aOrderData['customer']['address']['country'];
                    }
                    // optionals - country code
                    if (!empty($aOrderData['customer']['address']['country_code'])) {
                        $aAddress['country_code'] = $aOrderData['customer']['address']['country_code'];
                    }
                    // optionals - longitude
                    if (!empty($aOrderData['customer']['address']['longitude'])) {
                        $aAddress['longitude'] = $aOrderData['customer']['address']['longitude'];
                    }
                    // optionals - latitude
                    if (!empty($aOrderData['customer']['address']['latitude'])) {
                        $aAddress['latitude'] = $aOrderData['customer']['address']['latitude'];
                    }
                    // transform it into object as required in the MC's API
                    if (!empty($aAddress)) {
                        $aParams['customer']['address'] = $aAddress;
                    }
                }
                // use case - order lines
                $aOrderLines = array();
                foreach ($aOrderData['lines'] as $aOrderLine) {
                    if (!empty($aOrderLine['id'])
                        && !empty($aOrderLine['product_id'])
                        && !empty($aOrderLine['product_variant_id'])
                        && !empty($aOrderLine['quantity'])
                        && isset($aOrderLine['price'])
                    ) {
                        $aOrderLines[] = array(
                            'id' => (string)$aOrderLine['id'],
                            'product_id' => (string)$aOrderLine['product_id'],
                            'product_variant_id' => (string)$aOrderLine['product_variant_id'],
                            'quantity' => (int)$aOrderLine['quantity'],
                            'price' => $aOrderLine['price'],
                        );
                    } else {
                        throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => order lines are not valid and do not include the required data, order ID:', 'ecommerce-order_class') . ' ' . $aOrderLine['id'], 1550);
                    }
                }
                $aParams['lines'] = $aOrderLines;

                // optionals - processed_at_foreign
                if (!empty($aOrderData['processed_at_foreign'])) {
                    $aParams['processed_at_foreign'] = (string)$aOrderData['processed_at_foreign'];
                }
                // optionals - shipping_address
                if (!empty($aOrderData['shipping_address'])) {
                    $aParams['shipping_address'] = $aOrderData['shipping_address'];
                }
                // optionals - billing_address
                if (!empty($aOrderData['billing_address'])) {
                    $aParams['billing_address'] = $aOrderData['billing_address'];
                }
                // optionals - campaign_id
                if (!empty($aOrderData['campaign_id'])) {
                    $aParams['campaign_id'] = $aOrderData['campaign_id'];
                }
                // optionals - tax_total
                if (!empty($aOrderData['tax_total'])) {
                    $aParams['tax_total'] = $aOrderData['tax_total'];
                }
                // optionals - discount_total
                if (!empty($aOrderData['discount_total'])) {
                    $aParams['discount_total'] = (float)$aOrderData['discount_total'];
                }
                // optionals - shipping_total
                if (!empty($aOrderData['shipping_total'])) {
                    $aParams['shipping_total'] = (float)$aOrderData['shipping_total'];
                }
                // optionals - tracking_code
                if (!empty($aOrderData['tracking_code'])) {
                    $aParams['tracking_code'] = $aOrderData['tracking_code'];
                }
                // optionals - campaign_id
                if (!empty($aOrderData['landing_site'])) {
                    $aParams['landing_site'] = $aOrderData['landing_site'];
                }

            } else {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the customer ID is not valid', 'ecommerce-order_class'), 1551);
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => customer or order\'s lines are empty data', 'ecommerce-order_class'), 1552);
        }

        return $this->app->call(self::API_ORDER_URL . '/' . $this->getId() . '/orders', $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get orders' information
     *
     * @param int $iOrderId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iOrderId = null,
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
            self::API_ORDER_URL . '/' . $this->getId() . '/orders' . (!empty($iOrderId) ? '/' . $iOrderId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update orders' information
     *
     * @throws \Exception
     * @param int $iOrderId
     * @param array $aOrderData
     * @return mixed : result of the API call
     */
    public function update($iOrderId, array $aOrderData)
    {
        $aParams = array();

        // check the good information of customer and order's lines
        if (!empty($iOrderId)
            && is_array($aOrderData)
        ) {
            // use case - the order amount and currency ISO
            if (!empty($aOrderData['order_total'])) {
                $aParams['order_total'] = (float)$aOrderData['order_total'];
            }
            if (!empty($aOrderData['currency_code'])) {
                $aParams['currency_code'] = $aOrderData['currency_code'];
            }
            // test if we have the financial status and fulfillment status for order notifications
            if (!empty($aOrderData['financial_status'])) {
                $aParams['financial_status'] = $aOrderData['financial_status'];
            }
            if (!empty($aOrderData['fulfillment_status'])) {
                $aParams['fulfillment_status'] = $aOrderData['fulfillment_status'];
            }

            // check if the customer ID is well defined
            if (!empty($aOrderData['customer']['id'])) {
                $aCustomer['id'] = (string)$aOrderData['customer']['id'];

                // optionals values - email
                if (!empty($aOrderData['customer']['email'])) {
                    $aCustomer['email_address'] = $aOrderData['customer']['email'];
                }
                // optionals values - opt-in
                if (!empty($aOrderData['customer']['opt_in_status'])) {
                    $aCustomer['opt_in_status'] = (bool)$aOrderData['customer']['opt_in_status'];
                }
                // optionals values - company
                if (!empty($aOrderData['customer']['company'])) {
                    $aCustomer['company'] = $aOrderData['customer']['company'];
                }
                // optionals values - firstname
                if (!empty($aOrderData['customer']['first_name'])) {
                    $aCustomer['first_name'] = $aOrderData['customer']['first_name'];
                }
                // optionals values - lastname
                if (!empty($aOrderData['customer']['last_name'])) {
                    $aCustomer['last_name'] = $aOrderData['customer']['last_name'];
                }
                // optionals values - orders_count
                if (!empty($aOrderData['customer']['orders_count'])) {
                    $aCustomer['orders_count'] = $aOrderData['customer']['orders_count'];
                }
                // optionals values - total_spent
                if (!empty($aOrderData['customer']['total_spent'])) {
                    $aCustomer['total_spent'] = $aOrderData['customer']['total_spent'];
                }
                // set the customer object
                $aParams['customer'] = $aCustomer;

                // optionals - check address option
                if (!empty($aOrderData['customer']['address'])) {
                    $aAddress = array();

                    // optionals - address 1
                    if (!empty($aOrderData['customer']['address']['address1'])) {
                        $aAddress['address1'] = $aOrderData['customer']['address']['address1'];
                    }
                    // optionals - address 2
                    if (!empty($aOrderData['customer']['address']['address2'])) {
                        $aAddress['address2'] = $aOrderData['customer']['address']['address2'];
                    }
                    // optionals - city
                    if (!empty($aOrderData['customer']['address']['city'])) {
                        $aAddress['city'] = $aOrderData['customer']['address']['city'];
                    }
                    // optionals - province
                    if (!empty($aOrderData['customer']['address']['province'])) {
                        $aAddress['province'] = $aOrderData['customer']['address']['province'];
                    }
                    // optionals - province code
                    if (!empty($aOrderData['customer']['address']['province_code'])) {
                        $aAddress['province_code'] = $aOrderData['customer']['address']['province_code'];
                    }
                    // optionals - postal code
                    if (!empty($aOrderData['customer']['address']['postal_code'])) {
                        $aAddress['postal_code'] = $aOrderData['customer']['address']['postal_code'];
                    }
                    // optionals - country code
                    if (!empty($aOrderData['customer']['address']['country'])) {
                        $aAddress['country'] = $aOrderData['customer']['address']['country'];
                    }
                    // optionals - country code
                    if (!empty($aOrderData['customer']['address']['country_code'])) {
                        $aAddress['country_code'] = $aOrderData['customer']['address']['country_code'];
                    }
                    // optionals - longitude
                    if (!empty($aOrderData['customer']['address']['longitude'])) {
                        $aAddress['longitude'] = $aOrderData['customer']['address']['longitude'];
                    }
                    // optionals - latitude
                    if (!empty($aOrderData['customer']['address']['latitude'])) {
                        $aAddress['latitude'] = $aOrderData['customer']['address']['latitude'];
                    }
                    // transform it into object as required in the MC's API
                    if (!empty($aAddress)) {
                        $aParams['customer']['address'] = $aAddress;
                    }
                }
            }
            // use case - order lines
            if (!empty($aOrderData['lines'])) {
                $aOrderLines = array();
                foreach ($aOrderData['lines'] as $aOrderLine) {
                    if (!empty($aOrderLine['id'])
                        && !empty($aOrderLine['product_id'])
                        && !empty($aOrderLine['product_variant_id'])
                        && !empty($aOrderLine['quantity'])
                        && isset($aOrderLine['price'])
                    ) {
                        $aOrderLines[] = array(
                            'id' => (string)$aOrderLine['id'],
                            'product_id' => (string)$aOrderLine['product_id'],
                            'product_variant_id' => (string)$aOrderLine['product_variant_id'],
                            'quantity' => (int)$aOrderLine['quantity'],
                            'price' => $aOrderLine['price'],
                        );
                    } else {
                        throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => order lines are not valid and do not include the required data, order ID:', 'ecommerce-order_class') . ' ' . $aOrderLine['id'], 1553);
                    }
                }
                $aParams['lines'] = $aOrderLines;
            }

            // optionals - processed_at_foreign
            if (!empty($aOrderData['processed_at_foreign'])) {
                $aParams['processed_at_foreign'] = (string)$aOrderData['processed_at_foreign'];
            }
            // optionals - shipping_address
            if (!empty($aOrderData['shipping_address'])) {
                $aParams['shipping_address'] = $aOrderData['shipping_address'];
            }
            // optionals - billing_address
            if (!empty($aOrderData['billing_address'])) {
                $aParams['billing_address'] = $aOrderData['billing_address'];
            }
            // optionals - campaign_id
            if (!empty($aOrderData['campaign_id'])) {
                $aParams['campaign_id'] = $aOrderData['campaign_id'];
            }
            // optionals - tax_total
            if (!empty($aOrderData['tax_total'])) {
                $aParams['tax_total'] = $aOrderData['tax_total'];
            }
            // optionals - discount_total
            if (!empty($aOrderData['discount_total'])) {
                $aParams['discount_total'] = (float)$aOrderData['discount_total'];
            }
            // optionals - shipping_total
            if (!empty($aOrderData['shipping_total'])) {
                $aParams['shipping_total'] = (float)$aOrderData['shipping_total'];
            }
            // optionals - tracking_code
            if (!empty($aOrderData['tracking_code'])) {
                $aParams['tracking_code'] = $aOrderData['tracking_code'];
            }
            // optionals - campaign_id
            if (!empty($aOrderData['landing_site'])) {
                $aParams['landing_site'] = $aOrderData['landing_site'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => customer or order\'s lines are empty data', 'ecommerce-order_class'), 1554);
        }

        return $this->app->call(
            self::API_ORDER_URL . '/' . $this->getId() . '/orders' . (!empty($iOrderId) ? '/' . $iOrderId : ''),
            $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete order
     *
     * @param string $iOrderId : order ID
     * @return mixed : result of the API call
     */
    public function delete($iOrderId)
    {
        return $this->app->call(self::API_ORDER_URL . '/' . $this->getId() . '/orders/' . $iOrderId, null, \MCE\Chimp\Api::DELETE);
    }
}

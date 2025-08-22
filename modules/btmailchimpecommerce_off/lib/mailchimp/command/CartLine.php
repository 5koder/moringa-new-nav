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

class CartLine extends BaseCommand
{
    /**
     * @const API_CART_LINE_URL
     */
    const API_CART_LINE_URL = 'ecommerce/stores/';

    /**
     * add a new cart line in the MC store
     *
     * @throws Exception
     * @param int $iCartId
     * @param array $aCartLine
     * @return mixed : result of the API call
     */
    public function add($iCartId, array $aCartLine)
    {
        $aParams = array();

        // check the good information of customer and cart's lines
        if (!empty($iCartId)
            && is_array($aCartLine)
            && isset($aCartLine['id'])
            && isset($aCartLine['product_id'])
            && isset($aCartLine['product_variant_id'])
            && isset($aCartLine['quantity'])
            && isset($aCartLine['price'])
        ) {
            $aParams = array(
                'id' => (string)$aCartLine['id'],
                'product_id' => (string)$aCartLine['product_id'],
                'product_variant_id' => (string)$aCartLine['product_variant_id'],
                'quantity' => (int)$aCartLine['quantity'],
                'price' => $aCartLine['price'],
            );
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => cart lines are not valid and do not include the required data, cart ID:', 'ecommerce-cart-line_class') . ' ' . $iCartId, 1510);
        }

        return $this->app->call(
            self::API_CART_URL . '/' . $this->getId() . '/carts/' . $iCartId . '/lines', $aParams,
            \MCE\Chimp\Api::POST
        );
    }


    /**
     * get carts' information
     *
     * @param int $iCartId
     * @param int $iCartLineId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iCartId,
        $iCartLineId = null,
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
            self::API_CART_LINE_URL . '/' . $this->getId() . '/carts/' . $iCartId . '/lines' . (!empty($iCartLineId) ? '/' . $iCartLineId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update carts' information
     *
     * @throws Exception
     * @param int $iCartId
     * @param int $iCartLineId
     * @param array $aCartLine
     * @return mixed : result of the API call
     */
    public function update($iCartId, $iCartLineId, array $aCartLine)
    {
        $aParams = array();

        // check the good information of cart's lines
        if (!empty($iCartId)
            && is_array($aCartLine)
        ) {
            if (isset($aCartLine['product_id'])) {
                $aParams['product_id'] = (string)$aCartLine['product_id'];
            }
            if (isset($aCartLine['product_variant_id'])) {
                $aParams['product_variant_id'] = (string)$aCartLine['product_variant_id'];
            }
            if (isset($aCartLine['quantity'])) {
                $aParams['quantity'] = (int)$aCartLine['quantity'];
            }
            if (isset($aCartLine['price'])) {
                $aParams['price'] = $aCartLine['price'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => customer or cart\'s lines are empty data', 'ecommerce-cart-line_class'), 1511);
        }

        return $this->app->call(
            self::API_CART_LINE_URL . '/' . $this->getId() . '/carts/' . $iCartId . '/lines/' . $iCartLineId,
            $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete cart
     *
     * @param string $iCartId : cart ID
     * @param string $iCartLineId : cart line ID
     * @return mixed : result of the API call
     */
    public function delete($iCartId, $iCartLineId)
    {
        return $this->app->call(
            self::API_CART_LINE_URL . '/' . $this->getId() . '/carts/' . $iCartId . '/lines/' . $iCartLineId,
            null, \MCE\Chimp\Api::DELETE
        );
    }
}

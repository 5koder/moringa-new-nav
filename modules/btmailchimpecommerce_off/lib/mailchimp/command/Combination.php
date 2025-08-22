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

class Combination extends BaseCommand
{
    /**
     * @const API_VARIANT_URL
     */
    const API_VARIANT_URL = 'ecommerce/stores/';

    /**
     * add / update a new product in the MC account
     *
     * @param string $iProdId
     * @param string $iVariantId
     * @param string $sTitle
     * @param bool $bOptinStatus
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add($iProdId, $iVariantId, $sTitle, array $aOpts = array())
    {
        // required values
        $aParams = array(
            'id' => (string)$iVariantId,
            'title' => $sTitle,
        );

        // optional product's variant data
        if (isset($aOpts['url'])) {
            $aParams['url'] = $aOpts['url'];
        }
        // optional product's variant data
        if (isset($aOpts['sku'])) {
            $aParams['sku'] = $aOpts['sku'];
        }
        // optional product's variant data
        if (isset($aOpts['price'])) {
            $aParams['price'] = $aOpts['price'];
        }
        // optional product's variant data
        if (isset($aOpts['inventory_quantity'])) {
            $aParams['inventory_quantity'] = (int)$aOpts['inventory_quantity'];
        }
        // optional product's variant data
        if (isset($aOpts['image_url'])) {
            $aParams['image_url'] = $aOpts['image_url'];
        }
        // optional product's variant data
        if (isset($aOpts['backorders'])) {
            $aParams['backorders'] = $aOpts['backorders'];
        }
        // optional product's variant data
        if (isset($aOpts['visibility'])) {
            $aParams['visibility'] = $aOpts['visibility'];
        }

        return $this->app->call(
            self::API_VARIANT_URL . '/' . $this->getId() . '/products/' . $iProdId . '/variants/' . $iVariantId,
            $aParams, \MCE\Chimp\Api::PUT
        );
    }


    /**
     * get product variant' information
     *
     * @param int $iProdId
     * @param int $iVariantId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iProdId,
        $iVariantId = null,
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
            self::API_VARIANT_URL . '/' . $this->getId() . '/products/' . $iProdId . '/variants' . (!empty($iVariantId) ? '/' . $iVariantId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * delete product's variant
     *
     * @param string $iProdId : product ID
     * @param string $iVariantId : variant ID
     * @return mixed : result of the API call
     */
    public function delete($iProdId, $iVariantId)
    {
        return $this->app->call(
            self::API_VARIANT_URL . '/' . $this->getId() . '/products/' . $iProdId . '/variants/' . $iVariantId,
            null, \MCE\Chimp\Api::DELETE
        );
    }
}

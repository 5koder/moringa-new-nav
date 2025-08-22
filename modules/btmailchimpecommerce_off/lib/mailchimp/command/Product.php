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

class Product extends BaseCommand
{
    /**
     * @const API_PRODUCT_URL
     */
    const API_PRODUCT_URL = 'ecommerce/stores/';

    /**
     * add a new product in the MC account
     *
     * @throws Exception
     * @param string $iProdId
     * @param string $sTitle
     * @param array $aVariants
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add($iProdId, $sTitle, $aVariants, array $aOpts = array())
    {
        // required values
        $aParams = array(
            'id' => (string)$iProdId,
            'title' => $sTitle,
        );

        // required - check product's variants
        if (!empty($aVariants)
            && is_array($aVariants)
            && isset($aVariants[0])
        ) {
            // use case - product's variants
            $aProducts = array();
            $aProduct = array();
            foreach ($aVariants as $aVariant) {
                if (!empty($aVariant['id'])
                    && !empty($aVariant['title'])
                ) {
                    $aProduct['id'] = (string)$aVariant['id'];
                    $aProduct['title'] = (string)$aVariant['title'];

                    // optional product's variant data
                    if (isset($aVariant['url'])) {
                        $aProduct['url'] = $aVariant['url'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['sku'])) {
                        $aProduct['sku'] = $aVariant['sku'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['price'])) {
                        $aProduct['price'] = $aVariant['price'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['inventory_quantity'])) {
                        $aProduct['inventory_quantity'] = (int)$aVariant['inventory_quantity'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['image_url'])) {
                        $aProduct['image_url'] = $aVariant['image_url'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['backorders'])) {
                        $aProduct['backorders'] = $aVariant['backorders'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['visibility'])) {
                        $aProduct['visibility'] = $aVariant['visibility'];
                    }
                    $aProducts[] = $aProduct;
                } else {
                    throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => variant lines are not valid and do not include the required data, product variant ID:', 'ecommerce-product_class') . ' ' . $aVariant['id'], '',1530);
                }
            }
            $aParams['variants'] = $aProducts;
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => product\'s variants are empty', 'ecommerce-product_class'), '',1531);
        }

        // optionals values - handle
        if (isset($aOpts['handle'])) {
            $aParams['handle'] = $aOpts['handle'];
        }
        // optionals values - url
        if (isset($aOpts['url'])) {
            $aParams['url'] = $aOpts['url'];
        }
        // optionals values - description
        if (isset($aOpts['description'])) {
            $aParams['description'] = $aOpts['description'];
        }
        // optionals values - type
        if (isset($aOpts['type'])) {
            $aParams['type'] = $aOpts['type'];
        }
        // optionals values - vendor
        if (isset($aOpts['vendor'])) {
            $aParams['vendor'] = $aOpts['vendor'];
        }
        // optionals values - image_url
        if (isset($aOpts['image_url'])) {
            $aParams['image_url'] = $aOpts['image_url'];
        }
        // optionals values - published_at_foreign
        if (isset($aOpts['published_at_foreign'])) {
            $aParams['published_at_foreign'] = $aOpts['published_at_foreign'];
        }

        return $this->app->call(self::API_PRODUCT_URL . '/' . $this->getId() . '/products', $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get product' information
     *
     * @param int $iProdId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iProdId = null,
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
            self::API_PRODUCT_URL . '/' . $this->getId() . '/products' . (!empty($iProdId) ? '/' . $iProdId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update product's information
     *
     * @throws Exception
     * @param int $iProdId
     * @param string $sTitle
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update($iProdId, $sTitle, array $aOpts = array())
    {
        // required values
        $aParams = array(
            'title' => $sTitle,
        );

        // required - check product's variants
        if (!empty($aOpts['variants'])
            && is_array($aOpts['variants'])
            && isset($aOpts['variants'][0])
        ) {
            // use case - product's variants
            $aProducts = array();
            $aProduct = array();
            foreach ($aOpts['variants'] as $aVariant) {
                if (!empty($aVariant['id'])
                    && !empty($aVariant['title'])
                ) {
                    $aProduct['id'] = (string)$aVariant['id'];
                    $aProduct['title'] = (string)$aVariant['title'];

                    // optional product's variant data
                    if (isset($aVariant['url'])) {
                        $aProduct['url'] = $aVariant['url'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['sku'])) {
                        $aProduct['sku'] = $aVariant['sku'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['price'])) {
                        $aProduct['price'] = $aVariant['price'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['inventory_quantity'])) {
                        $aProduct['inventory_quantity'] = (int)$aVariant['inventory_quantity'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['image_url'])) {
                        $aProduct['image_url'] = $aVariant['image_url'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['backorders'])) {
                        $aProduct['backorders'] = $aVariant['backorders'];
                    }
                    // optional product's variant data
                    if (isset($aVariant['visibility'])) {
                        $aProduct['visibility'] = $aVariant['visibility'];
                    }
                    $aProducts[] = $aProduct;
                } else {
                    throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => variant lines are not valid and do not include the required data, product variant ID:', 'ecommerce-product_class') . ' ' . $aVariant['id'], '',1532);
                }
            }
            $aParams['variants'] = $aProducts;
        }

        // optionals values - handle
        if (isset($aOpts['handle'])) {
            $aParams['handle'] = $aOpts['handle'];
        }
        // optionals values - url
        if (isset($aOpts['url'])) {
            $aParams['url'] = $aOpts['url'];
        }
        // optionals values - description
        if (isset($aOpts['description'])) {
            $aParams['description'] = $aOpts['description'];
        }
        // optionals values - type
        if (isset($aOpts['type'])) {
            $aParams['type'] = $aOpts['type'];
        }
        // optionals values - vendor
        if (isset($aOpts['vendor'])) {
            $aParams['vendor'] = $aOpts['vendor'];
        }
        // optionals values - image_url
        if (isset($aOpts['image_url'])) {
            $aParams['image_url'] = $aOpts['image_url'];
        }
        // optionals values - published_at_foreign
        if (isset($aOpts['published_at_foreign'])) {
            $aParams['published_at_foreign'] = $aOpts['published_at_foreign'];
        }

        return $this->app->call(
            self::API_PRODUCT_URL . '/' . $this->getId() . '/products/' . $iProdId, $aParams,
            \MCE\Chimp\Api::PATCH
        );
    }

    /**
     * delete product
     *
     * @param string $iProdId : product ID
     * @return mixed : result of the API call
     */
    public function delete($iProdId)
    {
        return $this->app->call(self::API_PRODUCT_URL . '/' . $this->getId() . '/products/' . $iProdId, null,
            \MCE\Chimp\Api::DELETE
        );
    }
}

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

use \MCE\Chimp\Format\Customer;

class Cart extends Formatter
{
    /**
     * @var null
     */
    public $cart = null;

    /**
     * @var int
     */
    public $currency_id_mc = null;

    /**
     * @var int
     */
    public $customer_id = 0;

    /**
     * @var int
     */
    public $currency_id = null;

    /**
     * @var string
     */
    public $campaign_id = '';

    /**
     * @var float
     */
    public $total_order = 0.00;

    /**
     * @var float
     */
    public $total_tax = 0.00;

    /**
     * @var int
     */
    public $limit = 0;


    /**
     * Cart constructor.
     * @param int $iCartId
     * @param int $iLangId
     * @param int $iMcCurrencyId
     * @param int $iCustomerId
     * @param bool $bNested
     * @param int $iProductLimit
     */
    public function __construct($iCartId, $iLangId, $iMcCurrencyId, $iCustomerId, $bNested = false, $iProductLimit = 0)
    {
        $this->cart = new \Cart($iCartId);
        $this->lang_id = $iLangId;
        $this->currency_id_mc = $iMcCurrencyId;
        $this->currency_id = $this->cart->id_currency;
        $this->customer_id = $iCustomerId;
        $this->nested = $bNested;
        $this->limit = $iProductLimit;

        require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');
    }


    /**
     * Format cart data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->cart)) {
            // get cart products
            $aCartProducts = $this->cart->getProducts(true);

            if (!empty($aCartProducts)) {
                $this->data = array(
                    'id' => (string)$this->cart->id,
                    'customer' => (new \MCE\Chimp\Format\Customer($this->customer_id, $this->lang_id, $this->currency_id_mc))->format(),
                    'currency_code' => \Tools::strtoupper($this->getCurrencyCode()),
                    'order_total' => $this->getOrderTotal(),
                );

                // define the option values
                $this->options = array(
                    'checkout_url' => $this->getCheckoutUrl(),
                );

                // get the campaign ID and landing site
                if (!empty($this->campaign_id)) {
                    $this->options['campaign_id'] = $this->campaign_id;
                }

                // use case - if $bDirectFormat == true, we have to format the final array like the MailChimp facade expects it, so to do nested arrays
                $this->isNested();

                // manage product lines of order
                $this->data['lines'] = array();

                // check the products limitation for the cart's product number
                if ($this->limit != 0 && count($aCartProducts) > $this->limit) {
                    // reverse the product list
                    $aCartProducts = array_reverse($aCartProducts);

                    // slice to increase server performance
                    $aCartProducts = array_slice($aCartProducts, 0, $this->limit);
                }

                // loop on each product to prepare them to be exported in MC
                foreach ($aCartProducts as $iIndex => $aProduct) {
                    $this->data['lines'][] = array(
                        'id' => (string)($iIndex+1),
                        'product_id' => parent::setProductID($aProduct['id_product'], $this->lang_id),
                        'product_variant_id' => parent::setProductID($aProduct['id_product'], $this->lang_id, !empty($aProduct['id_product_attribute']) ? $aProduct['id_product_attribute'] : '1'),
                        'quantity' => ((int)$aProduct['quantity']),
                        'price' => (float)number_format(\MCE\Tools::round(\MCE\Tools::convertAmount($aProduct['total_wt'], $this->currency_id, $this->currency_id_mc)), 2, '.', ''),
                    );
                }
            }
        }

        return $this->data;
    }


    /**
     * define the order currency code
     * @return string
     */
    private function getCurrencyCode()
    {
        return \MCE\Tools::getCurrency('iso_code', $this->currency_id_mc);
    }

    /**
     * define the order total
     * @return float
     */
    private function getOrderTotal()
    {
        $this->total_order = (float)number_format(\MCE\Tools::round(\MCE\Tools::convertAmount($this->cart->getOrderTotal(), $this->currency_id, $this->currency_id_mc)), 2, '.', '');
        return $this->total_order;
    }


    /**
     * get the campaign ID passed by MC
     * @return float
     */
    private function getCampaignId()
    {
        $cookie_value = \MCE\Cookie::get(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'mc_cid');

        if (!empty($cookie_value)) {
            $this->campaign_id = $cookie_value;
        }
    }

    /**
     * get the checkout URL formatted by the module to manage the abandoned cart automation
     * @return string
     */
    private function getCheckoutUrl()
    {
        // get the module index.php with path_info to handle the redirect on checkout URL
        $sModuleLink = \Context::getContext()->shop->getBaseURL() . 'modules/' . _MCE_MODULE_SET_NAME . '/index.php';
        $sModuleLink .= '/' . \Context::getContext()->customer->id . '/' . $this->cart->id . '/' . \MCE\Tools::setSecureKey(_MCE_SECURE_HASH, $this->customer_id, $this->cart->id) . '/';

        return $sModuleLink;
    }
}

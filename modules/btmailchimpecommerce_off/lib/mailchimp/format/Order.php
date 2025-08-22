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
use \MCE\Chimp\Format\Address;

class Order extends Formatter
{
    /**
     * @var null
     */
    public $order = null;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var int
     */
    public $currency_id_mc = null;

    /**
     * @var int
     */
    public $currency_id = null;

    /**
     * @var string
     */
    public $campaign_id = '';

    /**
     * @var string
     */
    public $landing_site = '';

    /**
     * @var string
     */
    public $financial_status = '';

    /**
     * @var string
     */
    public $fulfillment_status = '';

    /**
     * @var float
     */
    public $total_order = 0.00;

    /**
     * @var float
     */
    public $total_tax = 0.00;

    /**
     * @var float
     */
    public $total_discount = 0.00;

    /**
     * @var float
     */
    public $total_shipping = 0.00;


    /**
     * Order constructor.
     * @param $mOrder
     * @param $iLangId
     * @param $iMcCurrencyId
     * @param $bNested
     * @param $aParams
     */
    public function __construct($mOrder, $iLangId, $iMcCurrencyId, $bNested = false, array $aParams = array())
    {
        $this->lang_id = $iLangId;
        $this->order = $this->getOrder($mOrder);
        $this->currency_id_mc = $iMcCurrencyId;
        $this->params = $aParams;
        $this->nested = $bNested;

        // define if we get a currency as param or get the order currency id
        $this->setCurrency();

        require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');
    }


    /**
     * Format order data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->order)) {
            // get order products
            $aOrderProducts = $this->order->getProducts();

            if (!empty($aOrderProducts)) {
                // set the order status
                $this->setOrderStatus();

                // get the campaign ID
                $this->getCampaignId();

                // get the landing site
                $this->getLandingSite();

                $this->data = array(
                    'id' => (string)$this->order->id,
                    'customer' => (new \MCE\Chimp\Format\Customer($this->order->id_customer, $this->lang_id, $this->currency_id_mc))->format(),
                    'currency_code' => \Tools::strtoupper($this->getCurrencyCode()),
                    'order_total' => $this->getOrderTotal(),
                    'cart_id' => $this->order->id_cart,
                    'tax_total' => $this->getTotaltax(),
                    'processed_at_foreign' => $this->order->date_add,
                    'discount_total' => $this->getTotalDiscount(),
                    'shipping_total' => $this->getTotalShipping(),
                    'shipping_address' => (new \MCE\Chimp\Format\Address($this->order->id_address_delivery, $this->lang_id))->format(),
                    'billing_address' => (new \MCE\Chimp\Format\Address($this->order->id_address_invoice, $this->lang_id))->format(),
                );

                // check the order status
                if (!empty($this->financial_status)) {
                    $this->data['financial_status'] = $this->financial_status;
                }
                // check the fulfillment order status
                if (!empty($this->fulfillment_status)) {
                    $this->data['fulfillment_status'] = $this->fulfillment_status;
                }
                // get the campaign ID and landing site
                if (!empty($this->campaign_id)) {
                    $this->data['campaign_id'] = $this->campaign_id;
                }
                if (!empty($this->landing_site)) {
                    $this->data['landing_site'] = $this->landing_site;
                }

                // manage product lines of order
                $this->data['lines'] = array();

                // loop on each product to prepare them to be exported in MC
                foreach ($aOrderProducts as $iIndex => $aProduct) {
                    $this->data['lines'][] = array(
                        'id' => (string)$iIndex,
                        'product_id' => parent::setProductID($aProduct['product_id'], $this->lang_id),
                        'product_variant_id' => parent::setProductID($aProduct['product_id'], $this->lang_id, !empty($aProduct['product_attribute_id']) ? $aProduct['product_attribute_id'] : '1'),
                        'quantity' => (int)$aProduct['product_quantity'],
                        'price' => (float)number_format(\MCE\Tools::round(\MCE\Tools::convertAmount($aProduct['total_wt'], $this->currency_id, $this->currency_id_mc)), 2, '.', ''),
                    );
                }
            }
        }

        return $this->data;
    }

    /**
     * Get a real order object
     *
     * @param $mOrder
     * @return Order
     */
    private function getOrder($mOrder)
    {
        return (is_object($mOrder) ? $mOrder : new \Order($mOrder, $this->lang_id));
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
     * define the order status
     * @return float
     */
    private function setOrderStatus()
    {
        $sOrderStatus = '';

        if (!empty($this->params['bForceOrderStatus'])) {
            if (in_array($this->order->module, $GLOBALS['MCE_ORDER_VALIDATE_PENDING_MODULES'])) {
                $sOrderStatus = 'pending';
            } else {
                $sOrderStatus = 'payment';
            }
        } elseif (!empty($this->params['sForceOrderTemplate'])) {
            $sOrderStatus = $this->params['sForceOrderTemplate'];
        } else {
            $aOrderStatus = \MCE\Dao::getOrderStateLang($this->order->current_state, $this->lang_id);
            $sOrderStatus = $aOrderStatus['template'];
        }
        // set the financial and fulfillment statuses
        if (!empty($sOrderStatus)) {
            $this->financial_status = $this->mapOrderStatus($sOrderStatus);

            if ($this->financial_status == 'shipped') {
                $this->fulfillment_status = 'shipped';
            }
        }
    }


    /**
     * define the order total
     * @return float
     */
    private function getOrderTotal()
    {
        $this->order->total_paid_real = (float)$this->order->total_paid_real;
        $this->order->total_paid = (float)$this->order->total_paid;

        // detect the total paid according to the status related to the payment because the total_paid_real is assigned only if the order is considered as valid in the linked payment status
        $this->total_order = !empty($this->order->total_paid_real) ? $this->order->total_paid_real : $this->order->total_paid;
        $this->total_order = number_format(\MCE\Tools::round(\MCE\Tools::convertAmount($this->total_order, $this->currency_id, $this->currency_id_mc)), 2, '.', '');

        return $this->total_order;
    }


    /**
     * define the total tax
     * @return float
     */
    private function getTotaltax()
    {
        $this->total_tax = (isset($this->order->total_paid_tax_incl) && $this->order->total_paid_tax_excl) ? $this->order->total_paid_tax_incl - $this->order->total_paid_tax_excl : 0.00;
        $this->total_tax = (!empty($this->total_tax) ? (float)number_format(\MCE\Tools::round(\MCE\Tools::convertAmount($this->total_tax, $this->currency_id, $this->currency_id_mc)), 2, '.', '') : 0.00);

        return $this->total_tax;
    }

    /**
     * define the total discount
     * @return float
     */
    private function getTotalDiscount()
    {
        if (
            isset($this->order->total_discounts_tax_incl)
            && !empty($this->order->total_discounts_tax_incl)
        ) {
            $this->total_discount = (float)$this->order->total_discounts_tax_incl;
        }

        return $this->total_discount;
    }


    /**
     * define the total shipping
     * @return float
     */
    private function getTotalShipping()
    {
        if (
            isset($this->order->total_shipping_tax_incl)
            && !empty($this->order->total_shipping_tax_incl)
        ) {
            $this->total_shipping = (float)$this->order->total_shipping_tax_incl;
        }

        return $this->total_shipping;
    }

    /**
     * define the current currency ID
     * @return float
     */
    private function setCurrency()
    {
        $this->currency_id = !empty($this->params['iCurrentCurrencyId']) ? $this->params['iCurrentCurrencyId'] : $this->order->id_currency;
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
     * get the landing site passed by MC
     * @return float
     */
    private function getLandingSite()
    {
        $cookie_value = \MCE\Cookie::get(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'landing_site');

        if (!empty($cookie_value)) {
            $this->landing_site = $cookie_value;
        }
    }

    /**
     * Map the order statuses
     *
     * @param string $sOrderStatus
     * @return string
     */
    private function mapOrderStatus($sOrderStatus)
    {
        $sStatus = '';

        switch ($sOrderStatus) {
            case 'pending':
            case 'bankwire':
            case 'cheque':
                $sStatus = 'pending';
                break;
            case 'payment':
                $sStatus = 'paid';
                break;
            case 'order_canceled':
                $sStatus = 'cancelled';
                break;
            case 'refund':
                $sStatus = 'refunded';
                break;
            case 'shipped':
                $sStatus = 'shipped';
                break;
            default:
                $sStatus = '';
                break;
        }

        return $sStatus;
    }
}

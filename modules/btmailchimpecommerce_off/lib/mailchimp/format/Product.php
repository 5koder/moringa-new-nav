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

use BTMailchimpEcommerce;
use \MCE\Chimp\Format\Combination;

class Product extends Formatter
{
    /**
     * @var null
     */
    public $product = null;

    /**
     * @var bool
     */
    public $cat_label = false;

    /**
     * @var string
     */
    public $vendor = '';

    /**
     * @var string
     */
    public $vendor_type = '';

    /**
     * @var string
     */
    public $image = '';

    /**
     * @var int
     */
    public $desc_type = 0;

    /**
     * @var string
     */
    public $desc = '';

    /**
     * @var string
     */
    public $force_url = '';

    /**
     * Product constructor.
     * @param $mProductId
     * @param $iLangId
     * @param $bLabelCatOnly
     * @param $sVendorType
     * @param $sImageSize
     * @param int $iDescType
     * @param string $sForceImgUrl
     * @param bool $bNested
     */
    public function __construct($mProductId, $iLangId, $bLabelCatOnly, $sVendorType, $sImageSize, $iDescType = 0, $sForceImgUrl = '', $bNested = false)
    {
        $this->lang_id = $iLangId;
        $this->product = $this->getProduct($mProductId);
        $this->cat_label = $bLabelCatOnly;
        $this->vendor_type = $sVendorType;
        $this->image = $sImageSize;
        $this->desc_type = $iDescType;
        $this->force_url = $sForceImgUrl;
        $this->nested = $bNested;
    }


    /**
     * Format product data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->product)) {
            $this->data = array(
                'id' => $this->getId(),
                'title' => self::sanitizeLanguageField($this->product->name, $this->lang_id),
            );

            // set the vendor
            $this->setVendor();

            // set description
            $this->setDescription();

            // set the option values
            $this->options = array(
                'url' => \Context::getContext()->link->getProductLink($this->product->id, null, null, null, $this->lang_id),
                'image_url' => ($this->force_url != '' ? $this->force_url : \MCE\Tools::getProductImage($this->product, $this->image)),
                'type' => \MCE\Tools::getProductPath($this->product->id_category_default, $this->lang_id, $this->cat_label, '', '>', false),
            );

            // get vendor and description
            if (!empty($this->vendor)) {
                $this->options['vendor'] = (string)$this->vendor;
            }

            if (!empty($this->desc)) {
                $this->options['description'] = (string)$this->desc;
            }

            // detect if combination
            if ($this->hasCombinations()) {
                $combinations = $this->getCombinationIds();
                foreach ($combinations as $ids) {
                    $this->options['variants'][] = (new \MCE\Chimp\Format\Combination($this->product, $ids['id_product_attribute'], $this->lang_id, $this->image))->format();
                }
            } else {
                $this->options['variants'][] = (new \MCE\Chimp\Format\Combination($this->product, 0, $this->lang_id, $this->image, true))->format();
            }

            // use case - if $bDirectFormat == true, we have to format the final array like the MailChimp facade expects it, so to do nested arrays
            $this->isNested();
        }

        return $this->data;
    }


    /**
     * Format the product Id as we want to use many languages for all the products
     *
     * @return string
     */
    private function getId()
    {
        return (string)parent::setProductID($this->product->id, $this->lang_id);
    }

    /**
     * Set the vendor value according to the configuration
     */
    private function setVendor()
    {
        if ($this->vendor_type == 'brand') {
            $oManufacturer = new \Manufacturer($this->product->id_manufacturer, $this->lang_id);
            if (!empty($oManufacturer->name)) {
                $this->vendor = $oManufacturer->name;
            } else {
                $oSupplier = new \Supplier($this->product->id_supplier, $this->lang_id);
                $this->vendor = $oSupplier->name;
            }
        } elseif($this->vendor_type == 'supplier') {
            $oSupplier = new \Supplier($this->product->id_supplier, $this->lang_id);
            if (!empty($oSupplier->name)) {
                $this->vendor = $oSupplier->name;
            } else {
                $oManufacturer = new \Manufacturer($this->product->id_manufacturer, $this->lang_id);
                $this->vendor = $oManufacturer->name;
            }
        } else {
            $oCat = new \Category($this->product->id_category_default, \BTMailchimpEcommerce::$iCurrentLang);
            $this->vendor = (string)$oCat->name;
        }
    }

    /**
     * Set the description value according to the configuration
     */
    private function setDescription()
    {
        // set product description
        switch ($this->desc_type) {
            case 0:
                $this->desc = '';
                break;
            case 1:
                $this->desc = $this->product->description_short;
                break;
            case 2:
                $this->desc = $this->product->description;
                break;
            case 3:
                $this->desc = $this->product->description_short . '<br />' . $this->product->description;
                break;
            case 4:
                $this->desc = $this->product->meta_description;
                break;
            default:
                $this->desc = (!empty($this->product->description_short) ? $this->product->description_short : (!empty($this->product->description) ? $this->product->description : $this->product->meta_description));
                break;
        }
    }


    /**
     * detect if the product has combinations
     *
     * @return bool
     */
    private function hasCombinations()
    {
        if (!empty(\BTMailchimpEcommerce::$bCompare1610)) {
            $sQuery = 'SELECT count(*) as total'
                . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas'
                . ' WHERE pas.`id_product` = ' . (int)$this->product->id . ' AND pas.id_shop = ' . (int)\BTMailchimpEcommerce::$iShopId;
        } else {
            $sQuery = 'SELECT count(*) as total'
                . ' FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . ' WHERE pa.`id_product` = ' . (int)$this->product->id;
        }

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aResult[0]['total']) ? true : false;
    }


    /**
     * returns the product's combination IDs
     *
     * @return mixed
     */
    private function getCombinationIds()
    {
        if (!empty(\BTMailchimpEcommerce::$bCompare1610)) {
            $sQuery = 'SELECT pas.id_product_attribute, pas.id_product'
                . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas'
                . ' WHERE pas.`id_product` = ' . (int)$this->product->id . ' AND pas.id_shop = ' . (int)\BTMailchimpEcommerce::$iShopId;
        } else {
            $sQuery = 'SELECT pa.id_product_attribute, pa.id_product'
                . ' FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . ' WHERE pa.`id_product` = ' . (int)$this->product->id;
        }

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aResult) ? $aResult : false;
    }
}

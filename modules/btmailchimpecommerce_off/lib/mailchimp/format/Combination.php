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

class Combination extends Formatter
{
    /**
     * @var null
     */
    public $product = null;

    /**
     * @var null
     */
    public $price = null;

    /**
     * @var string
     */
    public $image = '';

    /**
     * @var bool
     */
    public $default = false;

    /**
     * Product constructor.
     * @param $mProductId
     * @param $iProdAttrId
     * @param $iLangId
     * @param $iShopId
     * @param $sImageSize
     * @param $default
     */
    public function __construct($mProductId, $iProdAttrId, $iLangId, $sImageSize, $default = false)
    {
        $this->lang_id = $iLangId;
        $this->product_attribute_id = $iProdAttrId;
        $this->product = $this->getProduct($mProductId);
        $this->image = $sImageSize;
        $this->default = $default;
    }

    /**
     * Format product combination data
     * @return array
     */
    public function format()
    {
        if ($this->isValid($this->product)) {
            $this->data = array(
                'id' => $this->getId(),
                'title' => $this->getTitle(),
                'inventory_quantity' => $this->getQuantity(),
                'price' => $this->setPrice(),
                'image_url' => $this->getImageUrl()
            );
        }

        return $this->data;
    }

    /**
     * Format the product attribute Id as we want to use many languages for all the product combinations
     *
     * @return string
     */
    private function getId()
    {
        $iProdAttrId = (!$this->default) ? $this->product_attribute_id : '1';

        return (string)parent::setProductID($this->product->id, $this->lang_id, $iProdAttrId);
    }

    /**
     * Set the combination price
     *
     * @return null|string
     */
    private function setPrice()
    {

        $bUseTax = !empty(\BTMailchimpEcommerce::$conf['MCE_PRODUCT_TAX']) ? true : false;

        // get the combination price
        if (!$this->default) {
            $this->price = \Product::getPriceStatic($this->product->id, $bUseTax , (int)$this->product_attribute_id);
        } else {
            $this->price = \Product::getPriceStatic($this->product->id, $bUseTax);
        }
        $this->price = number_format(\MCE\Tools::round($this->price), 2, '.', '');

        return $this->price;
    }


    /**
     * get the combination image URL
     *
     * @return null|string
     */
    private function getImageUrl()
    {
        // get combination images
        if (!$this->default) {
            $aAttributeImages = $this->product->getCombinationImages($this->lang_id);

            if (isset($aAttributeImages[$this->product_attribute_id])
                && is_array($aAttributeImages[$this->product_attribute_id])
                && isset($aAttributeImages[$this->product_attribute_id][0]['id_image'])
            ) {
                $sCombinationImgUrl = \MCE\Tools::getProductImage($this->product, $this->image, array('id_image' => $aAttributeImages[$this->product_attribute_id][0]['id_image']));
            }
        }

        // if any image has been assigned, we set the cover image
        if (empty($sCombinationImgUrl)) {
            $sCombinationImgUrl = \MCE\Tools::getProductImage($this->product, $this->image);
        }

        return $sCombinationImgUrl;
    }

    /**
     * get the real quantity of the product
     *
     * @return null|string
     */
    private function getQuantity()
    {
        return !$this->default ? \Product::getRealQuantity($this->product->id, $this->product_attribute_id) : \Product::getRealQuantity($this->product->id);
    }


    /**
     * get the product combination title
     *
     * @return null|string
     */
    private function getTitle()
    {
        return !$this->default ? \MCE\Tools::getProductCombinationName(self::sanitizeLanguageField($this->product->name, $this->lang_id), $this->product_attribute_id, $this->lang_id, \BTMailchimpEcommerce::$iShopId) : self::sanitizeLanguageField($this->product->name, $this->lang_id);
    }
}

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

namespace MCE;

class Voucher
{
    /**
     * var array $aAuthorizeKeys = list of authorized keys
     */
    public $aAvailableKeys = array(
        'code' => true,
        'name' => true,
        'discount' => true,
        'amount' => true,
        'minimum' => true,
        'validity' => true,
        'highlight' => true,
        'cumulativeOther' => true,
        'cumulativeReduc' => true,
        'langs' => true,
        'prefix' => false,
        'currency' => false,
        'tax' => false,
    );


    /**
     * var array $values = values of the voucher
     */
    public $values = array();


    /**
     * check the values of the voucher and set them
     *
     * @param array $aValues
     * @return bool
     */
    public function setVoucher(array $aValues)
    {
        $bReturn = true;

        if (!empty($aValues)) {
            foreach ($this->aAvailableKeys as $sOption => $bOptional) {
                if ($bOptional
                    && !array_key_exists($sOption, $aValues)
                ) {
                    $bReturn = false;
                }
            }
        } else {
            $bReturn = false;
        }

        // only in case of getting all the required keys available into the array of values
        if ($bReturn) {
            $this->values = $aValues;
        }

        return $bReturn;
    }

    /**
     * check if the code exists or not
     *
     * @param string $sCode
     * @return bool
     */
    public function isExist($sCode)
    {
        return \CartRule::cartRuleExists($sCode);
    }


    /**
     * returns create the voucher
     *
     * @param int $iCustId
     * @param string $this ->values
     * @return array : code name and status, name empty if the code exists or is badly created
     */
    public function add($iCustId)
    {
        // set
        $sCodeName = '';
        $sStatus = '';

        if (!empty($this->values)) {
            // stock voucher code
            $sCodeName = $this->values['code'];

            // get object
            $oDiscount = new \CartRule();

            // set language for name or description according to 1.5 or lower versions
            foreach ($this->values['langs'] as $iLangId => $sTitle) {
                // set languages name
                $oDiscount->name[$iLangId] = $sTitle;

                // set description
                $oDiscount->description = \BTMailchimpEcommerce::$oModule->l('Voucher won with', 'Voucher') . ' "' . $this->values['name'] . '"';
            }

            // set code
            $oDiscount->code = $sCodeName;

            // get reduction type
            $sType = $this->values['discount'] == 'amount' ? 'reduction_amount' : 'reduction_percent';

            // set amount
            $oDiscount->{$sType} = floatval($this->values['amount']);

            // set reduction currency + minimum amount
            $oDiscount->reduction_currency = !empty($this->values['currency']) ? intval($this->values['currency']) : false;
            $oDiscount->minimum_amount = $this->values['minimum'];
            $oDiscount->minimum_amount_currency = !empty($this->values['currency']) ? intval($this->values['currency']) : false;
            $oDiscount->highlight = $this->values['highlight'];
            $oDiscount->reduction_tax = !empty($this->values['tax']) ? $this->values['tax'] : false;
            $oDiscount->cart_rule_restriction = (intval($this->values['cumulativeOther']) == 0 ? 1 : 0);
            $oDiscount->reduction_product = -2;

            // test for activate exception
            if (!empty($this->values['categories'])) {
                $oDiscount->product_restriction = 1;
            }

            // shared data
            $oDiscount->value = floatval($this->values['amount']);
            $oDiscount->id_customer = $iCustId;
            $oDiscount->quantity = 1;
            $oDiscount->quantity_per_user = 1;
            $oDiscount->cumulable = intval($this->values['cumulativeOther']);
            $oDiscount->cumulable_reduction = intval($this->values['cumulativeReduc']);
            $oDiscount->active = 1;
            $oDiscount->date_from = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $oDiscount->date_to = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + intval($this->values['validity']), date('Y')));

            // set transaction
            \Db::getInstance()->Execute('BEGIN');

            // use case - adding succeed
            $bInsert = $oDiscount->add(true, false);

            // use case - only if there is specific categories to include as exception
            if ($bInsert && !empty($this->values['categories'])) {
                require_once(_MCE_PATH_LIB . 'Dao.php');
                // add a cart rule
                $bInsert = \MCE\Dao::addProductRule($oDiscount->id, 1, 'categories', $this->values['categories']);
            }

            if (!$bInsert) {
                $sCodeName = '';
                $sStatus = \BTMailchimpEcommerce::$oModule->l('Internal server error! The creation of the voucher code went wrong, and it comes from the database, the query couldn\'t insert it well', 'Voucher');
            }

            // succeeded
            if ($bInsert) {
                \Db::getInstance()->Execute('COMMIT');
            } // failure
            else {
                \Db::getInstance()->Execute('ROLLBACK');
            }
        } else {
            $sCodeName = '';
            $sStatus = \BTMailchimpEcommerce::$oModule->l('Values of the voucher you want to create have not been set to this object', 'Voucher');
        }

        return array(
            'name' => $sCodeName,
            'status' => $sStatus
        );
    }

    /**
     * returns singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oCtrl;

        if (null === $oCtrl) {
            $oCtrl = new \MCE\Voucher();
        }
        return $oCtrl;
    }
}

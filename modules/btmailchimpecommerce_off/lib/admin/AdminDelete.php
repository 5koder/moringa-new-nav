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

class AdminDelete implements \BT_IAdmin
{
    /**
     * delete content according to the parameters
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     * @return array
     */
    public function run($sType, array $aParam = null)
    {
        // set variables
        $aDisplayData = array();

        switch ($sType) {
            case 'excludedDomain' : // use case - delete one e-mail domain name used to exclude customers with this e-mail domain mail
            case 'voucher' : // use case - delete one automation voucher
                // execute match function
                $aDisplayData = call_user_func_array(array($this, 'delete' . ucfirst($sType)), array($aParam));
                break;
            default :
                break;
        }
        return $aDisplayData;
    }


    /**
     * delete the excluded domain name
     *
     * @param array $aPost
     * @return array
     */
    private function deleteExcludedDomain(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();
        $aExcludedDomain = array();

        try {
            $iExcludedId = \Tools::getValue('iExcludedMail');

            // use case - get the excluded domain id
            if ($iExcludedId === false) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The excluded domain name ID is empty', 'AdminDelete') . '.', 120);
            }

            // get the current excluded mail list
            $aExcludedMail = is_string(\BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']) && empty(\BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']) ? array() : \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'];

            if (isset($aExcludedMail[$iExcludedId])) {
                $assign['sDeletedName'] = $aExcludedMail[$iExcludedId];
                unset($aExcludedMail[$iExcludedId]);
            }

            // update
            \Configuration::updateValue('MCE_MAIL_EXCLUSION', serialize($aExcludedMail));
            $assign['aEmailExclusions'] = $aExcludedMail;
            $assign['aQueryParams'] = $GLOBALS['MCE_REQUEST_PARAMS'];
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        $assign['bDelete'] = empty($assign['aErrors']) ? true : false;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_EXCLUSION_LIST,
            'assign' => $assign,
        );
    }


    /**
     * delete an automation voucher
     *
     * @param array $aPost
     * @return array
     */
    private function deleteVoucher(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            // use case - get current voucher to delete
            $sVoucher = \Tools::getValue('bt_voucher');
            // get stored vouchers
            $aVouchers = !empty(\BTMailchimpEcommerce::$conf['MCE_VOUCHERS']) ? \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'] : array();

            if (!empty($sVoucher)
                && isset($aVouchers[$sVoucher])
            ) {
                unset($aVouchers[$sVoucher]);

                if (!\Configuration::updateValue('MCE_VOUCHERS', serialize($aVouchers))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the vouchers update', 'AdminUpdate') . '.', 130);
                }

                $assign['aConfirmDetail'][] = \BTMailchimpEcommerce::$oModule->l('You just deleted your discount voucher with success', 'AdminDelete') . ' "' . $sVoucher . '"';
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('Something wrong happened during the discount voucher deletion or maybe this voucher doesn\'t exist anymore.', 'AdminDelete') . '.', 131);
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to display first page of admin with lists and stores settings updated
        $aDisplay = \MCE\AdminDisplay::create()->run('vouchers');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($assign['aErrors']) ? true : false),
        ), $assign);

        return $aDisplay;
    }


    /**
     * create() method set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oDelete;

        if (null === $oDelete) {
            $oDelete = new \MCE\AdminDelete();
        }
        return $oDelete;
    }
}

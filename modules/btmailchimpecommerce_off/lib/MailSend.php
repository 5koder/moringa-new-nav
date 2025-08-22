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

class MailSend
{
    /**
     * @var bool $bProcess : define if process or not
     */
    protected $bProcess = null;

    /**
     * execute hook
     *
     * @param string $sType
     * @param array $aParams
     * @param string $sPath
     * @return bool
     */
    public function run($sType, array $aParams, $sPath = _PS_MAIL_DIR_)
    {
        $bSend = false;
        $this->bProcess = false;

        switch ($sType) {
            case 'voucherNotification' :
                $aParams = $this->sendVoucherNotification($aParams);
                break;
            case 'apiSyncError' :
                $aParams = $this->sendApiErrorNotification($aParams);
                break;
            default :
                break;
        }

        // use case - only if process true
        if ($this->bProcess) {
            // set iso template mail
            if (is_dir($sPath . $aParams['iso'] . '/') && !empty($aParams['isoId'])) {
                $iIsoLangId = $aParams['isoId'];
            } // get default language
            else {
                $iIsoLangId = \Configuration::get('PS_LANG_DEFAULT');
            }

            // use case - send e-mail with bcc
            if (isset($aParams['bcc']) && is_string($aParams['bcc'])) {
                $bSend = \Mail::send($iIsoLangId, $aParams['tpl'], $aParams['subject'], $aParams['vars'], $aParams['email'], null, null, null, null, null, $sPath, false, null, $aParams['bcc']);
            } else {
                $bSend = \Mail::send($iIsoLangId, $aParams['tpl'], $aParams['subject'], $aParams['vars'], $aParams['email'], null, null, null, null, null, $sPath);
            }
        }

        return $bSend;
    }


    /**
     * process data for sending an e-mail notification to the customer with his fresh created voucher
     *
     * @param array $aPost
     * @return array
     */
    private function sendVoucherNotification(array $aPost)
    {
        $aParams = array();

        if (!empty($aPost['voucher'])
            && !empty($aPost['email'])
        ) {
            // get iso id & iso lang & email
            $aParams['subject'] = \BTMailchimpEcommerce::$oModule->l('Your voucher has been created !', 'MailSend');
            $aParams['isoId'] = !empty($aPost['langId']) ? $aPost['langId'] : \Configuration::get('PS_LANG_DEFAULT');
            $aParams['iso'] = !empty($aPost['langIso']) ? $aPost['langIso'] : \MCE\Tools::getLangIso($aParams['isoId']);
            $aParams['email'] = $aPost['email'];
            $aParams['tpl'] = 'voucher_new';

            // send mail vars
            $aParams['vars'] = array(
                '{firstname}' => $aPost['firstname'],
                '{lastname}' => $aPost['lastname'],
                '{voucher_num}' => $aPost['voucher']['code'],
            );

            $this->bProcess = true;
        }
        return $aParams;
    }


    /**
     * send an email
     *
     * @param array $aPost
     * @return array
     */
    private function sendApiErrorNotification(array $aPost)
    {
        $aParams = array();

        if (!empty($aPost['data_id'])
            && !empty($aPost['data_type'])
            && !empty($aPost['data_detail'])
            && !empty($aPost['data_sync_limit'])
            && !empty($aPost['back_module_url'])
            && !empty($aPost['email'])
        ) {
            // get iso id & iso lang & email
            $aParams['subject'] = \BTMailchimpEcommerce::$oModule->l('[MAILCHIMP-MODULE] Warning! something went wrong with the automatic synching', 'MailSend');
            $aParams['isoId'] = !empty($aPost['langId']) ? $aPost['langId'] : \Configuration::get('PS_LANG_DEFAULT');
            $aParams['iso'] = !empty($aPost['langIso']) ? $aPost['langIso'] : \MCE\Tools::getLangIso($aParams['isoId']);
            $aParams['email'] = $aPost['email'];
            $aParams['tpl'] = 'api-sync-error';

            // send mail vars
            $aParams['vars'] = array(
                '{data_id}' => $aPost['data_id'],
                '{data_type}' => $aPost['data_type'],
                '{data_detail}' => $aPost['data_detail'],
                '{data_sync_limit}' => $aPost['data_sync_limit'],
                '{back_module_url}' => $aPost['back_module_url'],
            );

            $this->bProcess = true;
        }
        return $aParams;
    }


    /**
     * set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oMailSend;

        if (null === $oMailSend) {
            $oMailSend = new \MCE\MailSend();
        }
        return $oMailSend;
    }
}

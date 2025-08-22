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

class Member extends Formatter
{
    const SUBSCRIBED = 'subscribed';
    const UNSUBSCRIBED = 'unsubscribed';
    const CLEANED = 'cleaned';
    const PENDING = 'pending';

    /**
     * @var array
     */
    public $customer = [];

    /**
     * @var string
     */
    public $status = '';

    /**
     * @var string
     */
    public $default_status = '';

    /**
     * @var array
     */
    public $merge_fields = [];


    /**
     * @var bool
     */
    public $double_optin = false;

    /**
     * @var string
     */
    public $export_type = '';

    /**
     * Member constructor.
     * @param $mCustomer
     * @param $iLangId
     * @param $sExportType
     * @param $bDoubleOptin
     * @param $iDefaultStatus
     */
    public function __construct($mCustomer, $iLangId, $sExportType, $bDoubleOptin = false, $sDefaultStatus = '')
    {
        $this->lang_id = (is_array($mCustomer) && isset($mCustomer['id_lang'])) ? $mCustomer['id_lang'] : $iLangId;
        $this->customer = $this->getCustomer($mCustomer);
        $this->export_type = $sExportType;
        $this->double_optin = $bDoubleOptin;
        $this->default_status = $sDefaultStatus;
    }


    /**
     * Format customer data
     * @return array
     */
    public function format()
    {
        // get merge fields
        $this->getMergeFields($GLOBALS['MCE_MERGE_FIELDS'], $this->customer);

        $this->data = array(
            'email_address' => $this->customer['email'],
            'status' => $this->getStatus(),
            'language' => \MCE\Tools::getLangIso($this->lang_id),
            'language_id' => $this->lang_id
        );

        // hanlde merge fields
        if (!empty($this->merge_fields)) {
            $this->data['merge_fields'] = $this->merge_fields;
        }

        return $this->data;
    }

    /**
     * Get a customer array
     *
     * @param $mCustomer
     * @return Customer
     */
    private function getCustomer($mCustomer)
    {
        return (is_object($mCustomer) ? (array)$mCustomer : $mCustomer);
    }


    /**
     * define if the customer optin or not
     * @return bool
     */
    private function getStatus()
    {
        if (!empty($this->default_status)) {
            $this->status = $this->default_status;
        } else {
            if (!empty($this->customer['newsletter'])) {
                $this->status = $this->double_optin ? Member::PENDING : Member::SUBSCRIBED;
            } elseif ($this->export_type != 'optin') {
                $this->status = $this->double_optin ? Member::PENDING : Member::SUBSCRIBED;
            } else {
                $this->status = Member::UNSUBSCRIBED;
            }
        }

        return $this->status;
    }


    /**
     * format the merge fields
     *
     * @param array $defaults
     * @param array $data
     * @return array
     */
    private function getMergeFields(array $defaults, array $data)
    {
        foreach ($defaults as $field) {
            $check_params = true;
            $get_params = array();
            foreach ($field['callback']['params'] as $param) {
                if (!isset($data[$param])) {
                    $check_params = false;
                } else {
                    $get_params[] = $data[$param];
                }
            }

            if ($check_params) {
                $result = call_user_func_array($field['callback']['function'], $get_params);

                if (!empty($result)) {
                    $this->merge_fields[$field['tag']] = $result;
                }
            }
        }
    }
}

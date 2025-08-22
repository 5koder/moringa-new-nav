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

class MergeFields extends BaseCommand
{
    /**
     * @const API_MERGE_FIELD_URL
     */
    const API_MERGE_FIELD_URL = 'lists';

    /**
     * var array $aMergeFieldTypes : define the list of merge field type available in MC
     */
    public static $aMergeFieldTypes = array(
        'text',
        'number',
        'address',
        'phone',
        'date',
        'url',
        'imageurl',
        'radio',
        'dropdown',
        'birthday',
        'zip'
    );

    /**
     * add a new merge field in the MC list
     *
     * @throws Exception
     * @param string $sFieldName
     * @param string $sFieldType
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add($sFieldName, $sFieldType, array $aOpts = array())
    {
        $aParams = array();

        // check the good information of customer and cart's lines
        if (!empty($sFieldName)
            && !empty($sFieldType)
            && in_array($sFieldType, self::$aMergeFieldTypes)
        ) {
            $aParams['name'] = $sFieldName;
            $aParams['type'] = $sFieldType;

            if (!empty($aOpts['tag'])) {
                $aParams['tag'] = $aOpts['tag'];
            }
            if (!empty($aOpts['options'])) {
                $aParams['options'] = $aOpts['options'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the merge_field doesn\'t respect the available list of field type', 'list-merge-fields_class'), 1580);
        }

        return $this->app->call(self::API_MERGE_FIELD_URL . '/' . $this->getId() . '/merge-fields', $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get merge fields' information
     *
     * @param int $iMergeFieldId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $iMergeFieldId = null,
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
        if (!empty($iCount)) {
            $aParams['count'] = $iCount;
        }
        // optionals - offset
        if ($iOffset !== null) {
            $aParams['offset'] = $iOffset;
        }

        return $this->app->call(
            self::API_MERGE_FIELD_URL . '/' . $this->getId() . '/merge-fields' . (!empty($iMergeFieldId) ? '/' . $iMergeFieldId : ''),
            $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update merge fields' information
     *
     * @throws Exception
     * @param int $iMergeFieldId
     * @param string $sFieldName
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update($iMergeFieldId, $sFieldName, array $aOpts = array())
    {
        $aParams = array();

        // check the merge field name
        if (!empty($sFieldName)) {
            $aParams['name'] = $sFieldName;

            if (!empty($aOpts)
                && is_array($aOpts)
            ) {
                if (!empty($aOpts['default_value'])
                    && is_string($aOpts['default_value'])
                ) {
                    $aParams['default_value'] = (string)$aOpts['default_value'];
                }
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the merge field name is empty', 'list-merge-fields_class'), 1581);
        }

        return $this->app->call(
            self::API_MERGE_FIELD_URL . '/' . $this->getId() . '/merge-fields' . (!empty($iMergeFieldId) ? '/' . $iMergeFieldId : ''),
            $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete merge field
     *
     * @param string $iMergeFieldId : merge field ID
     * @return mixed : result of the API call
     */
    public function delete($iMergeFieldId)
    {
        return $this->app->call(
            self::API_MERGE_FIELD_URL . '/' . $this->getId() . '/merge-fields/' . $iMergeFieldId, null,
            \MCE\Chimp\Api::DELETE
        );
    }
}

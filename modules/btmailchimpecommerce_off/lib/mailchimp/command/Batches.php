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

class Batches extends BaseCommand
{
    /**
     * @const API_LISTS_URL
     */
    const API_BATCH_URL = 'batches';

    /**
     * @var array $aMethodValues : list of possible method values
     */
    public $aMethodValues = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');

    /**
     * create a new batch in the MC account
     *
     * @throws Exception
     * @param array $aOperations
     * @return mixed : result of the API call
     */
    public function add(array $aOperations)
    {
        if (!empty($aOperations['operations']) && is_array($aOperations['operations'])) {
            foreach ($aOperations['operations'] as $aOperation) {
                if (!empty($aOperation['method'])
                    && in_array($aOperation['method'], $this->aMethodValues)
                    && !empty($aOperation['path'])
                    && ((!empty($aOperation['body'])
                    && $aOperation['method'] != 'DELETE')
                    || (empty($aOperation['body'])
                    && $aOperation['method'] == 'DELETE'))
                ) {
                    // do nothing
                } else {
                    throw new \MCE\Chimp\MailchimpException(self::API_BATCH_URL, \BTMailchimpEcommerce::$oModule->l('Internal server error => missing information for the batch creation', 'batches_class') . ' (' . \Tools::jsonEncode($aOperation) . ')',  '',1501);
                }
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(self::API_BATCH_URL, \BTMailchimpEcommerce::$oModule->l('Internal server error => missing "operations" key in the parameter', 'batches_class'), '', 1502);
        }

        return $this->app->call(self::API_BATCH_URL, $aOperations, \MCE\Chimp\Api::POST);
    }


    /**
     * get batch's information
     *
     * @param string $sId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $sId = null,
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

        return $this->app->call(self::API_BATCH_URL . (!empty($sId) ? '/' . $sId : ''), $aParams, \MCE\Chimp\Api::GET);
    }


    /**
     * delete batch
     *
     * @param string $sId : batch ID
     * @return mixed : result of the API call
     */
    public function delete($sId)
    {
        return $this->app->call(self::API_BATCH_URL . '/' . $sId, null, \MCE\Chimp\Api::DELETE);
    }
}

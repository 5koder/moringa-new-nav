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

class ConnectedSites extends BaseCommand
{
    /**
     * @const API_CONNECT_URL
     */
    const API_CONNECT_URL = 'connected-sites';

    /**
     * add a PS shop to a MC store
     *
     * @param string $sId
     * @return array
     */
    public function action($sId)
    {
        return $this->app->call(
            self::API_CONNECT_URL . '/' . $sId . '/actions/verify-script-installation', array(),
            \MCE\Chimp\Api::POST
        );
    }


    /**
     * add a new connected site in the MC account
     *
     * @param string $sId
     * @param string $domain
     * @return mixed : result of the API call
     */
    public function add($sId, $domain)
    {
        // required values
        $aParams = array(
            'foreign_id' => $sId,
            'domain' => $domain,
        );

        return $this->app->call(self::API_CONNECT_URL, $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get connected sites information
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

        return $this->app->call(self::API_CONNECT_URL . (!empty($sId) ? '/' . $sId : ''), $aParams, \MCE\Chimp\Api::GET);
    }

    /**
     * delete connected site
     *
     * @param string $sId : store ID
     * @return mixed : result of the API call
     */
    public function delete($sId)
    {
        return $this->app->call(self::API_CONNECT_URL . '/' . $sId, null, \MCE\Chimp\Api::DELETE);
    }
}

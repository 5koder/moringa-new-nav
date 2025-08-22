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

class ApiRoot extends BaseCommand
{
    /**
     * @const API_ROOT_URL
     */
    const API_ROOT_URL = '';

    /**
     * @var array $aVisibilityValues : list of possible visibility values
     */
    public $aVisibilityValues = array('pub', 'prv');


    /**
     * get root's information
     *
     * @param string $sId
     * @param array $aFields
     * @param array $aExcludeFields
     * @return mixed : result of the API call
     */
    public function get(
        array $aFields = array(),
        array $aExcludeFields = array()
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

        return $this->app->call(self::API_ROOT_URL, $aParams, \MCE\Chimp\Api::GET);
    }
}

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

class ListWebhook extends BaseCommand
{
    /**
     * @const API_LIST_HOOK_URL
     */
    const API_LIST_HOOK_URL = 'lists';

    /**
     * @var array
     */
    public $events = ['subscribe' => false, 'unsubscribe' => true, 'profile' => false, 'cleaned' => false, 'upemail' => false, 'campaign' => false];

    /**
     * @var array
     */
    public $sources = ['user' => true, 'admin' => true, 'api' => true];

    /**
     * create a new batch in the MC account
     *
     * @throws Exception
     * @param string $url
     * @return mixed : result of the API call
     */
    public function add($url, $events = [], $sources = [])
    {
        $params = array();

        if (!empty($url)
            && is_string($url)
            && filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)
        ) {
            $params['url'] = $url;

            // handle events
            if (!empty($events)
                && is_array($events)
            ) {
                foreach ($events as $event => $value) {
                    if (array_key_exists($event, $this->events)) {
                        $params['events'][$event] = $value;
                    }
                }
            } else {
                $params['events'] = $this->events;
            }

            // handle sources
            if (!empty($sources)
                && is_array($sources)
            ) {
                foreach ($sources as $source => $value) {
                    if (array_key_exists($source, $this->sources)) {
                        $params['sources'][$source] = $value;
                    }
                }
            } else {
                $params['sources'] = $this->sources;
            }

        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the URL is not a valid URL', 'ListWebhook'), '', 1600);
        }

        return $this->app->call(self::API_LIST_HOOK_URL .'/'. $this->getId() .'/webhooks', $params, \MCE\Chimp\Api::POST);
    }


    /**
     * get list webhook's information
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

        return $this->app->call(self::API_LIST_HOOK_URL .'/'. $this->getId() .'/webhooks'. (!empty($sId) ? '/' . $sId : ''), $aParams, \MCE\Chimp\Api::GET);
    }


    /**
     * delete list webhook
     *
     * @param string $sId : batch ID
     * @return mixed : result of the API call
     */
    public function delete($sId)
    {
        return $this->app->call(self::API_LIST_HOOK_URL .'/'. $this->getId() .'/webhooks/' . $sId, null, \MCE\Chimp\Api::DELETE);
    }
}

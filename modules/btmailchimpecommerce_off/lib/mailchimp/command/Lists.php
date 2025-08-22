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

class Lists extends BaseCommand
{
    /**
     * @const API_LISTS_URL
     */
    const API_LISTS_URL = 'lists';

    /**
     * @var array $aVisibilityValues : list of possible visibility values
     */
    public $aVisibilityValues = array('pub', 'prv');

    /**
     * add a new list in the MC account
     *
     * @throws Exception
     * @param string $sName
     * @param array $aContact
     * @param string $sPermissionReminder
     * @param bool $bEmailTypeOption
     * @param array $aCampaignDefaults
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function add(
        $sName,
        $aContact,
        $sPermissionReminder,
        $bEmailTypeOption,
        $aCampaignDefaults,
        array $aOpts = array()
    ) {
        if (!empty($sName)
            && !empty($aContact)
            && !empty($aCampaignDefaults)
            && !empty($sPermissionReminder)
            && is_bool($bEmailTypeOption)
        ) {
            // use case - address
            if (isset($aContact['company'])
                && isset($aContact['address1'])
                && isset($aContact['city'])
                && isset($aContact['state'])
                && isset($aContact['zip'])
                && isset($aContact['country'])
            ) {
                $aParams['contact']['company'] = $aContact['company'];
                $aParams['contact']['address1'] = $aContact['address1'];
                $aParams['contact']['zip'] = $aContact['zip'];
                $aParams['contact']['city'] = $aContact['city'];
                $aParams['contact']['state'] = $aContact['state'];
                $aParams['contact']['country'] = $aContact['country'];

                // optionals values - address
                if (!empty($aContact['address2'])) {
                    $aParams['contact']['address2'] = $aContact['address2'];
                }
                // optionals values - phone
                if (isset($aContact['phone'])) {
                    $aParams['contact']['phone'] = $aContact['phone'];
                }
            } else {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => missing contact information for the list creation', 'lists_class'), 1540);
            }

            // use case - campaign defaults
            if (isset($aCampaignDefaults['from_name'])
                && isset($aCampaignDefaults['from_email'])
                && isset($aCampaignDefaults['subject'])
                && isset($aCampaignDefaults['language'])
            ) {
                $aParams['campaign_defaults']['from_name'] = $aCampaignDefaults['from_name'];
                $aParams['campaign_defaults']['from_email'] = $aCampaignDefaults['from_email'];
                $aParams['campaign_defaults']['subject'] = $aCampaignDefaults['subject'];
                $aParams['campaign_defaults']['language'] = $aCampaignDefaults['language'];
            } else {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => missing campaign defaults information for the list creation', 'lists_class'), 1541);
            }

            // required values
            $aParams['name'] = $sName;
            $aParams['permission_reminder'] = $sPermissionReminder;
            $aParams['email_type_option'] = $bEmailTypeOption;

            // optionals values - use_archive_bar
            if (isset($aOpts['use_archive_bar'])) {
                $aParams['use_archive_bar'] = $aOpts['use_archive_bar'];
            }
            // optionals values - notify_on_subscribe
            if (isset($aOpts['notify_on_subscribe'])) {
                $aParams['notify_on_subscribe'] = $aOpts['notify_on_subscribe'];
            }
            // optionals values - notify_on_unsubscribe
            if (isset($aOpts['notify_on_unsubscribe'])) {
                $aParams['notify_on_unsubscribe'] = $aOpts['notify_on_unsubscribe'];
            }
            // optionals values - visibility
            if (isset($aOpts['visibility'])
                && in_array($aOpts['visibility'], $this->aVisibilityValues)
            ) {
                $aParams['visibility'] = $aOpts['visibility'];
            }
            // optionals values - double_optin
            if (isset($aOpts['double_optin'])) {
                $aParams['double_optin'] = (bool)$aOpts['double_optin'];
            }
            // optionals values - marketing_permissions
            if (isset($aOpts['gdpr'])) {
                $aParams['marketing_permissions'] = (bool)$aOpts['gdpr'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => missing information for the list creation', 'lists_class'), 1542);
        }

        return $this->app->call(self::API_LISTS_URL, $aParams, \MCE\Chimp\Api::POST);
    }


    /**
     * get list's information
     *
     * @param string $sId
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function get(
        $sId = null,
        array $aFields = array(),
        array $aExcludeFields = array(),
        $iCount = null,
        $iOffset = null,
        array $aOpts = array()
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
        // optionals - before a created date
        if (isset($aOpts['before_date_created'])) {
            $aParams['before_date_created'] = $aOpts['before_date_created'];
        }
        // optionals - since a created date
        if (isset($aOpts['since_date_created'])) {
            $aParams['since_date_created'] = $aOpts['since_date_created'];
        }
        // optionals - before the last campaign date sent
        if (isset($aOpts['before_campaign_last_sent'])) {
            $aParams['before_campaign_last_sent'] = $aOpts['before_campaign_last_sent'];
        }
        // optionals - since the last campaign date sent
        if (isset($aOpts['since_campaign_last_sent'])) {
            $aParams['since_campaign_last_sent'] = $aOpts['since_campaign_last_sent'];
        }
        // optionals - email
        if (isset($aOpts['email'])) {
            $aParams['email'] = $aOpts['email'];
        }

        return $this->app->call(self::API_LISTS_URL . (!empty($sId) ? '/' . $sId : ''), $aParams, \MCE\Chimp\Api::GET);
    }


    /**
     * update list's information
     *
     * @throws Exception
     * @param string $sName
     * @param array $aContact
     * @param string $sPermissionReminder
     * @param bool $bEmailTypeOption
     * @param array $aCampaignDefaults
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update(
        $sId,
        $sName,
        array $aContact,
        $sPermissionReminder,
        $bEmailTypeOption,
        $aCampaignDefaults,
        array $aOpts = array()
    ) {
        // optional values
        $aParams = array();

        if (!empty($sName)
            && !empty($aContact)
            && !empty($aCampaignDefaults)
            && !empty($sPermissionReminder)
            && is_bool($bEmailTypeOption)
        ) {
            // required values
            $aParams['name'] = $sName;
            $aParams['permission_reminder'] = $sPermissionReminder;
            $aParams['email_type_option'] = $bEmailTypeOption;

            // use case - address
            if (isset($aContact['company'])
                && isset($aContact['address1'])
                && isset($aContact['city'])
                && isset($aContact['state'])
                && isset($aContact['zip'])
                && isset($aContact['country'])
            ) {
               
                $aParams['contact']['company'] = $aContact['company'];
                $aParams['contact']['address1'] = $aContact['address1'];
                $aParams['contact']['city'] = $aContact['city'];
                $aParams['contact']['zip'] = $aContact['zip'];
                $aParams['contact']['state'] = $aContact['state'];
                $aParams['contact']['country'] = $aContact['country'];

                // optionals values - address
                if (isset($aContact['address2'])) {
                    $aParams['contact']['address2'] = $aContact['address2'];
                }
                // optionals values - phone
                if (isset($aContact['phone'])) {
                    $aParams['contact']['phone'] = $aContact['phone'];
                }
            } else {
                throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => missing contact information for the list creation', 'lists_class'), 1544);
            }

            // use case - campaign defaults
            if (isset($aCampaignDefaults['from_name'])
                && isset($aCampaignDefaults['from_email'])
                && isset($aCampaignDefaults['subject'])
                && isset($aCampaignDefaults['language'])
            ) {
                if (!filter_var($aCampaignDefaults['from_email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \MCE\Chimp\MailchimpException(self::API_LISTS_URL, \BTMailchimpEcommerce::$oModule->l('Internal server error => the sender\'s email is not valid', 'lists_class'), '',1545);
                }

                $aParams['campaign_defaults']['from_name'] = $aCampaignDefaults['from_name'];
                $aParams['campaign_defaults']['from_email'] = $aCampaignDefaults['from_email'];
                $aParams['campaign_defaults']['subject'] = $aCampaignDefaults['subject'];
                $aParams['campaign_defaults']['language'] = $aCampaignDefaults['language'];
            } else {
                throw new \MCE\Chimp\MailchimpException(self::API_LISTS_URL, \BTMailchimpEcommerce::$oModule->l('Internal server error => missing campaign defaults information for the list creation', 'lists_class'),  '',1546);
            }


            // optionals values - use_archive_bar
            if (isset($aOpts['use_archive_bar'])) {
                $aParams['use_archive_bar'] = $aOpts['use_archive_bar'];
            }
            // optionals values - notify_on_subscribe
            if (isset($aOpts['notify_on_subscribe'])) {
                $aParams['notify_on_subscribe'] = $aOpts['notify_on_subscribe'];
            }
            // optionals values - notify_on_unsubscribe
            if (isset($aOpts['notify_on_unsubscribe'])) {
                $aParams['notify_on_unsubscribe'] = $aOpts['notify_on_unsubscribe'];
            }
            // optionals values - visibility
            if (isset($aOpts['visibility'])
                && in_array($aOpts['visibility'], $this->aVisibilityValues)
            ) {
                $aParams['visibility'] = $aOpts['visibility'];
            }
            // optionals values - double_optin
            if (isset($aOpts['double_optin'])) {
                $aParams['double_optin'] = (bool)$aOpts['double_optin'];
            }
            // optionals values - marketing_permissions
            if (isset($aOpts['gdpr'])) {
                $aParams['marketing_permissions'] = (bool)$aOpts['gdpr'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(self::API_LISTS_URL, \BTMailchimpEcommerce::$oModule->l('Internal server error => missing information for the list creation', 'lists_class'), '', 1547);
        }

        return $this->app->call(self::API_LISTS_URL . '/' . $sId, $aParams, \MCE\Chimp\Api::PATCH);
    }


    /**
     * delete list
     *
     * @param string $sId : list ID
     * @return mixed : result of the API call
     */
    public function delete($sId)
    {
        return $this->app->call(self::API_LISTS_URL . '/' . $sId, null, \MCE\Chimp\Api::DELETE);
    }
}

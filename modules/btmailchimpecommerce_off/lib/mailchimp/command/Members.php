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

class Members extends BaseCommand
{
    /**
     * @const API_MEMBER_URL
     */
    const API_MEMBER_URL = 'lists';

    /**
     * var array $aMemberStatuses : define the list of status type available in MC
     */
    public static $aMemberStatuses = array('subscribed', 'unsubscribed', 'cleaned', 'pending');

    /**
     * add a new member in the MC list
     *
     * @throws Exception
     * @param string $sEmail
     * @param string $sStatus
     * @param array $aOpts
     * @param string $sMethod
     * @return mixed : result of the API call
     */
    public function add($sEmail, $sStatus, array $aOpts = array(), $sMethod = 'post')
    {
        $aParams = array();

        // check the good information of customer and cart's lines
        if (!empty($sEmail)
            && !empty($sStatus)
            && in_array($sStatus, self::$aMemberStatuses)
        ) {
            $aParams['email_address'] = $sEmail;
            $aParams['status'] = $sStatus;

            if (!empty($aOpts['email_type'])
                && in_array($aOpts['email_type'], array('html', 'text'))
            ) {
                $aParams['email_type'] = $aOpts['email_type'];
            }
            if (!empty($aOpts['merge_fields'])
                && is_array($aOpts['merge_fields'])
            ) {
                $aParams['merge_fields'] = $aOpts['merge_fields'];
            }
            if (!empty($aOpts['language'])) {
                $aParams['language'] = $aOpts['language'];
            }
            if (!empty($aOpts['vip'])
                && is_bool($aOpts['vip'])) {
                $aParams['vip'] = (bool)$aOpts['vip'];
            }
            if (!empty($aOpts['location'])
                && is_array($aOpts['location'])
                && isset($aOpts['location']['latitude'])
                && isset($aOpts['location']['longitude'])
            ) {
                $aParams['location'] = $aOpts['location'];
            }
            if (!empty($aOpts['ip_signup'])) {
                $aParams['ip_signup'] = $aOpts['ip_signup'];
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the member doesn\'t respect the available list of field type', 'list-members_class'), 1570);
        }

        return $this->app->call(
            self::API_MEMBER_URL . '/' . $this->getId() . '/members' . ($sMethod == 'put' ? '/' . md5($sEmail) : ''),
            $aParams,
            ($sMethod == 'put' ? \MCE\Chimp\Api::PUT : \MCE\Chimp\Api::POST)
        );
    }


    /**
     * get member' information
     *
     * @param int $sEmail
     * @param array $aFields
     * @param array $aExcludeFields
     * @param int $iCount
     * @param int $iOffset
     * @return mixed : result of the API call
     */
    public function get(
        $sEmail = null,
        array $aFields = array(),
        array $aExcludeFields = array(),
        $iCount = null,
        $iOffset = null
    ) {
        // optional values
        $aParams = array();

        // optionals - fields
        if (!empty($aFields)) {
            $aParams['fields'] = implode(',', $aFields);
        }
        // optionals - exclude fields
        if (!empty($aExcludeFields)) {
            $aParams['exclude_fields'] = implode(',', $aExcludeFields);
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
            self::API_MEMBER_URL . '/' . $this->getId() . '/members' . (!empty($sEmail) ? '/' . md5($sEmail) : ''), $aParams, \MCE\Chimp\Api::GET
        );
    }


    /**
     * update member fields' information
     *
     * @throws Exception
     * @param int $sEmail
     * @param string $sFieldName
     * @param array $aOpts
     * @return mixed : result of the API call
     */
    public function update($sEmail, array $aOpts = array())
    {
        $aParams = array();

        // check the member mail
        if (!empty($sEmail)) {
            if (!empty($aOpts)
                && is_array($aOpts)
            ) {
                if (!empty($aOpts['status'])) {
                    $aParams['status'] = (string)$aOpts['status'];
                }
                if (!empty($aOpts['email_type'])
                    && in_array($aOpts['email_type'], array('html', 'text'))
                ) {
                    $aParams['email_type'] = $aOpts['email_type'];
                }
                if (!empty($aOpts['merge_fields'])
                    && is_array($aOpts['merge_fields'])
                ) {
                    $aParams['merge_fields'] = $aOpts['merge_fields'];
                }
                if (!empty($aOpts['language'])) {
                    $aParams['language'] = $aOpts['language'];
                }
                if (!empty($aOpts['vip'])
                    && is_bool($aOpts['vip'])) {
                    $aParams['vip'] = (bool)$aOpts['vip'];
                }
                if (!empty($aOpts['location'])
                    && is_array($aOpts['location'])
                    && isset($aOpts['location']['latitude'])
                    && isset($aOpts['location']['longitude'])
                ) {
                    $aParams['location'] = $aOpts['location'];
                }
                if (!empty($aOpts['ip_signup'])) {
                    $aParams['ip_signup'] = $aOpts['ip_signup'];
                }
            }
        } else {
            throw new \MCE\Chimp\MailchimpException(\BTMailchimpEcommerce::$oModule->l('Internal server error => the member email is empty', 'list-members_class'), 1571);
        }

        return $this->app->call(
            self::API_MEMBER_URL . '/' . $this->getId() . '/members' . '/' . md5($sEmail), $aParams, \MCE\Chimp\Api::PATCH
        );
    }


    /**
     * delete merge field
     *
     * @param string $sEmail : email
     * @return mixed : result of the API call
     */
    public function delete($sEmail)
    {
        return $this->app->call(
            self::API_MEMBER_URL . '/' . $this->getId() . '/members/' . md5($sEmail), null,
            \MCE\Chimp\Api::DELETE
        );
    }
}

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

use \MCE\Member;

class AdminUpdate implements \BT_IAdmin
{
    /**
     * Update all tabs content of admin page
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     * @return array
     */
    public function run($sType, array $aParam = null)
    {
        // set variables
        $aDisplayData = array();

        require_once(_MCE_PATH_LIB . 'Dao.php');

        // get loader
        \BTMailchimpEcommerce::getMailchimpLoader();

        // update the BO module URL
        \Configuration::updateValue('MCE_MODULE_BO_URL', \Context::getContext()->link->getAdminLink('AdminModules'));

        switch ($sType) {
            case 'mailchimpSettings' : // use case - update mailchimp settings
            case 'mailExclusion' : // use case - update exclusion e-mail domain settings
            case 'userList' : // use case - update creation list settings
            case 'synchingHistory' : // use case - update synching history data
            case 'search' : // use case - update search form
            case 'newsletterExportSettings' : // use case - update NL export settings when we export the user list
            case 'syncStatus' : // use case - update sync status
            case 'signupFormModule' : // use case - update sign-up form settings
            case 'signupFormMailchimp' : // use case - update sign-up form settings
            case 'ecommerce' : // use case - update ecommerce settings
            case 'voucher' : // use case - update voucher settings
            case 'newsletterSynching' : // use case - do the NL user synching
            case 'customerSynching' : // use case - do the customer synching
            case 'productSynching' : // use case - do the product synching
            case 'orderSynching' : // use case - do the past order synching
            case 'syncData' : // use case - update synch data
                // execute match function
                $aDisplayData = call_user_func_array(array($this, 'update' . ucfirst($sType)), array($aParam));
                break;
            default :
                break;
        }
        return $aDisplayData;
    }

    /**
     * update mailchimp settings
     *
     * @param array $aPost
     * @return array
     */
    private function updateMailchimpSettings(array $aPost)
    {
        // set
        $aData = array();

        try {
            // use case - check if the API key if filled out
            $sMcApiKey = \Tools::getValue('bt_mc_api_key');

            // check if we have a new API key - reset all the data related to the former API key
            if (!empty($sMcApiKey)
                && !empty(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY'])
                && $sMcApiKey != \BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']
            ) {
                // reset the tables
                \MCE\Dao::resetTables(\BTMailchimpEcommerce::$iShopId);
                $aData['bApiKeyModified'] = true;
            }

            if (!\Configuration::updateValue('MCE_MC_API_KEY', $sMcApiKey)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the MailChmip API key update', 'AdminUpdate') . '.', 5001);
            }

            // use case - check the cookie life time
            $iCookieTll = \Tools::getValue('bt_mc_cookie_ttl');
            if (!\Configuration::updateValue('MCE_COOKIE_TTL', ($iCookieTll * 60 * 60))) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the cookie lifetime update', 'AdminUpdate') . '.', 5002);
            }

        } catch (\Exception $e) {
            $aData['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to display first page of admin with mailchimp settings updated
        $aDisplay = \MCE\AdminDisplay::create()->run('');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $aData);

        return $aDisplay;
    }

    /**
     * execute the ajax request to exclude e-mail domain names
     *
     * @param array $aPost
     * @return array
     */
    private function updateMailExclusion(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();
        $aExcludedDomain = array();

        try {
            $sExcludedDomain = \Tools::getValue('bt_exclusion_domain');

            // use case - get the excluded domain
            if (!$sExcludedDomain) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The excluded domain name is empty', 'AdminUpdate') . '.', 5010);
            }

            $aExcludedDomain = strstr($sExcludedDomain, '|') ? explode('|', $sExcludedDomain) : array($sExcludedDomain);

            // get the current excluded mail list
            $aExcludedMail = is_string(\BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']) && empty(\BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']) ? array() : \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'];

            foreach ($aExcludedDomain as $sDomain) {
                if (!strstr($sDomain, '.')) {
                    $assign['aErrors'][] = array(
                        'msg' => \BTMailchimpEcommerce::$oModule->l('The excluded domain name is formatted badly, it should be like \'.\' with a tld valid, example: mydomain.com See the domain name in error:', 'AdminUpdate') . ' ' . $sDomain,
                        'code' => 198
                    );
                } else {
                    if (strstr($sDomain, '@')) {
                        $sDomain = str_replace('@', '', $sDomain);
                    }
                    $aExcludedMail[] = $sDomain;
                }
            }

            if (empty($assign['aErrors'])) {
                // update
                \Configuration::updateValue('MCE_MAIL_EXCLUSION', serialize($aExcludedMail));
            }

            $assign['aEmailExclusions'] = $aExcludedMail;
            $assign['aQueryParams'] = $GLOBALS['MCE_REQUEST_PARAMS'];
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        // get configuration options
        \MCE\Tools::getConfig();


        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_EXCLUSION_LIST,
            'assign' => $assign,
        );
    }

    /**
     * update list creation settings : define which list to use and if we need to create merge fields and the related store
     *
     * @param array $aPost
     * @return array
     */
    private function updateUserList(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();
        // get the list Id and name : check if we create or update a list
        $sListId = \Tools::getValue('bt_list_id');
        $sListName = \Tools::getValue('bt_list_name');
        $bDoubleOptin = \Tools::getValue('bt_nl_double_optin');
        $bGdpr = \Tools::getValue('bt_nl_gdpr');
        $is_same_list = false;

        // define shop domain
        $sDomain = \Context::getContext()->shop->domain;

        // instantiate the MC's controller
        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

        try {
            // if we have to create or update a list
            if (!empty($sListName)
                || !empty($sListId)
            ) {
                // check if a list is already activated and used
                $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);

                // get params for list creation
                $sPermissionReminder = \Tools::getValue('bt_list_reminder');
                $bEmailType = \Tools::getValue('bt_list_email_type') == false ? false : true;
                $aContact = array(
                    'company' => \Tools::getValue('bt_list_company_name'),
                    'address1' => \Tools::getValue('bt_list_company_address1'),
                    'address2' => \Tools::getValue('bt_list_company_address2'),
                    'zip' => \Tools::getValue('bt_list_company_zip'),
                    'city' => \Tools::getValue('bt_list_company_city'),
                    'state' => (\Tools::getValue('bt_list_company_state') == false ? \BTMailchimpEcommerce::$oModule->l('No state', 'AdminUpdate') : \Tools::getValue('bt_list_company_state')),
                    'country' => \Tools::getValue('bt_list_company_country'),
                );

                $sFromName = urldecode(\Tools::getValue('bt_campaign_name'));
                
                $aCampaignDefaults = array(
                    'from_name' => str_replace('"', '', $sFromName),
                    'from_email' => \Tools::getValue('bt_campaign_email'),
                    'subject' => urldecode(\Tools::getValue('bt_campaign_subject')),
                    'language' => \Tools::getValue('bt_campaign_language'),
                );

                // remove some lines not mandatory and should not be present in the array if empty
                if (empty($aContact['address2'])) {
                    unset($aContact['address2']);
                }

                // create new list
                if (!empty($sListName)) {
                    // use case - detect if the list name is well passed
                    if (empty($sListName)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The list name has not been filled in', 'AdminUpdate') . '.', 5020);
                    }
                    // use case - detect if the list permission reminder is well passed
                    if (empty($sPermissionReminder)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The GDPR fields option for the list has not been defined', 'AdminUpdate') . '.', 5021);
                    }
                    // use case - detect if the list company name is well passed
                    if (empty($aContact['company'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The company name for the list has not been filled in', 'AdminUpdate') . '.', 5022);
                    }
                    // use case - detect if the list company address 1 is well passed
                    if (empty($aContact['address1'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The company address for the list has not been filled in', 'AdminUpdate') . '.', 5023);
                    }
                    // use case - detect if the list company city is well passed
                    if (empty($aContact['city'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The company city for the list has not been filled in', 'AdminUpdate') . '.', 5024);
                    }
                    // use case - detect if the list company zip is well passed
                    if (empty($aContact['zip'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The company zip code for the list has not been filled in', 'AdminUpdate') . '.', 5025);
                    }
                    // use case - detect if the list company country is well passed
                    if (empty($aContact['country'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The company country for the list has not been filled in', 'AdminUpdate') . '.', 5026);
                    }
                    // use case - detect if the list sender's name is well passed
                    if (empty($aCampaignDefaults['from_name'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The sender\'s name for the list has not been filled in', 'AdminUpdate') . '.', 5027);
                    }
                    // use case - detect if the list sender's email is well passed
                    if (empty($aCampaignDefaults['from_email'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The sender\'s e-mail address for the list has not been filled in', 'AdminUpdate') . '.', 5028);
                    }
                    // use case - detect if the list subject is well passed
                    if (empty($aCampaignDefaults['subject'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The e-mails default subject for the list has not been filled in', 'AdminUpdate') . '.', 5029);
                    }
                    // use case - detect if the campaign language is well passed
                    if (empty($aCampaignDefaults['language'])) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The campaign language has not been filled in', 'AdminUpdate') . '.', 5030);
                    }

                    try {
                        // instantiate the MC's controller
                        $aResult = $oMcCtrl->lists->add(
                            $sListName,
                            $aContact,
                            $sPermissionReminder,
                            $bEmailType,
                            $aCampaignDefaults,
                            array(
                                'double_optin' => $bDoubleOptin,
                                'gdpr' => $bGdpr,
                            )
                        );
                    } catch (\MCEChimpMailchimpException $e) {
                        throw new \Exception($e->getFriendlyMessage(), 5037);
                    }

                    // register the new created store in our local DB
                    \MCE\Dao::createList(\BTMailchimpEcommerce::$iShopId, $aResult['id'], $sListName, '0', '', 1);
                    $assign['aConfirmDetail'][] = \BTMailchimpEcommerce::$oModule->l('The list has been created successfully with the name and ID:', 'AdminUpdate') . ' ' . $sListName . ' (' . $aResult['id'] . ')';

                    // assign the fresh list ID created
                    $iListId = $aResult['id'];

                    // check the double optin and GDPR
                    if (!\Configuration::updateValue('MCE_DOUBLE_OPTIN', $bDoubleOptin)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the double optin update', 'AdminUpdate') . '.', 5031);
                    }
                    if (!\Configuration::updateValue('MCE_GDPR', $bGdpr)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the GDPR fields option update', 'AdminUpdate') . '.', 5032);
                    }

                    \Configuration::updateValue('MCE_NL_ACTIVE', 0);

                    // update a list
                } else {
                    // explode it to get list ID / name / Store ID and name
                    list($iListId, $sListName) = explode('¤', $sListId);
                    $sListName = urldecode($sListName);

                    // check if we have the same list ID sent as the current active one
                    if (!empty($aActiveList)
                        && !empty($iListId)
                        && $aActiveList[0]['id'] == $iListId
                    ) {
                        $is_same_list = true;
                    }

                    try {
                        // instantiate the MC's controller
                        $aResult = $oMcCtrl->lists->update(
                            $iListId,
                            $sListName,
                            $aContact,
                            $sPermissionReminder,
                            $bEmailType,
                            $aCampaignDefaults,
                            array(
                                'double_optin' => $bDoubleOptin,
                                'gdpr' => $bGdpr
                            )
                        );
                    } catch (MCEChimpMailchimpException $e) {
                        throw new \Exception($e->getFriendlyMessage(), 5036);
                    }

                    // check the double optin and GDPR
                    if (!\Configuration::updateValue('MCE_DOUBLE_OPTIN', $bDoubleOptin)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the double optin update', 'AdminUpdate') . '.', 5033);
                    }
                    if (!\Configuration::updateValue('MCE_GDPR', $bGdpr)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the GDPR fields option update', 'AdminUpdate') . '.', 5034);
                    }
                }

                // reset all the other active list in order to get only one list active
                \MCE\Dao::updateList($iListId, array('active' => 0, 'not_list_id' => true));

                // check the batch/list webhook URL
                foreach (array('batch', 'list') as $webhook_type) {
                    // instantiate the MC's controller
                    $oMcCtrlWebhook = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                    // check the batch webhook
                    $name = $webhook_type .'Webhook';
                    $oMcCtrlWebhook->{$name}->setId($iListId);
                    $aWebhooks = $oMcCtrlWebhook->{$name}->get();

                    if (!empty($aWebhooks['webhooks'])) {
                        try {
                            foreach ($aWebhooks['webhooks'] as $webhook) {
                                $aResult = $oMcCtrlWebhook->{$name}->delete($webhook['id']);
                            }
                        } catch (\Exception $e) {}
                    }

                    try {
                        // Use case to use front controller with batch case
                        if ($webhook_type == 'batch') {
                            $uri = \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_BATCH_WEBHOOK, array('token' => \BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN']));
                        }

                        //Use case to use front controller with list case
                        if ($webhook_type == 'list') {
                            $uri = \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_LIST_WEBHOOK, array('token' => \BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN']));
                        }

                        $oMcCtrlWebhook->{$name}->add($uri);
                    } catch (\MCE\Chimp\MailchimpException $e) {
                        $assign['s'. ucfirst($webhook_type) .'ErrorUri'] = $uri;
                        throw new \Exception($e->getFriendlyMessage(), 5037);
                    }
                }

                // test if we have the same list Id as the current active one
                if (!$is_same_list) {
                    // if already exist an active list
                    $assign['sExistingActiveList'] = (!empty($aActiveList[0]) && $aActiveList[0]['id'] != $iListId) ? $aActiveList[0]['name'] : 0;

                    // check the list status
                    $aNewsletterSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $iListId, 'newsletter');

                    if (empty($aNewsletterSyncStatus)) {
                        \Configuration::updateValue('MCE_NL_ACTIVE', 0);
                    }

                    // check the stores
                    $aStores = $oMcCtrl->store->get();

                    // check if an existing store is already using the current domain
                    if (!empty($aStores['stores'])) {
                        foreach ($aStores['stores'] as $store) {
                            if ($store['domain'] == $sDomain) {
                                // instantiate the MC's controller
                                $oMcCtrlStore = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                                // we update the other store using the current domain
                                $aResult = $oMcCtrlStore->store->delete($store['id']);

                                // update into our tables to make it new again for the next time the merchant will choose again this store ID
                                \MCE\Dao::updateList($store['list_id'], array('store_id' => '', 'store_name' => '', 'data' => array()));

                                // delete the ecommerce synching from the global sync table
                                \MCE\Dao::deleteSyncStatus(\BTMailchimpEcommerce::$iShopId, $store['list_id'], array('product', 'customer', 'order'));

                                // update
                                \Configuration::updateValue('MCE_ECOMMERCE_ACTIVE', 0);
                            }
                        }
                    }

                    // check if the list already exist in the DB
                    $aList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, $iListId);

                    // update
                    if (!empty($aList)) {
                        \MCE\Dao::updateList($iListId, array('active' => 1));
                    } else {
                        // add
                        \MCE\Dao::createList(\BTMailchimpEcommerce::$iShopId, $iListId, $sListName, '', '', 1, []);
                    }
                } else {
                    $assign['aConfirmDetail'][] = \BTMailchimpEcommerce::$oModule->l('You didn\'t select another list. Only the double opt-in and GDPR fields options have been updated for the selected list', 'AdminUpdate') . '.';
                }
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t fill all the required fields for the list creation form or select an existing list neither!', 'AdminUpdate') . '.', 5035);
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

        // get run of admin display for user list panel
        $aDisplay = \MCE\AdminDisplay::create()->run('userList');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $assign);

        return $aDisplay;
    }


    /**
     * execute the ajax request to return the synching history
     *
     * @param array $aPost
     * @return array
     */
    private function updateSynchingHistory(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array('aResult' => array());

        try {
            $iDelay = \Tools::getValue('bt_dashboard_delay');

            // use case - the list or store is not selected
            if (!$iDelay) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The number of past days is empty', 'AdminUpdate') . '.', 410);
            }

            // check if a list is already activated and used
            $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
            $assign['aListDashboard'] = !empty($aActiveList[0])? $aActiveList[0] : false;
            $assign['iDelay'] = $iDelay;

            if (!empty($assign['aListDashboard'])) {
                $iDelay = $iDelay * 86400;

                // define the figures for the different type of data
                foreach (array('member', 'customer', 'product', 'cart', 'order') as $type) {
                    // define the figures for the order data type
                    $assign['aListDashboard']['aSyncData'][$type]['general'] = \MCE\Dao::getSyncData(\BTMailchimpEcommerce::$iShopId, $assign['aListDashboard']['id'], $type, true);
                    $assign['aListDashboard']['aSyncData'][$type]['details'] = \MCE\Dao::getSyncData(\BTMailchimpEcommerce::$iShopId, $assign['aListDashboard']['id'], $type, false, time(), $iDelay);
                    $aDetailCount = \MCE\Tools::countSyncDetail($type, $assign['aListDashboard']['aSyncData'][$type]['details']);
                    $assign['aListDashboard']['aSyncData'][$type] = array_merge($assign['aListDashboard']['aSyncData'][$type], $aDetailCount);
                }
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        $assign['bDashboard'] = empty($assign['aErrors']) ? true : false;
        $assign['bTableDisplay'] = true;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_DASHBOARD_TABLE,
            'assign' => $assign,
        );
    }


    /**
     * execute the ajax request to get the search result from MC API
     *
     * @param array $aPost
     * @return array
     */
    private function updateSearch(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array('aResult' => array());

        try {
            // check if a list is already activated and used
            $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);

            if (!empty($aActiveList[0])) {
                $sListId = $aActiveList[0]['id'];
                $sListName = $aActiveList[0]['name'];
                $sStoreId = $aActiveList[0]['store_id'];
                $sDataType = \Tools::getValue('bt_search_data_type');
                $sEltId = \Tools::getValue('bt_search_elt_id');
                $iLangId = \Tools::getValue('bt_elt_lang_id');

                if (empty($iLangId)) {
                    $iLangId = \Configuration::get('PS_LANG_DEFAULT');
                }

                // use case - the data type is empty
                if (!$sDataType) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The data type of the search query is empty', 'AdminUpdate') . '.', 5040);
                }
                // use case - the element ID is empty
                if (!$sEltId) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The element ID of the search query is empty', 'AdminUpdate') . '.', 5041);
                }

                // define the search type
                $sType = ($sDataType == 'member' || $sDataType == 'mergefield') ? 'list' : 'store';

                $assign['sDataType'] = $sDataType;
                $assign['sEltId'] = $sEltId;
                $assign['sSearchType'] = $sType;
                $assign['iLangId'] = $iLangId;
                $assign['aResult'] = \MCE\Chimp\Facade::search($sType, $sListId, $sStoreId, $sDataType, $sEltId, $iLangId);
                $assign['aQueryParams'] = $GLOBALS['MCE_REQUEST_PARAMS'];
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('There isn\'t any active list right now! Please select a user list before trying to check your synchronized data. The same for the e-commerce data synchronisation: the feature must have been activated and the first synching must have been done before any checks can be made here.', 'AdminUpdate') . '.', 5042);
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        $assign['sURI'] = \MCE\Tools::truncateUri(array('&iPage', '&sAction'));
        $assign['bSearch'] = empty($assign['aErrors']) ? true : false;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_SEARCH_UPDATE,
            'assign' => $assign,
        );
    }


    /**
     * update NL export settings
     *
     * @param array $aPost
     * @return array
     */
    private function updateNewsletterExportSettings(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = array();

        try {
            $sUserType = \Tools::getValue('bt_customer_export_type');
            if (!\Configuration::updateValue('MCE_CUST_TYPE_EXPORT', $sUserType)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the update of subscribers export type', 'AdminUpdate') . '.', 5060);
            }

            // check the language for newsletter subscribers export
            $iNewsletterExportLanguage = \Tools::getValue('bt_user_language');
            if (!\Configuration::updateValue('MCE_NL_MODULE_LANG', $iNewsletterExportLanguage)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the update of the language to be taken into account for the export of subscribers', 'AdminUpdate') . '.', 5061);
            }

            $_POST['type'] = 'newsletter';
            $sTpl = \Tools::getValue('sTpl');
            $function = $sTpl == 'popup' ? 'syncForm' : 'newsletterConfig';

        } catch (\Exception $e) {
            $aData['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to sync form popup or to just update settings and display the NL config settings
        $aDisplay = \MCE\AdminDisplay::create()->run($function);

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $aData);

        return $aDisplay;
    }


    /**
     * update customers / product catalog / orders / newsletter status
     *
     * @param array $aPost
     * @return array
     */
    private function updateSyncStatus(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            // use case - get the sync type
            if (!\Tools::getIsset('bt_sync_type')) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The sync type has not been passed as argument', 'AdminUpdate') . '.', 5080);
            }

            $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
            $sListId = $aActiveList[0]['id'];
            $sSyncType = \Tools::getValue('bt_sync_type');

            // update the sync type status to 1 => finished
            \MCE\Dao::updateSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType, array('sync' => 1));
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }
        $assign['bUpdate'] = empty($assign['aErrors']) ? true : false;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);
        $assign['sSyncType'] = $sSyncType;

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_SYNC_UPD,
            'assign' => $assign,
        );
    }


    /**
     * update Module signup form settings
     *
     * @param array $aPost
     * @return array
     */
    private function updateSignupFormModule(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = array();

        try {
            // check if we use a NL module
            if (\Tools::getIsset('bt_use_nl_module')) {
                $bNewsletterModule = \Tools::getValue('bt_use_nl_module');
                if (!\Configuration::updateValue('MCE_NL_MODULE', $bNewsletterModule)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter block module use" option update', 'AdminUpdate') . '.', 5101);
                }

                // check the NL module selected
                $sNewsletterSelectModule = \Tools::getValue('bt_nl_module');
                if (!\Configuration::updateValue('MCE_NL_SELECT_MODULE', $sNewsletterSelectModule)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter block module selection" option update', 'AdminUpdate') . '.', 5102);
                }

                // check the NL module form submit
                $sNewsletterFormSubmit = \Tools::getValue('bt_nl_form_submit');
                if (!empty($sNewsletterFormSubmit)) {
                    if (!\Configuration::updateValue('MCE_NL_MODULE_SUBMIT', $sNewsletterFormSubmit)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter form submit" option update', 'AdminUpdate') . '.', 5103);
                    }
                }

                // check the NL module form email field
                $sNewsletterFormField = \Tools::getValue('bt_nl_form_email');
                if (!empty($sNewsletterFormField)) {
                    if (!\Configuration::updateValue('MCE_NL_MODULE_EMAIL_FIELD', $sNewsletterFormField)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter form e-mail field" option update', 'AdminUpdate') . '.', 5104);
                    }
                }

                // check the NL module form ajax mode
                $sNewsletterFormAjax = \Tools::getValue('bt_nl_form_ajax');
                if (!\Configuration::updateValue('MCE_NL_MODULE_AJAX', $sNewsletterFormAjax)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter form submitted via ajax request" option update', 'AdminUpdate') . '.', 5105);
                }

                // check the NL module form selector mode
                $sNewsletterFormSelector = \Tools::getValue('bt_nl_form_selector');
                if (!empty($sNewsletterFormSelector)) {
                    if (!\Configuration::updateValue('MCE_NL_MODULE_SELECTOR', $sNewsletterFormSelector)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "newsletter block HTML element" option update', 'AdminUpdate') . '.', 5106);
                    }
                }
            }

        } catch (\Exception $e) {
            $aData['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to sync form popup or to just update settings and display the NL config settings
        $aDisplay = \MCE\AdminDisplay::create()->run('signupForm');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $aData);

        return $aDisplay;
    }


    /**
     * update MC signup form settings
     *
     * @param array $aPost
     * @return array
     */
    private function updateSignupFormMailchimp(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = array();

        try {
            // test if we use the MC signup form
            $bUseSignupForm = \Tools::getValue('bt_mc_signup_form_use');

            if (!\Configuration::updateValue('MCE_SIGNUP', $bUseSignupForm)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up form use" option update', 'AdminUpdate') . '.', 5090);
            }

            // the way of displaying the signup form
            $sSignupDisplayType = \Tools::getValue('bt_mc_signup_form_display');

            if (!empty($sSignupDisplayType)) {
                if (!\Configuration::updateValue('MCE_SIGNUP_DISPLAY', $sSignupDisplayType)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up form display type" option update', 'AdminUpdate') . '.', 5093);
                }
            } elseif (!empty($bUseSignupForm)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You have activated the MailChimp sign-up form without selecting a way to display it', 'AdminUpdate') . '.', 5104);
            }

            // get MC signup form code by language
            $aSignupFormCode = \Tools::getValue('bt_mc_signup_code');

            // detect if the field was empty
            $empty  = true;

            if (!empty($aSignupFormCode)) {
                foreach ($aSignupFormCode as &$sHtml) {
                    if (!empty($sHtml)) {
                        $sHtml = htmlspecialchars($sHtml);
                        $empty = false;
                    }
                }

                if (!\Configuration::updateValue('MCE_SIGNUP_HTML', serialize($aSignupFormCode))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up HTML code" update', 'AdminUpdate') . '.', 5091);
                }
            }

            if ($empty) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t fill in the embedded HTML code in any languages', 'AdminUpdate') . '.', 5092);
            }

            // the way of displaying the signup form
            $sSignupDisplayType = \Tools::getValue('bt_mc_signup_form_display');

            if (!empty($sSignupDisplayType)) {
                if (!\Configuration::updateValue('MCE_SIGNUP_DISPLAY', $sSignupDisplayType)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up form display type" option update', 'AdminUpdate') . '.', 5093);
                }
            } elseif (!empty($bUseSignupForm)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You have activated the MailChimp sign-up form without selecting a way to display it', 'AdminUpdate') . '.', 5104);
            }

            // get MC signup form  - dedicated page link label by language
            $aSignupLinkLabel = \Tools::getValue('bt_mc_signup_page_link_label');

            if (!empty($aSignupLinkLabel)) {
                if (!\Configuration::updateValue('MCE_SIGNUP_LINK_LABEL', serialize($aSignupLinkLabel))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up HTML code" update', 'AdminUpdate') . '.', 5094);
                }
            }

            // get MC signup form  - popup form number of displaying it
            $iSignupPopupTimes = \Tools::getValue('bt_mc_signup_form_times');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TIMES', $iSignupPopupTimes)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "number of display of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5095);
            }

            // get MC signup form  - popup => type of pages
            $aSignupDedicatedPages = \Tools::getValue('bt_mc_signup_form_pages');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_PAGES', (!empty($aSignupDedicatedPages) ? serialize($aSignupDedicatedPages) : ''))) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "pages of display of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5096);
            }

            // get MC signup form  - popup form number of displaying it
            $bSignupNotDisplayButton = \Tools::getValue('bt_mc_signup_form_not_display');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_NOT_DISPLAY', $bSignupNotDisplayButton)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "MailChimp sign-up HTML code" update', 'AdminUpdate') . '.', 5097);
            }

            // get MC signup form  - popup text
            $aSignupPopupText = \Tools::getValue('bt_mc_signup_popup_text');

            if (!empty($aSignupPopupText)) {
                foreach ($aSignupPopupText as &$sHtml) {
                    if (!empty($sHtml)) {
                        $sHtml = htmlspecialchars($sHtml);
                    }
                }
                if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TEXT', serialize($aSignupPopupText))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "promotional text of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5098);
                }
            }

            // get MC signup form  - popup image
            $bPopupimage = \Tools::getValue('bt_mc_signup_popup_image');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_IMAGE', $bPopupimage)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during MailChimp sign-up pop-up image update', 'AdminUpdate') . '.', 5097);
            }

            if ($bPopupimage) {
                // get MC signup form  - popup image by language
                foreach (\Language::getLanguages() as $aLanguage) {
                    $sFieldName = 'bt_signup_popup_image_' . $aLanguage['id_lang'];
                    $sDestination = _MCE_PATH_ROOT . _MCE_PATH_VIEWS . _MCE_PATH_IMG . 'signup-popup_' . $aLanguage['id_lang'] . '_shop_' . \BTMailchimpEcommerce::$iShopId . '.jpg';
                    $sError = \MCE\Tools::checkAndUploadImage($sFieldName, _MCE_IMG_MAX_SIZE, $sDestination);

                    if (!empty($sError)) {
                        throw new \Exception($sError, 5099);
                    }
                }

                // get MC signup form  - popup text valign
                $sPopupTextValign = \Tools::getValue('bt_mc_signup_popup_text_valign');

                if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TEXT_VALIGN', $sPopupTextValign)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "vertical alignment of promotional text" option update', 'AdminUpdate') . '.', 5102);
                }

                // get MC signup form  - popup text halign
                $sPopupTextHalign = \Tools::getValue('bt_mc_signup_popup_text_halign');

                if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TEXT_HALIGN', $sPopupTextHalign)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "horizontal alignment of promotional text" option update', 'AdminUpdate') . '.', 5103);
                }

                // get MC signup form  - popup custom H text middle
                $iCustomHTextMiddle = \Tools::getValue('bt_mc_signup_popup_text_halign_custom');

                if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TEXT_HALIGN_CUSTOM', $iCustomHTextMiddle)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "adjust the horizontal central alignment" option update', 'AdminUpdate') . '.', 5103);
                }

                // get MC signup form  - popup custom V text middle
                $iCustomVTextMiddle = \Tools::getValue('bt_mc_signup_popup_text_valign_custom');

                if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_TEXT_VALIGN_CUSTOM', $iCustomVTextMiddle)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "adjust the vertical central alignment" option update', 'AdminUpdate') . '.', 5104);
                }
            }

            // get MC signup form  - popup width
            $iPopupWidth = \Tools::getValue('bt_mc_signup_popup_width');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_WIDTH', $iPopupWidth)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "width of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5100);
            }

            // get MC signup form  - popup height
            $iPopupHeight = \Tools::getValue('bt_mc_signup_popup_height');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_HEIGHT', $iPopupHeight)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "height of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5101);
            }

            // get MC signup form  - popup height
            $iPopupHeight = \Tools::getValue('bt_mc_signup_popup_height');

            if (!\Configuration::updateValue('MCE_SIGNUP_POPUP_HEIGHT', $iPopupHeight)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "height of MailChimp sign-up pop-up form" option update', 'AdminUpdate') . '.', 5102);
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to sync form popup or to just update settings and display the NL config settings
        $aDisplay = \MCE\AdminDisplay::create()->run('tabs');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $aData);
        
        return $aDisplay;
    }


    /**
     * update ecommerce settings
     *
     * @param array $aPost
     * @return array
     */
    private function updateEcommerce(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = array();

        try {
            // test if we use the MC ecommerce feature form
            $bUseEcommerce = \Tools::getValue('bt_mc_ecommerce_use');

            if (!\Configuration::updateValue('MCE_ECOMMERCE_ACTIVE', $bUseEcommerce)) {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "e-commerce feature use" option update', 'AdminUpdate') . '.', 5110);
            }

            if ($bUseEcommerce) {
                // register the product description
                $iProductDesc = \Tools::getValue('bt_prod_desc');

                if (!\Configuration::updateValue('MCE_PROD_DESC_TYPE', $iProductDesc)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "product description" option update', 'AdminUpdate') . '.', 5111);
                }

                // register the product image format
                $sImageFormat = \Tools::getValue('bt_prod_img_size');

                if (!\Configuration::updateValue('MCE_PROD_IMG_FORMAT', $sImageFormat)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "product image format" option update', 'AdminUpdate') . '.', 5112);
                }

                // register the product vendor type
                $sProductVendor = \Tools::getValue('bt_prod_vendor_type');

                if (!\Configuration::updateValue('MCE_PROD_VENDOR_TYPE', $sProductVendor)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "how to define the product vendor" option update', 'AdminUpdate') . '.', 5113);
                }

                // register the category label format
                $iProductDesc = \Tools::getValue('bt_cat_label_format');

                if (!\Configuration::updateValue('MCE_CAT_LABEL_FORMAT', $iProductDesc)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "category wording" option update', 'AdminUpdate') . '.', 5114);
                }

                // register the category label format
                $bProductTax = \Tools::getValue('bt_mc_product_tax');

                if (!\Configuration::updateValue('MCE_PRODUCT_TAX', $bProductTax)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the export tax option update', 'AdminUpdate') . '.', 5122);
                }

                // register the number of product per cart
//                $iMaxProduct = \Tools::getValue('bt_cart_max_prod');
//
//                if (!\Configuration::updateValue('MCE_CART_MAX_PROD', $iMaxProduct)) {
//                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "max products per cart" option update', 'AdminUpdate') . '.', 5115);
//                }

                // use case - order status
                if (!empty($aPost['bt_order_status'])) {
                    // update status selection
                    if (!\Configuration::updateValue('MCE_STATUS_SELECTION', serialize($aPost['bt_order_status']))) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "order statuses selection" update', 'AdminUpdate'), 5116);
                    }
                } else {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t select any order status', 'AdminUpdate'), 5117);
                }

                // test if we use the cron
                $bUseCron = \Tools::getValue('bt_use_cron');

                if (!\Configuration::updateValue('MCE_ECOMMERCE_CRON', $bUseCron)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "CRON task use for e-commerce synchronizations" option update', 'AdminUpdate') . '.', 5118);
                }

                if ($bUseCron) {
                    // register the number of item per cycle
                    $iItemCycle = \Tools::getValue('bt_items_cron_cycle');

                    if (!\Configuration::updateValue('MCE_ECOMMERCE_CRON_CYCLE', $iItemCycle)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "number of items per cycle" option update', 'AdminUpdate') . '.', 5119);
                    }
                }

                // get product languages/currencies
                $aProdLang = \Tools::getValue('bt_prod_languages');

                if (empty($aProdLang)) {
                    $aProdLang = array(\Configuration::get('PS_LANG_DEFAULT'));
                }
                if (!\Configuration::updateValue('MCE_PROD_LANG', serialize($aProdLang))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during "product languages" option update', 'AdminUpdate') . '.', 5120);
                }

                // detect which mode to sync members / products / customers / carts / orders
                foreach (array('member','product','customer','cart','order') as $type) {
                    $sTypeSyncMode = \Tools::getValue('bt_'. $type .'_sync_mode');

                    if (!\Configuration::updateValue('MCE_'. \tools::strtoupper($type) .'_SYNC_MODE', $sTypeSyncMode)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the "data synchronization mode" option update', 'AdminUpdate') . '.', 5121);
                    }
                }

                // get the current list
                $aActiveList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);

                if (!empty($aActiveList[0])) {
                    $sListId = $aActiveList[0]['id'];
                    $sStoreId = $aActiveList[0]['store_id'];
                    $sStoreName = $aActiveList[0]['store_name'];
                    $aActiveList = $aActiveList[0];
                }

                // check if the store already exist or not
                if (empty($sStoreId)
                    && empty($sStoreName)
                ) {
                    // instantiate the MC's controller
                    $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                    // define shop mail and domain
                    $sShopMail = \Configuration::get('PS_SHOP_EMAIL');
                    $sDomain = \Context::getContext()->shop->domain;

                    // get params for store creation
                    $id_currency = (int)\Configuration::get('PS_CURRENCY_DEFAULT');
                    $oStoreCurrency = new \Currency(($id_currency == 0) ? 1 : $id_currency);
                    $sStoreId = 'MC-' . \BTMailchimpEcommerce::$iShopId . '-' . \Tools::strtoupper($oStoreCurrency->iso_code) .'-'. $sListId;
                    $sStoreName = \Context::getContext()->shop->name;

                    $aResult = $oMcCtrl->store->add(
                        $sStoreId,
                        $sListId,
                        $sStoreName,
                        $oStoreCurrency->iso_code,
                        array(
                            'platform' => 'PrestaShop',
                            'domain' => $sDomain,
                            'email_address' => $sShopMail,
                        )
                    );
                    // get the site script information
                    $site_script = $aResult['connected_site']['site_script'];

                    // verify the script
                    $aResult = $oMcCtrl->connectedSites->action($sStoreId);

                    // encode the fragment code
                    if (isset($site_script['fragment'])) {
                        $site_script['fragment'] = htmlentities($site_script['fragment']);
                    }

                    // use case - detect which store has been created first, and get its script URL and code to avoid the front-office to be overloaded for nothing.
                    $aListData = array(
                        'mc' => array(
                            'site_script' => $site_script
                        )
                    );

                    // get the local data
                    if ($aActiveList['data'] !== null
                        && is_string($aActiveList['data'])
                        && !empty($aActiveList['data'])
                    ) {
                        $aActiveList['data'] = unserialize($aActiveList['data']);
                        if (isset($aActiveList['data']['mc'])
                            && isset($aActiveList['mc'])
                        ) {
                            $aListData['mc'] = array_merge($aActiveList['data']['mc'], $aListData['mc']);
                        } else {
                            $aListData = array_merge($aActiveList['data'], $aListData);
                        }
                    }

                    \MCE\Dao::updateList($sListId, array(
                        'active' => 1,
                        'store_id' => $sStoreId,
                        'store_name' => $sStoreName,
                        'data' => $aListData
                    ));
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // require admin configure class - to factorise
        require_once(_MCE_PATH_LIB_ADMIN . 'AdminDisplay.php');

        // get run of admin display in order to display the ecommerce settings
        $aDisplay = \MCE\AdminDisplay::create()->run('ecommerce');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], array(
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ), $aData);

        return $aDisplay;
    }


    /**
     * execute the ajax request to update the voucher create or updated
     *
     * @param array $aPost
     * @return array
     */
    private function updateVoucher(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();
        // get stored vouchers
        $aVouchers = !empty(\BTMailchimpEcommerce::$conf['MCE_VOUCHERS']) ? \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'] : array();

        try {
            // get the current automation to associate the voucher settings
            $sAutomation = \Tools::getValue('bt_voucher_automation');
            $bNew = \Tools::getValue('bt_new_voucher');

            if (!empty($sAutomation)) {
                // translate to get the display name of predefined automation vouchers
                \MCE\Tools::translateAutomationVouchers();

                // use case - detect if we update an automation voucher as custom voucher
                if ($sAutomation == 'other') {
                    $sName = \Tools::getValue('bt_custom_automation');

                    if (!empty($sName)) {
                        $sAutomation = \Tools::strtolower(
                            str_replace(
                                ' ',
                                '-',
                                str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '"', '\'', '!', '<', '>', ',', ';', '?', '=', '+', '(', ')', '@', '#', '�', '{', '}', '_', '$', '%', ':'), '', $sName))
                        );
                    } else {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You have selected the \'Other\' value as e-mails campaign type but you didn\'t fill in the custom name of this campaign type', 'AdminUpdate') . '.', 5500);
                    }
                } else {
                    $sName = isset($GLOBALS['MCE_AUTOMATION'][$sAutomation]) ? $GLOBALS['MCE_AUTOMATION'][$sAutomation] : $sAutomation;
                }
                // detect if the type of automation voucher has already been configured
                if ($bNew
                    && isset($aVouchers[$sAutomation])
                ) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You have already configured this type of campaign. You cannot configure many vouchers for the same campaign type', 'AdminUpdate') . '.', 5501);
                }

                // use case - it's a predefined automation
                $aVouchers[$sAutomation]['name'] = $sName;
                $aVouchers[$sAutomation]['type'] = $sAutomation;

                // get the prefix code
                $sPrefixCode = \Tools::getValue('bt_voucher_prefix_code');
                $aVouchers[$sAutomation]['prefix'] = $sPrefixCode;

                // get the discount type
                $sDiscountType = \Tools::getValue('bt_discount_type');
                if (empty($sDiscountType)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t select any discount type', 'AdminUpdate') . '.', 5503);
                }
                $aVouchers[$sAutomation]['discount'] = $sDiscountType;

                // use case - percentage
                if ($sDiscountType == 'percentage') {
                    $sVoucherAmount = \Tools::getValue('bt_voucher_percent');
                    if (empty($sVoucherAmount)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t define any amount (percentage)', 'AdminUpdate') . '.', 5504);
                    }
                }
                // use case - amount
                if ($sDiscountType == 'amount') {
                    $sVoucherAmount = \Tools::getValue('bt_voucher_amount');
                    if (empty($sVoucherAmount)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t define any amount (monetary value)', 'AdminUpdate') . '.', 5504);
                    }
                    $iCurrencyId = \Tools::getValue('bt_currency_id');
                    if (empty($iCurrencyId)) {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t select the voucher currency', 'AdminUpdate') . '.', 5505);
                    }
                    $aVouchers[$sAutomation]['currency'] = $iCurrencyId;

                    // use case if the type tax exist
                    if (\Tools::getIsset('bt_voucher_tax_id')) {
                        $aVouchers[$sAutomation]['tax'] = \Tools::getValue('bt_voucher_tax_id');
                    }
                }
                $aVouchers[$sAutomation]['amount'] = $sVoucherAmount;

                // get the voucher minimum
                $aVouchers[$sAutomation]['minimum'] = \Tools::getValue('bt_voucher_minimum');

                // get the validity
                $iValidity = \Tools::getValue('bt_voucher_validity');
                if (empty($iValidity)) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t define the voucher validity period', 'AdminUpdate') . '.', 5506);
                }
                $aVouchers[$sAutomation]['validity'] = $iValidity;

                // get the highlight
                $bHighlight = \Tools::getValue('bt_voucher_highlight');
                $aVouchers[$sAutomation]['highlight'] = $bHighlight == true ? true : false;

                // get the cumulative with others vouchers
                $bCumulative = \Tools::getValue('bt_cumulative_other');
                $aVouchers[$sAutomation]['cumulativeOther'] = $bCumulative == true ? true : false;

                // get the cumulative with others reduction
                $bCumulativeReduc = \Tools::getValue('bt_cumulative_reduc');
                $aVouchers[$sAutomation]['cumulativeReduc'] = $bCumulativeReduc == true ? true : false;

                // use case - check language for desc
                foreach (\Language::getLanguages() as $nKey => $aLang) {
                    if (empty($aPost['bt_tab_voucher_name'][$aLang['id_lang']])) {
                        // If the merchant did set a value we use the value of the 1st field
                        $aVouchers[$sAutomation]['langs'][$aLang['id_lang']] = strip_tags($aPost['bt_tab_voucher_name'][1]);
                    } else {
                        $aVouchers[$sAutomation]['langs'][$aLang['id_lang']] = strip_tags($aPost['bt_tab_voucher_name'][$aLang['id_lang']]);
                    }
                }
                // use case - check categories selected
                if (\Tools::getIsset('bt_category_box')) {
                    $aVouchers[$sAutomation]['categories'] = \Tools::getValue('bt_category_box');
                } else {
                    $aVouchers[$sAutomation]['categories'] = array();
                }

                if (!\Configuration::updateValue('MCE_VOUCHERS', serialize($aVouchers))) {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('An error occurred during the vouchers update', 'AdminUpdate') . '.', 5508);
                }
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('You didn\'t select any valid type of campaign', 'AdminUpdate') . '.', 5509);
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // get configuration options
        \MCE\Tools::getConfig();

        // force xhr mode activated
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        $assign['bUpdate'] = empty($assign['aErrors']) ? true : false;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_VOUCHER_UPD,
            'assign' => $assign,
        );
    }


    /**
     * execute the ajax request to synchronize the newsletter lists
     *
     * @param array $aPost
     * @return array
     */
    private function updateNewsletterSynching(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            $sSyncType = \Tools::getValue('sSyncType');
            $sListId = \Tools::getValue('sListId');
            $iStep = \Tools::getValue('iStep');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');
            $bOldListSync = \Tools::getValue('bOldListSync');

            if (($sListId != false && is_string($sListId))
                && ($iStep !== false && is_numeric($iStep))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
            ) {
                $_POST['sSyncType'] = $sSyncType;
                $_POST['sListId'] = $sListId;
                $_POST['iStep'] = $iStep;
                $_POST['iFloor'] = $iFloor + $iStep;
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;

                // detect if need to create the nl e-mail list status for this store during the first step
                if ($iFloor == 0) {
                    // detect if need to create the nl e-mail list sync status for this list
                    $aNewsletterSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);

                    if (empty($aNewsletterSyncStatus)) {
                        $bResult = \MCE\Dao::createSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);
                    }

                    // update the item number per cycle
                    \Configuration::updateValue('MCE_MEMBER_PER_CYCLE', $iStep);
                }

                // get newsletter e-mails by range
                // use case - classic sync, local users of the shop
                if (!$bOldListSync) {
                    $aMails = \MCE\Dao::getUsers(\BTMailchimpEcommerce::$iShopId, false, \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'], \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_LANG'], $iFloor, $iStep);
                // use case - migration of old list members
                } else {
                    $aMails = \MCE\Chimp\Facade::getUsersFromLists(\BTMailchimpEcommerce::$conf['MCE_OLD_CONFIG'], $iFloor, $iStep);
                }

                if (!empty($aMails)) {
                    // loop on e-mails to prepare them to the batch operation in MailChimp
                    foreach ($aMails as $iKey => $aMail) {
                        // format and send
                        $bResult = \MCE\Chimp\Facade::processMember($sListId, $aMail, \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_LANG'], false, 'put', 'cron');
                    }

                    // get the content stored for creating batches
                    $aCurrentMails = \MCE\Chimp\Facade::getItemsForBatches();

                    // get the serialized content stored to mapping batches and shop's data
                    $aSerialized = \MCE\Chimp\Facade::getSerializedForBatches();
                    
                    // use case - synching the member data
                    if (!empty($aCurrentMails)) {
                        // instantiate the MC's controller
                        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                        // post the current range of e-mail addresses by creating a batch in MC
                        $aResult = $oMcCtrl->batches->add(array('operations' => $aCurrentMails));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'manual','newsletter', $iFloor, $iStep, $aSerialized);
                        }
                    }
                }
//                } else {
//                    $sMsg = \BTMailchimpEcommerce::$oModule->l('The shop didn\'t return a valid list of user e-mails for the last cycle', 'AdminUpdate');
//                    throw new \Exception($sMsg, 5070);
//                }

                $assign['status'] = 'ok';
                $assign['counter'] = $iFloor + $iStep;
                $assign['process'] = ($iProcess + $iStep);

                // last step
                if ($assign['counter'] >= $iTotal) {
                    // define the newsletter feature active
                    \Configuration::updateValue('MCE_NL_ACTIVE', 1);
                    // set the sync type status to "finished"
                    $bResult = \MCE\Dao::updateSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType, array('sync' => 2));

                    // use case of migration
                    if (!empty($bOldListSync)) {
                        \Configuration::updateValue('MCE_SYNC_OLD_LISTS_FLAG', 1);
                    }
                }
            } else {
                $sMsg = \BTMailchimpEcommerce::$oModule->l('The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!', 'AdminUpdate') . '.' . "<br/>";
                $sMsg .= \BTMailchimpEcommerce::$oModule->l('List ID', 'AdminUpdate') . ': ' . $sListId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Step', 'AdminUpdate') . ': ' . $iFloor . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Total e-mails to process', 'AdminUpdate') . ': ' . $iTotal . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Stock the real number of e-mails to process', 'AdminUpdate') . ': ' . $iProcess . "<br/>";
                throw new \Exception($sMsg, 5071);
            }
        } catch (\Exception $e) {
            // delete the batches created in our table when MC's API returns an error.
            \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('id' => $sListId, 'type' => $sSyncType, 'mode' => 'manual'));

            $assign['status'] = 'ko';
            $assign['error'][] = array(
                'msg' => ((method_exists($e, 'getFriendlyMessage')) ? $e->getFriendlyMessage() : $e->getMessage()),
                'code' => $e->getCode()
            );
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_ITEM_SYNCHRO,
            'assign' => array('json' => \MCE\Tools::jsonEncode($assign)),
        );
    }


    /**
     * execute the ajax request to synchronize customer list
     *
     * @param array $aPost
     * @return array
     */
    private function updateCustomerSynching(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            $sSyncType = \Tools::getValue('sSyncType');
            $sListId = \Tools::getValue('sListId');
            $sStoreId = \Tools::getValue('sStoreId');
            $iStep = \Tools::getValue('iStep');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');

            if (($sListId != false && is_string($sListId))
                && ($sStoreId != false && is_string($sStoreId))
                && ($iStep !== false && is_numeric($iStep))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
            ) {
                $_POST['sSyncType'] = $sSyncType;
                $_POST['sListId'] = $sListId;
                $_POST['sStoreId'] = $sStoreId;
                $_POST['iStep'] = $iStep;
                $_POST['iFloor'] = $iFloor + $iStep;
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;

                // detect if need to create the catalog status for this store during the first step
                if ($iFloor == 0) {
                    // detect if need to create the customer e-mail list sync status for this list
                    $aCustomerSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);

                    if (empty($aCustomerSyncStatus)) {
                        $bResult = \MCE\Dao::createSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);
                    }

                    // update the item number per cycle
                    \Configuration::updateValue('MCE_CUST_PER_CYCLE', $iStep);
                }

                // get customers by range
                $aCustomers = \MCE\Dao::getCustomerData(false, \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'], $iFloor, $iStep);

                if (!empty($aCustomers)) {
                    // loop on customers to prepare them to the batch operation in MailChimp
                    foreach ($aCustomers as $iKey => $aCustomer) {
                        // format and send
                        $bResult = \MCE\Chimp\Facade::processMember($sListId, $aCustomer, $aCustomer['id_lang'], false, 'put', 'cron');

                        // format and send
                        $bResult = \MCE\Chimp\Facade::processCustomer($sListId, $sStoreId, (new \Customer($aCustomer['id'])), $aCustomer['id_lang'], 'cron', 'update');
                    }

                    // get the content stored for creating batches
                    $aCurrentCustomers = \MCE\Chimp\Facade::getItemsForBatches();

                    // get the serialized content stored to mapping batches and shop's data
                    $aSerialized = \MCE\Chimp\Facade::getSerializedForBatches();

                    if (!empty($aCurrentCustomers)) {
                        // instantiate the MC's controller
                        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                        // post the current range of products by creating a batch in MC
                        $aResult = $oMcCtrl->batches->add(array('operations' => $aCurrentCustomers));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'manual','customer', $iFloor, $iStep, $aSerialized);
                        }
                    }
                } else {
                    $sMsg = \BTMailchimpEcommerce::$oModule->l('The shop didn\'t return a valid list of customers for the last cycle', 'AdminUpdate');
                    throw new \Exception($sMsg, 5200);
                }

                $assign['status'] = 'ok';
                $assign['counter'] = $iFloor + $iStep;
                $assign['process'] = ($iProcess + $iStep);

                // last step
                if ($assign['counter'] >= $iTotal) {
                    $bResult = \MCE\Dao::updateSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType, array('sync' => 2));
                }
            } else {
                $sMsg = \BTMailchimpEcommerce::$oModule->l('The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!', 'AdminUpdate') . '.' . "<br/>";
                $sMsg .= \BTMailchimpEcommerce::$oModule->l('Shop ID', 'AdminUpdate') . ': ' . \BTMailchimpEcommerce::$iShopId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('List ID', 'AdminUpdate') . ': ' . $sListId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Step', 'AdminUpdate') . ': ' . $iFloor . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Total customers to process', 'AdminUpdate') . ': ' . $iTotal . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Stock the real number of customers to process', 'AdminUpdate') . ': ' . $iProcess . "<br/>";
                throw new \Exception($sMsg, 5201);
            }
        } catch (\Exception $e) {
            // delete the batches created in our table when MC's API returns an error.
            \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('id' => $sListId, 'type' => $sSyncType, 'mode' => 'manual'));

            $assign['status'] = 'ko';
            $assign['error'][] = array(
                'msg' => ((method_exists($e, 'getFriendlyMessage')) ? $e->getFriendlyMessage() : $e->getMessage()),
                'code' => $e->getCode()
            );
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_ITEM_SYNCHRO,
            'assign' => array('json' => \MCE\Tools::jsonEncode($assign)),
        );
    }


    /**
     * execute the ajax request to synchronize product catalog
     *
     * @param array $aPost
     * @return array
     */
    private function updateProductSynching(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            $sSyncType = \Tools::getValue('sSyncType');
            $sListId = \Tools::getValue('sListId');
            $sStoreId = \Tools::getValue('sStoreId');
            $iStep = \Tools::getValue('iStep');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');
            $sNewSyncType = \Tools::getValue('sNewSyncType');
            $iDescType = \BTMailchimpEcommerce::$conf['MCE_PROD_DESC_TYPE'];
            $sImageSize = \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'];
            $sVendorType = \BTMailchimpEcommerce::$conf['MCE_PROD_VENDOR_TYPE'];

            if (($sListId != false && is_string($sListId))
                && ($sStoreId != false && is_string($sStoreId))
                && ($iStep !== false && is_numeric($iStep))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
                && ($sNewSyncType !== false && is_string($sNewSyncType))
            ) {
                $_POST['sSyncType'] = $sSyncType;
                $_POST['sListId'] = $sListId;
                $_POST['sStoreId'] = $sStoreId;
                $_POST['iStep'] = $iStep;
                $_POST['iFloor'] = $iFloor + $iStep;
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;
                $_POST['sNewSyncType'] = $sNewSyncType;

                if ($iFloor == 0) {
                    // detect if need to create the customer sync status for this store
                    $aProductSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);

                    if (empty($aProductSyncStatus)) {
                        $bResult = \MCE\Dao::createSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);
                    }

                    // update the item number per cycle
                    \Configuration::updateValue('MCE_PROD_PER_CYCLE', $iStep);
                }

                // get products by range
                $aProducts = \MCE\Dao::getProductIds($iFloor, $iStep);

                if (!empty($aProducts)) {
                    // define the roduct method
                    $sMethod = $sNewSyncType == 'new' ? 'add' : 'update';

                    // define the MC store currency to the context in order to calculate the product price according to the MC currency
                    $oCurrentCurrency = \Context::getContext()->currency;

                    $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($sStoreId, 'currency');
                    $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                    // set the matching currency
                    \Context::getContext()->currency = new \Currency($iMCCurrencyId);

                    // loop on products to prepare them to the batch operation in MailChimp
                    foreach ($aProducts as $iKey => $aProduct) {
                        // loop on product languages to get the same product in all the active languages and currencies
                        foreach (\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] as $id_lang) {
                            // format and send
                            $bResult = \MCE\Chimp\Facade::processProduct($sListId, $sStoreId, $aProduct['id'], $id_lang, 'cron', $sMethod, '', false, false);
                        }
                    }

                    // set the previous currency
                    \Context::getContext()->currency = $oCurrentCurrency;

                    // get the content stored for creating batches
                    $aCurrentProducts = \MCE\Chimp\Facade::getItemsForBatches();

                    // get the serialized content stored to mapping batches and shop's data
                    $aSerialized = \MCE\Chimp\Facade::getSerializedForBatches();

                    if (!empty($aCurrentProducts)) {
                        // instantiate the MC's controller
                        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                        // post the current range of products by creating a batch in MC
                        $aResult = $oMcCtrl->batches->add(array('operations' => $aCurrentProducts));

                        if (!empty($aResult['id'])) {
                            \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'manual','product', $iFloor, $iStep, $aSerialized);
                        }
                    }
                } else {
                    $sMsg = \BTMailchimpEcommerce::$oModule->l('The shop didn\'t return a valid list of products for the last cycle', 'AdminUpdate');
                    throw new \Exception($sMsg, 5300);
                }

                $assign['status'] = 'ok';
                $assign['counter'] = $iFloor + $iStep;
                $assign['process'] = ($iProcess + $iStep);

                // last step
                if ($assign['counter'] >= $iTotal) {
                    $bResult = \MCE\Dao::updateSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType, array('sync' => 2));
                }
            } else {
                $sMsg = \BTMailchimpEcommerce::$oModule->l('The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!', 'AdminUpdate') . '.' . "<br/>";
                $sMsg .= \BTMailchimpEcommerce::$oModule->l('Shop ID', 'AdminUpdate') . ': ' . \BTMailchimpEcommerce::$iShopId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('List ID', 'AdminUpdate') . ': ' . $sListId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Step', 'AdminUpdate') . ': ' . $iFloor . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Total products to process', 'AdminUpdate') . ': ' . $iTotal . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Total products to process (without counting combinations)', 'AdminUpdate') . ': ' . $iTotal . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Stock the real number of products to process', 'AdminUpdate') . ': ' . $iProcess . "<br/>";
                throw new \Exception($sMsg, 5301);
            }
        } catch (\Exception $e) {
            // delete the batches created in our table when MC's API returns an error.
            \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('id' => $sListId, 'type' => $sSyncType, 'mode' => 'manual'));

            $assign['status'] = 'ko';
            $assign['error'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_ITEM_SYNCHRO,
            'assign' => array('json' => \MCE\Tools::jsonEncode($assign)),
        );
    }


    /**
     * execute the ajax request to synchronize past orders
     *
     * @param array $aPost
     * @return array
     */
    private function updateOrderSynching(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $assign = array();

        try {
            $sSyncType = \Tools::getValue('sSyncType');
            $sListId = \Tools::getValue('sListId');
            $sStoreId = \Tools::getValue('sStoreId');
            $iStep = \Tools::getValue('iStep');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');
            $sDateFrom = \Tools::getValue('sDateFrom');
            $sDateTo = \Tools::getValue('sDateTo');

            if (($sListId != false && is_string($sListId))
                && ($sStoreId != false && is_string($sStoreId))
                && ($iStep !== false && is_numeric($iStep))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
                && ($sDateFrom !== false && is_string($sDateFrom))
                && ($sDateTo !== false && is_string($sDateTo))
            ) {
                $_POST['sSyncType'] = $sSyncType;
                $_POST['sListId'] = $sListId;
                $_POST['sStoreId'] = $sStoreId;
                $_POST['iStep'] = $iStep;
                $_POST['iFloor'] = $iFloor + $iStep;
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;
                $_POST['sDateFrom'] = $sDateFrom;
                $_POST['sDateTo'] = $sDateTo;

                if ($iFloor == 0) {
                    // detect if need to create the order status for this list
                    $aOrderSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);

                    if (empty($aOrderSyncStatus)) {
                        $bResult = \MCE\Dao::createSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType);
                    }

                    // update the item number per cycle
                    \Configuration::updateValue('MCE_ORDER_PER_CYCLE', $iStep);
                }

                // get statuses selection
                $aSelection = !empty(\BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION']) ? unserialize(\BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION']) : \BTMailchimpEcommerce::$conf['MCE_STATUS_SELECTION'];

                // get matching orders
                $aOrders = \MCE\Dao::getOrdersIdByDate($sDateFrom, $sDateTo, \Configuration::get('PS_LANG_DEFAULT'), null, 'date_add', $aSelection, $iFloor, $iStep);

                if (!empty($aOrders)) {
                    // detect the currency code
                    $sCurrencyCode = \MCE\Chimp\Facade::getExplodeStoreId($sStoreId, 'currency');
                    $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                    // loop on order IDs to prepare them to the batch operation in MailChimp
                    foreach ($aOrders as $iKey => $iOrderId) {
                        // set the order data
                        $aOptions = array(
                            'iMCCurrencyId' => $iMCCurrencyId,
                            'sCurrencyCode' => $sCurrencyCode,
                        );

                        // format and send
                        $bResult = \MCE\Chimp\Facade::processOrder($sListId, $sStoreId, $iOrderId, 0, \Configuration::get('PS_LANG_DEFAULT'), 'cron', false, 'add', $aOptions);
                    }

                    // get the content stored for creating batches
                    $aCurrentOrders = \MCE\Chimp\Facade::getItemsForBatches();

                    // get the serialized content stored to mapping batches and shop's data
                    $aSerialized = \MCE\Chimp\Facade::getSerializedForBatches();

                    if (!empty($aCurrentOrders)) {
                        // instantiate the MC's controller
                        $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                        // post the current range of orders by creating a batch in MC
                        $aResult = $oMcCtrl->batches->add(array('operations' => $aCurrentOrders));

                        if (!empty($aResult['id'])) {
                            \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'manual','order', $iFloor, $iStep, $aSerialized);
                        }
                    }
                } else {
                    $sMsg = \BTMailchimpEcommerce::$oModule->l('The shop didn\'t return a valid list of orders for the last cycle', 'AdminUpdate');
                    throw new \Exception($sMsg, 5400);
                }

                $assign['status'] = 'ok';
                $assign['counter'] = $iFloor + $iStep;
                $assign['process'] = ($iProcess + $iStep);

                // last step
                if ($assign['counter'] >= $iTotal) {
                    $bResult = \MCE\Dao::updateSyncStatus(\BTMailchimpEcommerce::$iShopId, $sListId, $sSyncType, array('sync' => 2));
                }
            } else {
                $sMsg = \BTMailchimpEcommerce::$oModule->l('The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!', 'AdminUpdate') . '.' . "<br/>";
                $sMsg .= \BTMailchimpEcommerce::$oModule->l('Shop ID', 'AdminUpdate') . ': ' . \BTMailchimpEcommerce::$iShopId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('List ID', 'AdminUpdate') . ': ' . $sListId . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Step', 'AdminUpdate') . ': ' . $iFloor . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Total number of orders to process', 'AdminUpdate') . ': ' . $iTotal . "<br/>"
                    . \BTMailchimpEcommerce::$oModule->l('Stock the real number of orders to process', 'AdminUpdate') . ': ' . $iProcess . "<br/>";
                throw new \Exception($sMsg, 5401);
            }
        } catch (\Exception $e) {
            // delete the batches created in our table when MC's API returns an error.
            \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('id' => $sListId, 'type' => $sSyncType, 'mode' => 'manual'));

            $assign['status'] = 'ko';
            $assign['error'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        // force xhr mode
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_ITEM_SYNCHRO,
            'assign' => array('json' => \MCE\Tools::jsonEncode($assign)),
        );
    }


    /**
     * execute the ajax request to sync requested data via the search popup (diagnostic tool)
     *
     * @throws \Exception
     * @param array $aPost
     * @return array
     */
    private function updateSyncData(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        //set
        $assign = array();
        $aResult = array();
        $aDataToSync = null;
        $sSyncType = '';

        $sListId = \Tools::getValue('bt_list_id');
        $sStoreId = \Tools::getValue('bt_store_id');
        $sDataType = \Tools::getValue('bt_data_type');
        $sEltId = \Tools::getValue('bt_elt_id');
        $iLangId = \Tools::getValue('bt_language_id');

        // define the search type
        $sType = ($sDataType == 'member' || $sDataType == 'mergefield') ? 'list' : 'store';

        // use case - test the list ID
        if (!$sListId) {
            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The list ID is empty or not valid', 'AdminUpdate') . '.', 5050);
        }
        // use case - test the type of data to check
        if (!$sDataType) {
            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The type of the data you want to verify is not valid', 'AdminUpdate') . '.', 5051);
        }
        // use case - test the element ID
        if (!$sEltId) {
            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The element ID of the data you want to verify is not valid, it is mandatory', 'AdminUpdate') . '.', 5052);
        }

        switch ($sType) {
            // in case of list, we can synchronize the member
            case 'list':
                // member
                if ($sDataType == 'member') {
                    // format and send
                    if (is_string($sEltId) && strstr($sEltId, '@')) {
                        $bResult = \MCE\Chimp\Facade::processMember($sListId, $sEltId, $iLangId, false, 'put', 'regular');

                        // get detail
                        $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'member');
                    } else {
                        $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('The user e-mail address is not a valid address', 'AdminUpdate');
                        $aResult['code'] = 5053;
                    }
                    // assign the sync type
                    $sSyncType = 'member';
                }
                break;
            // in case of store, we can synchronize product / combination (variant) / cart / order and customer
            case 'store':
                if (!empty($sStoreId)) {
                    switch ($sDataType) {
                        case 'product':
                            // format and send
                            $bResult = \MCE\Chimp\Facade::processProduct($sListId, $sStoreId, $sEltId, $iLangId, 'regular');

                            // get detail
                            $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'product');

                            // assign the sync type
                            $sSyncType = 'single-product';
                            break;
                        case 'variant':
                            if (strstr($sEltId, 'C')) {
                                // get the product and combination IDs
                                list($iProdId, $iAttributeId) = explode('C', $sEltId);

                                // format and send
                                $bResult = \MCE\Chimp\Facade::processVariant($sListId, $sStoreId, $iProdId, $iAttributeId, $iLangId, true);

                                // get detail
                                $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'variant');

                                // assign the sync type
                                $sSyncType = 'single-combination';
                            } else {
                                $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected the \'store\' type + product combination and the product combination reference you have filled in is not formatted well. It should be like: product ID + V + product attribute id. For example: 1V1', 'AdminUpdate');
                                $aResult['code'] = 5054;
                            }
                            break;
                        case 'cart':
                            $oCart = new \Cart($sEltId);

                            if (\Validate::isLoadedObject($oCart)) {
                                // format and send
                                $bResult = \MCE\Chimp\Facade::processCart($sListId, $sStoreId, $oCart->id, $oCart->id_customer, $iLangId, 'regular');

                                // get detail
                                $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart');

                                // assign the sync type
                                $sSyncType = 'single-cart';
                            }
                            break;
                        case 'order':
                            $oOrder = new \Order($sEltId);

                            if (\Validate::isLoadedObject($oOrder)) {
                                // format and send
                                $bResult = \MCE\Chimp\Facade::processOrder($sListId, $sStoreId, $sEltId, $oOrder->id_cart, $iLangId, 'regular', false, 'add', []);

                                // get detail
                                $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order');

                                // assign the sync type
                                $sSyncType = 'single-order';
                            }
                            break;
                        case 'customer':
                            $oCustomer = new \Customer($sEltId);

                            if (\Validate::isLoadedObject($oCustomer)) {
                                if (!\MCE\Chimp\Facade::excludeEmail($oCustomer->email)) {
                                    // format and send
                                    $bResult = \MCE\Chimp\Facade::processCustomer($sListId, $sStoreId, $oCustomer, $oCustomer->id_lang, 'regular');

                                    // get detail
                                    $aResult = \MCE\Chimp\Detail::get()->read($sEltId, $sListId, \BTMailchimpEcommerce::$iShopId, 'customer');

                                    // assign the sync type
                                    $sSyncType = 'single-customer';

                                } else {
                                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('This user could not be synchronized due to your excluded domain list. Here is the list of excluded e-mail domain names:', 'AdminUpdate') . ' ' . implode(', ', \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION']);
                                    $aResult['code'] = 5055;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                } else {
                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You don\'t get a valid store related to the current list.', 'AdminUpdate');
                    $aResult['code'] = 5056;
                }
                break;
            default:
                $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You don\'t get a valid store related to the current list.', 'AdminUpdate');
                $aResult['code'] = 5057;
                break;
        }

        // check if we got error in the log for the matching item
        if (!empty($aResult['detail'])) {
            $detail = \MCE\Tools::jsonDecode($aResult['detail']);

            if (isset($detail->status)
                && $detail->status == 'ko'
            ) {
                $aResult['error'] = $detail->error;
                $aResult['code'] = 5058;
            }
        }


        $assign['aResult'] = $aResult;
        $assign['sSyncType'] = $sSyncType;
        $assign['bUpdate'] = empty($assign['aResult']['error']) ? true : false;
        $assign['aErrors'] = !empty($assign['aResult']['error']) ? array(array('msg' => $assign['aResult']['error'], 'code' => $assign['aResult']['code'])) : array();
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        // force xhr mode activated
        \BTMailchimpEcommerce::$sQueryMode = 'xhr';

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_SYNC_UPD,
            'assign' => $assign,
        );
    }


    /**
     * set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oUpdate;

        if (null === $oUpdate) {
            $oUpdate = new \MCE\AdminUpdate();
        }
        return $oUpdate;
    }
}

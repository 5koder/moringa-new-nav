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

namespace MCE\Chimp;

use \MCE\Chimp\Format\Product;
use \MCE\Chimp\Format\Combination;

class Facade
{
    /**
     * @var array
     */
    private static $aActive = [];

    /**
     * @var bool
     */
    private static $isEcommerce = false;

    /**
     * @var bool
     */
    private static $isNewsletter = false;

    /**
     * @var array
     */
    protected static $aBatches = [];


    /**
     * @var array
     */
    protected static $aSerialized = [];

    /**
     * @var array $aStoreMatching : define the matching IDs to each element of the PHP explode done on the store ID that compose the store ID as : platform / PS shop ID / Currency / List ID
     */
    public static $aStoreMatching = array(
        'platform' => 0,
        'ps' => 1,
        'currency' => 2,
        'list' => 3,
    );

    /**
     * @var array $aList : get the active list
     */
    public static $aList = array();


    /**
     * set list locally
     *
     * @param array $aList
     * @param bool $bSet
     */
    public static function setLists(array $aList)
    {
        self::$aList = $aList;
    }

    /**
     * return the static batches list content
     *
     * @return array
     */
    public static function getItemsForBatches()
    {
        return self::$aBatches;
    }

    /**
     * return the static batches serialized content
     *
     * @return array
     */
    public static function getSerializedForBatches()
    {
        return self::$aSerialized;
    }

    /**
     * define if the newsletter or ecommerce feature are available and then authorize the automatic synching
     *
     * @param string $type
     * @param bool $current
     * @return bool
     */
    public static function isActive($type = '', $current = null)
    {
        empty($type) ? $type = 'general' : '';

        if (!isset(self::$aActive[$type])) {
            require_once(_MCE_PATH_LIB . 'Dao.php');

            // get the current list
            if (empty(self::$aList)) {
                $aList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
                self::$aList = !empty($aList[0]) ? $aList[0] : array();
            }

            if (!empty(self::$aList)) {
                // check the list status
                $aSyncStatus = \MCE\Dao::getSyncStatus(\BTMailchimpEcommerce::$iShopId, self::$aList['id']);

                if (!empty($aSyncStatus)) {
                    $customer = false;
                    $product = false;
                    foreach ($aSyncStatus as $status) {
                        if (
                            $status['type'] == 'newsletter'
                            && $status['sync'] == 1
                            && !empty(\BTMailchimpEcommerce::$conf['MCE_NL_ACTIVE'])
                        ) {
                            self::$isNewsletter = true;
                        }
                        if (
                            $status['type'] == 'customer'
                            && $status['sync'] == 1
                        ) {
                            $customer = true;
                        }
                        if (
                            $status['type'] == 'product'
                            && $status['sync'] == 1
                        ) {
                            $product = true;
                        }
                    }
                    if (
                        !empty($customer)
                        && !empty($product)
                        && !empty(\BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_ACTIVE'])
                    ) {
                        self::$isEcommerce = true;
                    }
                }
            }

            switch ($type) {
                case 'newsletter':
                    self::$aActive[$type] = self::$isNewsletter == true ? true : false;
                    break;
                case 'ecommerce':
                    self::$aActive[$type] = self::$isEcommerce == true ? true : false;
                    break;
                case 'general':
                default:
                    self::$aActive[$type] = self::$isNewsletter == true && self::$isEcommerce == true ? true : false;
                    break;
            }
        }

        return self::$aActive[$type];
    }

    /**
     * get list infos
     *
     * @param string $detail
     * @param mixed
     * @return mixed
     */
    public static function get($detail)
    {
        $value = null;

        if (!empty(self::$aList)) {
            switch ($detail) {
                case 'id':
                    if (isset(self::$aList['id'])) {
                        $value = self::$aList['id'];
                    }
                    break;
                case 'name':
                    if (isset(self::$aList['name'])) {
                        $value = self::$aList['name'];
                    }
                    break;
                case 'store_id':
                    if (isset(self::$aList['store_id'])) {
                        $value = self::$aList['store_id'];
                    }
                    break;
                case 'store_name':
                    if (isset(self::$aList['store_name'])) {
                        $value = self::$aList['store_name'];
                    }
                    break;
                case 'data':
                    if (isset(self::$aList['data'])) {
                        $value = unserialize(self::$aList['data']);
                    }
                    break;
                case 'site_script':
                    if (isset(self::$aList['data'])) {
                        $data = unserialize(self::$aList['data']);

                        if (isset($data['mc'][$detail])) {
                            $value = $data['mc'][$detail];

                            if (isset($value['fragment'])) {
                                $value['fragment'] = html_entity_decode($value['fragment']);
                            }
                        }
                    }
                    break;
                case 'url':
                    if (isset(self::$aList['data'])) {
                        $data = unserialize(self::$aList['data']);

                        if (isset($data['mc']['site_script']['url'])) {
                            $value = $data['mc']['site_script']['url'];
                        }
                    }
                    break;
                case 'fragment':
                    if (isset(self::$aList['data'])) {
                        $data = unserialize(self::$aList['data']);

                        if (isset($data['mc']['site_script']['fragment'])) {
                            $value = html_entity_decode($data['mc']['site_script']['fragment']);
                        }
                    }
                    break;
                default:
                    $value = self::$aList;
                    break;
            }
        }

        return $value;
    }


    /**
     * return the element passed in parameter: it could be the currency ISO / list ID / Shop ID etc...
     *
     * @param string $sStoreId
     * @param string $sType
     * @param bool $bLower
     * @param mixed $mdefault
     * @return int
     */
    public static function getExplodeStoreId($sStoreId, $sType, $bLower = true, $mDefault = '')
    {
        $mDetail = '';

        if (!empty($sStoreId)) {
            $aExplode = explode('-', $sStoreId);

            if (isset(self::$aStoreMatching[$sType]) && isset($aExplode[self::$aStoreMatching[$sType]])) {
                $mDetail = $aExplode[self::$aStoreMatching[$sType]];

                if ($bLower) {
                    $mDetail = \Tools::strtolower($mDetail);
                }
            } elseif ($mDefault !== '') {
                $mDetail = $mDefault;
            }
        }

        return $mDetail;
    }

    /**
     * check if the e-mail should be excluded or not according to the module configuration
     *
     * @param string $sMail
     * @return bool
     */
    public static function excludeEmail($sMail)
    {
        $bExclude = false;

        $aExcludedList = \BTMailchimpEcommerce::$conf['MCE_MAIL_EXCLUSION'];

        if (
            !empty($aExcludedList)
            && is_array($aExcludedList)
        ) {
            foreach ($aExcludedList as $sDomainName) {
                if (strstr($sMail, $sDomainName)) {
                    $bExclude = true;
                }
            }
        }

        return $bExclude;
    }


    /**
     * get list from local and MailChimp app
     *
     * @throws \Exception
     * @param bool $bLocaleOnly
     * @return
     */
    public static function getLists($bLocalOnly = false)
    {
        $aLists = array();

        require_once(_MCE_PATH_LIB . 'Dao.php');
        require_once(_MCE_PATH_LIB_MC . 'Api.php');

        try {
            // get the local list
            $aLocalLists = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId);

            if (
                !_MCE_DEBUG
                && !$bLocalOnly
            ) {
                // instantiate the MC's controller
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                // get list and stores already exists in MC
                $aMCStores = $oMcCtrl->store->get();
                $aMCLists = $oMcCtrl->lists->get(null, array(), array(), 50);
            } else {
                $aMCLists['lists'] = array(
                    'zer5674er65749z8e74r6' => array(
                        'shop_id' => 1,
                        'active' => false,
                        'active_ml' => true,
                        'id' => 'zer5674er65749z8e74r6',
                        'name' => 'BusinessTech Modules French',
                        'store_id' => '',
                        'store_name' => '',
                    ),
                    '564ar54ez6r4654erz' => array(
                        'shop_id' => 1,
                        'active' => false,
                        'active_ml' => true,
                        'id' => '564ar54ez6r4654erz',
                        'name' => 'BusinessTech Modules Anglais',
                        'store_id' => '',
                        'store_name' => '',
                    ),
                    '7897ez97eaz9879' => array(
                        'shop_id' => 1,
                        'active' => false,
                        'active_ml' => true,
                        'id' => '7897ez97eaz9879',
                        'name' => 'Agences',
                        'store_id' => '',
                        'store_name' => '',
                    ),
                );
                $aMCStores = array();
            }

            // use case - aggregate stores and lists with the registered local lists
            if (
                !empty($aLocalLists)
                && !$bLocalOnly
            ) {
                foreach ($aLocalLists as $iKey => &$aLocalList) {
                    $aLocalList['store_id'] = '';
                    $aLocalList['store_name'] = '';

                    if (!empty($aMCLists['lists'])) {
                        $bStillActive = false;
                        foreach ($aMCLists['lists'] as $iKey => $aList) {
                            if ($aLocalList['id'] == $aList['id']) {
                                $bStillActive = true;

                                if (!empty($aMCStores)) {
                                    foreach ($aMCStores['stores'] as $aStore) {
                                        if (
                                            $aStore['list_id'] == $aList['id']
                                            && empty($aLocalList['store_id'])
                                            && empty($aLocalList['store_name'])
                                        ) {
                                            $aLocalList['store_id'] = $aStore['id'];
                                            $aLocalList['store_name'] = $aStore['name'];
                                        }
                                    }
                                }
                                $aLocalList['name'] = $aList['name'];
                                $aLocalList['double_optin'] = $aList['double_optin'];
                                $aLocalList['gdpr'] = $aList['marketing_permissions'];
                                unset($aMCLists['lists'][$iKey]);
                            }
                        }
                        if (!$bStillActive) {
                            $aLocalList['active'] = false;
                            $aLocalList['active_ml'] = false;
                        } else {
                            $aLocalList['active_ml'] = true;
                        }
                    } else {
                        $aLocalList['active'] = false;
                        $aLocalList['active_ml'] = false;
                    }
                }
                // detect list not registered locally
                if (!empty($aMCLists['lists'])) {
                    foreach ($aMCLists['lists'] as $iKey => $aList) {
                        $aTmp = array(
                            'shop_id' => 0,
                            'active' => false,
                            'active_ml' => true,
                            'id' => $aList['id'],
                            'name' => $aList['name'],
                            'store_id' => '',
                            'store_name' => '',
                            'double_optin' => $aList['double_optin'],
                            'gdpr' => $aList['marketing_permissions'],
                        );
                        if (!empty($aMCStores)) {
                            foreach ($aMCStores['stores'] as $aStore) {
                                if ($aStore['list_id'] == $aList['id']) {
                                    $aTmp['store_id'] = $aStore['id'];
                                    $aTmp['store_name'] = $aStore['name'];
                                }
                            }
                        }
                        $aLocalLists[] = $aTmp;
                    }
                }
                $aLists = $aLocalLists;
            } elseif (
                !$bLocalOnly
                && !empty($aMCLists['lists'])
            ) {
                foreach ($aMCLists['lists'] as $aList) {
                    $aLists[$aList['id']] = array(
                        'shop_id' => 0,
                        'active' => false,
                        'active_ml' => true,
                        'id' => $aList['id'],
                        'name' => $aList['name'],
                        'store_id' => '',
                        'store_name' => '',
                        'double_optin' => $aList['double_optin'],
                        'gdpr' => $aList['marketing_permissions'],
                    );

                    if (!empty($aMCStores)) {
                        foreach ($aMCStores['stores'] as $aStore) {
                            if ($aStore['list_id'] == $aList['id']) {
                                $aLists[$aList['id']]['store_id'] = $aStore['id'];
                                $aLists[$aList['id']]['store_name'] = $aStore['name'];
                            }
                        }
                    }
                }
            } else {
                $aLists = $aLocalLists;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
        }

        return $aLists;
    }


    /**
     * test if the store exists in MailChimp
     *
     * @throws \Exception
     * @param string $sStoreId
     * @return bool
     */
    public static function isStoreExist($sStoreId)
    {
        $bExist = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // get the store
            $aResult = $oMcCtrl->store->get($sStoreId);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (
            empty($aResult['error'])
            && !empty($aResult['id'])
        ) {
            $bExist = true;
        }

        return $bExist;
    }


    /**
     * get root info from the MC account
     */
    public static function getRootInfo()
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // get API root information
            $aResult = $oMcCtrl->apiRoot->get();
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        return $aResult;
    }


    /**
     * add a member
     *
     * @throws \Exception
     * @param string $sListId
     * @param array $aData
     * @param string $sMethod
     * @param string $sMode
     * @return bool
     */
    public static function addMember($sListId, array $aData, $sMethod = 'post', $sMode = 'regular')
    {
        $bResult = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // use case check if the sync detail not already exist
            if (!\MCE\Chimp\Detail::get()->read($aData['email_address'], $sListId, \BTMailchimpEcommerce::$iShopId, 'member', true)) {
                // add the detail sync in our history table
                $bResult = \MCE\Chimp\Detail::get()->create($aData['email_address'], 'member', $sListId, \BTMailchimpEcommerce::$iShopId);
            }

            switch ($sMode) {
                case 'regular':
                    // set the current list Id
                    $oMcCtrl->members->setId($sListId);

                    // update a member
                    $aResult = $oMcCtrl->members->add($aData['email_address'], $aData['status'], $aData, $sMethod);

                    // register detail of request
                    $bResult = \MCE\Chimp\Detail::get()->update($aData['email_address'], $sListId, \BTMailchimpEcommerce::$iShopId, 'member', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => \Tools::strtoupper($sMethod),
                        'path' => '/lists/' . $sListId . '/members/' . md5($aData['email_address']),
                        'body' => \Tools::jsonEncode($aData),
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $aData['email_address'], 'type' => 'member', 'lang_id' => $aData['language_id']);

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));
                        // we create the batch
                        $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'newsletter', 0, 1, array($serialize));
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // store the error
            \MCE\Chimp\Detail::get()->update($aData['email_address'], $sListId, \BTMailchimpEcommerce::$iShopId, 'member', \MCE\Chimp\Detail::get()->format($aResult));
        }

        return $bResult;
    }


    /**
     * add a new customer into MC
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function addCustomer($sListId, $sStoreId, array $aData, $sMode = 'regular')
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $bResult = false;
        $aResult = [];

        try {
            // use case check if the sync detail not already exist
            if (!\MCE\Chimp\Detail::get()->read($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', true)) {
                // add the detail sync in our history table
                $bResult = \MCE\Chimp\Detail::get()->create($aData['id'], 'customer', $sListId, \BTMailchimpEcommerce::$iShopId);
            }

            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->customer->setId($sStoreId);

                    // add new customer
                    $aResult = $oMcCtrl->customer->add($aData['id'], $aData['email_address'], $aData['opt_in_status'], $aData);

                    // register detail of request
                    $bResult = \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => \MCE\Chimp\Api::PUT,
                        'path' => '/ecommerce/stores/' . $sStoreId . '/customers/' . $aData['id'],
                        'body' => \Tools::jsonEncode($aData),
                    );

                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $aData['id'], 'type' => 'customer', 'lang_id' => $aData['lang_id'], 'method' => 'add');

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        // create batch
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // serialize the content to identify the item via the batch webhook
                            $serialize = array(array('id' => $aData['id'], 'type' => 'customer', 'lang_id' => $aData['lang_id'], 'method' => 'add'));
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'customer', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // register detail of request
            \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', \MCE\Chimp\Detail::get()->format($aResult));
        }

        return $bResult;
    }


    /**
     * update a customer into MC
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iCustomerId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function updateCustomer($sListId, $sStoreId, $iCustomerId, array $aData, $sMode = 'regular')
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $bResult = false;
        $aResult = [];

        try {
            // use case check if the sync detail not already exist
            if (!\MCE\Chimp\Detail::get()->read($iCustomerId, $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', true)) {
                // add the detail sync in our history table
                $bResult = \MCE\Chimp\Detail::get()->create($iCustomerId, 'customer', $sListId, \BTMailchimpEcommerce::$iShopId);
            }

            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->customer->setId($sStoreId);

                    // update a customer
                    $aResult = $oMcCtrl->customer->update($iCustomerId, $aData);

                    // register detail of request
                    $bResult = \MCE\Chimp\Detail::get()->update($iCustomerId, $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => \MCE\Chimp\Api::PUT,
                        'path' => '/ecommerce/stores/' . $sStoreId . '/customers/' . $iCustomerId,
                        'body' => \Tools::jsonEncode($aData),
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $iCustomerId, 'type' => 'customer', 'lang_id' => $aData['lang_id'], 'method' => 'update');

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'customer', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // register detail of request
            \MCE\Chimp\Detail::get()->update($iCustomerId, $sListId, \BTMailchimpEcommerce::$iShopId, 'customer', \MCE\Chimp\Detail::get()->format($aResult));
        }

        return $bResult;
    }


    /**
     * detect if a product already exists
     *
     * @throws \Exception
     * @param string $sStoreId
     * @param int $iProductId
     * @param int $iLangId
     * @return bool
     */
    public static function isProductExist($sStoreId, $iProductId, $iLangId)
    {
        $bExist = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // set the current store Id
            $oMcCtrl->product->setId($sStoreId);

            // get product id and check if already exists in MC
            $aResult = $oMcCtrl->product->get(\MCE\Chimp\Format\Formatter::setProductID($iProductId, $iLangId));
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (
            empty($aResult['error'])
            && !empty($aResult['id'])
        ) {
            $bExist = true;
        }

        return $bExist;
    }


    /**
     * add a product
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function addProduct($sListId, $sStoreId, array $aData, $sMode = 'regular')
    {
        $bResult = false;
        $aResult = [];

        // check the default values
        if (
            !empty($aData['id'])
            && !empty($aData['title'])
            && !empty($aData['variants'])
        ) {
            require_once(_MCE_PATH_LIB_MC . 'Api.php');
            require_once(_MCE_PATH_LIB . 'Dao.php');

            try {
                // check if we have really create the sync detail with the actionProductAdd hook
                if (!\MCE\Chimp\Detail::get()->read($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', true)) {
                    $bResult = \MCE\Chimp\Detail::get()->create($aData['id'], 'product', $sListId, \BTMailchimpEcommerce::$iShopId);
                }

                // instantiate the MC's controller
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                switch ($sMode) {
                    case 'regular':
                        // set the current store Id
                        $oMcCtrl->product->setId($sStoreId);

                        // add new product
                        $aResult = $oMcCtrl->product->add(
                            $aData['id'],
                            $aData['title'],
                            $aData['variants'],
                            $aData
                        );

                        // register detail of request
                        $bResult = \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', \MCE\Chimp\Detail::get()->format($aResult));
                        break;
                    case 'cron':
                    case 'batch':
                        $batch = array(
                            'method' => \MCE\Chimp\Api::POST,
                            'path' => '/ecommerce/stores/' . $sStoreId . '/products',
                            'body' => \Tools::jsonEncode($aData),
                        );
                        // serialize the content to identify the item via the batch webhook
                        $serialize = array('id' => $aData['id'], 'type' => 'product', 'lang_id' => $aData['lang_id'], 'method' => 'add');

                        // store the batch instructions
                        self::$aBatches[] = $batch;
                        // store the serialized content
                        self::$aSerialized[] = $serialize;

                        if ($sMode == 'batch') {
                            $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                            if (!empty($aResult['id'])) {
                                // we create the batch
                                $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'product', 0, 1, array($serialize));
                            }
                        }
                        break;
                    default:
                        break;
                }
            } catch (\MCE\Chimp\MailchimpException $e) {
                // do nothing because we need to return the empty content if an exception is caught up
                $aResult['error'] = $e->getFriendlyMessage();
                $aResult['code'] = $e->getCode();

                // register detail of request
                \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', \MCE\Chimp\Detail::get()->format($aResult));
            }
        }

        return $bResult;
    }


    /**
     * update a product
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function updateProduct($sListId, $sStoreId, array $aData, $sMode = 'regular')
    {
        $bResult = false;
        $aResult = [];

        // check the default values
        if (
            !empty($aData['id'])
            && !empty($aData['title'])
        ) {
            require_once(_MCE_PATH_LIB_MC . 'Api.php');
            require_once(_MCE_PATH_LIB . 'Dao.php');

            try {
                // check if we have really create the sync detail with the actionProductAdd hook
                if (!\MCE\Dao::getSyncDetail($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', true)) {
                    $bResult = \MCE\Dao::createSyncDetail($aData['id'], 'product', $sListId, \BTMailchimpEcommerce::$iShopId);
                }

                // instantiate the MC's controller
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                switch ($sMode) {
                    case 'regular':
                        // set the current store Id
                        $oMcCtrl->product->setId($sStoreId);

                        // add new product
                        $aResult = $oMcCtrl->product->update(
                            $aData['id'],
                            $aData['title'],
                            $aData
                        );

                        // register detail of request
                        $bResult = \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', \MCE\Chimp\Detail::get()->format($aResult));
                        break;
                    case 'cron':
                    case 'batch':
                        $batch = array(
                            'method' => \MCE\Chimp\Api::PATCH,
                            'path' => '/ecommerce/stores/' . $sStoreId . '/products/' . $aData['id'],
                            'body' => \Tools::jsonEncode($aData),
                        );
                        // serialize the content to identify the item via the batch webhook
                        $serialize = array('id' => $aData['id'], 'type' => 'product', 'lang_id' => $aData['lang_id'], 'method' => 'update');

                        // store the batch instructions
                        self::$aBatches[] = $batch;
                        // store the serialized content
                        self::$aSerialized[] = $serialize;

                        if ($sMode == 'batch') {
                            $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                            if (!empty($aResult['id'])) {
                                // we create the batch
                                $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'product', 0, 1, array($serialize));
                            }
                        }
                        break;
                    default:
                        break;
                }
            } catch (\MCE\Chimp\MailchimpException $e) {
                // do nothing because we need to return the empty content if an exception is caught up
                $aResult['error'] = $e->getFriendlyMessage();
                $aResult['code'] = $e->getCode();

                // register detail of request
                \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'product', \MCE\Chimp\Detail::get()->format($aResult));
            }
        }

        return $bResult;
    }


    /**
     * delete a product
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iProductId
     * @param array $aLangId
     * @param bool $bForceLive
     * @return bool
     */
    public static function deleteProduct($sListId, $sStoreId, $iProductId, $aLangId, $sMode = 'regular')
    {
        $bDeleted = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->product->setId($sStoreId);

                    foreach ($aLangId as $id_lang) {
                        // if product exist
                        if (self::isProductExist($sStoreId, $iProductId, $id_lang)) {
                            // delete the current product
                            $aResult = $oMcCtrl->product->delete(\MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang));

                            // delete the sync detail once it's deleted
                            if (empty($aResult['error'])) {
                                $bResult = \MCE\Chimp\Detail::get()->delete(\MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang), $sListId, \BTMailchimpEcommerce::$iShopId, 'product');
                            }
                        }
                    }
                    break;
                case 'cron':
                case 'batch':
                    $batch = [];
                    $serialize = [];
                    foreach ($aLangId as $id_lang) {
                        $batch[] = array(
                            'method' => \MCE\Chimp\Api::DELETE,
                            'path' => '/ecommerce/stores/' . $sStoreId . '/products/' . \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang)
                        );
                        // serialize the content to identify the item via the batch webhook
                        $serialize[] = array('id' => \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang), 'type' => 'product');
                    }

                    // store the batch instructions
                    self::$aBatches[] = array_merge(self::$aBatches, $batch);
                    // store the serialized content
                    self::$aSerialized[] = array_merge(self::$aSerialized, $serialize);

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'product', 0, 1, $serialize);
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (empty($aResult['error'])) {
            $bDeleted = true;
        }

        return $bDeleted;
    }


    /**
     * add a product's variant
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iProductId
     * @param int $iLangId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function addProductVariant($sListId, $sStoreId, $iProductId, $iLangId, array $aData, $sMode = 'regular')
    {
        $bResult = false;
        $aResult = [];

        // check the default values
        if (
            !empty($aData['id'])
            && !empty($aData['title'])
        ) {
            require_once(_MCE_PATH_LIB_MC . 'Api.php');
            require_once(_MCE_PATH_LIB . 'Dao.php');

            try {
                // check if we have really create the sync detail with the actionProductAdd hook
                if (!\MCE\Chimp\Detail::get()->read($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'variant', true, $iProductId)) {
                    $bResult = \MCE\Chimp\Detail::get()->create($aData['id'], 'variant', $sListId, \BTMailchimpEcommerce::$iShopId, $iProductId);
                }

                // instantiate the MC's controller
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                switch ($sMode) {
                    case 'regular':
                        // set the current store Id
                        $oMcCtrl->combination->setId($sStoreId);

                        // add new product combination
                        $aResult = $oMcCtrl->combination->add(
                            \MCE\Chimp\Format\Formatter::setProductID($iProductId, $iLangId),
                            $aData['id'],
                            $aData['title'],
                            $aData
                        );

                        // register detail of request
                        $bResult = \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'variant', \MCE\Chimp\Detail::get()->format($aResult));
                        break;
                    case 'cron':
                    case 'batch':
                        $batch = array(
                            'method' => \MCE\Chimp\Api::PUT,
                            'path' => '/ecommerce/stores/' . $sStoreId . '/products/' . \MCE\Chimp\Format\Formatter::setProductID($iProductId, $iLangId) . '/variants/' . $aData['id'],
                            'body' => \Tools::jsonEncode($aData),
                        );
                        // serialize the content to identify the item via the batch webhook
                        $serialize = array('id' => $aData['id'], 'type' => 'variant', 'lang_id' => $aData['lang_id']);

                        // store the batch instructions
                        self::$aBatches[] = $batch;
                        // store the serialized content
                        self::$aSerialized[] = $serialize;

                        if ($sMode == 'batch') {
                            $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                            if (!empty($aResult['id'])) {
                                // we create the batch
                                $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'variant', 0, 1, array($serialize));
                            }
                        }
                        break;
                    default:
                        break;
                }
            } catch (\MCE\Chimp\MailchimpException $e) {
                // do nothing because we need to return the empty content if an exception is caught up
                $aResult['error'] = $e->getFriendlyMessage();
                $aResult['code'] = $e->getCode();

                // register detail of request
                \MCE\Chimp\Detail::get()->update($aData['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'variant', \MCE\Chimp\Detail::get()->format($aResult));
            }
        }

        return $bResult;
    }


    /**
     * delete a product combination
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iProductId
     * @param int $iVariantId
     * @param array $aLangId
     * @param string $sMode
     * @return bool
     */
    public static function deleteProductVariant($sListId, $sStoreId, $iProductId, $iVariantId, $aLangId, $sMode = 'regular')
    {
        $bDeleted = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->combination->setId($sStoreId);

                    foreach ($aLangId as $id_lang) {
                        // delete the current product
                        $aResult = $oMcCtrl->combination->delete(
                            \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang),
                            \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang, $iVariantId)
                        );

                        // delete the sync detail once it's deleted
                        if (empty($aResult['error'])) {
                            $bResult = \MCE\Chimp\Detail::get()->delete(\MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang, $iVariantId), $sListId, \BTMailchimpEcommerce::$iShopId, 'variant');
                        }
                    }
                    break;
                case 'cron':
                case 'batch':
                    $batch = [];
                    $serialize = [];
                    foreach ($aLangId as $id_lang) {
                        $batch[] = array(
                            'method' => \MCE\Chimp\Api::DELETE,
                            'path' => '/ecommerce/stores/' . $sStoreId . '/products/' . \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang) . '/variants/' . \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang, $iVariantId)
                        );
                        // serialize the content to identify the item via the batch webhook
                        $serialize[] = array('id' => \MCE\Chimp\Format\Formatter::setProductID($iProductId, $id_lang, $iVariantId), 'type' => 'variant');
                    }

                    // store the batch instructions
                    self::$aBatches[] = array_merge(self::$aBatches, $batch);
                    // store the serialized content
                    self::$aSerialized[] = array_merge(self::$aSerialized, $serialize);

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'variant', 0, 1, $serialize);
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (empty($aResult['error'])) {
            $bDeleted = true;
        }

        return $bDeleted;
    }

    /**
     * detect if a cart already exists
     *
     * @throws \Exception
     * @param string $sStoreId
     * @param int $iCartId
     * @return true
     */
    public static function isCartExist($sStoreId, $iCartId)
    {
        $bExist = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // set the current store Id
            $oMcCtrl->cart->setId($sStoreId);

            // get cart id and check if already exists in MC
            $aResult = $oMcCtrl->cart->get($iCartId);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (
            empty($aResult['error'])
            && !empty($aResult['lines'])
        ) {
            $bExist = true;
        }

        return $bExist;
    }


    /**
     * add a cart
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iCartId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function addCart($sListId, $sStoreId, $iCartId, array $aData, $sMode = 'regular')
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $bResult = false;
        $aResult = [];

        try {
            // use case - check if we have already or not synchronized this cart
            if (!\MCE\Chimp\Detail::get()->read($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart', true)) {
                \MCE\Chimp\Detail::get()->create($iCartId, 'cart', $sListId, \BTMailchimpEcommerce::$iShopId);

                // define the method
                $sMethod = 'add';
            } else {
                // define the method
                $sMethod = 'update';

                // if already registered but with error, then we change the method
                $result = \MCE\Chimp\Detail::get()->read($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart');
                if (!empty($result['detail'])) {
                    $log = \MCE\Tools::jsonDecode($result['detail']);

                    if (isset($log->error)) {
                        $sMethod = 'add';
                    }
                }
            }

            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->cart->setId($sStoreId);

                    // update the current cart
                    if ($sMethod == 'update') {
                        $aResult = $oMcCtrl->cart->update($iCartId, $aData);
                    } else {
                        $aResult = $oMcCtrl->cart->add($iCartId, $aData);
                    }

                    // use case - update sync detail
                    $bResult = \MCE\Chimp\Detail::get()->update($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => ($sMethod == 'add' ? \MCE\Chimp\Api::POST : \MCE\Chimp\Api::PATCH),
                        'path' => '/ecommerce/stores/' . $sStoreId . '/carts' . ($sMethod == 'update' ? '/' . $iCartId : ''),
                        'body' => \Tools::jsonEncode($aData),
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $iCartId, 'type' => 'cart', 'lang_id' => $aData['lang_id']);

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'cart', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // use case - update sync detail
            \MCE\Chimp\Detail::get()->update($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart', \MCE\Chimp\Detail::get()->format($aResult));
        }

        return $bResult;
    }


    /**
     * delete a cart
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iCartId
     * @param string $sMode
     * @return bool
     */
    public static function deleteCart($sListId, $sStoreId, $iCartId, $sMode = 'regular')
    {
        $bDeleted = false;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->cart->setId($sStoreId);

                    // delete the current cart
                    $aResult = $oMcCtrl->cart->delete($iCartId);

                    // delete the sync detail once it's deleted
                    if (empty($aResult['error'])) {
                        $bDeleted = \MCE\Chimp\Detail::get()->delete($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart');
                    }
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => \MCE\Chimp\Api::DELETE,
                        'path' => '/ecommerce/stores/' . $sStoreId . '/carts/' . $iCartId
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $iCartId, 'type' => 'cart');

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'cart', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        if (empty($aResult['error'])) {
            $bDeleted = true;
        }

        return $bDeleted;
    }


    /**
     * add an order
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iOrderId
     * @param array $aData
     * @param int $iCartId
     * @param bool $bForceLive
     * @return bool
     */
    public static function addOrder($sListId, $sStoreId, $iOrderId, array $aData, $iCartId = 0, $sMode = 'regular')
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $bResult = false;
        $aResult = [];

        try {
            // use case - check if we have already or not synchronized this order
            if (!\MCE\Chimp\Detail::get()->read($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', true)) {
                \MCE\Chimp\Detail::get()->create($iOrderId, 'order', $sListId, \BTMailchimpEcommerce::$iShopId);
            }

            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->order->setId($sStoreId);

                    // add the current order
                    $aResult = $oMcCtrl->order->add($iOrderId, $aData);

                    // if no error
                    if (
                        empty($aResult['error'])
                        && !empty($iCartId)
                        && self::isCartExist($sStoreId, $iCartId)
                    ) {
                        require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');

                        // delete the cart related to the order
                        self::deleteCart($sListId, $sStoreId, $iCartId);
                        // update the sync status to 2 in order to define if this cart has been turned on an order
                        $bResult = \MCE\Chimp\Detail::get()->update($iCartId, $sListId, \BTMailchimpEcommerce::$iShopId, 'cart', array('sync' => 2));
                    }

                    // use case - update sync detail
                    $bResult = \MCE\Chimp\Detail::get()->update($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    // delete the cart related to the order
                    self::deleteCart($sListId, $sStoreId, $iCartId, $sMode);

                    $batch = array(
                        'method' => \MCE\Chimp\Api::POST,
                        'path' => '/ecommerce/stores/' . $sStoreId . '/orders',
                        'body' => \Tools::jsonEncode($aData),
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $iOrderId, 'type' => 'order', 'lang_id' => $aData['lang_id'], 'cart_id' => $aData['cart_id'], 'method' => 'add');

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'order', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // use case - update sync detail
            $bResult = \MCE\Chimp\Detail::get()->update($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', \MCE\Chimp\Detail::get()->format($aResult));
        }

        // delete the cookie to wait the next cookie
        $bResult = \MCE\Cookie::delete(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'landing_site');
        $bResult = \MCE\Cookie::delete(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'mc_cid');

        return $bResult;
    }


    /**
     * update an order
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iOrderId
     * @param array $aData
     * @param string $sMode
     * @return bool
     */
    public static function updateOrder($sListId, $sStoreId, $iOrderId, array $aData, $sMode = 'regular')
    {
        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        $bResult = false;
        $aResult = [];

        try {
            // use case - check if we have already or not synchronized this order
            if (!\MCE\Chimp\Detail::get()->read($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', true)) {
                \MCE\Chimp\Detail::get()->create($iOrderId, 'order', $sListId, \BTMailchimpEcommerce::$iShopId);
            }

            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sMode) {
                case 'regular':
                    // set the current store Id
                    $oMcCtrl->order->setId($sStoreId);

                    // add the current order
                    $aResult = $oMcCtrl->order->update($iOrderId, $aData);

                    // use case - update sync detail
                    $bResult = \MCE\Chimp\Detail::get()->update($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', \MCE\Chimp\Detail::get()->format($aResult));
                    break;
                case 'cron':
                case 'batch':
                    $batch = array(
                        'method' => \MCE\Chimp\Api::PATCH,
                        'path' => '/ecommerce/stores/' . $sStoreId . '/orders/' . $iOrderId,
                        'body' => \Tools::jsonEncode($aData),
                    );
                    // serialize the content to identify the item via the batch webhook
                    $serialize = array('id' => $iOrderId, 'type' => 'order', 'lang_id' => $aData['lang_id'], 'cart_id' => $aData['cart_id'], 'method' => 'update');

                    // store the batch instructions
                    self::$aBatches[] = $batch;
                    // store the serialized content
                    self::$aSerialized[] = $serialize;

                    if ($sMode == 'batch') {
                        $aResult = $oMcCtrl->batches->add(array('operations' => array($batch)));

                        if (!empty($aResult['id'])) {
                            // we create the batch
                            $bResult = \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'automatic', 'order', 0, 1, array($serialize));
                        }
                    }
                    break;
                default:
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty list if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();

            // use case - update sync detail
            \MCE\Chimp\Detail::get()->update($iOrderId, $sListId, \BTMailchimpEcommerce::$iShopId, 'order', \MCE\Chimp\Detail::get()->format($aResult));
        }

        return $bResult;
    }


    /**
     * detect if a merge_field exists for a specific list
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sMergeField
     * @param int $iCount
     * @return int
     */
    public static function isMergeFieldExist($sListId, $sMergeField, $iCount = 50)
    {
        $iFieldId = 0;

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // set the current list Id
            $oMcCtrl->mergeFields->setId($sListId);

            // get merge_field id and check if already exists in MC
            $aResult = $oMcCtrl->mergeFields->get(null, array(), array(), $iCount);

            if (
                !empty($aResult['merge_fields'])
                && is_array($aResult['merge_fields'])
            ) {
                foreach ($aResult['merge_fields'] as $aMergeField) {
                    if (
                        $aMergeField['name'] == $sMergeField
                        || $aMergeField['tag'] == $sMergeField
                    ) {
                        $iFieldId = $aMergeField['merge_id'];
                    }
                }
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        return $iFieldId;
    }


    /**
     * add a merge_field
     *
     * @throws \Exception
     * @param string $sId
     * @param string $sName
     * @param string $sType
     * @param array $aOpts
     * @return array
     */
    public static function addMergeField($sId, $sName, $sType, array $aOpts = array())
    {
        $aResult = array();

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            // set the current list Id
            $oMcCtrl->mergeFields->setId($sId);

            // add new merge_field
            $aResult = $oMcCtrl->mergeFields->add($sName, $sType, $aOpts);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        return $aResult;
    }


    /**
     * execute a search query to check the good synching of the asked parameter
     *
     * @throws \Exception
     * @param string $sType
     * @param string $sListId
     * @param string $sStoreId
     * @param string $sDataType
     * @param string $sElt
     * @param string $iLangId
     * @return array
     */
    public static function search($sType, $sListId, $sStoreId, $sDataType, $sElt, $iLangId)
    {
        $aResult = array();

        require_once(_MCE_PATH_LIB_MC . 'Api.php');
        require_once(_MCE_PATH_LIB . 'Dao.php');

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            switch ($sType) {
                case 'list':
                    // member
                    if ($sDataType == 'member') {
                        // set the current list Id
                        $oMcCtrl->members->setId($sListId);

                        // get the member data
                        $aResult = $oMcCtrl->members->get($sElt);

                        // merge field
                    } elseif ($sDataType == 'mergefield') {
                        // set the current list Id
                        $oMcCtrl->mergeFields->setId($sListId);

                        $iFieldId = self::isMergeFieldExist($sListId, $sElt);

                        if (!empty($iFieldId)) {
                            // get the member data
                            $aResult = $oMcCtrl->mergeFields->get($iFieldId);
                        } else {
                            $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('This merge field doesn\'t exist for this list in MailChimp.',  'Facade');
                            $aResult['code'] = 404;
                        }
                    } else {
                        $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected a \'list\' data type but you didn\'t fill other filters in the good way.',  'Facade');
                        $aResult['code'] = 404;
                    }
                    break;
                case 'store':
                    if (!empty($sStoreId)) {
                        switch ($sDataType) {
                            case 'product':
                                // set the current list Id
                                $oMcCtrl->product->setId($sStoreId);
                                $aResult = $oMcCtrl->product->get($sElt . 'L' . $iLangId);
                                break;
                            case 'variant':
                                // set the current list Id
                                $oMcCtrl->combination->setId($sStoreId);

                                if (strstr($sElt, 'C')) {
                                    list($iProdId, $iAttributeId) = explode('C', $sElt);
                                    $aResult = $oMcCtrl->combination->get($iProdId . 'L' . $iLangId, $iAttributeId);
                                } else {
                                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected the product combination data type but the product combination reference you have filled in is not formatted correctly. It must be like: product ID + C + product attribute ID. For example: 1C2',  'Facade');
                                    $aResult['code'] = 404;
                                }
                                break;
                            case 'cart':
                                // set the current list Id
                                $oMcCtrl->cart->setId($sStoreId);
                                $aResult = $oMcCtrl->cart->get($sElt);
                                break;
                            case 'order':
                                // set the current list Id
                                $oMcCtrl->order->setId($sStoreId);
                                $aResult = $oMcCtrl->order->get($sElt);
                                break;
                            case 'customer':
                                // set the current list Id
                                $oMcCtrl->customer->setId($sStoreId);
                                $aResult = $oMcCtrl->customer->get($sElt);
                                break;
                            default:
                                break;
                        }
                    } else {
                        $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected a \'e-commerce\' data type but there isn\'t any store related to the current list selected.',  'Facade');
                        $aResult['code'] = 404;
                    }
                    break;
                default:
                    $aResult['error'] = \BTMailchimpEcommerce::$oModule->l('You have selected a \'store\' data type but you didn\'t fill other filters in the good way.',  'Facade');
                    $aResult['code'] = 404;
                    break;
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
            $aResult['error'] = $e->getFriendlyMessage();
            $aResult['code'] = $e->getCode();
        }

        return $aResult;
    }


    /**
     * format the batch response
     *
     * @throws \Exception
     * @param string $sBatchId
     * @param string $sResponseUrl
     * @return array
     */
    public static function formatBatchResponse($sBatchId, $sResponseUrl)
    {
        $aResponses = array();
        $result = false;

        // set the temporary filename
        $sTempFilename = tempnam(sys_get_temp_dir(), _MCE_MODULE_NAME . '_');

        // try to copy the source file and if it's 10 minutes over, then we get the response_body_url with the API
        $result = @copy($sResponseUrl, $sTempFilename . '.tar.gz');

        if (!$result) {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
            $aBatch = $oMcCtrl->batches->get($sBatchId);

            if (!empty($aBatch)) {
                // try to copy the source file and if it's 10 minutes over, then we get the response_body_url via the API
                $result = @copy($aBatch['response_body_url'], $sTempFilename . '.tar.gz');
            }
        }

        if (!empty($result)) {
            $sDestinationPath = $sTempFilename . '_batch_response';
            $sZipName = $sTempFilename . '.zip';

            $p = new \PharData($sTempFilename . '.tar.gz');
            $p->convertToData(\Phar::ZIP);
            $zip = new \ZipArchive;
            $res = $zip->open($sZipName);
            if ($res === true) {
                $zip->extractTo($sDestinationPath);
                $zip->close();
            }
            $oIterator = new \RecursiveDirectoryIterator($sDestinationPath);

            foreach (new \RecursiveIteratorIterator($oIterator) as $file) {
                if ($file->getExtension() === 'json') {
                    $aItems = \MCE\Tools::jsonDecode(\Tools::file_get_contents($file), true);
                    foreach ($aItems as $aItem) {
                        $aResponses[] = array(
                            'status_code' => $aItem->status_code,
                            'operation_id' => $aItem->operation_id,
                            'response' => \MCE\Tools::jsonDecode($aItem->response)
                        );
                    }
                }
            }

            @\Tools::deleteDirectory($sDestinationPath, true);
            @\Tools::deleteFile($sZipName);
            @\Tools::deleteFile($sTempFilename . '.tar.gz');
            @\Tools::deleteFile($sTempFilename . '.tar');
            @\Tools::deleteFile($sTempFilename);
        }

        return $aResponses;
    }


    /**
     * create new batch
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sId
     * @param string $sType
     * @param array $data
     * @return bool
     */
    public static function createNewBatch($sListId, $sStoreId, $sId, $sType, $data)
    {
        $bResult = false;

        switch ($sType) {
            case 'member':
                // format and send
                if (is_string($sId) && strstr($sId, '@')) {
                    $bResult = self::processMember($sListId, $sId, $data['lang_id'], \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', 'batch');
                }
                break;
            case 'product':
                if (strstr($sId, 'L')) {
                    list($iProdId, $iLangId) = explode('L', $sId);
                } else {
                    $iProdId = $sId;
                }
                // format and send
                $bResult = self::processProduct($sListId, $sStoreId, $iProdId, $data['lang_id'], 'batch', $data['method']);
                break;
            case 'variant':
                if (strstr($sId, 'C')) {
                    // get the product and combination IDs
                    list($sProdId, $iAttributeId) = explode('C', $sId);
                    if (strstr($sId, 'L')) {
                        list($iProdId, $iLangId) = explode('L', $sId);
                    } else {
                        $iProdId = $sProdId;
                    }
                    // format and send
                    $bResult = self::processVariant($sListId, $sStoreId, $iProdId, $iAttributeId, $data['lang_id'], 'batch');
                }
                break;
            case 'customer':
                $oCustomer = new \Customer($sId);

                if (\Validate::isLoadedObject($oCustomer)) {
                    // format and send
                    $bResult = self::processCustomer($sListId, $sStoreId, $oCustomer, $data['lang_id'], 'batch', $data['method']);
                }
                break;
            case 'cart':
                // format and send
                $bResult = self::processCart($sListId, $sStoreId, $sId, $data['customer_id'], $data['lang_id'], 'batch', false, \BTMailchimpEcommerce::$conf['MCE_CART_MAX_PROD']);
                break;
            case 'order':
                // format and send
                $bResult = self::processOrder($sListId, $sStoreId, $sId, $data['cart_id'], $data['lang_id'], 'batch', false, $data['method']);
                break;
            default:
                break;
        }

        return $bResult;
    }


    /**
     * format and send member data
     *
     * @throws \Exception
     * @param string $sListId
     * @param mixed $mCustomer
     * @param int $iLangId
     * @param bool $bDoubleOptin
     * @param string $sMethod
     * @param string $sMode
     * @return bool
     */
    public static function processMember($sListId, $mCustomer, $iLangId, $bDoubleOptin = false, $sMethod = 'put', $sMode = 'regular')
    {
        $bResult = false;

        try {
            if (is_string($mCustomer) && strstr($mCustomer, '@')) {
                $iCustomerId = \Customer::customerExists($mCustomer, true);
                if (!empty($iCustomerId)) {
                    $oCustomer = new \Customer($iCustomerId);
                    $customer = (array) $oCustomer;
                } else {
                    $customer = ['email' => $mCustomer];
                }
                $email = $customer['email'];
            } else {
                $customer = $mCustomer;
                $email = is_array($mCustomer) ? $mCustomer['email'] : $mCustomer->email;
            }

            // if double optin activated, we have to check if the member has already been synchronized to not update information with the double optin as the member with the pending status is no longer in the list until he's confirmed again his inscription
            if (!empty($bDoubleOptin)) {
                // use case check if the sync detail not already exist
                $result = \MCE\Chimp\Detail::get()->read($email, $sListId, \BTMailchimpEcommerce::$iShopId, 'member', true);

                // check if already synchronized
                if (!empty($result['detail'])) {
                    $detail = \MCE\Tools::jsonDecode($result['detail']);

                    if (
                        isset($detail->status)
                        && $detail->status == 'ok'
                    ) {
                        $bDoubleOptin = false;
                    }
                }
            }

            // get default status if needed
            $default_status = is_array($customer) && !empty($customer['default_status']) ? $customer['default_status'] : '';

            // format customer
            $aDataToSync = (new \MCE\Chimp\Format\Member($customer, $iLangId, \BTMailchimpEcommerce::$conf['MCE_CUST_TYPE_EXPORT'], $bDoubleOptin, $default_status))->format();

            // sync to MC
            $bResult = self::addMember($sListId, $aDataToSync, $sMethod, $sMode);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }

    /**
     * format and send product data
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param string $mItemId
     * @param int $iLangId
     * @param string $sMode
     * @param string $sMethod
     * @param string $sForceImgUrl
     * @param bool $bNested
     * @param bool $bChangeCurrency
     * @return bool
     */
    public static function processProduct($sListId, $sStoreId, $mItemId, $iLangId, $sMode = 'regular', $sMethod = 'add', $sForceImgUrl = '', $bNested = false, $bChangeCurrency = true)
    {
        $bResult = false;

        try {
            // get currency code and get the conversion rate
            if ($bChangeCurrency) {
                $sCurrencyCode = self::getExplodeStoreId($sStoreId, 'currency');
                $iMCCurrencyId = \Currency::getIdByIsoCode($sCurrencyCode, \BTMailchimpEcommerce::$iShopId);

                // define the MC store currency to the context in order to calculate the product price according to the MC currency
                $oCurrentCurrency = \Context::getContext()->currency;
                \Context::getContext()->currency = new \Currency($iMCCurrencyId);
            }

            // set the product data format to sync them
            $aDataToSync = (new \MCE\Chimp\Format\Product(
                $mItemId,
                $iLangId,
                \BTMailchimpEcommerce::$conf['MCE_CAT_LABEL_FORMAT'] == 'short' ? true : false,
                \BTMailchimpEcommerce::$conf['MCE_PROD_VENDOR_TYPE'],
                !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_format',
                \BTMailchimpEcommerce::$conf['MCE_PROD_DESC_TYPE'],
                $sForceImgUrl,
                $bNested
            ))->format();

            // set the previous currency
            if ($bChangeCurrency) {
                \Context::getContext()->currency = $oCurrentCurrency;
            }

            // add the lang id for the batch details
            $aDataToSync['lang_id'] = $iLangId;

            // send to MC : add or update
            $bResult = $sMethod == 'add' ? self::addProduct($sListId, $sStoreId, $aDataToSync, $sMode) : self::updateProduct($sListId, $sStoreId, $aDataToSync, $sMode);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }

    /**
     * format and send combination data
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iProdId
     * @param int $iCombinationId
     * @param int $iLangId
     * @param string $sMode
     * @return bool
     */
    public static function processVariant($sListId, $sStoreId, $iProdId, $iCombinationId, $iLangId, $sMode = 'regular')
    {
        $bResult = false;

        try {
            // define the MC store currency to the context in order to calculate the product price according to the MC currency
            $iMCCurrencyId = \Currency::getIdByIsoCode(self::getExplodeStoreId($sStoreId, 'currency'), \BTMailchimpEcommerce::$iShopId);
            $oCurrentCurrency = \Context::getContext()->currency;
            \Context::getContext()->currency = new \Currency($iMCCurrencyId);

            // set the product combination required
            $aDataToSync = (new \MCE\Chimp\Format\Combination(
                $iProdId,
                $iCombinationId,
                $iLangId,
                !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_format'
            ))->format();

            // set the previous currency
            \Context::getContext()->currency = $oCurrentCurrency;

            // add the lang id for the batch details
            $aDataToSync['lang_id'] = $iLangId;

            // sync to MC
            $bResult = self::addProductVariant($sListId, $sStoreId, $iProdId, $iLangId, $aDataToSync, $sMode);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }

    /**
     * format and send customer data
     *
     * @throws \Exception
     * @param string $sListId
     * @param mixed $sStoreId
     * @param obj $oCustomer
     * @param int $iLangId
     * @param string $sMode
     * @param string $sMethod
     * @return bool
     */
    public static function processCustomer($sListId, $sStoreId, $oCustomer, $iLangId, $sMode = 'regular', $sMethod = 'add')
    {
        $bResult = false;

        try {
            if (!self::excludeEmail($oCustomer->email)) {
                // detect the currency code
                $iCurrencyId = \Currency::getIdByIsoCode(self::getExplodeStoreId($sStoreId, 'currency'), \BTMailchimpEcommerce::$iShopId);

                // format the customer data
                $aDataToSync = (new \MCE\Chimp\Format\Customer($oCustomer, $iLangId, $iCurrencyId))->format();

                // add the lang id for the batch details
                $aDataToSync['lang_id'] = $iLangId;

                // send to MC : add or update
                $bResult = $sMethod == 'add' ? self::addCustomer($sListId, $sStoreId, $aDataToSync, $sMode) : self::updateCustomer($sListId, $sStoreId, $aDataToSync['id'], $aDataToSync, $sMode);
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }


    /**
     * format and send cart data
     *
     * @throws \Exception
     * @param string $sListId
     * @param mixed $sStoreId
     * @param int $iCartId
     * @param int $iCustomerId
     * @param int $iLangId
     * @param string $sMode
     * @param bool $bNested
     * @param int $iLimit
     * @return bool
     */
    public static function processCart($sListId, $sStoreId, $iCartId, $iCustomerId, $iLangId, $sMode = 'regular', $bNested = false, $iLimit = 0)
    {
        $bResult = false;

        try {
            // get the currency ID
            $iMcCurrencyId = \Currency::getIdByIsoCode(self::getExplodeStoreId($sStoreId, 'currency'), \BTMailchimpEcommerce::$iShopId);

            // set the cart data
            $aDataToSync = (new \MCE\Chimp\Format\Cart(
                $iCartId,
                $iLangId,
                $iMcCurrencyId,
                $iCustomerId,
                $bNested,
                $iLimit
            ))->format();

            // add the lang id for the batch details
            $aDataToSync['lang_id'] = $iLangId;
            $aDataToSync['customer_id'] = $iCustomerId;

            // send to MC
            $bResult = self::addCart($sListId, $sStoreId, $iCartId, $aDataToSync, $sMode);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }

    /**
     * format and send order data
     *
     * @throws \Exception
     * @param string $sListId
     * @param string $sStoreId
     * @param int $iOrderId
     * @param int $iCartId
     * @param int $iLangId
     * @param string $sMode
     * @param bool $bNested
     * @param string $sMethod
     * @param array $aOptions
     * @return bool
     */
    public static function processOrder($sListId, $sStoreId, $iOrderId, $iCartId, $iLangId, $sMode = 'regular', $bNested = false, $sMethod = 'add', $aOptions = array())
    {
        $bResult = false;

        try {
            // get the MC currency ID
            $iMcCurrencyId = \Currency::getIdByIsoCode(self::getExplodeStoreId($sStoreId, 'currency'), \BTMailchimpEcommerce::$iShopId);

            // set the order format
            $aDataToSync = (new \MCE\Chimp\Format\Order($iOrderId, $iLangId, $iMcCurrencyId, $bNested, $aOptions))->format();

            empty($iCartId) ? $iCartId = $aDataToSync['cart_id'] : '';

            // add the lang id for the batch details
            $aDataToSync['lang_id'] = $iLangId;
            $aDataToSync['cart_id'] = $iCartId;

            // send to MC : add or update
            $bResult = $sMethod == 'add' ? self::addOrder($sListId, $sStoreId, $iOrderId, $aDataToSync, $iCartId, $sMode) : self::updateOrder($sListId, $sStoreId, $iOrderId, $aDataToSync, $sMode);
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $bResult;
    }


    /**
     * get members from an array of lists (one ore many)
     *
     * @throws \Exception
     * @param array $aListIds
     * @param int $iFloor
     * @param int $iStep
     * @return array
     */
    public static function getUsersFromLists($aListIds, $iFloor = null, $iStep = null)
    {
        $aMembers = [];

        try {
            // instantiate the MC's controller
            $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

            foreach ($aListIds as $aList) {
                // set the current list Id
                $oMcCtrl->members->setId($aList['id']);

                // define the list of parameters to return
                $fields = ['members.email_address', 'members.status'];

                // get members
                if ($iFloor !== null && $iStep !== null) {
                    $aResult = $oMcCtrl->members->get(null, $fields, [], $iStep, $iFloor);
                } else {
                    $aResult = $oMcCtrl->members->get(null, $fields);
                }

                if (!empty($aResult['members'])) {
                    foreach ($aResult['members'] as $aMember) {
                        $aMembers[] = [
                            'email' => $aMember['email_address'],
                            'default_status' => ($aMember['status'] == 'transactional' ? \MCE\Chimp\Format\Member::UNSUBSCRIBED : $aMember['status']),
                            'id_lang' => $aList['lang_id']
                        ];
                    }
                }
            }
        } catch (\MCE\Chimp\MailchimpException $e) {
            // do nothing because we need to return the empty content if an exception is caught up
        }

        return $aMembers;
    }
}

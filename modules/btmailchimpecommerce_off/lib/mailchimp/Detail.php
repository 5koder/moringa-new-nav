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


class Detail
{
    /**
     * Detail constructor.
     */
    public function __construct()
    {
        require_once(_MCE_PATH_LIB . 'Dao.php');
    }

    /**
     * format sync detail to register the good detail to the synchronized element
     *
     * @param array $aResult
     * @param int $iSync
     * @return array
     */
    public function format(array $aResult, $iSync = 1)
    {
        // use case - no error - update sync to 1 (success)
        if (empty($aResult['error'])) {
            $aData = array(
                'sync' => $iSync,
                'detail' => \Tools::jsonEncode(array('status' => 'ok')),
            );
        } // use case - error - update sync to 0 (failed) and we register the error detail
        else {
            $aData = array(
                'detail' => \Tools::jsonEncode(array('status' => 'ko', 'error' => $aResult['error']))
            );
        }

        return $aData;
    }


    /**
     * register the current sync detail
     *
     * @param mixed $sSyncItemId
     * @param string $sType
     * @param string $sMCId
     * @param int $iShopId
     * @param string $sLinkedId
     * @param array $aData
     * @return bool
     */
    public function create($sSyncItemId, $sType, $sMCId, $iShopId, $sLinkedId = '0', $aData = array())
    {
        return \MCE\Dao::createSyncDetail($sSyncItemId, $sType, $sMCId, $iShopId, $sLinkedId, serialize($aData));
    }


    /**
     * return the sync detail
     *
     * @param mixed $sSyncItem
     * @param string $sMCId
     * @param int $iShopId
     * @param string $sType
     * @param bool $bCount
     * @param string $iLinkedId
     * @return bool
     */
    public function read($sSyncItem, $sMCId, $iShopId, $sType, $bCount = false, $iLinkedId = 0)
    {
        return \MCE\Dao::getSyncDetail($sSyncItem, $sMCId, $iShopId, $sType, $bCount, $iLinkedId);
    }


    /**
     * update the current sync detail like a customer / product / cart / order / member
     *
     * @param mixed $sSyncItem
     * @param string $sMCId
     * @param int $iShopId
     * @param string $sSyncType
     * @param array $aParams
     * @return bool
     */
    public static function update($sSyncItem, $sMCId, $iShopId, $sSyncType, array $aParams)
    {
        return \MCE\Dao::updateSyncDetail($sSyncItem, $sMCId, $iShopId, $sSyncType, $aParams);
    }

    /**
     * delete a all batches related to the current store
     *
     * @param mixed $sSyncItem
     * @param string $sMCId
     * @param int $iShopId
     * @param string $sSyncType
     * @return bool
     */
    public static function delete($sSyncItem, $sMCId, $iShopId, $sSyncType)
    {
        return \MCE\Dao::deleteSyncDetail($sSyncItem, $sMCId, $iShopId, $sSyncType);
    }

    /**
     * set singleton
     * @return obj
     */
    public static function get()
    {
        static $oDetail;

        if (null === $oDetail) {
            $oDetail = new \MCE\Chimp\Detail();
        }
        return $oDetail;
    }
}

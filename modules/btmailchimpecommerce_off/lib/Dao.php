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

class Dao
{
    /**
     * register the current MC list to our table
     *
     * @param int $iShopId
     * @param string $sId
     * @param string $sName
     * @param string $sMcStoreId
     * @param string $sMcStoreName
     * @param int $iActive
     * @param array $aData
     * @return bool
     */
    public static function createList(
        $iShopId,
        $sId,
        $sName,
        $sMcStoreId,
        $sMcStoreName,
        $iActive,
        $aData = array()
    ) {
        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_list (id, shop_id, name, store_id, store_name, active, data) '
            . 'VALUES("' . pSQL($sId) . '", ' . (int)$iShopId . ', "' . pSQL($sName) . '", "' . pSQL($sMcStoreId) . '", "' . pSQL($sMcStoreName) . '", "' . pSQL($iActive) . '", "'. pSQL(serialize($aData)) .'")';

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * update the current MC list to our table
     *
     * @param string $sId
     * @param array $aParams
     * @return bool
     */
    public static function updateList($sId, array $aParams)
    {
        // set
        $bReturn = true;

        if (!empty($aParams)) {
            // create query
            $sQuery = 'UPDATE ' . _DB_PREFIX_ .'mce_list SET ';

            // active
            if (isset($aParams['active'])) {
                $sQuery .= ' active = "' . pSQL($aParams['active']) . '", ';
            }
            // store id
            if (isset($aParams['store_id'])) {
                $sQuery .= ' store_id = "' . pSQL($aParams['store_id']) . '", ';
            }
            // store name
            if (isset($aParams['store_name'])) {
                $sQuery .= ' store_name = "' . pSQL($aParams['store_name']) . '", ';
            }
            // serialized data
            if (isset($aParams['data'])
                && is_array($aParams['data'])
            ) {
                $sQuery .= ' data = "' . pSQL(serialize($aParams['data'])) . '", ';
            }
            if (substr($sQuery, -2, 2) == ', ') {
                $sQuery = substr($sQuery, 0, strlen($sQuery) - 2);
                $sQuery .= ' WHERE id '. (!empty($aParams['not_list_id']) ? ' <> ' : ' = ') .' "' . pSQL($sId) . '"';

                $bReturn = \Db::getInstance()->Execute($sQuery);
            }
        }
        return $bReturn;
    }


    /**
     * delete a list locally
     *
     * @param int $iShopId
     * @param string $iListId
     * @return bool
     */
    public static function deleteList($iShopId, $sId)
    {
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_list WHERE shop_id = ' . (int)$iShopId . ' AND id = "' . pSQL($sId) . '"';

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * return all the registered lists or the specific list as passed as parameter
     *
     * @param int $iShopId
     * @param string $iListId
     * @param bool $active
     * @return array
     */
    public static function getLists($iShopId, $sId = null, $active = false)
    {
        $sQuery = 'SELECT * FROM ' . _DB_PREFIX_ .'mce_list as mcl WHERE mcl.shop_id = ' . (int)$iShopId . (!empty($sId) ? ' AND mcl.id = "' . pSQL($sId) . '"' : '');

        if (!empty($active)) {
            $sQuery .= ' AND mcl.active = "1"';
        }

        return \Db::getInstance()->ExecuteS($sQuery);
    }


    /**
     * return all the registered or not customer e-mail + list of e-mail addresses registered as newsletter subscribers
     *
     * @param int $iShopId
     * @param bool $bCount
     * @param array $aExcludedMails
     * @param int $iLangId : default lang
     * @param int $iFloor
     * @param int $iStep
     * @return mixed: array or int
     */
    public static function getUsers($iShopId, $bCount = false, $aExcludedMails = array(), $iLangId = 0, $iFloor = null, $iStep = null)
    {
        $result = null;

        if (!empty($bCount)) {
            $iTotalCustomers = self::getCustomerData($bCount, $aExcludedMails);
            $iTotalSubscribers = self::getNewsletterEmails($iShopId, $bCount, $aExcludedMails);

            $result = $iTotalCustomers + $iTotalSubscribers;
        } else {
            $aCustomers = self::getCustomerData($bCount, $aExcludedMails, $iFloor, $iStep);
            $aTotalSubscribers = self::getNewsletterEmails($iShopId, $bCount, $aExcludedMails, $iFloor, $iStep);

            if (!empty($aTotalSubscribers)) {
                foreach ($aTotalSubscribers as &$subscriber) {
                    $subscriber['id_lang'] = $iLangId;
                    $subscriber['id'] = 0;
                    $subscriber['optin'] = 1;
                    $subscriber['newsletter'] = 1;
                    $subscriber['firstname'] = '';
                    $subscriber['lastname'] = '';
                    $subscriber['birthday'] = '';
                    $subscriber['id_default_group'] = 0;
                }
                $result = !empty($aCustomers)? array_merge($aCustomers, $aTotalSubscribers) : $aTotalSubscribers;
            } elseif (!empty($aCustomers)) {
                $result = $aCustomers;
            }
        }

        return $result;
    }


    /**
     * returns the customers and NL subscribers data used for the email sync
     *
     * @param int $iShopId
     * @param bool $bCount
     * @param array $aExcludedMails
     * @param int $iFloor
     * @param int $iStep
     * @return mixed
     */
    public static function getCustomerData(
        $bCount = false,
        $aExcludedMails = array(),
        $iFloor = null,
        $iStep = null
    ) {
        // return an array
        if (empty($bCount)) {
            $select = 'c.id_customer as id, c.email as email, c.optin, c.newsletter as newsletter, c.firstname as firstname, c.lastname as lastname, c.birthday as birthday, c.id_lang, c.id_default_group, c.id_gender';
        // return the total
        } else {
            $select = 'count(*) as total';
        }

        $sQuery = 'SELECT '. $select
            . ' FROM ' . _DB_PREFIX_ . 'customer c '
            . ' WHERE c.active = 1' .\Shop::addSqlRestriction(true, 'c');

        // use case - excluded email list
        if (!empty($aExcludedMails)
            && is_array($aExcludedMails)
        ) {
            foreach ($aExcludedMails as $sDomainName) {
                $sQuery .= ' AND c.`email` NOT LIKE "%@' . pSQL($sDomainName) . '"';
            }
        }

        // range or not
        if ($iFloor !== null && !empty($iStep)) {
            $sQuery .= ' LIMIT ' . (int)$iFloor . ', ' . (int)$iStep;
        }

        $mResult = \Db::getInstance()->ExecuteS($sQuery);

        if ($bCount) {
            $mResult = $mResult[0]['total'];
        } else {
            $mResult = !empty($mResult) ? $mResult : array();
        }

        return $mResult;
    }


    /**
     * return all the newsletter e-mails
     *
     * @param int $iShopId
     * @param bool $bCount
     * @param array $aExcludedMails
     * @param int $iFloor
     * @param int $iStep
     * @return mixed: array or int
     */
    public static function getNewsletterEmails(
        $iShopId,
        $bCount = false,
        $aExcludedMails = array(),
        $iFloor = null,
        $iStep = null
    ) {
        $oQuery = new \DbQuery();
        if (empty($bCount)) {
            $oQuery->select('nl.`email`, nl.`active`');
        } else {
            $oQuery->select('count(*) as total');
        }
        // define the table name according to the PS version
        $sTable = (!empty(\BTMailchimpEcommerce::$bCompare17) ? 'emailsubscription' : 'newsletter');

        $oQuery->from($sTable, 'nl');
        $sWhere = 'nl.`id_shop` = ' . (int)$iShopId;

        // use case - excluded email list
        if (!empty($aExcludedMails)
            && is_array($aExcludedMails)
        ) {
            foreach ($aExcludedMails as $sDomainName) {
                $sWhere .= ' AND nl.`email` NOT LIKE "%@' . pSQL($sDomainName) . '"';
            }
        }

        $oQuery->where($sWhere);

        $aSubscribers = \Db::getInstance()->executeS($oQuery->build());

        if ($bCount) {
            $mResult = $aSubscribers[0]['total'];
        } else {
            $mResult = !empty($aSubscribers) ? $aSubscribers : array();
        }

        return $mResult;
    }


    /**
     * count the number of product by combination or not
     *
     * @param int $iShopId
     * @param bool $bCombination
     * @return int
     */
    public static function countProducts($iShopId, $bCombination = false)
    {
        $sQuery = 'SELECT COUNT(p.id_product) as cnt'
            . ' FROM ' . _DB_PREFIX_ . 'product p'
            . (version_compare(_PS_VERSION_, '1.5', '>') ?\Shop::addSqlAssociation('product', 'p', false) : '')
            . ($bCombination ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.id_product = pa.id_product)' : '')
            . ' WHERE ' . ((version_compare(_PS_VERSION_, '1.5', '>')) ? 'product_shop.active = 1' : 'p.`active` = 1');

        $aResult = \Db::getInstance()->getRow($sQuery);

        return !empty($aResult['cnt']) ? $aResult['cnt'] : 0;
    }


    /**
     * returns a list of product ID
     *
     * @param int $iFloor
     * @param int $iStep
     * @return mixed
     */
    public static function getProductIds($iFloor = null, $iStep = null)
    {
        $sQuery = 'SELECT DISTINCT(p.id_product) as id'
            . ' FROM ' . _DB_PREFIX_ . 'product p '
            .\Shop::addSqlAssociation('product', 'p', false)
            . ' WHERE product_shop.active = 1';

        // range or not
        if ($iFloor !== null && !empty($iStep)) {
            $sQuery .= ' LIMIT ' . (int)$iFloor . ', ' . (int)$iStep;
        }

        return \Db::getInstance()->ExecuteS($sQuery);
    }


    /**
     * returns the product's combination IDs
     *
     * @param int $iShopId
     * @param int $iProductId
     * @return mixed
     */
    public static function getProductCombinationIds($iShopId, $iProductId)
    {
        if (!empty(\BTMailchimpEcommerce::$bCompare1610)) {
            $sQuery = 'SELECT pas.id_product_attribute, pas.id_product'
                . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas'
                . ' WHERE pas.`id_product` = ' . (int)$iProductId . ' AND pas.id_shop = ' . (int)$iShopId;
        } else {
            $sQuery = 'SELECT pa.id_product_attribute, pa.id_product'
                . ' FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . ' WHERE pa.`id_product` = ' . (int)$iProductId;
        }

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aResult) ? $aResult : false;
    }


    /**
     * returns the product's combinations
     *
     * @param int $iShopId
     * @param int $iProductId
     * @param bool $bFull
     * @param int $iProdAttrId
     * @return mixed
     */
    public static function getProductCombination($iShopId, $iProductId, $iProdAttrId)
    {
        $sQuery = 'SELECT p.*, pa.id_product_attribute,pl.*, i.*, il.*, m.name AS manufacturer_name, s.name AS supplier_name,'
            . ' ps.product_supplier_reference AS supplier_reference'
            . ' FROM ' . _DB_PREFIX_ . 'product as p '
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute as pa ON (p.id_product = pa.id_product AND pa.id_product_attribute = ' . intval($iProdAttrId) . ')'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'product_lang as pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . intval(\BTMailchimpEcommerce::$iCurrentLang) .\Shop::addSqlRestrictionOnLang('pl') . ')'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'image as i ON (i.id_product = p.id_product AND i.cover = 1)'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'image_lang as il ON (i.id_image = il.id_image AND il.id_lang = ' . intval(\BTMailchimpEcommerce::$iCurrentLang) . ')'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer as m ON m.id_manufacturer = p.id_manufacturer'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'supplier as s ON s.id_supplier = p.id_supplier'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'product_supplier as ps ON (p.id_product = ps.id_product AND pa.id_product_attribute = ps.id_product_attribute)'
            . ' WHERE p.id_product = ' . intval($iProductId);

        $aAttributes = \Db::getInstance()->ExecuteS($sQuery);

        $aProduct = array();

        if (!empty($aAttributes[0])) {
            // get properties
            $aProduct = Product::getProductProperties(\BTMailchimpEcommerce::$iCurrentLang, $aAttributes[0]);

            if (!empty($aProduct)) {
                $aProduct['supplier_reference'] = $aAttributes[0]['supplier_reference'];
            }
        }

        return $aProduct;
    }

    /**
     * returns the product's combination attributes
     *
     * @param int $iProdAttributeId
     * @param int $iLangId
     * @param int $iShopId
     * @return mixed
     */
    public static function getCombinationAttributeNames($iProdAttributeId, $iLangId, $iShopId)
    {
        $sQuery = 'SELECT distinct(al.`name`)'
            . ' FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pa'
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`'
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)$iLangId . ')'
            . ' WHERE pac.`id_product_attribute` = ' . (int)($iProdAttributeId)
            . ' AND pa.id_shop = ' . (int)$iShopId
            . ' ORDER BY al.`name`';

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aResult) ? $aResult : false;
    }


    /**
     * returns the sync status of the synchronization
     *
     * @param int $iShopId
     * @param string $sId
     * @param string $sSyncType
     * @return mixed array or false
     */
    public static function getSyncStatus($iShopId, $sId, $sSyncType = '')
    {
        $sQuery = 'SELECT shop_id, type, sync, UNIX_TIMESTAMP(date_add) as date_add, UNIX_TIMESTAMP(date_upd) as date_upd'
            . ' FROM ' . _DB_PREFIX_ .'mce_sync as mcs'
            . ' WHERE mcs.id = "'. pSQL($sId) .'" AND mcs.shop_id = ' . (int)$iShopId;

        if (!empty($sSyncType)) {
            if (is_array($sSyncType)) {
                $sQuery .= ' AND mcs.type IN(' . implode(', ', $sSyncType) . ')';
            } else {
                $sQuery .= ' AND mcs.type = "' . pSQL($sSyncType) . '"';
            }
        }

        $aSyncStatus = \Db::getInstance()->ExecuteS($sQuery);

        if (_MCE_DEBUG
            && empty($aSyncStatus)
        ) {
            $aSyncStatus[] = array(
                'shop_id' => $iShopId,
                'sync' => 2,
                'date_add' => time(),
                'date_upd' => time(),
            );
        }

        if (!empty($aSyncStatus)) {
            if (!empty($sSyncType) && is_string($sSyncType)) {
                $aSyncStatus = $aSyncStatus[0];
            }
        } else {
            $aSyncStatus = false;
        }

        return $aSyncStatus;
    }


    /**
     * create sync status about shop content synchronized to MC: a list content or store content
     *
     * @param int $iShopId
     * @param string $sId
     * @param string $sSyncType
     * @return bool
     */
    public static function createSyncStatus($iShopId, $sId, $sSyncType)
    {
        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_sync (type, id, shop_id, sync, date_add, date_upd) '
            . 'VALUES("' . pSQL($sSyncType) . '", "' . pSQL($sId) . '", ' . (int)$iShopId . ', "0", NOW(), NOW())';

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * update the current catalog
     *
     * @param int $iShopId
     * @param string $sId
     * @param string $sSyncType
     * @param array $aParams
     * @return bool
     */
    public static function updateSyncStatus($iShopId, $sId, $sSyncType, array $aParams)
    {
        // set
        $bReturn = true;

        if (!empty($aParams)) {
            // create query
            $sQuery = 'UPDATE ' . _DB_PREFIX_ .'mce_sync SET ';

            // sync
            if (isset($aParams['sync'])) {
                $sQuery .= ' sync = "' . pSQL($aParams['sync']) . '", ';
            }
            if (substr($sQuery, -2, 2) == ', ') {
                $sQuery = substr($sQuery, 0, strlen($sQuery) - 2);
            }
            $sQuery .= ' WHERE type = "' . pSQL($sSyncType) . '" AND id = "' . pSQL($sId) . '" AND shop_id = '. (int)$iShopId;
            $bReturn = \Db::getInstance()->Execute($sQuery);
        }

        return $bReturn;
    }


    /**
     * delete all synchronized content related to a list or a store
     *
     * @param int $iShopId
     * @param string $sId
     * @param string $sSyncType
     * @return bool
     */
    public static function deleteSyncStatus($iShopId, $sId, $sSyncType = null)
    {
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync WHERE id = "' . pSQL($sId) . '" AND shop_id = ' . (int)$iShopId;

        if ($sSyncType !== null) {
            if (is_array($sSyncType)) {
                $sQuery .= ' AND type IN("' . implode('", "', $sSyncType) . '")';
            } else {
                $sQuery .= ' AND type = "' . pSQL($sSyncType) . '"';
            }
        }

        return \Db::getInstance()->Execute($sQuery);
    }

    /**
     * register the current batch for ecommerce or newsletter part
     *
     * @param string $sBatchId
     * @param string $sId
     * @param int $iShopId
     * @param string $sMode
     * @param string $sType
     * @param int $iFloor
     * @param int $iStep
     * @param array $aData
     * @return bool
     */
    public static function createBatch($sBatchId, $sId, $iShopId, $sMode, $sType, $iFloor, $iStep, $aData)
    {
        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_batch (batch_id, id, shop_id, mode, type, floor, step, data) '
            . 'VALUES("' . pSQL($sBatchId) . '", "' . pSQL($sId) . '", ' . (int)$iShopId . ', "' . pSQL($sMode) . '", "' . pSQL($sType) . '", ' . (int)$iFloor . ', ' . (int)$iStep . ',"'. pSQL(serialize($aData)) .'")';

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * returns the list of batches created for one list / store products synchronization
     *
     * @param int $iShopId
     * @param array $aParams
     * @return mixed array or false
     */
    public static function getBatches($iShopId, $aParams = array())
    {
        $sQuery = 'SELECT * FROM ' . _DB_PREFIX_ .'mce_batch as mcsb '
            . ' WHERE mcsb.shop_id = ' . (int)$iShopId;

        if (!empty($aParams['batch_id'])) {
            $sQuery .= ' AND mcsb.batch_id = "' . pSQL($aParams['batch_id']) .'"';
        }

        if (!empty($aParams['id'])) {
            $sQuery .= ' AND mcsb.id = "' . pSQL($aParams['id']) .'"';
        }

        if (!empty($aParams['type'])) {
            $sQuery .= ' AND mcsb.type = "' . pSQL($aParams['type']) .'"';
        }

        if (!empty($aParams['mode'])) {
            $sQuery .= ' AND mcsb.mode = "' . pSQL($aParams['mode']) .'"';
        }

        $sQuery .= ' ORDER BY mcsb.floor ASC';

        $aBatches = \Db::getInstance()->ExecuteS($sQuery);

        // if not empty batch id
        if (!empty($aParams['batch_id'])
            && !empty($aBatches[0])
        ) {
            $aBatches = $aBatches[0];
        }

        return !empty($aBatches) ? $aBatches : false;
    }

    /**
     * delete a all batches related to the current store or a list or shop or one batch
     *
     * @param int $iShopId
     * @param array $aParams
     * @return bool
     */
    public static function deleteBatch($iShopId, $aParams = array())
    {
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_batch WHERE shop_id = ' . (int)$iShopId;

        if (!empty($aParams['batch_id'])) {
            $sQuery .= ' AND batch_id = "' . pSQL($aParams['batch_id']) .'"';
        }

        if (!empty($aParams['id'])) {
            $sQuery .= ' AND id = "' . pSQL($aParams['id']) .'"';
        }

        if (!empty($aParams['type'])) {
            $sQuery .= ' AND type = "' . pSQL($aParams['type']) .'"';
        }

        if (!empty($aParams['mode'])) {
            $sQuery .= ' AND mode = "' . pSQL($aParams['mode']) .'"';
        }

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * register the current sync detail
     *
     * @param mixed $sSyncItemId
     * @param string $sType
     * @param string $sMCId
     * @param int $iShopId
     * @param string $sLinkedId
     * @param string $sDetail
     * @return bool
     */
    public static function createSyncDetail($sSyncItemId, $sType, $sMCId, $iShopId, $sLinkedId = '0', $sDetail = '')
    {
        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_sync_detail (id, type, linked_id, mc_id, shop_id, sync, detail, date_add, date_upd, times) '
            . 'VALUES("' . pSQL($sSyncItemId) . '",  "' . pSQL($sType) . '", "' . pSQL($sLinkedId) . '", "' . pSQL($sMCId) . '", ' . (int)$iShopId . ', "0", "'. pSQL($sDetail) .'", NOW(), NOW(), 1)';

        return \Db::getInstance()->Execute($sQuery);
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
    public static function getSyncDetail($sSyncItem, $sMCId, $iShopId, $sType, $bCount = false, $iLinkedId = 0)
    {
        $sQuery = 'SELECT ' . (($bCount) ? 'count(*) as nb' : '*') . ' FROM ' . _DB_PREFIX_ .'mce_sync_detail as mcsd '
            . ' WHERE mcsd.id = "' . pSQL($sSyncItem) . '" AND mcsd.mc_id = "' . pSQL($sMCId) . '" AND mcsd.shop_id = ' . (int)$iShopId . ' AND mcsd.type = "' . pSQL($sType) . '"';

        if (!empty($iLinkedId)) {
            $sQuery .= ' AND mcsd.linked_id = ' . (int)$iLinkedId;
        }

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        if ($bCount) {
            $mReturn = !empty($aResult[0]['nb']) ? $aResult[0]['nb'] : 0;
        } else {
            $mReturn = !empty($aResult[0]) ? $aResult[0] : [];
        }

        return $mReturn;
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
    public static function updateSyncDetail($sSyncItem, $sMCId, $iShopId, $sSyncType, array $aParams)
    {
        // set
        $bReturn = true;

        if (!empty($aParams)) {
            // create query
            $sQuery = 'UPDATE ' . _DB_PREFIX_ .'mce_sync_detail SET ';

            // sync
            if (isset($aParams['sync'])) {
                $sQuery .= ' sync = "' . pSQL($aParams['sync']) . '", ';
            }
            // detail
            if (isset($aParams['detail'])) {
                $sQuery .= ' detail = "' . pSQL($aParams['detail']) . '", ';
            }
            // count
            if (!empty($aParams['count'])) {
                $sQuery .= ' times = times+1, ';
            }

            // date_upd
            $sQuery .= ' date_upd = NOW(), ';

            if (substr($sQuery, -2, 2) == ', ') {
                $sQuery = substr($sQuery, 0, strlen($sQuery) - 2);
            }
            $sQuery .= ' WHERE id = "' . pSQL($sSyncItem) . '" AND type = "' . pSQL($sSyncType) . '" AND mc_id = "' . pSQL($sMCId) . '" AND shop_id = ' . (int)$iShopId;

            $bReturn = \Db::getInstance()->Execute($sQuery);
        }

        return $bReturn;
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
    public static function deleteSyncDetail($sSyncItem, $sMCId, $iShopId, $sSyncType)
    {
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync_detail WHERE id = "' . pSQL($sSyncItem) . '" AND type = "' . pSQL($sSyncType) . '" AND mc_id = "' . pSQL($sMCId) . '" AND shop_id = ' . (int)$iShopId;

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * returns order IDs by date
     *
     * @param string $sDateFrom
     * @param string $sDateTo
     * @param int $iLangId
     * @param int $iCustomerId
     * @param string $sDateType : date_add or date_upd
     * @param array $aListStateIds : array of state IDs
     * @param int $iFloor
     * @param int $iStep
     * @return array
     */
    public static function getOrdersIdByDate(
        $sDateFrom,
        $sDateTo,
        $iLangId = null,
        $iCustomerId = null,
        $sDateType = 'date_add',
        $aListStateIds = null,
        $iFloor = null,
        $iStep = null
    ) {
        $sQuery = 'SELECT o.`id_order`'
            . ' FROM `' . _DB_PREFIX_ . 'orders` as o'
            . ($iCustomerId !== 0 ? ' LEFT JOIN ' . _DB_PREFIX_ . 'customer as c ON (o.id_customer = c.id_customer)' : '')
            . ($aListStateIds !== null ? ' LEFT JOIN ' . _DB_PREFIX_ . 'order_history as oh ON (o.id_order = oh.id_order)'
            . ' LEFT JOIN ' . _DB_PREFIX_ . 'order_state_lang as osl ON (oh.id_order_state = osl.id_order_state AND osl.id_lang = ' . (int)$iLangId . ')' : '')
            . ' WHERE o.' . pSQL($sDateType) . ' <= \'' . pSQL($sDateTo) . '\' AND o.' . pSQL($sDateType) . ' >= \'' . pSQL($sDateFrom) . '\''
            .\Shop::addSqlRestriction(false, 'o')
            . ($iCustomerId ? ' AND id_customer = ' . (int)$iCustomerId : '');

        if ($aListStateIds !== null && is_array($aListStateIds)) {
            $sQuery .= ' AND oh.id_order_state IN(' . implode(', ', $aListStateIds) . ')'
                . ' AND oh.id_order_history = (SELECT MAX(od2.id_order_history) FROM ' . _DB_PREFIX_ . 'order_history as od2 WHERE o.id_order = od2.id_order)';
        }

        // range or not
        if ($iFloor !== null && $iStep !== null) {
            $sQuery .= ' LIMIT ' . (int)$iFloor . ', ' . (int)$iStep;
        }

        $aResult = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sQuery);

        // set
        $aOrders = array();

        if (!empty($aResult)) {
            foreach ($aResult as $aOrder) {
                $aOrders[] = (int)$aOrder['id_order'];
            }
        }
        return $aOrders;
    }

    /**
     * Get customer orders.
     *
     * @param int $id_customer Customer id
     * @return array Customer orders
     */
    public static function getCustomerOrders($id_customer)
    {
        $sQuery = 'SELECT o.`total_paid`, o.`id_currency`, o.`id_address_invoice`'
            . ' FROM `' . _DB_PREFIX_ . 'orders` as o'
            . ' WHERE o.`valid` = 1 AND o.`id_customer` = ' . (int)$id_customer
            . \Shop::addSqlRestriction(false, 'o');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sQuery);
    }

    /**
     * returns list of status order
     *
     * @return array
     */
    public static function getOrderStatus()
    {
        // set variable
        $aStatusTmp = array();

        // set query
        $sQuery = 'SELECT * FROM ' . _DB_PREFIX_ . 'order_state_lang';

        $aStatusTmp = \Db::getInstance()->ExecuteS($sQuery);

        foreach ($aStatusTmp as $aStatus) {
            $aStatusOrder[$aStatus['id_order_state']][$aStatus['id_lang']] = $aStatus['name'];
        }

        return $aStatusOrder;
    }


    /**
     * insert the lang ID at the moment
     *
     * @param int $iShopId
     * @param int $iLangId
     * @param mixed $mCustomerIds : array or int
     * @return bool
     */
//    public static function addCustomerLangId($iShopId, $iLangId, $mCustomerIds)
//    {
//        // set query
//        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_customer_lang (id_customer, id_shop, id_lang)';
//
//        if (is_array($mCustomerIds)) {
//            $sQuery .= ' VALUES';
//            foreach ($mCustomerIds as $iCustomerId) {
//                $sQuery .= '(' . (int)$iCustomerId . ', ' . (int)$iShopId . ', ' . (int)$iLangId . '),';
//            }
//            if (substr($sQuery, -1, 1) == ',') {
//                $sQuery = substr($sQuery, 0, strlen($sQuery) - 1);
//            }
//        } else {
//            $sQuery .= ' VALUES(' . (int)$mCustomerIds . ', ' . (int)$iShopId . ', ' . (int)$iLangId . ')';
//        }
//        $sQuery .= 'ON DUPLICATE KEY UPDATE id_lang = VALUES(id_lang)';
//
//        return \Db::getInstance()->Execute($sQuery);
//    }


    /**
     * return the customers in PS table
     *
     * @param int $iShopId
     * @return array
     */
    public static function getCurrentCustomer($iShopId)
    {
        // set query
        $sQuery = 'SELECT id_customer, id_lang FROM ' . _DB_PREFIX_ . 'customer WHERE id_shop = ' . (int)$iShopId;

        $aCustomer = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aCustomer) ? $aCustomer : 0;
    }

    /**
     * return the sync detail
     *
     * @param int $iShopId
     * @param string $sListId
     * @param string $sSyncType
     * @param bool $bCount
     * @param int $iTime
     * @param int $iDelay
     * @return bool
     */
    public static function getSyncData(
        $iShopId,
        $sListId = null,
        $sSyncType = null,
        $bCount = false,
        $iTime = false,
        $iDelay = false
    ) {
        $sQuery = 'SELECT ' . (($bCount) ? 'count(*) as nb' : '*') . ' FROM ' . _DB_PREFIX_ .'mce_sync_detail as mcsd '
            . ' WHERE mcsd.shop_id = ' . (int)$iShopId;

        // use case - specific list
        if ($sListId !== null) {
            $sQuery .= ' AND mcsd.mc_id = "' . pSQL($sListId) . '"';
        }

        // use case - specific type
        if ($sSyncType !== null) {
            $sQuery .= ' AND mcsd.type = "' . pSQL($sSyncType) . '"';
        }

        // use case - specific date
        if (!empty($iTime) && !empty($iDelay)) {
            $sQuery .= ' AND ' . (int)$iTime . ' >= UNIX_TIMESTAMP(mcsd.date_add) AND UNIX_TIMESTAMP(mcsd.date_add) >= ' . ((int)$iTime - $iDelay);
        }
        $sQuery .= ' ORDER BY mcsd.date_add DESC';

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        if ($bCount) {
            $mReturn = $aResult[0]['nb'];
        } else {
            $mReturn = $aResult;
        }

        return $mReturn;
    }


    /**
     * add a new cart rule
     *
     * @param int $iCartRuleId
     * @param int $iQuantity
     * @param string $sType
     * @param array $aIds
     * @return bool
     */
    public static function addProductRule($iCartRuleId, $iQuantity, $sType, array $aIds)
    {
        $bInsert = false;

        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group (id_cart_rule, quantity) VALUES('
            . (int)$iCartRuleId . ', ' . (int)$iQuantity . ')';

        // only if group rule is added
        if (\Db::getInstance()->Execute($sQuery)) {

            $sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule (id_product_rule_group, type) VALUES('
                . \Db::getInstance()->Insert_ID() . ', "' . pSQL($sType) . '")';

            // only if product rule is added
            if (\Db::getInstance()->Execute($sQuery)) {

                if (!empty($aIds)) {
                    $bInsert = true;

                    $iLastInsertId = \Db::getInstance()->Insert_ID();

                    foreach ($aIds as $iId) {
                        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value (id_product_rule, id_item) VALUES('
                            . (int)$iLastInsertId . ', ' . (int)$iId . ')';

                        if (!\Db::getInstance()->Execute($sQuery)) {
                            $bInsert = false;
                        }
                    }
                }
            }
        }
        // commit or rollback transaction
        $bInsert = ($bInsert) ? \Db::getInstance()->Execute('COMMIT') : \Db::getInstance()->Execute('ROLLBACK');

        return $bInsert;
    }

    /**
     * get the order state lang values
     *
     * @param int $iOrderState
     * @param int $iLangId
     * @return array
     */
    public static function getOrderStateLang($iOrderState, $iLangId)
    {
        $sQuery = 'SELECT * FROM ' . _DB_PREFIX_ . 'order_state_lang WHERE id_order_state = ' . (int)$iOrderState . ' AND id_lang = ' . (int)$iLangId;

        return \Db::getInstance()->getRow($sQuery);
    }

    /**
     * check if the item already in the table for the cron execution
     *
     * @param int $iShopId
     * @param int $iItemId
     * @param string $sType
     * @return int
     */
    public static function isExistCronItem($iShopId, $iItemId, $sType)
    {
        // set query
        $sQuery = 'SELECT count(*) as nb FROM ' . _DB_PREFIX_ .'mce_sync_data WHERE shop_id = ' . (int)$iShopId . ' AND id = ' . (int)$iItemId .' AND type = "'. pSQL($sType) .'"';

        $aItem = \Db::getInstance()->ExecuteS($sQuery);

        return !empty($aItem[0]['nb']) ? $aItem[0]['nb'] : 0;
    }

    /**
     * add the item for the cron execution
     *
     * @param int $iShopId
     * @param int $iItemId
     * @param string $sType
     * @param array $aData
     * @return int
     */
    public static function addCronItem($iShopId, $iItemId, $sType, $aData)
    {
        $sQuery = 'INSERT INTO ' . _DB_PREFIX_ .'mce_sync_data (id, shop_id, type, data) '
            . 'VALUES(' . (int)$iItemId . ', ' . (int)$iShopId . ', "'. pSQL($sType) .'", "'. pSQL(serialize($aData)) .'")';

        return \Db::getInstance()->Execute($sQuery);
    }

    /**
     * return the item IDs to send to MC
     *
     * @param int $iShopId
     * @param bool $bCount
     * @param int $iFloor
     * @param int $iStep
     * @return int
     */
    public static function getCronItems($iShopId, $bCount = false, $iFloor = null, $iStep = null)
    {
        // set query
        $sQuery = 'SELECT ' . ($bCount ? 'count(*) as nb' : '*') . ' FROM ' . _DB_PREFIX_ .'mce_sync_data WHERE shop_id = ' . (int)$iShopId;

        // range or not
        if ($iFloor !== null && !empty($iStep)) {
            $sQuery .= ' LIMIT ' . (int)$iFloor . ', ' . (int)$iStep;
        }

        $aResult = \Db::getInstance()->ExecuteS($sQuery);

        if ($bCount) {
            $aResult = $aResult[0]['nb'];
        }

        return $aResult;
    }

    /**
     * delete all the items updated and processed via the cron URL and related to the current store
     *
     * @param int $iShopId
     * @return bool
     */
    public static function deleteCronItem($iShopId)
    {
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync_data WHERE shop_id = ' . (int)$iShopId;

        return \Db::getInstance()->Execute($sQuery);
    }


    /**
     * delete a list locally
     *
     * @param int $iShopId
     * @param string $iListId
     * @return bool
     */
    public static function resetTables($iShopId)
    {
        // reset the list table for the shop ID
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_list WHERE shop_id = ' . (int)$iShopId;
        $result = \Db::getInstance()->Execute($sQuery);

        // reset the sync table for the shop ID
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync WHERE shop_id = ' . (int)$iShopId;
        $result = \Db::getInstance()->Execute($sQuery);

        // reset the sync table for the shop ID
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync_detail WHERE shop_id = ' . (int)$iShopId;
        $result = \Db::getInstance()->Execute($sQuery);

        // reset the sync table for the shop ID
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_sync_data WHERE shop_id = ' . (int)$iShopId;
        $result = \Db::getInstance()->Execute($sQuery);

        // reset the batch table for the shop ID
        $sQuery = 'DELETE FROM ' . _DB_PREFIX_ .'mce_batch WHERE shop_id = ' . (int)$iShopId;
        $result = \Db::getInstance()->Execute($sQuery);

        return $result;
    }

}
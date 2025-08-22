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

class AdminGenerate implements \BT_IAdmin
{
    /**
     * generate content according to the parameters
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     * @return array
     */
    public function run($sType, array $aParam = null)
    {
        // set variables
        $aDisplayData = array();

        // include
        require_once(_MCE_PATH_LIB . 'Dao.php');

        // get loader
        \BTMailchimpEcommerce::getMailchimpLoader();

        switch ($sType) {
            case 'cron' : // use case - generate the cron to export the updated products
            case 'batchDelete' : // use case - generate the cron to delete batches created by the products cron URL
                // execute match function
                $aDisplayData = call_user_func_array(array($this, 'generate' . ucfirst($sType)), array($aParam));
                break;
            default :
                break;
        }
        return $aDisplayData;
    }


    /**
     * get the list of items to sync to MC
     *
     * @param array $aPost
     * @return array
     */
    private function generateCron(array $aPost)
    {
        // set
        $assign = array();

        try {
            // use case -  if list and the newsletter/ecommerce active
            if (\MCE\Chimp\Facade::isActive()) {

                // get the total items to sync to MC
                $iTotalItems = \MCE\Dao::getCronItems(\BTMailchimpEcommerce::$iShopId, true);

                if (!empty($iTotalItems)) {
                    // get the list and store ID
                    $sListId = \MCE\Chimp\Facade::get('id');
                    $sStoreId = \MCE\Chimp\Facade::get('store_id');
                    $iLoop = 1;
                    $iFloor = 0;
                    $iStep = \BTMailchimpEcommerce::$conf['MCE_ECOMMERCE_CRON_CYCLE'];

                    if ($iTotalItems > $iStep) {
                        $iLoop = (int)ceil(($iTotalItems / $iStep));
                    } else {
                        $iStep = $iTotalItems;
                    }


                    // loop on the number of items per cycle
                    for ($i = 1; $i <= $iLoop; $i++) {
                        if ($i != 1) {
                            $iFloor += $iStep;
                        }
                        // get the current cron items
                        $aItems = \MCE\Dao::getCronItems(\BTMailchimpEcommerce::$iShopId, false, $iFloor, $iStep);

                        if (!empty($aItems)) {
                            // loop on items to prepare them to the batch operation in MailChimp
                            foreach ($aItems as $iKey => $aItem) {
                                // unserialize data
                                $data = unserialize($aItem['data']);

                                switch ($aItem['type']) {
                                    case 'member':
                                        $bResult = \MCE\Chimp\Facade::processMember($sListId, $aItem['id'], $data['lang_id'], \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', 'cron');
                                        break;
                                    case 'customer':
                                        $oCustomer = new \Customer($aItem['id']);

                                        if (\Validate::isLoadedObject($oCustomer)) {
                                            $bResult = \MCE\Chimp\Facade::processMember($sListId, $oCustomer, $oCustomer->id_lang, \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', 'cron');
                                            // the case of creating the member as new customer of the Shop, then we should not set the newsletter status to true even if it's the case as the pending mode could be defined and we doesn't want to override it
                                            if (!empty(\BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'])) {
                                                $oCustomer->newsletter = false;
                                            }
                                            $bResult = \MCE\Chimp\Facade::processCustomer($sListId, $sStoreId, $oCustomer, $oCustomer->id_lang, 'cron', $data['method']);
                                        }
                                        break;
                                    case 'product':
                                        foreach (\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] as $id_lang) {
                                            // use case - the product already exists, we update it
                                            $method = \MCE\Chimp\Facade::isProductExist($sStoreId, $aItem['id'], $id_lang) ? 'update' : 'add';
                                            $bResult = \MCE\Chimp\Facade::processProduct($sListId, $sStoreId, $aItem['id'], $id_lang, 'cron', $method);
                                        }
                                        break;
                                    case 'variant':
                                        $bResult = \MCE\Chimp\Facade::processVariant($sListId, $sStoreId, $data['id_product'], $aItem['id'], $data['lang_id'], 'cron');
                                        break;
                                    case 'cart':
                                        $bResult = \MCE\Chimp\Facade::processCart($sListId, $sStoreId, $aItem['id'], $data['customer_id'], $data['lang_id'], 'cron', false, \BTMailchimpEcommerce::$conf['MCE_CART_MAX_PROD']);
                                        break;
                                    case 'order':
                                        $bResult = \MCE\Chimp\Facade::processOrder($sListId, $sStoreId, $aItem['id'], $data['cart_id'], $data['lang_id'], 'cron', false, $data['method']);
                                        break;
                                    default:
                                        break;
                                }
                            }

                            // get the content stored for creating batches
                            $aCurrentItems = \MCE\Chimp\Facade::getItemsForBatches();

                            // get the serialized content stored to mapping batches and shop's data
                            $aSerialized = \MCE\Chimp\Facade::getSerializedForBatches();

                            if (!empty($aCurrentItems)) {
                                // instantiate the MC's controller
                                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                                // post the current range of items by creating a batch in MC
                                $aResult = $oMcCtrl->batches->add(array('operations' => $aCurrentItems));

                                if (!empty($aResult['id'])) {
                                    \MCE\Dao::createBatch($aResult['id'], $sListId, \BTMailchimpEcommerce::$iShopId, 'cron','all', $iFloor, $iStep, $aSerialized);
                                    echo \BTMailchimpEcommerce::$oModule->l('The data batch was created with success!', 'AdminGenerate') . ' (ID: ' . $aResult['id'] . ')' . "\n";
                                }
                            }
                        }
                    }
                } else {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('There is no item to send to MailChimp', 'AdminGenerate_class') . '.', 123);
                }
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('There is no list / store activated', 'AdminGenerate_class') . '.', 124);
            }
            // delete the items in the cron table
            \MCE\Dao::deleteCronItem(\BTMailchimpEcommerce::$iShopId);
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        $assign['bUpdate'] = empty($assign['aErrors']) ? true : false;
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_ADMIN_PATH . _MCE_TPL_ERROR);

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_EMPTY,
            'assign' => $assign,
        );
    }

    /**
     * generate the list of updated items to synch to MC
     *
     * @param array $aPost
     * @return array
     */
    private function generateBatchDelete(array $aPost)
    {
        // set
        $assign = array();

        try {
            $aList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);
            $aList = !empty($aList[0]) ? $aList[0] : $aList;

            // get the batches to check if the synchronization is over for the current shop / list  / store
            $aBatches = \MCE\Dao::getBatches(\BTMailchimpEcommerce::$iShopId, array('id' => $aList['id']));

            if (!empty($aBatches)) {
                // get the MC matching object
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);

                foreach ($aBatches as &$aBatch) {
                    try {
                        $aResult = $oMcCtrl->batches->get($aBatch['batch_id']);
                    } catch (\Exception $e) {
                        $aResult = array();
                    }

                    // check if the batch exists on the MC side
                    if (!empty($aResult)) {
                        // check the status
                        if ($aResult['status'] == 'finished') {
                            // handle time : now and the completed date and time of the current batch
                            $now = time();
                            list($date, $time_adv) = explode('T', $aResult['completed_at']);
                            list($time, $jetlag) = explode('+', $time_adv);
                            list($year, $month, $day) = explode('-', $date);
                            list($hour, $min, $sec) = explode(':', $time);
                            $completed_date_and_time = mktime($hour, $min, $sec, $month, $day, $year);

                            // if the time is over the defined delay then we delete the batch on MC side
                            if (($now - $completed_date_and_time) >= _MCE_BATCH_DELAY) {
                                $aResult = $oMcCtrl->batches->delete($aResult['id']);
                                if (empty($aResult['errors'])) {
                                    echo 'Deleting the batch ID: '. $aBatch['batch_id'] .' on the Mailchimp side is done well!'. "\n";
                                }
                            }
                        }
                    } else {
                        // delete the current batch from our table
                      $bResult = \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('batch_id' => $aBatch['batch_id']));
                      echo 'Deleting the batch ID: '. $aBatch['batch_id'] .' locally is done well!'. "\n";
                    }
                }
            }
        } catch (\Exception $e) {}

        return array(
            'tpl' => _MCE_TPL_ADMIN_PATH . _MCE_TPL_EMPTY,
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
        static $oGenerate;

        if (null === $oGenerate) {
            $oGenerate = new \MCE\AdminGenerate();
        }
        return $oGenerate;
    }
}

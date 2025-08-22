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

use BTMailchimpEcommerce;

class HookAction extends HookBase
{
    /**
     * execute hook
     *
     * @param array $aParams
     * @return array
     */
    public function run(array $aParams = null)
    {
        // set variables
        $aDisplayHook = array();

        require_once(_MCE_PATH_LIB . 'Dao.php');

        // get loader
        \BTMailchimpEcommerce::getMailchimpLoader();

        switch ($this->sHook) {
            case 'productAdd':
                // use case - execute when a product is created
                $aDisplayHook = call_user_func_array(array($this, 'addProduct'), array($aParams));
                break;
            case 'productUpdate':
                // use case - execute when a product is updated
                $aDisplayHook = call_user_func_array(array($this, 'updateProduct'), array($aParams));
                break;
            case 'productDelete':
                // use case - execute when a product is deleted
                $aDisplayHook = call_user_func_array(array($this, 'deleteProduct'), array($aParams));
                break;
            case 'combinationUpdate':
                // use case - execute when a product combination is updated
                $aDisplayHook = call_user_func_array(array($this, 'updateCombination'), array($aParams));
                break;
            case 'combinationDelete':
                // use case - execute when a product combination is deleted
                $aDisplayHook = call_user_func_array(array($this, 'deleteCombination'), array($aParams));
                break;
            case 'customerAccountAdd':
                // use case - display nothing only process data to MC for the add customer action
                $aDisplayHook = call_user_func_array(array($this, 'addCustomer'), array($aParams));
                break;
            case 'customerAccountUpdate':
                // use case - display nothing only process data to MC for the update customer action
                $aDisplayHook = call_user_func_array(array($this, 'updateCustomer'), array($aParams));
                break;
            case 'cartSave':
                // use case - display nothing only process data to MC for the cart save action
                $aDisplayHook = call_user_func_array(array($this, 'saveCart'), array($aParams));
                break;
            case 'validateOrder':
                // use case - display nothing only process data to MC for the order validate
                $aDisplayHook = call_user_func_array(array($this, 'validateOrder'), array($aParams));
                break;
            case 'updateOrderStatus':
                // use case - display nothing only process data to MC for the order status update
                $aDisplayHook = call_user_func_array(array($this, 'updateOrderStatus'), array($aParams));
                break;
            case 'setMedia':
                // use case - add media to the controller
                $aDisplayHook = call_user_func_array(array($this, 'setMedia'), array($aParams));
                break;
            case 'batchWebhook':
                // use case - execute when a batch is processed and finalized
                $aDisplayHook = call_user_func_array(array($this, 'processBatchWebhook'), array($aParams));
                break;
            case 'listWebhook':
                // use case - execute when a MC event is processed and finalized
                $aDisplayHook = call_user_func_array(array($this, 'processListWebhook'), array($aParams));
                break;
            case 'signupDisplay':
                // use case - execute when the visitor clicks on the "not display the popup" anymore
                $aDisplayHook = call_user_func_array(array($this, 'processSignupPopupDisplay'), array($aParams));
                break;
            case 'registeremail':
                // use case - register the visitor as sbuscribed user to the MC list
                if (\Tools::getIsset('bt_nl_email')) {
                    $aDisplayHook = call_user_func(array($this, 'registerNewsletterEmail'), \Tools::getValue('bt_nl_email'));
                }
                break;
            default:
                break;
        }

        return $aDisplayHook;
    }


    /**
     * add a new product when it's created and we just create an entry in our table sync_detail
     *
     * @param array $aParams
     * @return array
     */
    private function addProduct(array $aParams = null)
    {
        // use case - detect the product id and the product obj
        if (
            !empty($aParams['id_product'])
            && !empty($aParams['product'])
            && is_object($aParams['product'])
        ) {
            try {
                if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                    // loop on product languages to get the same product in all the active languages and currencies
                    foreach (\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] as $id_lang) {
                        // add the detail sync in our history table
                        $bResult = \MCE\Chimp\Detail::get()->create(\MCE\Chimp\Format\Formatter::setProductID($aParams['id_product'], $id_lang), 'product', \MCE\Chimp\Facade::get('id'), \BTMailchimpEcommerce::$iShopId);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * detect if we update a product and this one has never been synchronized, so we synchronize it
     *
     * @param array $aParams
     * @return array
     */
    private function updateProduct(array $aParams = null)
    {
        // use case - detect the product id and the product obj
        if (
            !empty($aParams['id_product'])
            && !empty($aParams['product'])
            && is_object($aParams['product'])
        ) {
            try {
                // use case  - use cron
                if (\BTMailchimpEcommerce::$conf['MCE_PRODUCT_SYNC_MODE'] == 'cron') {
                    if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['id_product'], 'product')) {
                        \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['id_product'], 'product', []);
                    }
                    // use case  - live mode
                } elseif (\MCE\Chimp\Facade::isActive('ecommerce')) {
                    // get the list and store ID
                    $sListId = \MCE\Chimp\Facade::get('id');
                    $sStoreId = \MCE\Chimp\Facade::get('store_id');

                    // loop on product languages to get the same product in all the active languages and currencies
                    foreach (\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] as $id_lang) {
                        // instantiate the current product
                        $oProduct = new \Product($aParams['id_product'], false, $id_lang);

                        // get the product image format
                        $sImgFormat = !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_default';

                        // the unique condition to synchronize the product in this language is if the product gets already images linked to.
                        $sImageUrl = \MCE\Tools::getProductImage($oProduct, $sImgFormat);

                        if (!empty($sImageUrl)) {
                            // use case - the product already exists, we update it
                            $method = \MCE\Chimp\Facade::isProductExist($sStoreId, $oProduct->id, $id_lang) ? 'update' : 'add';

                            // format and send to API
                            $bResult = \MCE\Chimp\Facade::processProduct($sListId, $sStoreId, $oProduct, $id_lang, \BTMailchimpEcommerce::$conf['MCE_PRODUCT_SYNC_MODE'], $method, $sImageUrl);
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * delete a product
     *
     * @param array $aParams
     * @return array
     */
    private function deleteProduct(array $aParams = null)
    {
        // use case - detect the product id and the product obj
        if (!empty($aParams['id_product'])) {
            try {
                if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                    // delete product in MC
                    $bResult = \MCE\Chimp\Facade::deleteProduct(
                        \MCE\Chimp\Facade::get('id'),
                        \MCE\Chimp\Facade::get('store_id'),
                        $aParams['id_product'],
                        \BTMailchimpEcommerce::$conf['MCE_PROD_LANG']
                    );
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * detect if we update a product combination and this one has never been synchronized, so we synchronize it
     *
     * @param array $aParams
     * @return array
     */
    private function updateCombination(array $aParams = null)
    {
        $iProductId = \Tools::getValue('id_product');
        // use case - for PS 1.7, the id_product is included in the params array
        if ($iProductId == false) {
            $iProductId = isset($aParams['id_product']) ? $aParams['id_product'] : false;
        }

        // use case - detect the product id and the product obj
        if (
            !empty($aParams['id_product_attribute'])
            && !empty($iProductId)
        ) {
            try {
                // use case  - use cron
                if (\BTMailchimpEcommerce::$conf['MCE_PRODUCT_SYNC_MODE'] == 'cron') {
                    if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['id_product_attribute'], 'variant')) {
                        \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['id_product_attribute'], 'variant', ['id_product' => $aParams['id_product']]);
                    }
                    // use case  - live mode
                } elseif (\MCE\Chimp\Facade::isActive('ecommerce')) {
                    // get the list and store ID
                    $sListId = \MCE\Chimp\Facade::get('id');
                    $sStoreId = \MCE\Chimp\Facade::get('store_id');

                    // loop on product languages to get the same product in all the active languages and currencies
                    foreach (\BTMailchimpEcommerce::$conf['MCE_PROD_LANG'] as $id_lang) {
                        // instantiate the current product
                        $oProduct = new \Product($iProductId, false, $id_lang);

                        // get the options values set during the manual catalog products synchro
                        $sImgFormat = !empty(\BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT']) ? \BTMailchimpEcommerce::$conf['MCE_PROD_IMG_FORMAT'] : 'large_default';

                        // the unique condition to synchronize the product in this language is if the product gets already images linked to.
                        $sImageUrl = \MCE\Tools::getProductImage($oProduct, $sImgFormat);

                        if (!empty($sImageUrl)) {
                            // format and send
                            $bResult = \MCE\Chimp\Facade::processVariant($sListId, $sStoreId, $iProductId, $aParams['id_product_attribute'], $id_lang, \BTMailchimpEcommerce::$conf['MCE_PRODUCT_SYNC_MODE']);
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * delete a product combination
     *
     * @param array $aParams
     * @return array
     */
    private function deleteCombination(array $aParams = null)
    {
        $iProductId = \Tools::getValue('id_product');
        // use case - for PS 1.7, the id_product is included in the params array
        if ($iProductId == false) {
            $iProductId = isset($aParams['id_product']) ? $aParams['id_product'] : false;
        }

        // use case - detect the product id and the product obj
        if (
            !empty($aParams['id_product_attribute'])
            && !empty($iProductId)
        ) {
            try {
                if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                    // delete product in MC
                    $bResult = \MCE\Chimp\Facade::deleteProductVariant(
                        \MCE\Chimp\Facade::get('id'),
                        \MCE\Chimp\Facade::get('store_id'),
                        $aParams['id_product'],
                        $aParams['id_product_attribute'],
                        \BTMailchimpEcommerce::$conf['MCE_PROD_LANG']
                    );
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * synchronize the new customer created to MC
     *
     * @param array $aParams
     * @return array
     */
    private function addCustomer(array $aParams = null)
    {
        if (
            !empty($aParams['newCustomer'])
            && is_object($aParams['newCustomer'])
        ) {
            try {
                // check if the email is not in t he excluded list
                if (!\MCE\Chimp\Facade::excludeEmail($aParams['newCustomer']->email)) {
                    // use case  - use cron
                    if (\BTMailchimpEcommerce::$conf['MCE_CUSTOMER_SYNC_MODE'] == 'cron') {
                        if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['newCustomer']->id, 'customer')) {
                            \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $aParams['newCustomer']->id, 'customer', ['lang_id' => $aParams['newCustomer']->id_lang, 'method' => 'add']);
                        }
                        // use case  - live mode - at least the newsletter feature active
                    } elseif (\MCE\Chimp\Facade::isActive('newsletter')) {
                        // get the customer language
                        $iLangId = $aParams['newCustomer']->id_lang;

                        // get the list and store ID
                        $sListId = \MCE\Chimp\Facade::get('id');
                        $sStoreId = \MCE\Chimp\Facade::get('store_id');

                        // set the shop id
                        \BTMailchimpEcommerce::$iShopId = $aParams['newCustomer']->id_shop;

                        // format and send
                        $bResult = \MCE\Chimp\Facade::processMember($sListId, $aParams['newCustomer'], $iLangId, \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE']);

                        // use case "ecommerce" - format and send customer
                        if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                            // as we must get member value set previously, we force to false the NL subscription to not overridde the opt-in status when the double optin is activated
                            if (!empty(\BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'])) {
                                $aParams['newCustomer']->newsletter = false;
                            }
                            $bResult = \MCE\Chimp\Facade::processCustomer($sListId, $sStoreId, $aParams['newCustomer'], $iLangId, \BTMailchimpEcommerce::$conf['MCE_CUSTOMER_SYNC_MODE']);
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * synchronize the customer updated to MC
     *
     * @param array $aParams
     * @return array
     */
    private function updateCustomer(array $aParams = null)
    {
        if ((isset($aParams['object'])
                && is_a($aParams['object'], 'Customer'))
            || (isset($aParams['customer'])
                && is_a($aParams['customer'], 'Customer'))
        ) {
            try {
                $oCustomer = isset($aParams['object']) ? $aParams['object'] : $aParams['customer'];

                // check if the email is not in t he excluded list
                if (!\MCE\Chimp\Facade::excludeEmail($oCustomer->email)) {
                    // use case  - use cron
                    if (\BTMailchimpEcommerce::$conf['MCE_CUSTOMER_SYNC_MODE'] == 'cron') {
                        if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $oCustomer->id, 'customer')) {
                            \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $oCustomer->id, 'customer', ['lang_id' => $oCustomer->id_lang, 'method' => 'update']);
                        }
                        // use case  - live mode - at least the newsletter feature active
                    } elseif (\MCE\Chimp\Facade::isActive('newsletter')) {
                        // get the customer language
                        $iLangId = $oCustomer->id_lang;

                        // get the list and store ID
                        $sListId = \MCE\Chimp\Facade::get('id');
                        $sStoreId = \MCE\Chimp\Facade::get('store_id');

                        //Use case for the address we force the value before the mailchimp update
                        if (\Tools::getValue('controller') == 'address') {
                            $oAddress = new \Address((int) \Tools::getValue('id_address'));

                            $oAddress->firstname = (string) \Tools::getValue('firstname');
                            $oAddress->lastname = (string) \Tools::getValue('lastname');
                            $oAddress->address1 = (string) \Tools::getValue('address1');
                            $oAddress->address2 = (string) \Tools::getValue('address2');
                            $oAddress->postcode = (string) \Tools::getValue('postcode');
                            $oAddress->city = (string) \Tools::getValue('city');
                            $oAddress->id_country = (int) \Tools::getValue('id_country');
                            $oAddress->phone = (string) \Tools::getValue('phone');

                            if (!empty(BTMailchimpEcommerce::$bCompare17)) {
                                $oAddress->update();
                            }
                        }

                        // set the shop id
                        \BTMailchimpEcommerce::$iShopId = $oCustomer->id_shop;

                        //Use case if we are on the form with the checkbox for newsletter and if he just click on the checkbox and we are in double optin we need to send email for subscription
                        if (\Tools::getValue('controller') == 'identity' && !empty(\Tools::getValue('newsletter')) && !empty(\BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'])) {
                            $bResult = \MCE\Chimp\Facade::processMember($sListId, $oCustomer, $iLangId, \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE']);
                        } else {
                            // For all other case we don't need control the checkbox
                            $bResult = \MCE\Chimp\Facade::processMember($sListId, $oCustomer, $iLangId, false, 'put', \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE']);
                        }

                        // use case "ecommerce" - format and send customer
                        if (\MCE\Chimp\Facade::isActive('ecommerce')) {
                            // Use case if the customer didn't create his account with the newsletter checkbox checked we secure the newsletter option to false
                            if (!empty(\BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN']) && empty($oCustomer->newsletter)) {
                                $oCustomer->newsletter = false;
                            }
                            $bResult = \MCE\Chimp\Facade::processCustomer($sListId, $sStoreId, $oCustomer, $iLangId, \BTMailchimpEcommerce::$conf['MCE_CUSTOMER_SYNC_MODE'], 'update');
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * synchronize the cart created / updated to MC
     *
     * @param array $aParams
     * @return array
     */
    private function saveCart(array $aParams = null)
    {
        // set vars
        $iCartId = isset(\Context::getContext()->cart->id) ? \Context::getContext()->cart->id : false;
        $iLangId = isset(\Context::getContext()->cart->id_lang) ? \Context::getContext()->cart->id_lang : false;

        // only if the cart is available and the customer is logged
        if (
            !empty($iCartId)
            && isset(\Context::getContext()->customer->id)
            && \Context::getContext()->customer->id != false
            && !empty($iLangId)
            && in_array($iLangId, \BTMailchimpEcommerce::$conf['MCE_PROD_LANG'])
        ) {

            try {
                // use case  - use cron
                if (\BTMailchimpEcommerce::$conf['MCE_CART_SYNC_MODE'] == 'cron') {
                    if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, \Context::getContext()->customer->id, 'cart')) {
                        \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $iCartId, 'cart', ['lang_id' => $iLangId, 'customer_id' => \Context::getContext()->customer->id]);
                    }
                    // use case  - live mode
                } elseif (
                    !\MCE\Chimp\Facade::excludeEmail(\Context::getContext()->customer->email)
                    && \MCE\Chimp\Facade::isActive('ecommerce')
                ) {
                    // get the products of the current cart
                    $aCartProducts = \Context::getContext()->cart->getProducts();

                    // use case - synchronize the current products
                    if (!empty($aCartProducts)) {
                        // get the list and store ID
                        $sListId = \MCE\Chimp\Facade::get('id');
                        $sStoreId = \MCE\Chimp\Facade::get('store_id');

                        // format and send
                        $bResult = \MCE\Chimp\Facade::processCart($sListId, $sStoreId, $iCartId, \Context::getContext()->customer->id, $iLangId, \BTMailchimpEcommerce::$conf['MCE_CART_SYNC_MODE'], false, \BTMailchimpEcommerce::$conf['MCE_CART_MAX_PROD']);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * synchronize the order created
     *
     * @param array $aParams
     * @return array
     */
    private function validateOrder(array $aParams = null)
    {
        if (
            !empty($aParams['order'])
            && is_object($aParams['order'])
            && $aParams['order']->getCurrentState() != \Configuration::get('PS_OS_ERROR')
            && isset(\Context::getContext()->customer->id)
            && \Context::getContext()->customer->id != false
            && !\MCE\Chimp\Facade::excludeEmail(\Context::getContext()->customer->email)
        ) {
            try {
                $iOrderId = $aParams['order']->id;
                $iLangId = isset($aParams['order']->id_lang) ? $aParams['order']->id_lang : false;

                // use case  - use cron
                if (\BTMailchimpEcommerce::$conf['MCE_ORDER_SYNC_MODE'] == 'cron') {
                    if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $iOrderId, 'order')) {
                        \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $iOrderId, 'order', ['lang_id' => $iLangId, 'method' => 'add', 'cart_id' => $aParams['order']->id_cart]);
                    }
                    // use case  - live mode
                } elseif (
                    \MCE\Chimp\Facade::isActive('ecommerce')
                    && !empty($iLangId)
                    && in_array($iLangId, \BTMailchimpEcommerce::$conf['MCE_PROD_LANG'])
                    && !empty($aParams['order']->product_list)
                    && is_array($aParams['order']->product_list)
                ) {
                    // get the list and store ID
                    $sListId = \MCE\Chimp\Facade::get('id');
                    $sStoreId = \MCE\Chimp\Facade::get('store_id');

                    $aOptions = array(
                        'bForceOrderStatus' => true,
                    );

                    // format and send
                    $bResult = \MCE\Chimp\Facade::processOrder($sListId, $sStoreId, $iOrderId, $aParams['order']->id_cart, $iLangId, \BTMailchimpEcommerce::$conf['MCE_ORDER_SYNC_MODE'], true, 'add', $aOptions);
                    \PrestaShopLogger::addLog('[MAILCHIMP] : Result of process Order' .  print_r($bResult, true));
                }
            } catch (\Exception $e) {
                \PrestaShopLogger::addLog('[MAILCHIMP] :' . $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine());
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * synchronize the order created
     *
     * @param array $aParams
     * @return array
     */
    private function updateOrderStatus(array $aParams = null)
    {
        if (!empty($aParams['id_order'])) {
            try {
                // get the order ID
                $iOrderId = $aParams['id_order'];

                // get the order object
                $oOrder = new \Order($iOrderId);

                // get the customer object
                $oCustomer = new \Customer($oOrder->id_customer);

                // if active sync
                if (
                    !empty($oCustomer->email)
                    && !\MCE\Chimp\Facade::excludeEmail($oCustomer->email)
                ) {
                    // use case  - use cron
                    if (\BTMailchimpEcommerce::$conf['MCE_ORDER_SYNC_MODE'] == 'cron') {
                        if (!\MCE\Dao::isExistCronItem(\BTMailchimpEcommerce::$iShopId, $iOrderId, 'order')) {
                            \MCE\Dao::addCronItem(\BTMailchimpEcommerce::$iShopId, $iOrderId, 'order', ['lang_id' => $oOrder->id_lang, 'method' => 'update']);
                        }
                        // use case  - live mode
                    } elseif (\MCE\Chimp\Facade::isActive('ecommerce')) {
                        // get the list and store ID
                        $sListId = \MCE\Chimp\Facade::get('id');
                        $sStoreId = \MCE\Chimp\Facade::get('store_id');

                        $aOptions = array();

                        // detect if we have the new order state as parameter
                        if (!empty($aParams['newOrderStatus']->template)) {
                            $aOptions['sForceOrderTemplate'] = $aParams['newOrderStatus']->template;
                        }

                        // format and send
                        $bResult = \MCE\Chimp\Facade::processOrder($sListId, $sStoreId, $iOrderId, $oOrder->id_cart, $oOrder->id_lang, \BTMailchimpEcommerce::$conf['MCE_ORDER_SYNC_MODE'], true, 'update', $aOptions);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * add MC external JS
     *
     * @param array $aParams
     * @return array
     */
    private function setMedia(array $aParams = null)
    {
        try {
            // get the store data
            $aData = $this->getMailchimpJS();

            if (!empty($aData['url'])) {
                \Context::getContext()->controller->registerJavascript(
                    'mailchimp-remote',
                    $aData['url'],
                    array(
                        'position' => 'bottom',
                        'priority' => 10,
                        'server' => 'remote'
                    )
                );
            }
        } catch (\Exception $e) {
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }


    /**
     * process a batch webhook
     *
     * @param array $aParams
     * @return array
     */
    private function processBatchWebhook(array $aParams = null)
    {
        if (
            isset($aParams['response_body_url'])
            && isset($aParams['id'])
        ) {
            try {
                try {
                    // get the current list
                    $aList = \MCE\Dao::getLists(\BTMailchimpEcommerce::$iShopId, null, true);

                    if (!empty($aList[0])) {
                        $aList = $aList[0];
                        $aLocalBatch = \MCE\Dao::getBatches(\BTMailchimpEcommerce::$iShopId, array('batch_id' => $aParams['id']));

                        if (
                            !empty($aLocalBatch['data'])
                            && is_string($aLocalBatch['data'])
                        ) {
                            $aMatchingItems = unserialize($aLocalBatch['data']);
                            $aResponses = \MCE\Chimp\Facade::formatBatchResponse($aParams['id'], $aParams['response_body_url']);

                            if (!empty($aResponses)) {
                                foreach ($aResponses as $iKey => $aResponse) {
                                    if (isset($aMatchingItems[$iKey])) {
                                        $aSyncParams = [];

                                        // it means there is an error
                                        if (!empty($aResponse['response']->detail)) {
                                            $aDetail = ['error' => $aResponse['response']->detail];
                                        } else {
                                            $aDetail = [];
                                        }

                                        // use case check if the sync detail not already exist
                                        $aCurrentDetail = \MCE\Chimp\Detail::get()->read($aMatchingItems[$iKey]['id'], $aList['id'], \BTMailchimpEcommerce::$iShopId, $aMatchingItems[$iKey]['type']);
                                        if (empty($aCurrentDetail)) {
                                            // add the detail sync in our history table
                                            $bResult = \MCE\Chimp\Detail::get()->create(
                                                $aMatchingItems[$iKey]['id'],
                                                $aMatchingItems[$iKey]['type'],
                                                $aList['id'],
                                                \BTMailchimpEcommerce::$iShopId,
                                                '',
                                                \MCE\Chimp\Detail::get()->format($aDetail)
                                            );
                                        }
                                        // check if we have reached the limit of trying to sync this item
                                        if (
                                            $aLocalBatch['mode'] == 'automatic'
                                            && !empty($aDetail['error'])
                                        ) {
                                            if ($aCurrentDetail['times'] >= _MCE_BATCH_RESYNC_LIMIT) {
                                                try {
                                                    require_once(_MCE_PATH_LIB . 'MailSend.php');

                                                    // send an e-mail to the shop's owner about the unsuccesful data synching
                                                    $aError = array(
                                                        'data_id' => $aMatchingItems[$iKey]['id'],
                                                        'data_type' => $aMatchingItems[$iKey]['type'],
                                                        'data_detail' => $aDetail['error'],
                                                        'data_sync_limit' => _MCE_BATCH_RESYNC_LIMIT,
                                                        'email' => \Configuration::get('PS_SHOP_EMAIL'),
                                                        'back_module_url' => \BTMailchimpEcommerce::$conf['MCE_MODULE_BO_URL'] . '&configure=btmailchimpecommerce&tab_module=seo&module_name=btmailchimpecommerce&sSubTpl=diagnostic#tab-01|tab-05',
                                                    );
                                                    \MCE\MailSend::create()->run('apiSyncError', $aError, _MCE_PATH_MAILS);
                                                } catch (\Exception $e) {
                                                    \PrestaShopLogger::addLog(
                                                        '[MAILCHIMP] process batch : email exception => ' . $e->getMessage() . ' - File: ' . $e->getFile() . ' - line: ' . $e->getLine(),
                                                        1
                                                    );
                                                }
                                                // USE CASE - create another batch to try sync the item well
                                            } else {
                                                // need to increase the counter of the sync details
                                                $aSyncParams['count'] = true;
                                                // re-create a new batch
                                                $bResult = \MCE\Chimp\Facade::createNewBatch($aList['id'], (!empty($aList['store_id']) ? $aList['store_id'] : ''), $aMatchingItems[$iKey]['id'], $aMatchingItems[$iKey]['type'], $aMatchingItems[$iKey]);
                                            }
                                        }

                                        // need to increase the counter of the sync details
                                        $aSyncParams = array_merge($aSyncParams, \MCE\Chimp\Detail::get()->format($aDetail));

                                        // register detail of request
                                        $bResult = \MCE\Chimp\Detail::get()->update(
                                            $aMatchingItems[$iKey]['id'],
                                            $aList['id'],
                                            \BTMailchimpEcommerce::$iShopId,
                                            $aMatchingItems[$iKey]['type'],
                                            $aSyncParams
                                        );
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                }

                // in all the cases, we delete the batch
                $oMcCtrl = \MCE\Chimp\Api::get(\BTMailchimpEcommerce::$conf['MCE_MC_API_KEY']);
                $bResult = $oMcCtrl->batches->delete($aParams['id']);

                // delete it locally
                $bResult = \MCE\Dao::deleteBatch(\BTMailchimpEcommerce::$iShopId, array('batch_id' => $aParams['id']));
            } catch (\Exception $e) {
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * process a list webhook
     *
     * @param array $aParams
     * @return array
     */
    private function processListWebhook(array $aParams = null)
    {
        try {
            if (
                isset($aParams['type'])
                && isset($aParams['data'])
                && is_array($aParams['data'])
                && !empty($aParams['data'])
            ) {
                switch ($aParams['type']) {
                    case 'subscribe':
                    case 'unsubscribe':
                        if (isset($aParams['data']['email'])) {
                            $customer_id = \Customer::customerExists($aParams['data']['email'], true);

                            if (!empty($customer_id)) {
                                $customer = new \Customer($customer_id);
                                $customer->newsletter = $aParams['type'] == 'subscribe' ? 1 : 0;
                                $customer->update();
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        } catch (\Exception $e) {
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * manage if we display or not the signup popup aacording to the visitor's choice
     *
     * @param array $aParams
     * @return array
     */
    private function processSignupPopupDisplay(array $aParams = null)
    {
        try {
            // detect if we have the good parameter
            if (isset($aParams['bt_signup_display'])) {
                require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');

                \MCE\Cookie::update(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_stop', (int) $aParams['bt_signup_display']);
            }
        } catch (\Exception $e) {
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }

    /**
     * Register in MC the new e-mail subscription
     *
     * @param string $sEmail
     * @return bool
     */
    protected function registerNewsletterEmail($sEmail)
    {
        try {
            // if the automatic synching is active
            if (\MCE\Chimp\Facade::isActive('newsletter')) {
                // adjust the mode for the specific case
                $sMode = \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE'] == 'cron' ? 'batch' : \BTMailchimpEcommerce::$conf['MCE_MEMBER_SYNC_MODE'];
                // format and send
                $bResult = \MCE\Chimp\Facade::processMember(\MCE\Chimp\Facade::get('id'), ['email' => $sEmail, 'newsletter' => true], \BTMailchimpEcommerce::$iCurrentLang, \BTMailchimpEcommerce::$conf['MCE_DOUBLE_OPTIN'], 'put', $sMode);
                
            }
        } catch (\Exception $e) {}

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }
}

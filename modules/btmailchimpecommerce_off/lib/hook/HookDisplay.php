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

class HookDisplay extends HookBase
{
    /**
     * @var bool $bMcFormExecuted : detect if the mc form hook was already executed
     */
    static protected $bMcFormExecuted = false;


    /**
     * @var bool $bFooterExecuted : detect if the footer hook was already executed
     */
    static protected $bFooterExecuted = false;

    /**
     * Execute hook as controller
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
            case 'header':
                // use case - display in header
                $aDisplayHook = call_user_func(array($this, 'displayHeader'));
                break;
            case 'voucher':
                // use case - display in the front module controller "voucher" to generate the voucher that the customer should get and send an e-mail and display the voucher information
                $aDisplayHook = call_user_func(array($this, 'displayVoucher'));
                break;
            case 'dedicatedSignup':
                // use case - display in the front module controller "signup" to display the MC embedded HTML form
                $aDisplayHook = call_user_func(array($this, 'displayDedicatedSignup'));
                break;
            default:
                break;
        }

        return $aDisplayHook;
    }

    /**
     * Display header
     *
     * @return array
     */
    private function displayHeader()
    {
        // set
        $assign = array();

        \Context::getContext()->controller->addJS(_MCE_URL_JS . 'front.js');

        // detect the referrer from MailChimp to set during the order
        // use case - identify the mc_cid of MC when a MC e-mail link is clicked and like this we can pass the mc_cid to the cart or the order
        if (
            \Tools::getValue('utm_source') == 'mailchimp'
            || \Tools::getValue('mc_cid') != false
        ) {
            require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');

            // create the cookie if needed
            \MCE\Cookie::set(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'landing_site', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], time() + \BTMailchimpEcommerce::$conf['MCE_COOKIE_TTL']);

            if (\Tools::getValue('mc_cid') !== false) {
                \MCE\Cookie::set(_MCE_COOKIE . \BTMailchimpEcommerce::$iShopId, 'mc_cid', \Tools::getValue('mc_cid'), time() + \BTMailchimpEcommerce::$conf['MCE_COOKIE_TTL']);
            }
        }

        // use case - check if we have checkout URL parameters from MailChimp abandoned carts e-mails to log in automatically the customer and load his last cart
        $this->redirectToAbandonedCart();

        // use case - connected site
        $aData = $this->setMedia();

        if (!empty($aData)) {
            $assign = array_merge($assign, $aData);
        }

        // use case - if the newsletter feature is active
        if (\MCE\Chimp\Facade::isActive('newsletter')) {
            $aRender = $this->displayMailchimpForm();
            if (!empty($aRender)) {
                $assign = array_merge($assign, $aRender);
            }

            // use case - the NL module use ajax request to register email
            if (\BTMailchimpEcommerce::$conf['MCE_NL_MODULE_AJAX']) {
                $assign['nl_module_ajax'] = true;
                $assign['nl_module_submit'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_SUBMIT'];
                $assign['nl_module_email'] = \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_EMAIL_FIELD'];
                $assign['nl_module_url'] = \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_CTRL_NEWSLETTER);
                $assign['nl_module_params'] = 'sAction=register&sType=newsletter&bt_token=' . \MCE\Tools::setSecureKey(_MCE_SECURE_HASH, _MCE_TOKEN, 0) . '&bt_nl_email=';

                // use case - detect if the newsletter subscription form has been submitted
            } elseif (
                \Tools::isSubmit(\BTMailchimpEcommerce::$conf['MCE_NL_MODULE_SUBMIT'])
                && \Tools::getIsset(\BTMailchimpEcommerce::$conf['MCE_NL_MODULE_EMAIL_FIELD'])
            ) {
                $this->registerNewsletterEmail(\Tools::getValue(\BTMailchimpEcommerce::$conf['MCE_NL_MODULE_EMAIL_FIELD']));
            }
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_FRONT_HEADER, 'assign' => $assign);
    }


    /**
     * add MC external JS
     *
     * @param array $aParams
     * @return array
     */
    private function setMedia(array $aParams = null)
    {
        // set
        $assign = array();

        // get the store data
        $aData = $this->getMailchimpJS();

        if (!empty($aData['url'])) {
            \Context::getContext()->controller->addJS($aData['url']);
        }

        if (!empty($aData['fragment'])) {
            $assign['sMailChimpJS'] = $aData['fragment'];
        }

        return $assign;
    }


    /**
     * Display MailChimp newsletter form
     *
     * @return array
     */
    private function displayMailchimpForm()
    {
        // set
        $render = array();

        try {
            if (
                !empty(\BTMailchimpEcommerce::$conf['MCE_SIGNUP'])
                && is_array(\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'])
            ) {
                $use_mc_form = false;

                foreach (\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'] as $html) {
                    if (!empty($html)) {
                        $use_mc_form = true;
                    }
                }

                if (!empty($use_mc_form)) {
                    // get render object
                    require_once(_MCE_PATH_LIB_MC . 'signup/Render.php');

                    // get the tpl and assign values to render it
                    $render = (new \MCE\Chimp\Signup\Render(
                        \BTMailchimpEcommerce::$conf['MCE_SIGNUP_DISPLAY'],
                        array(
                            'lang_id' => \BTMailchimpEcommerce::$iCurrentLang,
                            'lang_id_default' => \Configuration::get('PS_LANG_DEFAULT'),
                            'use_nl_module' => \BTMailchimpEcommerce::$conf['MCE_NL_MODULE'],
                            'selector' => \BTMailchimpEcommerce::$conf['MCE_NL_MODULE_SELECTOR'],
                            'form' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'],
                            'label' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_LINK_LABEL'],
                            'link' => \Context::getContext()->link->getModuleLink(
                                _MCE_MODULE_SET_NAME,
                                'signup',
                                array(),
                                null
                            ),
                            'times' => (int)\BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TIMES'],
                            'pages' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_PAGES'],
                            'popup_not_display' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_NOT_DISPLAY'],
                            'popup_width' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_WIDTH'],
                            'popup_height' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_HEIGHT'],
                            'popup_text' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT'],
                            'popup_text_valign' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_VALIGN'],
                            'popup_text_halign' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_HALIGN'],
                            'popup_use_image' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_IMAGE'],
                            'popup_text_valign_custom' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_VALIGN_CUSTOM'],
                            'popup_text_halign_custom' => \BTMailchimpEcommerce::$conf['MCE_SIGNUP_POPUP_TEXT_HALIGN_CUSTOM'],
                        )
                    ))->render();
                }
            }
        } catch (\Exception $e) {
            //            dump($e->getMessage());
            //            dump($e->getFile());
            //            dump($e->getLine());
        }

        return $render;
    }


    /**
     * Check if the referrer comes from a link into the MC abandoned cart e-mail and redirect to the cart by connecting the customer and loading his cart
     */
    private function redirectToAbandonedCart()
    {
        try {
            $iCustomerId = \Tools::getValue('bt_cl');
            $iCartId = \Tools::getValue('bt_ct');
            $bRedirect = \Tools::getValue('bt_redirect');
            $sSecureHashKey = \Tools::getValue('bt_mcsid');
            // auth customer
            $oCustomer = new \Customer($iCustomerId);

            if (
                \Validate::isLoadedObject($oCustomer)
                && !empty($oCustomer->active)
                && (isset(\Context::getContext()->customer->logged)
                    && (\Context::getContext()->customer->logged == false
                        || (\Context::getContext()->customer->logged == true
                            && \Context::getContext()->customer->id != $oCustomer->id)))
                && !empty($iCartId)
                && !empty($sSecureHashKey)
                && $sSecureHashKey == \MCE\Tools::setSecureKey(_MCE_SECURE_HASH, $oCustomer->id, $iCartId)
                && empty($bRedirect)
            ) {
                // assign the new customer to the context
                $oCustomer->logged = 1;
                \Context::getContext()->customer = $oCustomer;
                // assign new values to the PS cookie
                \Context::getContext()->cookie->id_customer = intval($oCustomer->id);
                \Context::getContext()->cookie->customer_lastname = $oCustomer->lastname;
                \Context::getContext()->cookie->customer_firstname = $oCustomer->firstname;
                \Context::getContext()->cookie->logged = 1;
                \Context::getContext()->cookie->passwd = $oCustomer->passwd;
                \Context::getContext()->cookie->email = $oCustomer->email;
                \Context::getContext()->cookie->id_cart = $iCartId;

                // exec the authentication hook to log in automatically the customer
                \Hook::Exec('authentication');

                // redirect after logging the customer
                header("Location: " . $_SERVER['SCRIPT_URI'] . '?' . $_SERVER['QUERY_STRING'] . '&bt_redirect=true');
                exit(0);
            }
        } catch (\Exception $e) {
        }
    }


    /**
     * Display the voucher won by the customer after having clicked on the automation e-mail link
     *
     * @return array
     */
    private function displayVoucher()
    {
        // set
        $assign = array();
        $assign['path'] = \BTMailchimpEcommerce::$oModule->l('Discount vouchers', 'HookDisplay');
        $assign['meta_title'] = \BTMailchimpEcommerce::$oModule->l('Get a discount voucher', 'HookDisplay') . ' ' . \Configuration::get('PS_SHOP_NAME');
        $assign['meta_description'] = \BTMailchimpEcommerce::$oModule->l('Our customers can benefit from discount vouchers by receiving our e-mails', 'HookDisplay');
        $assign['aVoucher'] = array();
        $assign['sLoginURI'] = '';

        try {
            // get the type of the voucher
            $sType = \Tools::getValue('type');
            $sEmail = \Tools::getValue('email');

            // detect if we get a valid type and e-mail
            if (
                !empty($sType)
                && !empty($sEmail)
            ) {
                // the current type should exist in the voucher list
                if (array_key_exists($sType, \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'])) {
                    // add CSS
                    \Context::getContext()->controller->addCSS(_MCE_URL_CSS . 'front.css');

                    // and we need to test if we get a real customer from the e-mail
                    $iCustomerId = \Customer::customerExists($sEmail, true);

                    if ($iCustomerId) {
                        if ($iCustomerId == \Context::getContext()->cookie->id_customer) {
                            // get the customer data
                            $oCustomer = new \Customer($iCustomerId);

                            // assign all the needed variables to display the matching texts
                            $assign['sFirstName'] = $oCustomer->firstname;
                            $assign['sLastName'] = $oCustomer->lastname;
                            $assign['aVoucher'] = \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'][$sType];
                            $assign['aVoucher']['code'] =  !empty(\BTMailchimpEcommerce::$conf['MCE_VOUCHERS'][$sType]['prefix']) ? \BTMailchimpEcommerce::$conf['MCE_VOUCHERS'][$sType]['prefix'] . '-': '';
                            $assign['aVoucher']['code'] .= \Tools::strtoupper($oCustomer->firstname) . '-' . \Tools::strtoupper($oCustomer->lastname) . '-' . \Tools::strtoupper(substr(md5($oCustomer->email), 0, 6));
                            $assign['bAlreadyCreated'] = true;

                            // check if the automation is a predefined automatoin in that way we get the name in the good language
                            if (isset($GLOBALS['MCE_AUTOMATION'][$assign['aVoucher']['type']])) {
                                \MCE\Tools::translateAutomationVouchers();
                                $assign['aVoucher']['name'] = 'Mailchimp - ' . $GLOBALS['MCE_AUTOMATION'][$assign['aVoucher']['type']];
                            }

                            if ($assign['aVoucher']['discount'] == 'amount') {
                                $assign['aVoucher']['sign'] = \MCE\Tools::getCurrency('sign', $assign['aVoucher']['currency']);
                                $assign['aVoucher']['displayAmount'] = ($assign['aVoucher']['sign'] == '€') ? $assign['aVoucher']['amount'] . $assign['aVoucher']['sign'] : $assign['aVoucher']['sign'] . $assign['aVoucher']['amount'];
                            } else {
                                $assign['aVoucher']['sign'] = \MCE\Tools::getCurrency('sign', \Configuration::get('PS_CURRENCY_DEFAULT'));
                                $assign['aVoucher']['displayAmount'] = $assign['aVoucher']['amount'] . '%';
                            }

                            require_once(_MCE_PATH_LIB . 'Voucher.php');
                            require_once(_MCE_PATH_LIB . 'MailSend.php');

                            // use case - check if we already had generated the code for this customer
                            if (!\MCE\Voucher::create()->isExist($assign['aVoucher']['code'])) {
                                \MCE\Voucher::create()->setVoucher($assign['aVoucher']);
                                $assign['aVoucherAdd'] = \MCE\Voucher::create()->add($iCustomerId, $sEmail);
                                $assign['bAlreadyCreated'] = false;
                            } else {
                                $assign['bAlreadyCreated'] = true;
                            }

                            if (
                                $assign['bAlreadyCreated']
                                || (!$assign['bAlreadyCreated']
                                    && !empty($assign['aVoucherAdd']['name']))
                            ) {
                                // use case - already created or not, we send an e-mail to the customer with the voucher detail
                                $assign['bEmailSent'] = \MCE\MailSend::create()->run('voucherNotification', array(
                                    'firstname' => $assign['sFirstName'],
                                    'lastname' => $assign['sLastName'],
                                    'email' => $sEmail,
                                    'langId' => \BTMailchimpEcommerce::$iCurrentLang,
                                    'voucher' => $assign['aVoucher']
                                ));
                            }
                        } else {
                            // format login URI callback
                            $sURI = \MCE\Tools::truncateUri();
                            if (isset($sURI[strlen($sURI) - 1]) && $sURI[strlen($sURI) - 1] == '?') {
                                $sURI = substr($sURI, 0, strlen($sURI) - 1);
                            }
                            $assign['sLoginURI'] = \MCE\Tools::getLoginLink($sURI);
                            throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The e-mail passed in the URL doesn\'t match with the current customer! Please login to get the code of your discount voucher by clicking on the button below', 'HookDisplay') . '.', 401);
                        }
                    } else {
                        throw new \Exception(\BTMailchimpEcommerce::$oModule->l('The e-mail passed in the URL doesn\'t match with any customer address e-mail in the shop. Please contact the shop\'s owner to check and see why you cannot get your voucher code', 'HookDisplay') . '.', 402);
                    }
                } else {
                    throw new \Exception(\BTMailchimpEcommerce::$oModule->l('Type of discount voucher', 'HookDisplay') . ' "' . $sType . '".' . \BTMailchimpEcommerce::$oModule->l('This type of discount voucher doesn\'t exist or the shop\'s owner has never configured it. Please contact him to check and see why you cannot get your voucher code', 'HookDisplay') . '.', 403);
                }
            } else {
                throw new \Exception(\BTMailchimpEcommerce::$oModule->l('No discount voucher has been found for these URL parameters. Please contact the shop\'s owner to check and see why you cannot get your voucher code', 'HookDisplay') . '.', 404);
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }

        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_FRONT_PATH . _MCE_TPL_ERROR);
        $assign['bDebugPS'] = (_PS_MODE_DEV_ == true) ? true : false;

        // use case  - PS 1.7 should change the template path
        $sTpl = (\BTMailchimpEcommerce::$bCompare17 ? 'module:btmailchimpecommerce/views/templates/front/1.7/' : '1.6/') . _MCE_TPL_VOUCHER_CTRL;

        return array('tpl' => $sTpl, 'assign' => $assign);
    }

    /**
     * Display the MC dedicated signup form
     *
     * @return array
     */
    private function displayDedicatedSignup()
    {
        // set
        $assign = array();
        $assign['path'] = \BTMailchimpEcommerce::$oModule->l('MailChimp newsletter sign-up form', 'HookDisplay');
        $assign['meta_title'] = \BTMailchimpEcommerce::$oModule->l('Subscribe to the newsletter of our shop', 'HookDisplay') . ' ' . \Configuration::get('PS_SHOP_NAME');
        $assign['meta_description'] = \BTMailchimpEcommerce::$oModule->l('By subscribing to our newsletter, you will receive all the latest news about our products and much more!', 'HookDisplay');

        try {
            if (
                !empty(\BTMailchimpEcommerce::$conf['MCE_SIGNUP'])
                && !empty(\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'])
            ) {
                if (array_key_exists(\BTMailchimpEcommerce::$iCurrentLang, \BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'])) {
                    $assign['sNewsletterForm'] = html_entity_decode(\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'][\BTMailchimpEcommerce::$iCurrentLang]);
                } elseif (array_key_exists(\Configuration::get('PS_LANG_DEFAULT'), \BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'])) {
                    $assign['sNewsletterForm'] = html_entity_decode(\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML'][\Configuration::get('PS_LANG_DEFAULT')]);
                } else {
                    $assign['sNewsletterForm'] = html_entity_decode(reset(\BTMailchimpEcommerce::$conf['MCE_SIGNUP_HTML']));
                }
            }
        } catch (\Exception $e) {
            $assign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
        }
        $assign['sErrorInclude'] = \MCE\Tools::getTemplatePath(_MCE_PATH_TPL_NAME . _MCE_TPL_FRONT_PATH . _MCE_TPL_ERROR);

        // use case  - PS 1.7 should change the template path
        $sTpl = (\BTMailchimpEcommerce::$bCompare17 ? 'module:btmailchimpecommerce/views/templates/front/1.7/' : '1.6/') . _MCE_TPL_SIGNUP_CTRL;

        return array('tpl' => $sTpl, 'assign' => $assign);
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
        } catch (\Exception $e) {
        }

        return array('tpl' => _MCE_TPL_HOOK_PATH . _MCE_TPL_EMPTY, 'assign' => array());
    }
}

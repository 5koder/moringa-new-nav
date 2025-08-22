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

namespace MCE\Chimp\Signup;

class RenderPopup implements \IRender
{
    /**
     * @var int
     */
    protected $lang_id = null;

    /**
     * @var array
     */
    public $params = array();

    /**
     * RenderPopup constructor.
     * @param array $params
     */
    public function __construct($params)
    {
        // set params
        $this->params = $params;

        if (!isset($this->params['popup_not_display'])) {
            $this->params['popup_not_display'] = false;
        }

        $this->params['ajax'] = $this->params['popup_not_display'] ? true : false;

        require_once(_MCE_PATH_LIB_COMMON . 'Cookie.php');

        if (!\MCE\Cookie::exist(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_stop')) {
            \MCE\Cookie::set(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_stop', 0, time() + _MCE_COOKIE_SIGNUP_EXPIRE);
            \MCE\Cookie::set(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_times', 0, time() + _MCE_COOKIE_SIGNUP_EXPIRE);
        }
    }

    /**
     * check if requirements are valid
     */
    public function valid()
    {
        $valid = true;

        // check if the default form is valid
        if (empty($this->params['form'][$this->params['lang_id']])) {
            if (empty($this->params['form'][$this->params['lang_id_default']])) {
                $valid = false;
            } else {
                $this->lang_id = $this->params['lang_id_default'];
            }
        } else {
            $this->lang_id = $this->params['lang_id'];
        }

        // manage the displayed pages and cookie values
        if ($valid) {
            if (
                isset($this->params['times'])
                && isset($this->params['popup_not_display'])
                && isset($this->params['popup_width'])
                && isset($this->params['popup_height'])
                && isset($this->params['popup_text'])
                && isset($this->params['popup_text_valign'])
                && isset($this->params['popup_text_halign'])
            ) {
                if (!empty($this->params['pages'])) {
                    if (!in_array(\MCE\Tools::detectCurrentPage(), $this->params['pages'])) {
                        $valid = false;
                    }
                }
                // manage the cookie
                if ($valid) {
                    if (\MCE\Cookie::get(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_stop')) {
                        $valid = false;
                    } elseif (!empty($this->params['times'])) {
                        // the user decided to not display the popup anymore
                        $times = (int)\MCE\Cookie::get(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_times');

                        if (is_numeric($times)) {
                            if ($times >= $this->params['times']) {
                                $valid = false;
                            } else {
                                \MCE\Cookie::update(_MCE_COOKIE_SIGNUP . \BTMailchimpEcommerce::$iShopId, 'display_times', $times + 1);
                            }
                        }
                    }
                }
            } else {
                $valid = false;
            }
        }

        return $valid;
    }


    /**
     * return the assign HTML/JS values
     *
     * @throws \Exception
     * @return array
     */
    public function render()
    {
        \Context::getContext()->controller->addCSS(_MCE_URL_CSS . 'front.css');
        // get FancyBox plugin
        $aJsCss = \Media::getJqueryPluginPath('fancybox');

        // add fancybox plugin
        if (!empty($aJsCss['js']) && !empty($aJsCss['css'])) {
            \Context::getContext()->controller->addJqueryPlugin('fancybox');
        }

        $image = '';
        // check if the option is activated to display the banner
        if (!empty($this->params['popup_use_image'])) {
            // load signup popup images if exist
            $images = \MCE\Tools::loadImage('signup-popup', \BTMailchimpEcommerce::$iShopId, true, \Language::getLanguages(true));

            // get the matching language image
            if (!empty($images)) {
                if (isset($images[$this->lang_id])) {
                    $image = $images[$this->lang_id];
                } else {
                    $image = reset($images);
                }
            }
        }

        // get the matching language text
        if (isset($this->params['popup_text'][$this->lang_id])) {
            $text = $this->params['popup_text'][$this->lang_id];
        } else {
            $text = reset($this->params['popup_text']);
        }

        $html = \BTMailchimpEcommerce::$oModule->displayModule(
            _MCE_TPL_HOOK_PATH . 'signup/popup.tpl',
            array(
                'form' => $this->params['form'][$this->lang_id],
                'button' => $this->params['ajax'],
                'image' => $image,
                'text' => $text,
                'text_valign' => $this->params['popup_text_valign'],
                'text_halign' => $this->params['popup_text_halign'],
                'text_valign_custom' => $this->params['popup_text_valign_custom'],
                'text_halign_custom' => $this->params['popup_text_halign_custom'],
            )
        );

        return array(
            'render_html' => htmlspecialchars_decode($html),
            'render_js' => array(
                'replace' => false,
                'hide' => false,
                'popup' => true,
                'popup_selector' => 'mce_popup',
                'popup_not_display' => $this->params['popup_not_display'],
                'popup_width' => $this->params['popup_width'],
                'popup_height' => $this->params['popup_height'],
                'ajax' => $this->params['ajax'],
                'ajax_selector' => (!empty($this->params['ajax']) ? 'bt_signup_hide' : ''),
                'ajax_url' => (!empty($this->params['ajax']) ? \Context::getContext()->link->getModuleLink(_MCE_MODULE_SET_NAME, _MCE_FRONT_CTRL_NEWSLETTER) : ''),
                'ajax_params' => (!empty($this->params['ajax']) ? 'sAction=signup&sType=notdisplay&bt_token=' . \MCE\Tools::setSecureKey(_MCE_SECURE_HASH, _MCE_TOKEN, 0) . '&bt_signup_display=' : ''),
            ),
        );
    }
}

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

class RenderForm implements \IRender
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
     * RenderForm constructor.
     * @param array $params
     */
    public function __construct($params)
    {
        // set params
        $this->params = $params;
    }

    /**
     * check if requirements are valid
     */
    public function valid()
    {
        $valid = true;

        if (empty($this->params['selector'])) {
            $valid = false;
        } elseif (empty($this->params['label'][$this->params['lang_id']])) {
            if (empty($this->params['label'][$this->params['lang_id_default']])) {
                $valid = false;
            } else {
                $this->lang_id = $this->params['lang_id_default'];
            }
        } else {
            $this->lang_id = $this->params['lang_id'];
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
        // detect if the NL module is used
        if (isset($this->params['use_nl_module'])
            && $this->params['use_nl_module'] == false
        ) {
            $hide = false;
            $this->params['selector']= '#footer';
        } else {
            $hide = true;
        }

        $html = \BTMailchimpEcommerce::$oModule->displayModule(
            _MCE_TPL_HOOK_PATH . 'signup/mailchimp-form-alone.tpl',
            array(
                'form' => $this->params['form'][$this->lang_id],
                'hide_nl_module' => $hide
            )
        );

        return array(
            'render_html' => htmlspecialchars_decode($html),
            'render_js' => array(
                'hide' => $hide,
                'module_selector' => $this->params['selector'],
            ),
        );
    }
}

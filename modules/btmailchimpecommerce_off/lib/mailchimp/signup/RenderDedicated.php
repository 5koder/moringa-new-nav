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

class RenderDedicated implements \IRender
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
     * RenderDedicated constructor.
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

        if (empty($this->params['selector'])
            && empty($this->params['label'])
            && empty($this->params['link'])
        ) {
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

        // handle the template
        $html = \BTMailchimpEcommerce::$oModule->displayModule(
            _MCE_TPL_HOOK_PATH . 'signup/dedicated.tpl',
            array(
                'link_dedicated' => $this->params['link'],
                'label' => $this->params['label'][$this->lang_id],
                'hide_nl_module' => $hide
            )
        );

        return array(
            'render_html' => $html,
            'render_js' => array(
                'hide' => $hide,
                'module_selector' => $this->params['selector'],
            ),
        );
    }
}

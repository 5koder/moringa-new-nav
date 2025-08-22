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

class RenderShortcode implements \IRender
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
     * RenderShortcode constructor.
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

        if (empty($this->params['form'][$this->params['lang_id']])) {
            if (empty($this->params['form'][$this->params['lang_id_default']])) {
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
        $html = \BTMailchimpEcommerce::$oModule->displayModule(
            _MCE_TPL_HOOK_PATH . 'signup/shortcode.tpl',
            array(
                'form' => $this->params['form'][$this->lang_id],
            )
        );

        return array(
            'render_html' => htmlspecialchars_decode($html),
        );
    }
}

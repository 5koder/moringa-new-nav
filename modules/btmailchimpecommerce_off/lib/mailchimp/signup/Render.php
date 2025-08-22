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

class Render
{
    /**
     * @var string
     */
    public $type = null;

    /**
     * @var array
     */
    public $params = array();

    /**
     * @var string
     */
    protected $content = [];

    /**
     * @var object
     */
    protected $strategy = null;

    /**
     * Render constructor.
     * @param string $type => define the type of rendering the NL Signup form around HTML and JS code
     * @param array $params
     */
    public function __construct($type, $params)
    {
        // include interface
        require_once(_MCE_PATH_LIB_MC . 'signup/IRender.php');

        $this->type = $type;

        // load classes
        $this->autoload();

        // set params
        $this->params = $params;

        // set strategy
        $classname = '\MCE\Chimp\Signup\Render'. ucfirst($this->type);
        $this->strategy = new $classname($this->params);
    }


    /**
     * load the matching classes
     */
    public function autoload()
    {
        if (file_exists(_MCE_PATH_LIB_MC .'/signup/Render'. ucfirst($this->type). '.php')) {
            require_once(_MCE_PATH_LIB_MC .'/signup/Render'. ucfirst($this->type). '.php');
        }
    }

    /**
     * check if the content and configuration is valid
     */
    public function valid()
    {
        $valid = false;

        if (!empty($this->params['form'])
            && !empty($this->params['lang_id'])
            && !empty($this->params['lang_id_default'])
        ) {
            $valid = true;
            if (empty($this->params['form'][$this->params['lang_id']])) {
                if (empty($this->params['form'][$this->params['lang_id']])) {
                    $valid = false;
                }
            }
        }

        if ($valid) {
            $valid = $this->strategy->valid();
        }

        return $valid;
    }

    /**
     * check if we can return the content according to requirements for each type of MC NL form settigns
     * @return array
     */
    public function render()
    {
        if ($this->valid()) {
            $this->content = $this->strategy->render();
        }

        return $this->content;
    }
}

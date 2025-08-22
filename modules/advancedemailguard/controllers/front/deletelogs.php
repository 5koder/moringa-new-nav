<?php
/**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*/

class AdvancedemailguardDeletelogsModuleFrontController extends ModuleFrontController
{
    /**
     * Module instance.
     *
     * @var \Advancedemailguard
     */
    public $module;

    /**
     * Create new module front controller.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->module->isDemo()) {
            die;
        }

        if (Tools::getValue('token') !== $this->module->getLogsToken()) {
            http_response_code(401);
            die;
        }

        $days = (int) Tools::getValue('days');
        if (! $this->module->deleteLogs($days)) {
            http_response_code(500);
        }
        die;
    }
}

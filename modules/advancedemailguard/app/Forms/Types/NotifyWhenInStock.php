<?php
/**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*/

namespace ReduxWeb\AdvancedEmailGuard\Forms\Types;

use ReduxWeb\AdvancedEmailGuard\Forms\Form;
use ReduxWeb\AdvancedEmailGuard\Forms\EmailForm;

class NotifyWhenInStock extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return ($this->context->controller instanceof \Ps_EmailAlertsActionsModuleFrontController
            || $this->context->controller instanceof \MailalertsActionsModuleFrontController)
            && $this->getInput('process') === 'add';
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getInput('customer_email');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        if ($this->module->isLegacyMAModuleEnabled()) {
            die('-1');
        }
        $this->module->sendJson(array(
            'error' => true,
            'message' => $this->module->getValidationError($type),
        ), true);
    }
}

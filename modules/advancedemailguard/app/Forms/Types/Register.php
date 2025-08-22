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

class Register extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            // If one-page-checkout mode is enabled...
            if ($this->module->isOPCEnabled()) {
                return $this->context->controller instanceof \AuthController
                    && (bool) $this->getInput('is_new_customer')
                    && $this->submitExists('submitAccount');
            }

            return $this->context->controller instanceof \AuthController
                && !$this->inputExists('guest_email')
                && $this->submitExists('submitAccount');
        }

        return $this->context->controller instanceof \AuthController
            && $this->submitExists('submitCreate');
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getInput('email');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        if ($this->module->isLegacyOPCEnabled() && (bool) $this->getInput('ajax')) {
            $this->module->sendJson(array(
                'hasError' => true,
                'errors' => array($this->module->getValidationError($type)),
            ));
        } else {
            parent::sendResponseWithError($type);
        }
    }
}

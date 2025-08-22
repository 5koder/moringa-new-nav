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

class QuickOrder extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            if ($this->module->isOPCEnabled()) {
                return $this->context->controller instanceof \AuthController
                    && ! (bool) $this->getInput('is_new_customer')
                    && $this->submitExists('submitAccount');
            }

            return $this->context->controller instanceof \AuthController
                && $this->inputExists('guest_email')
                && $this->submitExists('submitGuestAccount');
        }

        return $this->context->controller instanceof \OrderController
            && $this->submitExists('submitCreate');
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7', '<') && ! $this->module->isOPCEnabled()) {
            return $this->getInput('guest_email');
        }
        return $this->getInput('email');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        if ($this->module->isLegacyOPCEnabled() && $this->inputExists('ajax')) {
            $this->module->sendJson(array(
                'hasError' => true,
                'errors' => array($this->module->getValidationError($type)),
            ));
        } else {
            parent::sendResponseWithError($type);
        }
    }
}

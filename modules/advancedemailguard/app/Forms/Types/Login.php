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

class Login extends Form
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            return ($this->context->controller instanceof \AuthController
                || $this->context->controller instanceof \OrderController)
                && $this->submitExists('submitLogin');
        }
        return $this->context->controller instanceof \AuthController
                && $this->submitExists('SubmitLogin');
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
                'token' => \Tools::getToken(false),
            ));
        } else {
            parent::sendResponseWithError($type);
        }
    }
}

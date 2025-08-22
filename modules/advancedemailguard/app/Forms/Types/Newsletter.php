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

class Newsletter extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        if ($this->submitExists('submitNewsletter')) {
            return true;
        }
        return $this->context->controller instanceof \Ps_EmailsubscriptionSubscriptionModuleFrontController
            && $this->context->controller->ajax;
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
        if ($this->context->controller->ajax) {
            $this->module->sendJson(array(
                'nw_error' => true,
                'value' => $this->getInput('email'),
                'msg' => $this->module->getValidationError($type),
            ));
        } else {
            parent::sendResponseWithError($type);
        }
    }
}

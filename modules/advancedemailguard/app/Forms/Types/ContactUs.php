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
use ReduxWeb\AdvancedEmailGuard\Forms\MessageForm;

class ContactUs extends Form implements EmailForm, MessageForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return $this->context->controller instanceof \ContactController
            && $this->submitExists('submitMessage');
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getInput('from');
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return $this->getInput('message');
    }
}

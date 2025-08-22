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

class SendToFriend extends Form implements EmailForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return $this->getInput('action') === 'sendToMyFriend';
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
        unset($type);
        die('0');
    }
}

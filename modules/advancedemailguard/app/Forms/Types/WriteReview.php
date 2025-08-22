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
use ReduxWeb\AdvancedEmailGuard\Forms\MessageForm;

class WriteReview extends Form implements MessageForm
{
    /**
     * {@inheritDoc}
     */
    public function isSubmitted()
    {
        return $this->context->controller instanceof \ProductCommentsPostCommentModuleFrontController
            || ($this->context->controller instanceof \ProductCommentsDefaultModuleFrontController
            && $this->getInput('action') === 'add_comment');
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        if (\Tools::version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            return $this->getInput('comment_content');
        }
        return $this->getInput('content');
    }

    /**
     * {@inheritDoc}
     */
    public function sendResponseWithError($type)
    {
        $this->module->sendJson(array(
            'success' => false,
            'errors' => array($this->module->getValidationError($type)),
        ), \Tools::version_compare(_PS_VERSION_, '1.7.6', '>='));
    }
}

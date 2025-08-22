<?php
/**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*/

namespace ReduxWeb\AdvancedEmailGuard\Forms;

interface MessageForm
{
    /**
     * Get form message content.
     *
     * @return string
     */
    public function getMessage();
}

{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

{if $logsType === null}
    {include file='./settings.tpl'}
    {include file='./summary.tpl'}
{else}
    {if $logsType === 'email'}
        {include file='./email.tpl'}
    {elseif $logsType === 'message'}
        {include file='./message.tpl'}
    {else}
        {include file='./recaptcha.tpl'}
    {/if}
{/if}
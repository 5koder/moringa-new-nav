{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

{if $recaptchaType === 'v3'}
    <div id="adveg-grecaptcha" class="adveg-grecaptcha-{if $recaptchaPos === 'inline'}inline{else}fixed{/if}"></div>
{/if}
{if $recaptchaLegal !== null}
    <div id="adveg-grecaptcha-legal">
        {l s='Site protected by reCAPTCHA.' mod='advancedemailguard'}
        <a href="{$recaptchaLegal.privacy|escape:'html':'UTF-8'}" target="_blank">{l s='Privacy' mod='advancedemailguard'}</a>  -
        <a href="{$recaptchaLegal.terms|escape:'html':'UTF-8'}" target="_blank">{l s='Terms' mod='advancedemailguard'}</a>
    </div>
{/if}
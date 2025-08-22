{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

{if $ratingMessage}
    <div id="ratingMessage" class="rounded-0 mb-0 bg-primary text-white py-2">
        <div class="container-fluid px-md-4 d-flex align-items-center justify-content-center">
            <div>{$ratingMessage}{* This is HTML content *} :)</div>
            <div class="ml-3">
                <a href="{$psAddonsLinks.ratings|escape:'html':'UTF-8'}" target="_blank"
                    class="btn btn-light js-rating-link" style="color: #e83e8c">
                    <i class="material-icons-outlined">grade</i>
                    {l s='Sure, take me there' mod='advancedemailguard'}
                </a>
                <a href="#" class="btn btn-outline-light ml-1 js-rating-dismiss">
                    {l s='No, thanks' mod='advancedemailguard'}
                </a>
            </div>
        </div>
    </div>
{/if}
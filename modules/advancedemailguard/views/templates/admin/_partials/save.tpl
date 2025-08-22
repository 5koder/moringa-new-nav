{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

<div class="pt-2">
    <div class="text-right">
        {if isset($disabledForDemo) && $disabledForDemo}
            {include file='./demo.badge.tpl'}
        {/if}
        <button type="submit" class="btn btn-outline-secondary px-4"{if isset($disabledForDemo) && $disabledForDemo} disabled{/if}>
            <i class="material-icons-outlined md-18 mr-1">done_outline</i>
            {$trans.save|escape:'html':'UTF-8'}
        </button>
    </div>
</div>

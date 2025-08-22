{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

<form action="{$url|escape:'html':'UTF-8'}" method="get">
    <input type="hidden" name="controller" value="{$smarty.get.controller|escape:'html':'UTF-8'}">
    <input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}">
    <input type="hidden" name="tab" value="logs">
    <input type="hidden" name="type" value="{$type|escape:'html':'UTF-8'}">
    <div class="form-group">
        <label>{l s='Show' mod='advancedemailguard'}</label>
        <select name="success" class="custom-select">
            <option value="">{$trans.all|escape:'html':'UTF-8'}</option>
            <option value="0"{if $filters.success === false} selected{/if}>{l s='Failed validations' mod='advancedemailguard'}</option>
            <option value="1"{if $filters.success === true} selected{/if}>{l s='Passed validations' mod='advancedemailguard'}</option>
        </select>
    </div>
    <div class="form-group">
        <label>{l s='Form' mod='advancedemailguard'}</label>
        <select name="form" class="custom-select">
            <option value="">{$trans.all|escape:'html':'UTF-8'}</option>
            {foreach $forms as $type => $form}
                <option value="{$type|escape:'html':'UTF-8'}"{if $filters.form === $type} selected{/if}>{$form.name|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <button type="submit" class="btn btn-outline-secondary btn-block">
        <i class="material-icons-outlined md-18 mr-1">done_outline</i>
        {$trans.apply|escape:'html':'UTF-8'}</button>
</form>
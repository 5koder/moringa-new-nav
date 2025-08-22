{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

<td>#{$log.id_log|escape:'html':'UTF-8'}</td>
<td>
    <i class="material-icons-outlined" style="color: {$log.display.color|escape:'html':'UTF-8'}">{$log.display.icon|escape:'html':'UTF-8'}</i>
    <span class="ml-1">{$log.display.name|escape:'html':'UTF-8'}</span>
</td>
<td class="text-center text-nowrap">
    {if $log.success}
        <i class="material-icons-outlined" style="color: #0fb9b1; cursor: default;"
            data-toggle="tooltip" title="{l s='Passed validation' mod='advancedemailguard'}">verified_user</i>
    {else}
        <i class="material-icons-outlined" style="color: #ff6b81; cursor: default;"
            data-toggle="tooltip" title="{l s='Failed validation' mod='advancedemailguard'}">block</i>
    {/if}
</td>
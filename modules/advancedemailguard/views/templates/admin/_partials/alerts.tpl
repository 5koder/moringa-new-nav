{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

{if $alerts}
    {foreach $alerts as $type => $items}
        {if $items}
            <div class="rounded-0 mb-0 px-0 alert alert-{if $type === 'success'}success{elseif $type === 'warning'}warning{elseif $type === 'error'}danger{else}info{/if}">
                <div class="container px-md-4">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {if count($items) === 1}
                        <i class="material-icons-outlined mr-1">{if $type === 'success'}done_outline{elseif $type === 'warning'}warning_amber{elseif $type === 'error'}error_outline{else}info_outline{/if}</i>
                        {$items.0|escape:'html':'UTF-8'}
                    {else}
                        <ul class="list-unstyled mb-0">
                            {foreach $items as $item}
                                <li>
                                    <i class="material-icons-outlined mr-1">{if $type === 'success'}done_outline{elseif $type === 'warning'}warning_amber{elseif $type === 'error'}error_outline{else}info_outline{/if}</i>
                                    {$item|escape:'html':'UTF-8'}
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </div>
            </div>
        {/if}
    {/foreach}
{/if}
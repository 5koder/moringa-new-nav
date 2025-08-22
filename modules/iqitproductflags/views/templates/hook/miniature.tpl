{if $flags|@count > 0}

    <div class="product-campagains d-flex flex-wrap gap-1 mt-2 mb-2 {if isset($params.align)} justify-content-{$params.align}{/if}">
        {foreach from=$flags item=flag}
            <span 
                class="product-flags__flag btn btn-light btn-sm cursor-initial" 
                style="{$flag.style}">
                {$flag.title}
            </span>
        {/foreach}
    </div>
{/if}
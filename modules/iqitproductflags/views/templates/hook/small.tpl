{if $flags|@count > 0}
    <div class="product-campagains d-flex flex-wrap gap-1 mb-3">
        {foreach from=$flags item=flag}
            {capture assign="tag"}{if $flag.link}a{else}span{/if}{/capture}
            <{$tag} 
                {if $tag == 'a'}href="{$flag.link}"{/if} 
                class="product-flags__flag  btn btn-light btn-sm  {if $tag == 'span'}cursor-initial{/if}" 
                style="{$flag.style}">
                {$flag.title}
            </{$tag}>
        {/foreach}
    </div>
{/if}
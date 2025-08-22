{if $flags|@count > 0}
    {foreach from=$flags item=flag}
        <div class="p-4 d-flex gap-2 flex-column my-3" style="{$flag.style}">
            <p class="h6 text-reset mb-0">{$flag.title}</p>
            {if $flag.description}<p class="mb-2">{$flag.description nofilter}</p>{/if}
            {if $flag.link}<a href="{$flag.link}" 
            class="text-reset text-decoration-underline align-self-start">{l s='Read more' d='Modules.Iqitproductflags.Shop'}</a>{/if}
    </div>
{/foreach}
{/if}
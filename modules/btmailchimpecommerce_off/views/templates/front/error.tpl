{*
*
* Mailchimp Pro - Newsletter sync and eCommerce Automation
*
* @author BusinessTech.fr
* @copyright Business Tech
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}

{block name='page_content'}
<div class="alert alert-danger">
	{*<button type="button" class="close" data-dismiss="alert">×</button>*}
	{foreach from=$aErrors name=condition key=nKey item=aError}
	<h3>{$aError.msg|escape:'htmlall':'UTF-8'}</h3>
	{if $bDebug == true}
	<ol>
		{if !empty($aError.code)}<li>{l s='Error code' mod='btmailchimpecommerce'} : {$aError.code|intval}</li>{/if}
		{if !empty($aError.file)}<li>{l s='Error file' mod='btmailchimpecommerce'} : {$aError.file|escape:'htmlall':'UTF-8'}</li>{/if}
		{if !empty($aError.line)}<li>{l s='Error line' mod='btmailchimpecommerce'} : {$aError.line|intval}</li>{/if}
		{if !empty($aError.context)}<li>{l s='Error context' mod='btmailchimpecommerce'} : {$aError.context|escape:'htmlall':'UTF-8'}</li>{/if}
	</ol>
	{/if}
	{/foreach}
</div>
{/block}
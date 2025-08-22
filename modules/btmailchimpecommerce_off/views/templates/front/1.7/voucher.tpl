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

<!-- MCE - Automation voucher for one the voucher automation settings -->
{extends file='page.tpl'}

{block name='page_content'}
<div id="{$sMceModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
	{* USE CASE - GOT ERRORS *}
	{if !empty($aErrors)}
		{if (!empty($bDebugPS))}
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">×</button>
				{foreach from=$aErrors name=condition key=nKey item=aError}
					<h3>{$aError.msg|escape:'htmlall':'UTF-8'}</h3>
						<ol>
							{if !empty($aError.code)}<li>{l s='Error code' mod='btmailchimpecommerce'} : {$aError.code|intval}</li>{/if}
							{if !empty($aError.file)}<li>{l s='Error file' mod='btmailchimpecommerce'} : {$aError.file|escape:'htmlall':'UTF-8'}</li>{/if}
							{if !empty($aError.line)}<li>{l s='Error line' mod='btmailchimpecommerce'} : {$aError.line|intval}</li>{/if}
							{if !empty($aError.context)}<li>{l s='Error context' mod='btmailchimpecommerce'} : {$aError.context|escape:'htmlall':'UTF-8'}</li>{/if}
						</ol>
				{/foreach}
			</div>
		{/if}
		{if !empty(sLoginURI)}
			<div class="alert alert-info">
				{l s='To get your voucher code you have to be logged  in' mod='btmailchimpecommerce'}
			</div>
			<div class="clr_10"></div>
			<div>
				<p class="center"><a class="btn btn-primary" href="{$sLoginURI|escape:'htmlall':'UTF-8'}"><i class="icon-star-empty"></i> {l s='Log in/Sign up' mod='btmailchimpecommerce'}</a></p>
			</div>		
		{/if}
	{* USE CASE - DISPLAY THE VOUCHER CODE *}
	{else}
		<h2>{l s='Your voucher code:' mod='btmailchimpecommerce'}</h2>
		<div class="clr_10"></div>

		{* USE CASE - SOMETHING WENT WRONG AND THE CODE HAS NOT BEEN CREATED *}
		{if isset($aVoucherAdd.name) && empty($aVoucherAdd.name)}
			<div class="alert alert-danger col-xs-12 col-md-12 col-lg-12" id="bt_confirm"><button type="button" class="close" data-dismiss="alert">×</button>
				{$aVoucherAdd.status|escape:'htmlall':'UTF-8'}
			</div>
		{else}
			<div class="form-group">
				<label for="bt_voucher-code">{l s='Code:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" type="text" value="{$aVoucher.code|escape:'htmlall':'UTF-8'}">
				<div class="clr_20"></div>
				<label for="bt_voucher-amount">{l s='Amount:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" id="disabledInput" type="text" placeholder="{$aVoucher.displayAmount|escape:'htmlall':'UTF-8'}" disabled>
				<div class="clr_20"></div>
				<label for="bt_voucher-amount">{l s='Validity:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" id="disabledInput" type="text" placeholder="{$aVoucher.validity|intval} {l s='days' mod='btmailchimpecommerce'}" disabled>
			</div>

			{* USE CASE - if the minimum amount filled or the cumulative with others or kind of reduction to false *}
			{if !empty($aVoucher.minimum)
				|| empty($aVoucher.cumulativeOther)
				|| empty($aVoucher.cumulativeReduc)
			}
				<div class="clr_10"></div>
				<div class="alert alert-warning bold col-xs-12 col-md-12 col-lg-12" id="bt_confirm"><button type="button" class="close" data-dismiss="alert">×</button>
					{l s='Please just note your voucher code has some restrictions, please find them below:' mod='btmailchimpecommerce'}
					{if !empty($aVoucher.minimum)}
						<div class="clr_5"></div>
						- {l s='The voucher code is only valid for a minimum purchase of:' mod='btmailchimpecommerce'} "{if $aVoucher.sign != '€'}{$aVoucher.sign|escape:'htmlall':'UTF-8'}{/if}{$aVoucher.minimum|floatval}{if $aVoucher.sign == '€'}{$aVoucher.sign|escape:'htmlall':'UTF-8'}{/if}"
					{/if}
					{if empty($aVoucher.cumulativeOther)}
						<div class="clr_5"></div>
						- {l s='The voucher code is not cumulative with others codes' mod='btmailchimpecommerce'}
					{/if}
					{if empty($aVoucher.cumulativeReduc)}
						<div class="clr_5"></div>
						- {l s='The voucher code is not cumulative with another kind of reduction applied on the catalog' mod='btmailchimpecommerce'}
					{/if}
				</div>
			{/if}
		{/if}
	{/if}
</div>
{/block}
<!-- /MCE - Automation voucher for one the voucher automation settings -->
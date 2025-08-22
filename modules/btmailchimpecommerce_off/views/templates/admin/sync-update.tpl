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

<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap bt_popup_sync_status_message">
	{if !empty($bUpdate)}
		<div class="clr_5"></div>
		<div class="alert alert-success col-xs-12 col-md-12 col-lg-12" id="bt_confirm"><button type="button" class="close" data-dismiss="alert">×</button>
		{if $sSyncType == 'customer'}
			{l s='Your customers have been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'newsletter'}
			{l s='Your e-mail addresses have been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'product'}
			{l s='Your products have been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'order'}
			{l s='Your past orders have been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'member'}
			{l s='The e-mail address has been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'single-product'}
			{l s='The product has been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'single-combination'}
			{l s='The product combination has been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'single-cart'}
			{l s='The cart has been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'single-order'}
			{l s='The order has been synchronized with success!' mod='btmailchimpecommerce'}
		{elseif $sSyncType == 'single-customer'}
			{l s='The customer has been synchronized with success!' mod='btmailchimpecommerce'}
		{/if}
		</div>
	{elseif !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
	{/if}
</div>
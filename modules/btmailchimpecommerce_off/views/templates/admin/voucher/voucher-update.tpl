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

<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap">
	{if !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
	{else}
		<div class="clr_5"></div>

		<div class="alert alert-success col-xs-12 col-md-12 col-lg-12" id="bt_confirm"><button type="button" class="close" data-dismiss="alert">×</button>
			{l s='Your voucher has been created/updated with success!' mod='btmailchimpecommerce'}
		</div>
	{/if}
</div>
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
	{if !empty($bUpdate)}
		<div class="clr_5"></div>
		<div class="alert alert-success col-xs-12 col-md-12 col-lg-12" id="bt_confirm"><button type="button" class="close" data-dismiss="alert">×</button>
			{l s='You just created the "BIRTHDAY" merge_field with success for the current MC store and language.' mod='btmailchimpecommerce'}
			<div class="clr_10"></div>
			{l s='To sync your customer list now, you have to close this window and coming back here to select how you want to sync it. Note, this window will automatically close itself in few seconds.' mod='btmailchimpecommerce'}
		</div>
		{literal}
		<script type="text/javascript">
			$('#bt_sync-button').hide();

			setTimeout(function () {$.fancybox.close()}, 10000);
		</script>
		{/literal}
	{elseif !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
	{/if}
</div>
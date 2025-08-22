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

<div class="alert alert-success validate-message col-xs-12 col-sm-12 col-md-12 col-lg-12" {if !empty($sConfirmId)}id="{$sConfirmId}"{/if}><button type="button" class="close" data-dismiss="alert">×</button>
	{l s='Settings updated' mod='btmailchimpecommerce'}
	{if !empty($aConfirmDetail) && is_array($aConfirmDetail)}
		{foreach from=$aConfirmDetail name=condition key=nKey item=sConfirmDetail}
		<div class="clr_5"></div>
		{$sConfirmDetail|escape:'htmlall':'UTF-8'}
		{/foreach}
	{/if}
</div>
{literal}
	<script type="text/javascript">
		$(".validate-message").delay(30000).slideUp();
	</script>
{/literal}
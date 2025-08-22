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

<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_form_mailchimp" name="bt_form_mailchimp" onsubmit="javascript: oMailchimp.form('bt_form_mailchimp', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_general_mailchimp', 'bt_settings_general_mailchimp', true, false, null, 'mailchimp', 'general');return false;">
	<input type="hidden" name="sAction" value="{$aQueryParams.mailchimp.action|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="sType" value="{$aQueryParams.mailchimp.type|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="sTpl" value="{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}mailchimp{/if}" />

	<h3><i class="icon icon-heart"></i>&nbsp;{l s='Mailchimp settings' mod='btmailchimpecommerce'}</h3>
	<div class="clr_10"></div>
	{if !empty($bUpdate)}
		{include file="`$sConfirmInclude`"}
		<div class="clr_10"></div>
		{if !empty($bApiKeyModified)}
			<div class="alert alert-warning warning-message col-xs-12 col-sm-12 col-md-12 col-lg-12"><button type="button" class="close" data-dismiss="alert">×</button>
				{l s='We have identified that you have changed your API key, so we need to reset all tables because an API key corresponds to a MailChimp account that itself corresponds to a single site URL. You must therefore start your configuration from scratch.' mod='btmailchimpecommerce'}
			</div>
			{literal}
				<script type="text/javascript">
					$(".warning-message").delay(15000).slideUp();
				</script>
			{/literal}
			<div class="clr_10"></div>
		{/if}
	{elseif !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
		<div class="clr_10"></div>
	{/if}

	<div class="form-group" id="bt_div_mc">
		<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
			<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the API Key of your MailChimp account (read our FAQ for help)' mod='btmailchimpecommerce'}">
				<strong>{l s='MailChimp API Key' mod='btmailchimpecommerce'}</strong>
			</span> :
		</label>
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-key"></i></span>
					<input type="text" id="bt_mc_api_key" name="bt_mc_api_key" size="35" value="{if !empty($sApiKey)}{$sApiKey|escape:'htmlall':'UTF-8'}{/if}" />
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the API Key of your MailChimp account (read our FAQ for help)' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
				<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/291" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about Mailchimp API key' mod='btmailchimpecommerce'}</a>
			</div>
			<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Be careful, if you change your API key, all the tables will be reset since an API key is linked to one account and one website URL' mod='btmailchimpecommerce'}</span>
		</div>
	</div>


	<div class="form-group" id="bt_div-mc-api-key">
		<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
			<strong>{l s='Click tracking cookie lifetime' mod='btmailchimpecommerce'}</strong> :
		</label>
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="col-xs-4 col-sm-4 col-md-4-lg-4">
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-clock-o"></i></span>
					<input type="text" id="bt_mc_cookie_ttl" name="bt_mc_cookie_ttl"  value="{if !empty($iCookieTime)}{$iCookieTime|intval}{/if}" />
				</div>
			</div>
			<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='In hours' mod='btmailchimpecommerce'}</span>
		</div>
	</div>

	<div class="clr_20"></div>
	<div class="clr_hr"></div>
	<div class="clr_20"></div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
			<div id="bt_error_mailchimp"></div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"><button class="btn btn-default pull-right" onclick="oMailchimp.form('bt_form_mailchimp', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_general_mailchimp', 'bt_settings_general_mailchimp', true, false, null, 'mailchimp', 'general');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button></div>
	</div>
</form>

<div class="clr_20"></div>

{literal}
<script type="text/javascript">
	//bootstrap components init
	$(document).ready(function() {
		$('.label-tooltip, .help-tooltip').tooltip();
	});
</script>
{/literal}
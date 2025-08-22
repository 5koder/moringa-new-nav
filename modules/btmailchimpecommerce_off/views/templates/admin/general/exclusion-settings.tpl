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

<script type="text/javascript">
	{literal}
	var oExclusionCallBack = [{
		'name' : 'updateList',
		'url' : '{/literal}{$sURI nofilter}{literal}',
		'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=list',
		'toShow' : 'bt_settings_list',
		'toHide' : 'bt_settings_list',
		'bFancybox' : false,
		'bFancyboxActivity' : false,
		'sLoadbar' : null,
		'sScrollTo' : null,
		'oCallBack' : {}
	}
	];
	{/literal}
</script>

<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_form_exclusion" name="bt_form_exclusion" onsubmit="return false;">
	<input type="hidden" name="sAction" value="{$aQueryParams.exclusion.action|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="sType" value="{$aQueryParams.exclusion.type|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="sTpl" value="{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}exclusion{/if}" />

	<h3><i class="icon icon-ban"></i>&nbsp;{l s='E-mail domain names exclusion' mod='btmailchimpecommerce'}</h3>
	<div class="clr_10"></div>
	{if !empty($bUpdate)}
		{include file="`$sConfirmInclude`"}
		<div class="clr_10"></div>
	{elseif !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
		<div class="clr_10"></div>
	{/if}


	{* Check if the MC API key is filled up *}
	{if !empty($sApiKey)}
		<div class="form-group" id="bt_div-mail-exclusion">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='All the users whom e-mail address matching with one of the domain names of this list won\'t be synchronized. In this way, you can exclude users who come from marketplaces, for example' mod='btmailchimpecommerce'}">
					<strong>{l s='E-mail domain names to exclude' mod='btmailchimpecommerce'}</strong>
				</span>
			</label>
			<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-envelope"></i></span>
						<input type="text" id="bt_exclusion_mail_domain" name="bt_exclusion_mail_domain"  value="" />
					</div>
				</div>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='All the users whom e-mail address matching with one of the domain names of this list won\'t be synchronized. In this way, you can exclude users who come from marketplaces, for example' mod='btmailchimpecommerce'}">&nbsp;<i class="icon-question-sign"></i></span>
				&nbsp;<input type="button" name="bt_exclude-mail-list" value="{l s='Exclude domain names' mod='btmailchimpecommerce'}" class="btn btn-success" onclick="excludeMailDomain();return false;" />
				<div class="clr_5"></div>
				<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='A valid domain name is \"name.extension\" like \"amazon.com\"' mod='btmailchimpecommerce'}</span>
				<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='You can add multiple values by separating them with \"\|\" character' mod='btmailchimpecommerce'}</span>
			</div>
		</div>

		<div class="form-group error-message" id="bt_exclusion_mails_error" style="display: none;">
			<div class="clr_10"></div>
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">&nbsp;</label>
			<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
				<div class="alert alert-danger ">
					<button type="button" class="close" onclick="$('#bt_exclusion_mails_error').slideUp();">×</button>
					{l s='You have not entered any domain name or a domain name indicated is not valid' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>

		<div class="clr_10"></div>
		<div id="bt_loading_div_exclusion_mails" style="display: none;">
			<div class="alert alert-info">
				<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
				<p style="text-align: center !important;">{l s='Your domains exclusion list update is in progress...' mod='btmailchimpecommerce'}</p>
			</div>
		</div>

		<div class="form-group" id="bt_div_exclusion_mails">
			{include file="`$sExclusionListInclude`"}
		</div>

		<div class="clr_20"></div>
		<div class="clr_hr"></div>
		<div class="clr_20"></div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
				<div id="bt_error_exclusion"></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
				{*<button class="btn btn-default pull-right" onclick="oMailchimp.form('bt_form_exclusion', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_general_exclusion', 'bt_settings_general_exclusion', false, false, oExclusionCallBack, 'exclusion', 'general');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button>*}
			</div>
		</div>
	{else}
		<div class="alert alert-warning">
			{l s='You don\'t have filled your API key yet! Please do it by clicking on the "Configure" button at the step 2 above or go to the "General settings > Mailchimp setttings" tab.' mod='btmailchimpecommerce'}
		</div>
	{/if}
</form>

<div class="clr_20"></div>

{literal}
	<script type="text/javascript">
		//bootstrap components init
		$(document).ready(function() {
			$('.label-tooltip, .help-tooltip').tooltip();
		});

		function excludeMailDomain() {
			if ($('#bt_exclusion_mail_domain').val() == '') {
				$('#bt_exclusion_mails_error').slideDown();
			}
			else {
				$('#bt_loading_div_exclusion_mails').show();
				$('#bt_exclusion_mails_error').slideUp();
				oMailchimp.ajax('{/literal}{$sURI nofilter}{literal}', '{/literal}sAction={$aQueryParams.exclusionEmail.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionEmail.type|escape:'htmlall':'UTF-8'}{literal}&bt_exclusion_domain='+$('#bt_exclusion_mail_domain').val(), 'bt_div_exclusion_mails', 'bt_div_exclusion_mails', false, false, 'exclusion_mails', false, null);
				$('#bt_exclusion_mail_domain').val('');
			}
		}
	</script>
{/literal}
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

{literal}
<script type="text/javascript">
	var oConfigSyncCallBack = [
		{
			'name' : 'updateSignup',
			'url' : '{/literal}{$sURI nofilter}{literal}',
			'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=signupForm',
			'toShow' : 'bt_settings_newsletter_sign_up',
			'toHide' : 'bt_settings_newsletter_sign_up',
			'bFancybox' : false,
			'bFancyboxActivity' : false,
			'sLoadbar' : null,
			'sScrollTo' : null,
			'oCallBack' : {}
		}];
</script>
{/literal}

<div class="bootstrap">
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_form_config_sync" name="bt_form_config_sync" onsubmit="javascript: oMailchimp.form('bt_form_config_sync', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_newsletter_config', 'bt_settings_newsletter_config', false, false, oConfigSyncCallBack, 'newsletter_config', 'newsletter');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.newsletterExport.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.newsletterExport.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sTpl" value="{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}config_sync{/if}" />

		<h3><i class="icon icon-wrench"></i>&nbsp;{l s='Users list synching' mod='btmailchimpecommerce'}</h3>
		<div class="clr_10"></div>

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
			{if !empty($aListNewsletter)}
				<div class="alert alert-info">
					{l s='The module is about to export not only all your customers to your MailChimp list but also users who have not created a customer account but who have subscribed to your newsletter.' mod='btmailchimpecommerce'}
				</div>
				<div class="clr_20"></div>

				<h4><i class="icon icon-pencil-square-o"></i>&nbsp;{l s='Configuration of your customers newsletter subscription' mod='btmailchimpecommerce'}</h4>
				<div class="clr_20"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Concerning your customers, you have the possibility, either to subscribe them all by default to your newsletter or to respect their choice to be subscribed or not (recommended to respect GDPR law)' mod='btmailchimpecommerce'}"><strong>{l s='Newsletter subscription' mod='btmailchimpecommerce'}</strong></span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<select name="bt_customer_export_type" id="bt_customer_export_type">
								<option value="optin" {if $sExportCustType == 'optin'}selected="selected"{/if}>{l s='Subscribe only customers who opted in (recommended)' mod='btmailchimpecommerce'}</option>
								<option value="all" {if $sExportCustType == 'all'}selected="selected"{/if}>{l s='Subscribe by default all customers' mod='btmailchimpecommerce'}</option>
							</select>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Concerning your customers, you have the possibility, either to subscribe them all by default to your newsletter or to respect their choice to be subscribed or not (recommended to respect GDPR law)' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
							<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/292" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about subscription of customers' mod='btmailchimpecommerce'}</a>
						</div>
						<div class="clr_10"></div>

						<div class="alert alert-warning" id="bt_warning_optin" style="display: {if $sExportCustType == 'all'}block{else}none{/if};">
							<b>{l s='WARNING: Remember that you\'re responsible for protecting your customer personal data.' mod='btmailchimpecommerce'}</b>&nbsp;
							{l s='Then, depending on your business area and localisation, make sure that subscribe by default all your customers to your newsletter is not a violation of law.' mod='btmailchimpecommerce'}
						</div>
					</div>
				</div>

				<div class="clr_20"></div>

				<h4><i class="icon icon-pencil-square-o"></i>&nbsp;{l s='First synchronization of users who are subscribers but not customers' mod='btmailchimpecommerce'}</h4>

				<div class="clr_10"></div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Only for the first export, you must choose which language to assign to current newsletter subscribers, who have not yet created a customer account' mod='btmailchimpecommerce'}">
								<strong>{l s='Language to be assigned to users who are only subscribers' mod='btmailchimpecommerce'}</strong>
							</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<select name="bt_user_language" id="bt_user_language">
								{foreach from=$aLanguages name=lang key=iPos item=aLanguage}
									<option value="{$aLanguage.id_lang|intval}"<option value="{$aLanguage.id_lang|intval}" {if !empty($iNewsletterModuleLang) && $iNewsletterModuleLang == $aLanguage.id_lang}selected="selected"{/if}>{$aLanguage.name|escape:'htmlall':'UTF-8'}</option>>{$aLanguage.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Only for the first export, you must choose which language to assign to current newsletter subscribers, who have not yet created a customer account' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							&nbsp;&nbsp;<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/293" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about first synching of subscribers' mod='btmailchimpecommerce'}</a>
						</div>
						<div class="clr_5"></div>
						<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='This option is only for the first synchronization because, then, the module will be able to detect the language of the new subscriber at the time of registration.' mod='btmailchimpecommerce'}</span>
					</div>
				</div>

				<div class="clr_20"></div>

				{if isset($aListNewsletter.active_catalog) && $aListNewsletter.active_catalog == 1}
					<div class="clr_hr"></div>
					<div class="clr_10"></div>

					<h4><i class="icon icon-pencil-square-o"></i>&nbsp;{l s='Manual synching' mod='btmailchimpecommerce'}</h4>

					<div class="alert alert-info">
						{l s='We have detected you already have synchronized your users list. However, you can always do it again by selecting "yes" below. The module will update information about the previous synchronized users and add new users.' mod='btmailchimpecommerce'}
					</div>
					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<strong>{l s='Last synchronization' mod='btmailchimpecommerce'}</strong> :
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="text" id="bt_last_synching" name="bt_last_synching" size="35" value="{$aListNewsletter.sync_date_last}" />
								</div>
							</div>
						</div>
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Once your user list has been synchronized for the first time, you can always synchronize it again. The module will update information about the previous synchronized users and add new users.' mod='btmailchimpecommerce'}">
								<strong>{l s='Redo the users list synching' mod='btmailchimpecommerce'}</strong>
							</span> :
						</label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_nl_resync" id="bt_nl_resync_on" value="1" onclick="oMailchimp.changeSelect(null, 'bt_div_btn_user_sync', null, null, true, true);" />
								<label for="bt_nl_resync_on" class="radioCheck">
									{l s='Yes' mod='btmailchimpecommerce'}
								</label>
								<input type="radio" name="bt_nl_resync" id="bt_nl_resync_off" value="0" checked="checked" onclick="oMailchimp.changeSelect(null, 'bt_div_btn_user_sync', null, null, true, false);" />
								<label for="bt_nl_resync_off" class="radioCheck">
									{l s='No' mod='btmailchimpecommerce'}
								</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>

					{* USE CASE - Upgrade from v1 to v2 and offer to the merchant to migrate old list members to the new one *}
					{if empty($bOldSyncFlag) && !empty($aOldLists)}
						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<div class="alert alert-warning">
									{l s='We have identified that you have upgraded the module. If you were using many lists, you can merge them all into the new chosen list by selecting "yes" on the migration option below and then by clicking on the "Export users" button. By this action, only your old lists members will be imported. Don\'t worry, the language information of people who subscribed through the PrestaShop native newsletter block will be retrieved by the module.' mod='btmailchimpecommerce'}
								</div>
							</div>
						</div>
					{/if}

					<div class="form-group" id="bt_div_btn_user_sync" style="display: none;">
						{if empty($bOldSyncFlag) && !empty($aOldLists)}
							<div class="form-group">
								<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
									{l s='Migrate your old lists members?' mod='btmailchimpecommerce'}
								</label>
								<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="bt_old_list_sync" id="bt_old_list_sync_on" value="1" />
									<label for="bt_old_list_sync_on" class="radioCheck">
										{l s='Yes' mod='btmailchimpecommerce'}
									</label>
									<input type="radio" name="bt_old_list_sync" id="bt_old_list_sync_off" value="0" checked="checked" />
									<label for="bt_old_list_sync_off" class="radioCheck">
										{l s='No' mod='btmailchimpecommerce'}
									</label>
									<a class="slide-button btn"></a>
								</span>
								</div>
							</div>

						{/if}
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							<button id="bt_btn_list_nl_sync" type="button" class="btn btn-info btn-lg" onclick="oMailchimp.formatFancyboxUrl('newsletter', '{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.newsletterExport.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.newsletterExport.type|escape:'htmlall':'UTF-8'}&sTpl=popup{if !empty($aListNewsletter.batches)}&batch=true{/if}{if empty($bOldSyncFlag) && !empty($aOldLists)}&oldsync='+$('input:checked[name=\'bt_old_list_sync\']').val()+'{/if}', 'a#bt_list_nl_sync');return false;"><i class="icon icon-refresh"></i>&nbsp;{l s='Export users' mod='btmailchimpecommerce'}</button>
							<a class="fancybox.ajax" id="bt_list_nl_sync" href="#"></a>&nbsp;&nbsp;<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/295" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about users list synchronization' mod='btmailchimpecommerce'}</a>
						</div>
					</div>
				{else}
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							<button id="bt_btn_list_nl_sync" type="button" class="btn btn-info btn-lg" onclick="oMailchimp.formatFancyboxUrl('newsletter', '{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.newsletterExport.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.newsletterExport.type|escape:'htmlall':'UTF-8'}&sTpl=popup{if !empty($aListNewsletter.batches)}&batch=true{/if}', 'a#bt_list_nl_sync');return false;"><i class="icon icon-refresh"></i>&nbsp;{l s='Export users' mod='btmailchimpecommerce'}</button>
							<a class="fancybox.ajax" id="bt_list_nl_sync" href="#"></a>&nbsp;&nbsp;<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/295" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about users list synchronization' mod='btmailchimpecommerce'}</a>
						</div>
					</div>
				{/if}


				<div class="clr_20"></div>
				<div class="clr_hr"></div>
				<div class="clr_20"></div>

				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
						<div id="bt_error_newsletter_config"></div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
						<button class="btn btn-default pull-right" id="bt_btn_nl_config" onclick="oMailchimp.form('bt_form_config_sync', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_newsletter_config', 'bt_settings_newsletter_config', false, false, oConfigSyncCallBack, 'newsletter_config', 'newsletter');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button>
					</div>
				</div>
			{else}
				<div class="alert alert-warning">
					{l s='You have not yet selected a list for exporting users. Please go to the "General settings > Users list choice" tab to select an existing list or create a new one.' mod='btmailchimpecommerce'}
				</div>
			{/if}
		{else}
			<div class="alert alert-warning">
				{l s='You don\'t have filled in the MailChimp API key yet! Please do it by clicking on the "Configure" button at the step 2 above or go to the "General settings > Mailchimp settings" tab.' mod='btmailchimpecommerce'}
			</div>
		{/if}
	</form>
	<div class="clr_20"></div>
</div>

{literal}
	<script type="text/javascript">
		function formatFormData(uri) {
			var user_type = $('#bt_customer_export_type option:selected').val();
			var user_language = $('#bt_user_language option:selected').val();

			uri += '&bt_customer_export_type=' + user_type + '&bt_user_language=' + user_language;
			$("a#bt_list_nl_sync").attr('href', uri);

			// trigger the click event
			$("a#bt_list_nl_sync").click();
		};

		//bootstrap components init
		$(document).ready(function() {
			$('.label-tooltip, .help-tooltip').tooltip();

			$("a#bt_list_nl_sync").fancybox({
				'hideOnContentClick': false,
				afterClose: function () {
					oMailchimp.clearTimeOut();
					return;
				}
			});

			// handle optin warning message
			$("#bt_customer_export_type").bind('change', function(event)
			{
				$("#bt_customer_export_type option:selected").each(function()
				{
					switch ($(this).val()) {
						case 'all' :
							$("#bt_warning_optin").show();
							break;
						case 'optin' :
							$("#bt_warning_optin").hide();
							break;
					}
				});
			}).change();
		});
	</script>
{/literal}
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
	var oListCallBack = [{
		'name' : 'updateSyncStatus',
		'url' : '{/literal}{$sURI nofilter}{literal}',
		'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=syncStatus',
		'toShow' : 'bt_settings_general_sync_status',
		'toHide' : 'bt_settings_general_sync_status',
		'bFancybox' : false,
		'bFancyboxActivity' : false,
		'sLoadbar' : null,
		'sScrollTo' : null,
		'oCallBack' : {}
	},
	{
		'name' : 'updateNLConfig',
		'url' : '{/literal}{$sURI nofilter}{literal}',
		'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=newsletterConfig',
		'toShow' : 'bt_settings_newsletter_config',
		'toHide' : 'bt_settings_newsletter_config',
		'bFancybox' : false,
		'bFancyboxActivity' : false,
		'sLoadbar' : null,
		'sScrollTo' : null,
		'oCallBack' : {}
	},
	{
		'name' : 'updateNLSignup',
		'url' : '{/literal}{$sURI nofilter}{literal}',
		'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=signupForm',
		'toShow' : 'bt_settings_newsletter_sign_up',
		'toHide' : 'bt_settings_newsletter_sign_up',
		'bFancybox' : false,
		'bFancyboxActivity' : false,
		'sLoadbar' : null,
		'sScrollTo' : null,
		'oCallBack' : {}
	},
	{
		'name' : 'updateEcommerce',
		'url' : '{/literal}{$sURI nofilter}{literal}',
		'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=ecommerce',
		'toShow' : 'bt_settings_ecommerce',
		'toHide' : 'bt_settings_ecommerce',
		'bFancybox' : false,
		'bFancyboxActivity' : false,
		'sLoadbar' : null,
		'sScrollTo' : null,
		'oCallBack' : {}
	}];
	{/literal}
</script>

<div class="bootstrap">
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_form_user_list" name="bt_form_user_list" onsubmit="javascript: oMailchimp.form('bt_form_user_list', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_general_list', 'bt_settings_general_list', false, false, oListCallBack, 'user_list', 'general');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.userList.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.userList.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sTpl" value="{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}user-list{/if}" />
		<input type="hidden" name="bt_list_form_display" id="bt_list_form_display" value="0" />

		<h3><i class="icon icon-list-alt"></i>&nbsp;{l s='Users list' mod='btmailchimpecommerce'}</h3>
		<div class="clr_10"></div>

		{if !empty($aErrors)}
			{include file="`$sErrorInclude`" aErrors=$aErrors}
			<div class="clr_10"></div>

			{if !empty($sBatchErrorUri)}
				<div class="alert alert-danger">
					{l s='MailChimp requires an URL reachable from their servers. This URL is used to process batches on MailChimp side, as batches created via the automatic synchronizations of products, customers, carts and orders data.' mod='btmailchimpecommerce'}
					<div class="clr_5"></div>
					{l s='Here is the URL to be made reachable from outside: ' mod='btmailchimpecommerce'}<strong>{$sBatchErrorUri|escape:'htmlall':'UTF-8'}</strong>
					<div class="clr_5"></div>
				</div>
			{/if}

			{if !empty($sListErrorUri)}
				<div class="alert alert-danger">
					{l s='MailChimp requires an URL reachable from their servers. This URL is used to process user unsubscribe events on MailChimp side and then report them on PrestaShop side' mod='btmailchimpecommerce'}
					<div class="clr_5"></div>
					{l s='Here is the URL to be made reachable from outside: ' mod='btmailchimpecommerce'}<strong>{$sListErrorUri|escape:'htmlall':'UTF-8'}</strong>
					<div class="clr_5"></div>
				</div>
			{/if}

		{elseif !empty($bUpdate)}
			{include file="`$sConfirmInclude`" sConfirmId="bt_list_creation_success"}
			<div class="clr_10"></div>
		{/if}

		{* Check if the MC API key is filled up *}
		{if !empty($sApiKey)}
			<div class="alert alert-info">
			{l s='To know how to choose an existing list or create one, please read' mod='btmailchimpecommerce'}&nbsp;<b><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/284" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='our FAQ about users list' mod='btmailchimpecommerce'}</a></b>
			</div>
			<div class="clr_10"></div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<span class="label-tooltip" data-toggle="tooltip" data-placement="left" title data-original-title="{l s='Select an existing MailChimp list or create a new one by clicking on the "Create list" button. This list will be used to synchronize your users (subscribers who do not have an account and, if you activate the e-commerce feature, the customers).'  mod='btmailchimpecommerce'}">
						<strong>{if !empty($aCurrentList)}{l s='Your current list:' mod='btmailchimpecommerce'}{else}{l s='Choose an existing list:' mod='btmailchimpecommerce'}{/if}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
					{if !empty($aListsAndStores)}
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<select name="bt_list_id" id="bt_list_id">
								<option value="0">-- {l s='Select a list' mod='btmailchimpecommerce'} --</option>
								{foreach from=$aListsAndStores name=list key=iPos item=aList}
									<option value="{$aList.id|escape:'htmlall':'UTF-8'}¤{$aList.name|urlencode}" {if !empty($aCurrentList) && $aCurrentList.id == $aList.id}selected="selected"{elseif !empty($aList.shop_id) && $aList.shop_id != $shop_id}disabled="disabled"{/if}>{$aList.name|escape:'htmlall':'UTF-8'}{if !empty($aList.shop_id) && $aList.shop_id != $shop_id} *** {l s='already associated to a shop' mod='btmailchimpecommerce'} ***{/if}</option>
								{/foreach}
							</select>
							
							<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Select an existing list or create one. If you enable the ecommerce feature, not only users created via the newsletter sign-up form but also those who have gone through the creation of a customer account, will be synchronized.' mod='btmailchimpecommerce'}</span>
							
							
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<strong>{l s='Or create a new one:' mod='btmailchimpecommerce'}</strong>&nbsp;&nbsp;<button class="btn btn-info btn-lg" onclick="javascript: displayCreationListForm('#bt_list_id', '#bt_create_list', '#bt_list_form_display', '#bt_list_block_info');return false;" id="bt_btn_create_list">{l s='Create list' mod='btmailchimpecommerce'}</button>
						</div>

						

						{if !empty($sExistingActiveList)}
							<div class="clr_10"></div>

							<div class="form-group">
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
									<div class="alert alert-warning">
										<strong>{l s='BE CAREFUL!' mod='btmailchimpecommerce'}</strong>&nbsp;{l s='You\'re already using a list. If you change your list, please note that, if you had activated the e-commerce feature for the previous list, this will be deactivated. If you want to use MailChimp automations for the new list, you will have to enable it again via the related tab and redo the first manual synchronizations so that the next synchronizations can run automatically.' mod='btmailchimpecommerce'}
										<div class="clr_5"></div>
										{l s='Your previous active list was:' mod='btmailchimpecommerce'} <strong>{$sExistingActiveList|escape:'htmlall':'UTF-8'}</strong>
									</div>
								</div>
							</div>
						{elseif !empty($aCurrentList)}
							<div class="clr_10"></div>

							<div class="form-group" id="bt_list_block_info">
								<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
									<div class="alert alert-info">
										<strong>{l s='Your current active list:' mod='btmailchimpecommerce'}</strong> {$aCurrentList.name|escape:'htmlall':'UTF-8'}
										<div class="clr_5"></div>
										<strong>{l s='IMPORTANT note:' mod='btmailchimpecommerce'}</strong>&nbsp;{l s='For any reasons, if you want to change your list, please note that, if you had activated the e-commerce feature for the previous list, this will be deactivated. If you want to use MailChimp automations for the new list, you will have to enable it again via the related tab and redo the first manual synchronizations so that the next synchronizations can run automatically.' mod='btmailchimpecommerce'}
									</div>
								</div>
							</div>
						{/if}
					{else}
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="alert alert-warning">
								{l s='You don\'t have any list created in your MailChimp account, you must therefore create one by clicking on the "Create list" button.' mod='btmailchimpecommerce'}
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							&nbsp;&nbsp;<button class="btn btn-info btn-lg" onclick="javascript: displayCreationListForm('#bt_list_id', '#bt_create_list', '#bt_list_form_display', '#bt_list_block_info');return false;" id="bt_btn_create_list">{l s='Create list' mod='btmailchimpecommerce'}</button>
						</div>
					{/if}
				</div>
			</div>

			<div id="bt_create_list" style="display: none;">

				<div class="clr_10"></div>
				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				<h4><i class="icon icon-pencil"></i>&nbsp;{l s='List creation form' mod='btmailchimpecommerce'}</h4>
				<div class="clr_10"></div>

				{* define automatically the reminder for the list, it's just made up as default text if you don't define it when you create a template for your newsletter *}
				<input type="hidden" name="bt_list_email_type" value="1" />

				{* define automatically the reminder for the list, it's just made up as default text if you don't define it when you create a template for your newsletter *}
				<input type="hidden" name="bt_list_reminder" value="{l s='You signed up for updates on our shop' mod='btmailchimpecommerce'} {$shop_name|escape:'htmlall':'UTF-8'}" />

				{* define automatically campaign defaults *}
				<input type="hidden" name="bt_campaign_name" value="{$shop_name|urlencode}" />
				<input type="hidden" name="bt_campaign_email" value="{$shop_email|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="bt_campaign_subject" value="{$shop_subject|urlencode}" />
				<input type="hidden" name="bt_campaign_language" value="{$current_language.name|escape:'htmlall':'UTF-8'}" />

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Your recipients will see this list name, so choose something appropriate' mod='btmailchimpecommerce'}">
							<strong>{l s='New list name:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_name" name="bt_list_name" size="35" value="" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Your recipients will see this list name, so choose something appropriate' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>
				
				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-4 col-sm-4 col-md-2 col-lg-2"></label>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-info">
							{l s='Anti-spam laws and' mod='btmailchimpecommerce'}&nbsp;<b><a href="https://mailchimp.com/legal/terms/" target="_blank">{l s='MailChimp\'s Terms Of Use' mod='btmailchimpecommerce'}</a></b> {l s='require you to include your company\'s physical address in your e-mails footer:' mod='btmailchimpecommerce'}
						</div>
					</div>
				</div>

				

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The company name for this list' mod='btmailchimpecommerce'}">
							<strong>{l s='Company name:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_name" name="bt_list_company_name" size="35" value="{if !empty($aRootInfo.contact.company)}{$aRootInfo.contact.company|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The company name for this list' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='First line of the company\'s street address' mod='btmailchimpecommerce'}">
							<strong>{l s='Company address (line 1):' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_address1" name="bt_list_company_address1" size="35" value="{if !empty($aRootInfo.contact.addr1)}{$aRootInfo.contact.addr1|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='First line of the company\'s street address' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Second line of the company\'s street address (optional)' mod='btmailchimpecommerce'}">
							<strong>{l s='Company address (line 2):' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_address2" name="bt_list_company_address2" size="35" value="{if !empty($aRootInfo.contact.addr2)}{$aRootInfo.contact.addr2|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Second line of the company\'s street address (optional)' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The city of the company' mod='btmailchimpecommerce'}">
							<strong>{l s='Company city:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_city" name="bt_list_company_city" size="35" value="{if !empty($aRootInfo.contact.city)}{$aRootInfo.contact.city|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The city of the company' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The postal or ZIP code of the company' mod='btmailchimpecommerce'}">
							<strong>{l s='Company ZIP or postal code:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_zip" name="bt_list_company_zip" size="35" value="{if !empty($aRootInfo.contact.zip)}{$aRootInfo.contact.zip|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The postal or ZIP code of the company' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The state of the company'  mod='btmailchimpecommerce'}">
							<strong>{l s='Company state:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_state" name="bt_list_company_state" size="35" value="{if !empty($aRootInfo.contact.state)}{$aRootInfo.contact.state|escape:'htmlall':'UTF-8'}{else}{l s='None' mod='btmailchimpecommerce'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The state of the company' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The country of the company'  mod='btmailchimpecommerce'}">
							<strong>{l s='Company country:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-pencil"></i></span>
								<input type="text" id="bt_list_company_country" name="bt_list_company_country" size="35" value="{if !empty($aRootInfo.contact.country)}{$aRootInfo.contact.country|escape:'htmlall':'UTF-8'}{/if}" />
							</div>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The country of the company' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>
			</div>

			<div class="clr_20"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want each new subscriber to receive an e-mail requesting confirmation of subscription before being actually registered as a "subscriber" in your list' mod='btmailchimpecommerce'}">
						<strong>{l s='Enable double opt-in?' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_nl_double_optin" id="bt_nl_double_optin_on" value="1" {if !empty($double_optin)}checked="checked"{/if} />
						<label for="bt_nl_double_optin_on" class="radioCheck">
							{l s='Yes' mod='btmailchimpecommerce'}
						</label>
						<input type="radio" name="bt_nl_double_optin" id="bt_nl_double_optin_off" value="0" {if empty($double_optin)}checked="checked"{/if} />
						<label for="bt_nl_double_optin_off" class="radioCheck">
							{l s='No' mod='btmailchimpecommerce'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want each new subscriber to receive an e-mail requesting confirmation of subscription before being actually registered as a "subscriber" in your list' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/286" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about double opt-in' mod='btmailchimpecommerce'}</a>
					<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='If you have selected an existing list above, the option value you define here will override the one that was registered in the list settings in your MailChimp account.' mod='btmailchimpecommerce'}</span>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want GDPR fields to appear in the MailChimp sign-up form. These fields and related texts are to be configured in your list form builder in your Mailchimp account' mod='btmailchimpecommerce'}">
						<strong>{l s='Enable GDPR fields?' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_nl_gdpr" id="bt_nl_gdpr_on" value="1" {if !empty($gdpr)}checked="checked"{/if} />
						<label for="bt_nl_gdpr_on" class="radioCheck">
							{l s='Yes' mod='btmailchimpecommerce'}
						</label>
						<input type="radio" name="bt_nl_gdpr" id="bt_nl_gdpr_off" value="0" {if empty($gdpr)}checked="checked"{/if} />
						<label for="bt_nl_gdpr_off" class="radioCheck">
							{l s='No' mod='btmailchimpecommerce'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want GDPR fields to appear in the MailChimp sign-up form. These fields and related texts are to be configured in your list form builder in your Mailchimp account' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
					&nbsp;&nbsp;<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/285" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about GDPR fields' mod='btmailchimpecommerce'}</a>
					<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Only available for MailChimp sign-up forms' mod='btmailchimpecommerce'}
					<br />
					{l s='If you have selected an existing list above, the option value you define here will override the one that was registered in the list settings in your MailChimp account.' mod='btmailchimpecommerce'}</span>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"></label>
				<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
				</div>
			</div>

			<div class="clr_20"></div>
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
					<div id="bt_error_user_list"></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"><button class="btn btn-default pull-right" id="bt_btn_user_list" onclick="oMailchimp.form('bt_form_user_list', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_general_list', 'bt_settings_general_list', false, false, oListCallBack, 'user_list', 'general');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button></div>
			</div>
		{else}
			<div class="alert alert-warning">
				{l s='You don\'t have filled in the MailChimp API key yet! Please do it by clicking on the "Configure" button at the step 2 above or go to the "General settings > Mailchimp settings" tab.' mod='btmailchimpecommerce'}
			</div>
		{/if}
	</form>
</div>

<div class="clr_20"></div>

{literal}
	<script type="text/javascript">
		var current_option_value = $('#bt_list_id option:selected').val();

		$('#bt_list_id').change(function() {
			current_option_value = $('#bt_list_id option:selected').val();
		});

		function displayCreationListForm(list_select_selector, btn_selector, hidden_selector, hidden_block) {
			var is_displayed  = $(hidden_selector).val();

			// use case - need to display the form
			if (is_displayed == '0') {
				$(list_select_selector + ' option:selected').prop('selected', false);
				$(list_select_selector + ' option:first').prop('selected', 'selected');
				$(hidden_block).hide();
				$(hidden_selector).val('1');
			// need to hide the form
			} else {
				$(list_select_selector + ' option:selected').prop('selected', false);
				$.each($(list_select_selector + " option"), function(){
					if ($(this).val() == current_option_value) {
						$(this).prop('selected', 'selected');
					}
				});
				$(hidden_selector).val('0');
				$(hidden_block).show();
			}
			$(btn_selector).slideToggle(400);
		}

		//bootstrap components init
		$(document).ready(function() {
			$('.label-tooltip, .help-tooltip').tooltip();
		});
	</script>
{/literal}
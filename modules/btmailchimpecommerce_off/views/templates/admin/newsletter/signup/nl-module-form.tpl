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
	var oSignupModuleCallBack = [];
</script>
{/literal}

<div class="bootstrap">
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_form_signup_module" name="bt_form_signup_module" onsubmit="javascript: oMailchimp.form('bt_form_signup_module', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_newsletter_sign_up', 'bt_settings_newsletter_sign_up', false, false, oSignupModuleCallBack, 'newsletter_signup_module', 'newsletter');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.signupModule.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.signupModule.type|escape:'htmlall':'UTF-8'}" />

		{* Check if the MC API key is filled up *}
		{if !empty($sApiKey)}
			{if !empty($aListSignup)}
				<div class="clr_10"></div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to use another newsletter block module. All users who subscribe through this block will be automatically synchronized with MailChimp. If you select NO, make sure the newsletter block modules are disabled in your back office' mod='btmailchimpecommerce'}">
							<strong>{l s='Do you want to use a newsletter block module?' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					
					<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_use_nl_module" id="bt_use_nl_module_on" value="1" {if !empty($bNewsletterModule)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_nl_module', null, null, true, true);" />
							<label for="bt_use_nl_module_on" class="radioCheck">
								{l s='Yes' mod='btmailchimpecommerce'}
							</label>
							<input type="radio" name="bt_use_nl_module" id="bt_use_nl_module_off" value="0" {if empty($bNewsletterModule)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_nl_module', null, null, true, false);" />
							<label for="bt_use_nl_module_off" class="radioCheck">
								{l s='No' mod='btmailchimpecommerce'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to use another newsletter block module. All users who subscribe through this block will be automatically synchronized with MailChimp. If you select NO, make sure the newsletter block modules are disabled in your back office' mod='btmailchimpecommerce'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>
						
						<div class="clr_5"></div>
						
						<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='If you select NO and you have a newsletter block module installed on your shop, don\'t forget to disable it.' mod='btmailchimpecommerce'}</span>
					</div>
				</div>

				<div class="clr_10"></div>

				<div id="bt_div_nl_module" style="display: {if !empty($bNewsletterModule)}block{else}none{/if};">
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate if you use the PrestaShop native newsletter block module or another. According to this, if needed, complete (by yourself or by asking your technical contact to do so) the additional information below' mod='btmailchimpecommerce'}">
								<strong>{l s='Select your newsletter block module' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<select name="bt_nl_module" id="bt_nl_module">
									<option value="native" {if $sSelectedModule == 'native'}selected="selected"{/if}>{l s='Native newsletter block by PrestaShop' mod='btmailchimpecommerce'}</option>
									<option value="other" {if $sSelectedModule == 'other'}selected="selected"{/if}>{l s='Other' mod='btmailchimpecommerce'}</option>
								</select>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate if you use the PrestaShop native newsletter block module or another. According to this, if needed, complete (by yourself or by asking your technical contact to do so) the additional information below' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
								<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/288" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about newsletter block modules' mod='btmailchimpecommerce'}</a>
							</div>
						</div>
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						{*<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>*}
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-warning">
								{l s='The following options allow you or your technical contact to configure your newsletter form, if needed. If you don’t have technical skills, leave the values as they are and call your technical contact.' mod='btmailchimpecommerce'}<br />
								{l s='For more information please' mod='btmailchimpecommerce'}
								<a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/288" target="_blank"><i class="icon icon-link"></i>&nbsp;<b>{l s='refer to our FAQ' mod='btmailchimpecommerce'}</b></a>.

							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the value of the "name" attribute of the HTML tag of "submit" type. To to so, check how the form is formatted into the related code file' mod='btmailchimpecommerce'}">
								<strong>{l s='Newsletter form submit' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-file-code-o"></i></span>
									<input type="text" id="bt_nl_form_submit" name="bt_nl_form_submit" size="35" value="{if !empty($sModuleSubmit)}{$sModuleSubmit}{/if}" />
								</div>
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the value of the "name" attribute of the HTML tag of "submit" type. To to so, check how the form is formatted into the related code file' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
							<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='This is the "name" attribute value of the HTML tag - e.g for native module: input type="submit" name="submitNewsletter"' mod='btmailchimpecommerce'}</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the value of the "name" attribute of the HTML tag of "text" type. To to so, check how the form is formatted into the related code file' mod='btmailchimpecommerce'}">
								<strong>{l s='Newsletter form e-mail field' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-file-code-o"></i></span>
									<input type="text" id="bt_nl_form_email" name="bt_nl_form_email" size="35" value="{if !empty($sModuleFieldEmail)}{$sModuleFieldEmail}{/if}" />
								</div>
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the value of the "name" attribute of the HTML tag of "text" type. To to so, check how the form is formatted into the related code file' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
							<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='This is the "name" attribute value of the HTML tag - e.g for native module: input type="text" name="email"' mod='btmailchimpecommerce'}</span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If your newsletter block module uses an AJAX request to submit form information, select YES here so that the module can take this into account and retrieve the data correctly.' mod='btmailchimpecommerce'}">
								<strong>{l s='Is the form submitted via an AJAX request?' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_nl_form_ajax" id="bt_nl_form_ajax_on" value="1" {if !empty($bNewsletterAjax)}checked="checked"{/if} />
								<label for="bt_nl_form_ajax_on" class="radioCheck">
									{l s='Yes' mod='btmailchimpecommerce'}
								</label>
								<input type="radio" name="bt_nl_form_ajax" id="bt_nl_form_ajax_off" value="0" {if empty($bNewsletterAjax)}checked="checked"{/if} />
								<label for="bt_nl_form_ajax_off" class="radioCheck">
									{l s='No' mod='btmailchimpecommerce'}
								</label>
								<a class="slide-button btn"></a>
							</span>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If your newsletter block module uses an AJAX request to submit form information, select YES here so that the module can take this into account and retrieve the data correctly.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate the HTML element that identify the newsletter block in your source code. Please respect the JS Jquery syntax. As a reminder, class name must be prefixed with a "." and an id with a "#"' mod='btmailchimpecommerce'}">
								<strong>{l s='HTML element identifying the newsletter block' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-file-code-o"></i></span>
									<input type="text" id="bt_nl_form_selector" name="bt_nl_form_selector" size="35" value="{if !empty($sModuleHtmlSelector)}{$sModuleHtmlSelector}{/if}" />
								</div>
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate the HTML element that identify the newsletter block in your source code. Please respect the JS Jquery syntax. As a reminder, class name must be prefixed with a "." and an id with a "#"' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
							<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='This is one of the "class" attribute values of the HTML tag - e.g for native module: div class="block_newsletter"' mod='btmailchimpecommerce'}</span>
						</div>
					</div>
				</div>

				<div class="clr_20"></div>
				<div class="clr_hr"></div>
				<div class="clr_20"></div>

				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
						<div id="bt_error_newsletter_signup_module"></div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
						<button class="btn btn-success pull-right" id="bt_btn_nl_module_use" onclick="oMailchimp.form('bt_form_signup_module', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_newsletter_sign_up', 'bt_settings_newsletter_sign_up', false, false, oSignupModuleCallBack, 'newsletter_signup_module', 'newsletter');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button>
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
		//bootstrap components init
		$(document).ready(function() {
			$('.label-tooltip, .help-tooltip').tooltip();
		});
	</script>
{/literal}
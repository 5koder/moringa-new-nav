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

<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
	{* HEADER *}
	{include file="`$sHeaderInclude`"  bContentToDisplay=true}
	{* /HEADER *}

	{* TOP *}
	{include file="`$sTopInclude`"}
	{* /TOP *}

	{literal}
	<script>
		var id_language = Number({/literal}{$iCurrentLang|intval}{literal});

		{/literal}
		{* USE CASE - use the new language flags system from PS 1.6 *}
		{if empty($bCompare16)}
		{literal}
		function hideOtherLanguage(id) {
			$('.translatable-field').hide();
			$('.lang-' + id).show();

			var id_old_language = id_language;
			id_language = id;
		}
		{/literal}
		{/if}
		{literal}
	</script>
	{/literal}

	<div class="clr_20"></div>

	<div class="row">
		<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
			<div class="list-group bt_body_tabs">
				<a class="list-group-item active" id="tab-01" data-toggle="collapse" href="#submenu-mailchimp"><span class="icon-heart">&nbsp;</span>{l s='General settings' mod='btmailchimpecommerce'}<span class="pull-right"><i class="icon-caret-down"></i></span></a>
				<div id="submenu-mailchimp" class="panel-collapse collapse in">
					<a class="list-group-item active" id="tab-01"><i class="submenu fa fa-key"></i>&nbsp;{l s='MailChimp settings' mod='btmailchimpecommerce'}</a>
					<a class="list-group-item" id="tab-02"><i class="submenu fa fa-ban"></i>&nbsp;{l s='Users exclusion' mod='btmailchimpecommerce'}</a>
					<a class="list-group-item" id="tab-03"><i class="submenu fa fa-user"></i>&nbsp;{l s='Users list choice' mod='btmailchimpecommerce'}</a>
					{*<a class="list-group-item" id="tab-04"><i class="submenu fa fa-adjust"></i>&nbsp;{l s='Custom fields' mod='btmailchimpecommerce'}</a>*}
					<a class="list-group-item" id="tab-05"><i class="submenu fa fa-battery-full"></i>&nbsp;{l s='Sync status' mod='btmailchimpecommerce'}</a>
				</div>

				<a class="list-group-item" id="tab-10" data-toggle="collapse" href="#submenu-newsletter"><span class="icon-sign-in">&nbsp;</span>{l s='Users list sync & newsletter forms' mod='btmailchimpecommerce'}<span class="pull-right"><i class="icon-caret-down"></i></span></a>
				<div id="submenu-newsletter" class="panel-collapse collapse">
					<a class="list-group-item" id="tab-10"><i class="submenu icon icon-wrench"></i>&nbsp;{l s='Users list synching' mod='btmailchimpecommerce'}</a>
					<a class="list-group-item" id="tab-11"><i class="submenu fa fa-sign-in"></i>&nbsp;{l s='Newsletter sign-up forms' mod='btmailchimpecommerce'}</a>
				</div>

				<a class="list-group-item" id="tab-2"><span class="icon-AdminParentOrders"></span>&nbsp;&nbsp;{l s='E-commerce' mod='btmailchimpecommerce'}</a>
				<a class="list-group-item" id="tab-3"><span class="icon-AdminPriceRule"></span>&nbsp;&nbsp;{l s='Discount vouchers' mod='btmailchimpecommerce'}</a>
			</div>

			{* others *}
			<div class="list-group">
				<a class="list-group-item" href="#"><span class="icon icon-info"></span>&nbsp;&nbsp;{l s='Version' mod='btmailchimpecommerce'} : {$sModuleVersion|escape:'htmlall':'UTF-8'}</a>
				<a class="list-group-item" target="_blank" href="{$sRateUrl|escape:'htmlall':'UTF-8'}"><i class="icon-star" style="color: #fbbb22;"></i>&nbsp;&nbsp;{l s='Rate this module' mod='btmailchimpecommerce'}</a>
				<a class="list-group-item" target="_blank" href="{$sContactUs|escape:'htmlall':'UTF-8'}"><span class="icon-user"></span>&nbsp;&nbsp;{l s='Contact support' mod='btmailchimpecommerce'}</a>
			</div>
		</div>

		<div class="tab-content col-xs-12 col-sm-10 col-md-10 col-lg-10"">
			{* USE CASE - check if we are in multishop / group configuration *}
			{if !empty($bMultiShop)}
				<div class="tab-pane in active panel">
					<div class="alert alert-danger">
						{l s='First of all, you cannot configure your module in the "all shops" or "group mode", you should select one of your shop before moving on into the configuration' mod='btmailchimpecommerce'}
					</div>
				</div>
			{* USE CASE - the cUrl is not already done *}
			{elseif $iCurlTest != 1}
				<div class="tab-pane in active panel">
				{if $iCurlTest == 0}
					<div class="alert alert-warning">
						{l s='First of all, you must do the cURL test in the module\'s top header position 1. If the test fails, ask your technical contact to install the PHP cURL library over SSL.' mod='btmailchimpecommerce'}
					</div>
				{else}
					<div class="alert alert-danger">
					{l s='Your test of cURL over SSL failed. Maybe the cURL PHP extension is not installed and enabled over SSL. Please contact your web host since the module needs cURL over SSL.' mod='btmailchimpecommerce'}
					</div>
				{/if}
				</div>
			{* USE CASE - everything is good *}
			{else}
				{* MAILCHIMP SETTINGS *}
				{* api + others settings *}
				<div id="content-tab-01" class="tab-pane in active panel">
					<div id="bt_settings_general_mailchimp">
						{include file="`$sMailchimpInclude`" sTpl="mailchimp"}
					</div>
				</div>
				{* /MAILCHIMP SETTINGS *}

				{* User exclusion settings *}
				<div id="content-tab-02" class="tab-pane panel">
					<div id="bt_settings_general_exclusion">
						{include file="`$sExclusionInclude`" sTpl="exclusion"}
					</div>
				</div>

				{* User list settings *}
				<div id="content-tab-03" class="tab-pane panel">
					<div id="bt_settings_general_list">
						{include file="`$sUserListInclude`" sTpl="list"}
					</div>
				</div>

				{* Custom fields settings *}
				<div id="content-tab-04" class="tab-pane panel">
					<div id="bt_settings_general_custom_fields">
						{*{include file="`$sBasicsInclude`" sTpl="custom-fields"}*}
					</div>
				</div>

				{* Sync status settings *}
				<div id="content-tab-05" class="tab-pane sub-tab-panel">
					<div id="bt_settings_general_sync_status">
						{include file="`$sSyncStatusInclude`"  sTpl="history"}
					</div>
				</div>

				{* loader for general settings *}
				<div id="bt_loading_div_general" style="display: none;">
					<div class="alert alert-info">
						<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
						<p style="text-align: center !important;">{l s='Your configuration update is in progress...' mod='btmailchimpecommerce'}</p>
					</div>
				</div>
				{* /MAILCHIMP SETTINGS *}

				{* NEWSLETTER SUBSCRIPTION FORM SETTINGS *}
				{* NL member synching settings *}
				<div id="content-tab-10" class="tab-pane panel">
					<div id="bt_settings_newsletter_config">
						{include file="`$sNewsletterConfigInclude`" sTpl="config_sync"}
					</div>
				</div>

				<div id="content-tab-11" class="tab-pane sub-tab-panel">
					<div id="bt_settings_newsletter_sign_up">
						{include file="`$sNewsletterSignupInclude`" sTpl="module"}
					</div>
				</div>

				<div id="bt_loading_div_newsletter" style="display: none;">
					<div class="alert alert-info">
						<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
						<p style="text-align: center !important;">{l s='Your configuration update is in progress...' mod='btmailchimpecommerce'}</p>
					</div>
				</div>
				{* /NEWSLETTER SUBSCRIPTION FORM SETTINGS *}

				{* ECOMMERCE SETTINGS *}
				<div id="content-tab-2" class="tab-pane sub-tab-panel">
					<div id="bt_settings_ecommerce">
						{include file="`$sEcommerceInclude`"}
					</div>
					<div class="clr_20"></div>
					<div id="bt_loading_div_ecommerce" style="display: none;">
						<div class="alert alert-info">
							<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
							<p style="text-align: center !important;">{l s='Your e-commerce configuration is in progress...' mod='btmailchimpecommerce'}</p>
						</div>
					</div>
				</div>
				{* /ECOMMERCE SETTINGS *}

				{* INCENTIVE VOUCHERS SETTINGS *}
				<div id="content-tab-3" class="tab-pane panel">
					<div id="bt_settings_voucher">
						{include file="`$sVoucherInclude`" sTpl="voucher"}
					</div>
					<div class="clr_20"></div>
					<div id="bt_loading_div_voucher" style="display: none;">
						<div class="alert alert-info">
							<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
							<p style="text-align: center !important;">{l s='Your configuration update is in progress...' mod='btmailchimpecommerce'}</p>
						</div>
					</div>
				</div>
				{* /INCENTIVE VOUCHERS SETTINGS *}
			{/if}
		</div>
	</div>

	{literal}
	<script type="text/javascript">
		$(document).ready(function() {
			$(".bt_body_tabs a").click(function(e) {
				e.preventDefault();
				// currentId is the current bt_body_tabs id
				var currentId = $(".bt_body_tabs a.active").attr('id').substr(4);
				// id is the wanted bt_body_tabs id
				var id = $(this).attr('id').substr(4);

				if ($(this).attr("id") != $(".bt_body_tabs a.active").attr('id')) {
					$(".bt_body_tabs a[id='tab-"+currentId+"']").removeClass('active');
					$("#content-tab-"+currentId).hide();
					$(".bt_body_tabs a[id='tab-"+id+"']").addClass('active');
					$("#content-tab-"+id).show();
				}
			});

			// detect the hash
			var sHash = $(location).attr('hash');

			if (sHash != null && sHash != '') {
				sHash = sHash.replace(/#/g, '');

				if (sHash.search(/|/g) != -1) {
					var aDoubleHash = sHash.split('|');
					for(var i = 0; i <= aDoubleHash.length; ++i) {
						$('.bt_body_tabs a[id="' + aDoubleHash[i] + '"]').click();
					}
				} else {
					$('.bt_body_tabs a[id="' + sHash + '"]').click();
				}
			}

			$('.label-tooltip, .help-tooltip').tooltip();
			$('.dropdown-toggle').dropdown();
		});
	</script>
	{/literal}
</div>
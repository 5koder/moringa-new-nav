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
	var oEcommerceCallBack = [{
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
	},];
	{/literal}
</script>

<form class="bootstrap form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI nofilter}" method="post" id="bt_form_ecommerce_config" name="bt_form_ecommerce_config" onsubmit="oMailchimp.form('bt_form_ecommerce_config', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_ecommerce', 'bt_settings_ecommerce', false, false, oEcommerceCallBack, 'ecommerce_config', 'ecommerce');return false;">
	<input type="hidden" name="sAction" value="{$aQueryParams.ecommerce.action|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="sType" value="{$aQueryParams.ecommerce.type|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="bCheckOrderStatus" value="1" />

	{* Check if the MC API key is filled up *}
	{if !empty($sApiKey)}
		{if !empty($bActiveNewsletter) && !empty($aListEcommerce)}
			<div class="col-xs-12 col-sm-12 col-md-12 col-md-12 col-lg-12">
				<div class="alert alert-info">
					{l s='If you want to use MailChimp e-commerce automations,' mod='btmailchimpecommerce'}&nbsp;<b>{l s='activate the option below' mod='btmailchimpecommerce'}</b>&nbsp;{l s='and configure the data to be exported.' mod='btmailchimpecommerce'}
					<div class="clr_5"></div>
					{l s='After saving,' mod='btmailchimpecommerce'}&nbsp;<b>{l s='go to the "Synching" tab' mod='btmailchimpecommerce'}</b>&nbsp;{l s='to do the synchronizations a first time manually in order to activate the automatic synchronization process.' mod='btmailchimpecommerce'}
				</div>
			</div>	
				
			<div class="clr_20"></div>
				
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES to activate the e-commerce feature and enter the configuration of the data to be exported'  mod='btmailchimpecommerce'}">
						<strong>{l s='Enable the e-commerce feature?' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_mc_ecommerce_use" id="bt_mc_ecommerce_use_on" value="1" {if !empty($bActiveEcommerce)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_mc_ecommerce_use', null, null, true, true);" />
						<label for="bt_mc_ecommerce_use_on" class="radioCheck">
							{l s='Yes' mod='btmailchimpecommerce'}
						</label>
						<input type="radio" name="bt_mc_ecommerce_use" id="bt_mc_ecommerce_use_off" value="0" {if empty($bActiveEcommerce)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_mc_ecommerce_use', null, null, true, false);" />
						<label for="bt_mc_ecommerce_use_off" class="radioCheck">
							{l s='No' mod='btmailchimpecommerce'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES to activate the e-commerce feature and enter the configuration of the data to be exported' mod='btmailchimpecommerce'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
					<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/289" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about e-commerce feature' mod='btmailchimpecommerce'}</a>
				</div>
			</div>
			

			<div id="bt_div_mc_ecommerce_use" style="display: {if !empty($bActiveEcommerce)}block{else}none{/if};">
				
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to able to use the product information (title, description, link) in the right language in Mailchimp automations, select here ALL the languages you will need (hold the CTRL key down).' mod='btmailchimpecommerce'}">
							<strong>{l s='Language(s) for the product export:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							{*<select name="bt_prod_languages_currencies[]" id="bt_prod_languages_currencies" multiple="multiple">*}
							<select name="bt_prod_languages[]" id="bt_prod_languages" multiple="multiple">
								{foreach from=$aLanguages name=lang key=iPos item=aLanguage}
									{*{foreach from=$aCurrencies name=currency key=iPos item=aCurrency}*}
										{*{assign var=separator value='¤'}*}
										{*{assign var=mergeIds value=$aLanguage.id_lang|cat:$separator|cat:$aCurrency.id_currency}*}
										{*<option value="{$aLanguage.id_lang|intval}¤{$aCurrency.id_currency|intval}" {if !empty($aLanguagesCurrencies)}{foreach from=$aLanguagesCurrencies name=selected key=iPos item=id_lang_currency}{if $id_lang_currency == $mergeIds}selected="selected"{/if}{/foreach}{/if}>{$aLanguage.name|escape:'htmlall':'UTF-8'} {l s='with' mod='btmailchimpecommerce'} {$aCurrency.name|escape:'htmlall':'UTF-8'}</option>*}
										<option value="{$aLanguage.id_lang|intval}" {if !empty($aSelectedLanguages)}{foreach from=$aSelectedLanguages name=selected key=iPos item=id_lang}{if $id_lang == $aLanguage.id_lang}selected="selected"{/if}{/foreach}{/if}>{$aLanguage.name|escape:'htmlall':'UTF-8'}</option>
									{*{/foreach}*}
								{/foreach}
							</select>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to able to use the product information (title, description, link) in the right language in Mailchimp automations, select here ALL the languages you will need (hold the CTRL key down).' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
						<div class="clr_5"></div>
						<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='If you don\'t select any language, the module will export the product information in your shop default language only.' mod='btmailchimpecommerce'}</span>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group" id="bt_div-catalog-prod-desc">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the best description type to associate to your products in MailChimp' mod='btmailchimpecommerce'}">
							<strong>{l s='Description to use for your products:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<select name="bt_prod_desc" id="bt_prod_desc">
								<option value="0" {if isset($iDescType) && $iDescType == 0}selected="selected"{/if}>{l s='no description' mod='btmailchimpecommerce'}</option>
								{foreach from=$aDescriptionType name=description key=iType item=sType}
									<option value="{$iType|intval}" {if !empty($iDescType) && $iDescType == $iType}selected="selected"{/if}>{$sType|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the best description type to associate to your products in MailChimp' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
						<div class="clr_10"></div>
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<div class="alert alert-warning">
								{l s='If the descriptions are too long, you may encounter some synchronization problem when updating products in your back office. So, if you don\'t really need the product descriptions, don\'t export them or choose a description type that\'s not too long.' mod='btmailchimpecommerce'}
							</div>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES to export product with tax'  mod='btmailchimpecommerce'}">
							<strong>{l s='Export product price with tax ?' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_mc_product_tax" id="bt_mc_product_tax_on" value="1" {if !empty($bProductTax)}checked="checked"{/if}/>
							<label for="bt_mc_product_tax_on" class="radioCheck">
								{l s='Yes' mod='btmailchimpecommerce'}
							</label>
							<input type="radio" name="bt_mc_product_tax" id="bt_mc_product_tax_off" value="0" {if empty($bProductTax)}checked="checked"{/if} />
							<label for="bt_mc_product_tax_off" class="radioCheck">
								{l s='No' mod='btmailchimpecommerce'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES to export product with tax' mod='btmailchimpecommerce'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group" id="bt_div-catalog-prod-desc">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose the best image size to present your products into your e-mails' mod='btmailchimpecommerce'}">
							<strong>{l s='Image size to use for your products:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<select name="bt_prod_img_size" id="bt_prod_img_size">
								{foreach from=$aImageTypes name=image key=iType item=aType}
									<option value="{$aType.name|escape:'htmlall':'UTF-8'}" {if !empty($sProdImgFormat)}{if $sProdImgFormat == $aType.name}selected="selected"{/if}{elseif $aType.name == 'large_default'}selected="selected"{/if}>{$aType.name|escape:'htmlall':'UTF-8'} ({$aType.width|escape:'htmlall':'UTF-8'}X{$aType.height|escape:'htmlall':'UTF-8'})</option>
								{/foreach}
							</select>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose the best image size to present your products into your e-mails' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group" id="bt_div-catalog-prod-desc">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the value you want to assign to the "vendor" of the products' mod='btmailchimpecommerce'}">
							<strong>{l s='What to use for the vendor:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<select name="bt_prod_vendor_type" id="bt_prod_vendor_type">
								<option value="brand" {if !empty($sVendorType) && $sVendorType == 'brand'}selected="selected"{/if}>{l s='Use the brand name' mod='btmailchimpecommerce'}</option>
								<option value="supplier" {if !empty($sVendorType) && $sVendorType == 'supplier'}selected="selected"{/if}>{l s='Use the supplier name' mod='btmailchimpecommerce'}</option>
								<option value="category" {if !empty($sVendorType) && $sVendorType == 'category'}selected="selected"{/if}>{l s='Use the category name' mod='btmailchimpecommerce'}</option>
							</select>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the value you want to assign to the "vendor" of the products' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This will define your product types. You can either choose the standard category name or the full breadcrumb for more precision.' mod='btmailchimpecommerce'}">
							<strong>{l s='Category wording:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_cat_label_format" id="bt_cat_label_format" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							{foreach from=$aCatLabelFormat name=mode key=sFormat item=sTitle}
								<option value="{$sFormat|escape:'htmlall':'UTF-8'}" {if $sCatLabelFormat == $sFormat}selected="selected"{/if}>{$sTitle|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This will define your product types. You can either choose the standard category name or the full breadcrumb for more precision.' mod='btmailchimpecommerce'}">&nbsp;&nbsp;&nbsp;<i class="icon-question-sign"></i></span>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3" for="bt_order-statuses">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Only past orders with one of the selected statuses will be exported. Select at least one status. You should select only order statuses that represent valid orders.' mod='btmailchimpecommerce'}">
							<strong>{l s='Export past orders with statuses:' mod='btmailchimpecommerce'}</strong>
						</span> 
					</label>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="btn-actions">
							<div class="btn btn-default btn-mini" id="categoryCheck" onclick="return oMailchimp.selectAll('.myCheckbox', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='btmailchimpecommerce'}</div> - <div class="btn btn-default btn-mini" id="categoryUnCheck" onclick="return oMailchimp.selectAll('.myCheckbox', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='btmailchimpecommerce'}</div>
							<div class="clr_10"></div>
						</div>
						<table cellspacing="0" cellpadding="0" class="table table-responsive table-bordered table-striped">
							{foreach from=$aOrderStatuses key=id item=aOrder}
								<tr>
									<td>
										<label style="float: right !important;" for="bt_order_status">{$aOrder[$iCurrentLang]|escape:'htmlall':'UTF-8'}</label>
									</td>
									<td>
										<input type="checkbox" name="bt_order_status[]" id="bt_order_status" value="{$id|escape:'htmlall':'UTF-8'}"{if !empty($aStatusSelection)}{foreach from=$aStatusSelection key=key item=iIdSelect}{if $iIdSelect == $id} checked="checked"{/if}{/foreach}{/if} class="myCheckbox" />
									</td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>

				<div class="clr_10"></div>
				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				<h4><i class="icon icon-tasks"></i>&nbsp;{l s='Server performances' mod='btmailchimpecommerce'}</h4>

				{*<div class="clr_10"></div>*}

				{*<div class="form-group">*}
					{*<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">*}
						{*<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The MailChimp "abandoned cart" automation allows you to set only 3 products in your e-mail template, and more, the API recommends to not exceeding 9 products. In most cases, 3 products will fit well to your server performance recommendations.' mod='btmailchimpecommerce'}">*}
							{*<strong>{l s='Define the number of products per cart to send' mod='btmailchimpecommerce'}</strong>*}
						{*</span> :*}
					{*</label>*}
					{*<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">*}
						{*<select name="bt_cart_max_prod" id="bt_cart_max_prod" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">*}
							{*{foreach from=$aCartMaxProd name=mode key=iKey item=iVal}*}
								{*<option value="{$iVal|escape:'htmlall':'UTF-8'}" {if $iCartMaxProd == $iVal}selected="selected"{/if}>{$iVal|escape:'htmlall':'UTF-8'}</option>*}
							{*{/foreach}*}
						{*</select>*}
						{*<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The MailChimp "abandoned cart" automation allows you to set only 3 products in your e-mail template, and more, the API recommends to not exceeding 9 products. In most cases, 3 products will fit well to your server performance recommendations.' mod='btmailchimpecommerce'}">&nbsp;<i class="icon-question-sign"></i></span>*}
					{*</div>*}
				{*</div>*}

				<div class="clr_10"></div>

				<h5><i class="icon icon-refresh"></i>&nbsp;{l s='Synching mode' mod='btmailchimpecommerce'}</h5>

				<div class="form-group">
					{*<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>*}
					<div class="col-xs-12 col-sm-12 col-md-12 col-md-12 col-lg-12">
						<div class="alert alert-info">
							{l s='Here is a quick definition of the different synching modes. For more details please read our' mod='btmailchimpecommerce'} <strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/283" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about synching modes' mod='btmailchimpecommerce'}</a></strong>
							<div class="clr_10"></div>
							<strong>{l s='Regular (real time)' mod='btmailchimpecommerce'}</strong>: {l s='synchronizes data in real time with MailChimp. Ideal for a small number of data to be updated at the same time.' mod='btmailchimpecommerce'}
							<div class="clr_5"></div>
							<strong>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</strong>: {l s='sends the data at once, in a "batch", instead of sending them one by one. However, the number of items to be sent at the same time must be less than 500.' mod='btmailchimpecommerce'}
							<div class="clr_5"></div>
							<strong>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</strong>: {l s='sends to MailChimp, at a specific time, several batches of up to 500 items each, one after the other, until there are no more. Useful for very large number of items.' mod='btmailchimpecommerce'}
						</div>
					</div>
				</div>
				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='How to synchronize your products?' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_product_sync_mode" id="bt_product_sync_mode" class="col-xs-8 col-sm-8 col-md-8 col-lg-8 bt_sync_mode">
							<option value="regular" {if $sProductExportMode == 'regular'}selected="selected"{/if}>{l s='Regular (real time)' mod='btmailchimpecommerce'}</option>
							<option value="batch" {if $sProductExportMode == 'batch'}selected="selected"{/if}>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</option>
							<option value="cron" {if $sProductExportMode == 'cron'}selected="selected"{/if}>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</option>
						</select>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='How to synchronize your customers?' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_customer_sync_mode" id="bt_customer_sync_mode" class="col-xs-8 col-sm-8 col-md-8 col-lg-8 bt_sync_mode">
							<option value="regular" {if $sCustomerExportMode == 'regular'}selected="selected"{/if}>{l s='Regular (real time)' mod='btmailchimpecommerce'}</option>
							<option value="batch" {if $sCustomerExportMode == 'batch'}selected="selected"{/if}>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</option>
							<option value="cron" {if $sCustomerExportMode == 'cron'}selected="selected"{/if}>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</option>
						</select>
					</div>
				</div>
				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='How to synchronize carts?' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_cart_sync_mode" id="bt_cart_sync_mode" class="col-xs-8 col-sm-8 col-md-8 col-lg-8 bt_sync_mode">
							<option value="regular" {if $sCartExportMode == 'regular'}selected="selected"{/if}>{l s='Regular (real time)' mod='btmailchimpecommerce'}</option>
							<option value="batch" {if $sCartExportMode == 'batch'}selected="selected"{/if}>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</option>
							<option value="cron" {if $sCartExportMode == 'cron'}selected="selected"{/if}>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</option>
						</select>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='How to synchronize orders?' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_order_sync_mode" id="bt_order_sync_mode" class="col-xs-8 col-sm-8 col-md-8 col-lg-8 bt_sync_mode">
							<option value="regular" {if $sOrderExportMode == 'regular'}selected="selected"{/if}>{l s='Regular (real time)' mod='btmailchimpecommerce'}</option>
							<option value="batch" {if $sOrderExportMode == 'batch'}selected="selected"{/if}>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</option>
							<option value="cron" {if $sOrderExportMode == 'cron'}selected="selected"{/if}>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</option>
						</select>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='How to synchronize your subscribers?' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<select name="bt_member_sync_mode" id="bt_member_sync_mode" class="col-xs-8 col-sm-8 col-md-8 col-lg-8 bt_sync_mode">
							<option value="regular" {if $sMemberExportMode == 'regular'}selected="selected"{/if}>{l s='Regular (real time)' mod='btmailchimpecommerce'}</option>
							<option value="batch" {if $sMemberExportMode == 'batch'}selected="selected"{/if}>{l s='Data batch (single bulk action, limited to 500 items)' mod='btmailchimpecommerce'}</option>
							<option value="cron" {if $sMemberExportMode == 'cron'}selected="selected"{/if}>{l s='CRON task (looped bulk action, for very large number of items)' mod='btmailchimpecommerce'}</option>
						</select>
					</div>
				</div>

				{*<div class="clr_10"></div>*}

				{*<div class="form-group">*}
					{*<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">*}
						{*You may have encountered issues when you upate your products / customers / orders (statuses) via your back-office or when you import them from a third-system, and because the MC API can take a long time to response. At last, the best way is to use a cron to update your products/customers/orders in bulk, it allows you to send packages of items (based on the number of items per cycle) and like this you will be able to update your data without any problem. DO NOT FORGET, once it is activated, the options above to choose how to sync content will be replaced by the cron mode.*}
							{*<strong>Data update issues? use a cron to synchronize your data</strong>*}
					{*</label>*}
					{*<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">*}
						{*<span class="switch prestashop-switch fixed-width-lg">*}
							{*<input type="radio" name="bt_use_cron" id="bt_use_cron_on" value="1" {if !empty($bUseProdCron)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_use_prod_cron', null, null, true, true);" />*}
							{*<label for="bt_use_cron_on" class="radioCheck">*}
								{*{l s='Yes' mod='btmailchimpecommerce'}*}
							{*</label>*}
							{*<input type="radio" name="bt_use_cron" id="bt_use_cron_off" value="0" {if empty($bUseProdCron)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_use_prod_cron', null, null, true, false);" />*}
							{*<label for="bt_use_cron_off" class="radioCheck">*}
								{*{l s='No' mod='btmailchimpecommerce'}*}
							{*</label>*}
							{*<a class="slide-button btn"></a>*}
						{*</span>*}
						{*You may have encountered issues when you upate your products / customers / orders (statuses) via your back-office or when you import them from a third-system, and because the MC API can take a long time to response. At last, the best way is to use a cron to update your products/customers/orders in bulk, it allows you to send packages of items (based on the number of items per cycle) and like this you will be able to update your data without any problem. DO NOT FORGET, once it is activated, the options above to choose how to sync content will be replaced by the cron mode.*}
					{*</div>*}
				{*</div>*}

				{*<div class="form-group">*}
					{*<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>*}
					{*<div class="col-xs-12 col-sm-12 col-md-12 col-md-6 col-lg-6">*}
						{*<div class="alert alert-warning">*}
							{*<strong>Issue around bulk action on products / customers / orders?</strong>In those cases, you should activate the cron feature in order to synchronize your shop\'s data without slowing your server request or simply crash them due to your server limitations.*}
							{*<div class="clr_10"></div>*}
							{*<strong>The batch mode above create too many batches on the MC side?</strong>In this case, you should also activate the cron feature in order to synchronize your shop\'s data as the cron will create baches with the number of item per cycle you could define below.*}
							{*<div class="clr_10"></div>*}
							{*In the most cases, the frequency should be once a day, but if for your needs you want to execute it more once a day, then it would be good to define the number of items per cycle, like this it will insure you to synchronize your data well. IMPORTANT NOTE: the number of items per cycle is really helpful when you are updating a lot of products/customers/orders at the same time.*}
						{*</div>*}
					{*</div>*}
				{*</div>*}

				<div id="bt_div_use_prod_cron" style="display: {if !empty($bUseCron)}block{else}none{/if};">

					<div class="clr_10"></div>
					<h5><i class="icon icon-wrench"></i>&nbsp;{l s='Options for the CRON mode' mod='btmailchimpecommerce'}</h5>
					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This URL will allow you to synchronize the data in bulk action to MailChimp' mod='btmailchimpecommerce'}">
								<strong>{l s='Your CRON URL:' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon-link"></i></span>
									<input type="text" id="bt_cron_url" name="bt_cron_url"  value="{$sCronUrl nofilter}" disabled="disabled" />
								</div>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This URL will allow you to synchronize the data in bulk action to MailChimp' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<button id="bt_copy_module_link_cron" type="button" class="btn btn-info btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{$sCronUrl nofilter}" title="{$sCronUrl nofilter}">&nbsp;<i class="fa fa-copy"></i>&nbsp;{l s='Copy to clipboard' mod='btmailchimpecommerce'} </button>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This option allows you to define the number of items that the CRON task have to process per execution cycle, i.e the number of items per batch. This could be useful if you have to update a lot of data in bulk, to avoid overloading the API. You MUST not exceed 500' mod='btmailchimpecommerce'}">
								<strong>{l s='Number of items per batch:' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon-repeat"></i></span>
									<input type="text" id="bt_items_cron_cycle" name="bt_items_cron_cycle"  value="{if !empty($iItemsCronCycle)}{$iItemsCronCycle|intval}{/if}" />
								</div>
							</div>
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This option allows you to define the number of items that the CRON task have to process per execution cycle, i.e the number of items per batch. This could be useful if you have to update a lot of data in bulk, to avoid overloading the API. You MUST not exceed 500' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						<div class="clr_5"></div>
						<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='You MUST not exceed 500' mod='btmailchimpecommerce'}</span>
						</div>
										
					</div>
					
					<div class="clr_10"></div>
					<div class="clr_hr"></div>
					<div class="clr_10"></div>
					
					<h5>{l s='OPTIONAL: CRON task to delete batches (to be used only in specific cases)' mod='btmailchimpecommerce'}</h5>
					<div class="clr_10"></div>
					<div class="alert alert-warning">
						{l s='MailChimp should normally delete batches already processed as the synchronization process progresses. Anyway, if needed, we give you another CRON task URL to force the deletion of the batches. However, this CRON task URL should only be used in very SPECIFIC cases and in a very JUDICIOUS way.' mod='btmailchimpecommerce'}<strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/283" target="_blank">&nbsp;<i class="icon icon-link"></i>&nbsp;{l s='See our FAQ for more information' mod='btmailchimpecommerce'}</a></strong>
					</div>
					
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This URL will allow you to force the deletion of the batches already processed by MailChimp in the event that it is not possible to wait until the end of the processing of all the batches' mod='btmailchimpecommerce'}">
								<strong>{l s='Your CRON URL to delete batches (optional):' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon-link"></i></span>
									<input type="text" id="bt_batch_delete_cron_url" name="bt_batch_delete_cron_url"  value="{$sBatchDeleteUrl nofilter}" disabled="disabled" />
								</div>
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This URL will allow you to force the deletion of the batches already processed by MailChimp in the event that it is not possible to wait until the end of the processing of all the batches' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3"></label>
						<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
							<button id="bt_copy_module_link_cron_delete" type="button" class="btn btn-info btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{$sBatchDeleteUrl nofilter}" title="{$sBatchDeleteUrl nofilter}">&nbsp;<i class="fa fa-copy"></i>&nbsp;{l s='Copy to clipboard' mod='btmailchimpecommerce'} </button>
						</div>
					</div>
				</div>
			</div>

			<div class="clr_20"></div>
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
					<div id="bt_error_ecommerce_config"></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"><button class="btn btn-default pull-right" id="bt_btn_ecommerce_config" onclick="oMailchimp.form('bt_form_ecommerce_config', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings_ecommerce', 'bt_settings_ecommerce', false, false, oEcommerceCallBack, 'ecommerce_config', 'ecommerce');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button></div>
			</div>
		{else}
			<div class="alert alert-warning">
				{l s='To be able to use the e-commerce feature, you must first choose a list (or create one) in the "General settings > Users list choice" tab and do a first export of your users through the "Users list sync & newsletter forms > Users list synching" tab.' mod='btmailchimpecommerce'}
			</div>
		{/if}
	{else}
		<div class="alert alert-warning">
			{l s='You don\'t have filled in the MailChimp API key yet! Please do it by clicking on the "Configure" button at the step 2 above or go to the "General settings > Mailchimp settings" tab.' mod='btmailchimpecommerce'}
		</div>
	{/if}
</form>
<div class="clr_20"></div>

{literal}
	<script type="text/javascript">
		// bootstrap components init
		$(document).ready(function() {
			// handle sync mode to display the cron options
			$(".bt_sync_mode").bind('change', function (event)
			{
				var cron_used = false;
				$(".bt_sync_mode option:selected").each(function ()
				{
					switch ($(this).val()) {
						case 'cron' :
							cron_used = true;
							break;
						case 'regular' :
						case 'batch' :
						default:
							break;
					}
				});

				if (cron_used) {
					$("#bt_div_use_prod_cron").show();
				}  else {
					$("#bt_div_use_prod_cron").hide();
				}
			});

			// Copy to clipboard
			$('.js-copy').click(function() {
				var text = $(this).attr('data-copy');
				var el = $(this);
				oMailchimp.copyToClipboard(text, el);
			});
		});
	</script>
{/literal}
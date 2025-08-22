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

<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap autoscroll">
	<script type="text/javascript">
		{literal}
		var oVoucherCallBack =
			[{  'name' : 'updateVouchers',
				'url' : '{/literal}{$sURI nofilter}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=vouchers',
				'toShow' : 'bt_settings_voucher',
				'toHide' : 'bt_settings_voucher',
				'bFancybox' : false,
				'bFancyboxActivity' : false,
				'sLoadbar' : null,
				'sScrollTo' : null,
				'oCallBack' : {}
			}];
		{/literal}
	</script>
	<form class="form-horizontal col-xs-12  col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_voucher_form" name="bt_voucher_form" onsubmit="javascript: oMailchimp.form('bt_voucher_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_voucher_form', 'bt_voucher_form', false, true, oVoucherCallBack, 'voucher_form', 'voucher_form');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.voucherUpd.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.voucherUpd.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="bt_new_voucher" value="{if !empty($aCurrentVoucher)}0{else}1{/if}" />

		<div class="clr_10"></div>

		<h3>{if !empty($aCurrentVoucher)}{l s='Update the discount voucher' mod='btmailchimpecommerce'} <strong>{$aCurrentVoucher.name|escape:'htmlall':'UTF-8'}</strong>{else}{l s='Create a discount voucher' mod='btmailchimpecommerce'}{/if}</h3>
		<div class="clr_hr"></div>
		<div class="clr_20"></div>

		<div class="alert alert-info">
		{l s='Select the campaign type concerned and complete the form. To know more about this tool, please' mod='btmailchimpecommerce'}&nbsp;<strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/189" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='read our FAQ'  mod='btmailchimpecommerce'}</a></strong>
		</div>
		
		
		<div class="form-group">
			<label class="control-label col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the type of campaign in which you are going to offer the voucher. If none of the two proposals match the type of campaign concerned by the voucher, select "Other" (e.g for abandoned cart automation or newsletter campaign, select "Other").'  mod='btmailchimpecommerce'}">
					<strong>{l s='Type of e-mails campaign that will contain your code:' mod='btmailchimpecommerce'}</strong>
				</span>
			</label>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
					<select name="bt_voucher_automation" id="bt_voucher_automation">
						{* USE CASE - update the voucher so we display the selected type of automation *}
						{if !empty($aCurrentVoucher)}
							{if !empty($aCurrentVoucher.other)}
								<option value="other" selected="selected">{l s='Other' mod='btmailchimpecommerce'}</option>
							{else}
								{foreach from=$aAutomations name=automation key=item item=name}
									{if $aCurrentVoucher.type == $item}
									<option value="{$item|escape:'htmlall':'UTF-8'}" selected="selected">{$name|escape:'htmlall':'UTF-8'}</option>
									{/if}
								{/foreach}
							{/if}
						{else}
						{* USE CASE - create the automation voucher *}
						<option value="0">-- {l s='Select an e-mails campaign type' mod='btmailchimpecommerce'} --</option>
						{foreach from=$aAutomations name=automation key=item item=name}
							<option value="{$item|escape:'htmlall':'UTF-8'}" {if !empty($aCurrentVoucher) && empty($aCurrentVoucher.other) && $aCurrentVoucher.type == $item}selected="selected" class="disabled"{/if}>{$name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
						<option value="other" {if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.other)}selected="selected" disabled="disabled"{/if}>{l s='Other' mod='btmailchimpecommerce'}</option>
						{/if}
					</select>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the type of campaign in which you are going to offer the voucher. If none of the two proposals match the type of campaign concerned by the voucher, select "Other" (e.g for abandoned cart automation or newsletter campaign, select "Other").'  mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
				</div>
			</div>
		</div>

		<div class="form-group" id="bt_div_other_automation" style="display: {if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.other)}block{else}none{/if};">
			<label class="control-label col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter a name for this other type of campaign (e.g abandoned cart automation, newsletter, ...)'  mod='btmailchimpecommerce'}">
					<strong>{l s='Enter a name for this e-mails campaign type:' mod='btmailchimpecommerce'}</strong>
				</span>
			</label>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-pencil"></i></span>
						{if !empty($aCurrentVoucher)}
							<input type="hidden" id="bt_custom_automation" name="bt_custom_automation" size="35" value="{if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.other)}{$aCurrentVoucher.name|escape:'htmlall':'UTF-8'}{/if}" />
							<input type="text" id="bt_custom_automation_upd" name="bt_custom_automation_upd" size="35" {if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.other)}value="{$aCurrentVoucher.name|escape:'htmlall':'UTF-8'}" disabled="disabled"{else}value=""{/if}  />
						{else}
							<input type="text" id="bt_custom_automation" name="bt_custom_automation" size="35" value="" />
						{/if}
					</div>
					<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Invalid characters: numbers and' mod='btmailchimpecommerce'} {literal}"'!<>,;?=+()@#"�{}_$%:{/literal}</span>
				</div>
				<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter a name for this other type of campaign (e.g abandoned cart automation, newsletter, ...)' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
				</div>
			</div>
		</div>

		<div class="clr_10"></div>

		<div id="bt_div_options_display" style="display: {if !empty($aCurrentVoucher)}block{else}none{/if};">
			{* VOUCHER PREFIX CODE *}
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='Enter a prefix of at least 3 characters long for the voucher\'s code. The discount code will consist of a prefix common to all users targeted by the campaign and a unique suffix for each of them. The code will therefore be a personal code' mod='btmailchimpecommerce'}">
						<strong>{l s='Prefix of the discount code:' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
						<input type="text" size="5" maxlength="5" id="bt_voucher_prefix_code" name="bt_voucher_prefix_code" value="{if !empty($aVoucher.prefixCode)}{$aVoucher.prefixCode|escape:'htmlall':'UTF-8'}{else}MCE{/if}" />
					</div>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter a prefix of at least 3 characters long for the voucher\'s code. The discount code will consist of a prefix common to all users targeted by the campaign and a unique suffix for each of them. The code will therefore be a personal code' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
					<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Invalid characters: numbers and' mod='btmailchimpecommerce'} {literal}!<>,;?=+()@#"�{}_$%:{/literal}</span>
				</div>
			</div>

			{* VOUCHER'S TYPE *}
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<strong>{l s='Voucher\'s type' mod='btmailchimpecommerce'}</strong> :
				</label>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<select name="bt_discount_type" id="bt_discount_type" class="col-xs-8">
						<option value="percentage" {if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'percentage'}selected="selected"{/if}>{l s='Discount on order (%)' mod='btmailchimpecommerce'}</option>
						<option value="amount" {if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'amount'}selected="selected"{/if}>{l s='Discount on order (amount)' mod='btmailchimpecommerce'}</option>
					</select>
				</div>
			</div>
			{* PERCENT *}
			<div id="bt_apply_discount_percent_div" class="form-group" style="display: {if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'percentage'}block{else}none{/if};">
				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4 required">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the monetary amount or percentage (%), depending on the voucher type selected above' mod='btmailchimpecommerce'}">
						<strong>{l s='Value:' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
					<div class="input-group col-xs-6 col-sm-6 col-md-6 col-lg-6" style="float: left;">
						<span class="input-group-addon">%</span>
						<input type="text" id="bt_voucher_percent" class="input-mini" name="bt_voucher_percent" value="{if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'percentage'}{$aCurrentVoucher.amount|floatval}{else}0{/if}">
					</div>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the monetary amount or percentage (%), depending on the voucher type selected above' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
					<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary"></i> {l s='Does not apply to shipping costs' mod='btmailchimpecommerce'}</span>
				</div>
			</div>
			{* AMOUNT *}
			<div id="bt_apply_discount_amount_div" class="form-group" style="display: {if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'amount'}block{else}none{/if};">
				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4 required">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the monetary amount or percentage (%), depending on the voucher type selected above' mod='btmailchimpecommerce'}">
						<strong>{l s='Value:' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
					<div class="row fixed-width-xxl" style="float: left;">
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
							<input type="text" id="bt_voucher_amount" name="bt_voucher_amount" value="{if !empty($aCurrentVoucher) && $aCurrentVoucher.discount == 'amount'}{$aCurrentVoucher.amount|floatval}{else}0{/if}" onchange="this.value = this.value.replace(/,/g, '.');">
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
							<select id="bt_currency_id" name="bt_currency_id">
								{foreach from=$aCurrencies name=currency key=iKey item=aCurrency}
									<option value="{$aCurrency.id_currency|intval}" {if !empty($aCurrentVoucher) && $aCurrentVoucher.currency == $aCurrency.id_currency}selected="selected"{/if} >{$aCurrency.sign|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<select id="bt_voucher_tax_id" name="bt_voucher_tax_id">
								<option value="0" {if !empty($aCurrentVoucher) && $aCurrentVoucher.tax == 0}selected="selected"{/if}>{l s='Tax Excluded' mod='btmailchimpecommerce'}</option>
								<option value="1" {if !empty($aCurrentVoucher) && $aCurrentVoucher.tax == 1}selected="selected"{/if}>{l s='Tax Included' mod='btmailchimpecommerce'}</option>
							</select>
						</div>
					</div>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the monetary amount or percentage (%), depending on the voucher type selected above' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
				</div>
			</div>

			{* VOUCHER'S DESCRIPTION *}
			<div id="bt_div_features_display">
				<div class="form-group ">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4 required">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For EACH language, choose an appropriate short description for the voucher (e.g: "Voucher for having purchased on our shop"). Be careful: this description will be displayed to your customers when they see their custom code appear' mod='btmailchimpecommerce'}">
							<strong>{l s='Short description:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
						{foreach from=$aLangs item=aLang}
							<div id="bt_voucher-name_{$aLang.id_lang|intval}" class="translatable-field row lang-{$aLang.id_lang|intval}" {if $aLang.id_lang != $iCurrentLang}style="display:none"{/if}>
								<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
									<input type="text" id="bt_tab_voucher_name[{$aLang.id_lang|intval}]" name="bt_tab_voucher_name[{$aLang.id_lang|intval}]" {if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.langs[$aLang.id_lang])}value="{$aCurrentVoucher.langs[$aLang.id_lang]|escape:'htmlall':'UTF-8'}"{/if}/>
								</div>
								<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
									<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
									<ul class="dropdown-menu">
										{foreach from=$aLangs item=aLang}
											<li><a href="javascript:hideOtherLanguage({$aLang.id_lang|intval});" tabindex="-1">{$aLang.name|escape:'htmlall':'UTF-8'}</a></li>
										{/foreach}
									</ul>
									<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For EACH language, choose an appropriate short description for the voucher (e.g: "Voucher for having purchased on our shop"). Be careful: this description will be displayed to your customers when they see their custom code appear' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
									
								</div>
								<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary"></i> {l s='Do not forget to fill a description for each language!' mod='btmailchimpecommerce'}</span>
							</div>
						{/foreach}
					</div>
				</div>

				{* CATEGORY TREE *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select the category(ies) to which the discount code can be applied. To apply the code to all categories, do not select any' mod='btmailchimpecommerce'}">
							<strong>{l s='Valid category(ies):' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-5">
						<div class="btn-actions">
							<div class="btn btn-default btn-mini" id="categoryCheck" onclick="return oMailchimp.selectAll('input.categoryBox', 'check');"><span class="icon-plus-square"></span>&nbsp;{l s='Check All' mod='btmailchimpecommerce'}</div> - <div class="btn btn-default btn-mini" id="categoryUnCheck" onclick="return oMailchimp.selectAll('input.categoryBox', 'uncheck');"><span class="icon-minus-square"></span>&nbsp;{l s='Uncheck All' mod='btmailchimpecommerce'}</div>
							<div class="clr_10"></div>
						</div>
						<table cellspacing="0" cellpadding="0" class="table table-bordered table-striped table-responsive" style="width: 100%;">
							{foreach from=$aFormatCat name=category key=iKey item=aCat}
								<tr class="alt_row">
									<td>
										{$aCat.id_category|intval}
									</td>
									<td>
										<input type="checkbox" name="bt_category_box[]" class="categoryBox" id="bt_category_box_{$aCat.iNewLevel|intval}" value="{$aCat.id_category|intval}" {if !empty($aCat.bCurrent)}checked="checked"{/if} />
									</td>
									<td>
										<span class="icon icon-folder{if !empty($aCat.bCurrent)}-open{/if}" style="margin-left: {$aCat.iNewLevel|intval}5px;"></span>&nbsp;&nbsp;<span style="font-size:12px;">{$aCat.name|escape:'htmlall':'UTF-8'}</span>
									</td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>

				{* MINIMUM VOUCHER'S AMOUNT *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You can specify a minimum purchase amount to be made to use the coupon. Enter 0 if there is no minimum amount.' mod='btmailchimpecommerce'}">
							<strong>{l s='Min purchase amount to use the code:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<input type="text" size="15" id="bt_voucher_minimum" name="bt_voucher_minimum" value="{if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.minimum)}{$aCurrentVoucher.minimum|intval}{else}0{/if}" onkeyup="javascript:this.value = this.value.replace(/,/g, '.'); " />
						</div>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You can specify a minimum purchase amount to be made to use the coupon. Enter 0 if there is no minimum amount.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
					</div>
				</div>

				{* VOUCHER'S VALIDITY *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate the number of days during which the coupon is valid' mod='btmailchimpecommerce'}">
							<strong>{l s='Validity:' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
						<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
							<input type="text" size="3" name="bt_voucher_validity" id="bt_voucher_validity" value="{if !empty($aCurrentVoucher) && !empty($aCurrentVoucher.validity)}{$aCurrentVoucher.validity|intval}{else}365{/if}" />
						</div>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Indicate the number of days during which the coupon is valid' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='In days' mod='btmailchimpecommerce'}</span>
					</div>
				</div>

				{* HIGHLIGHT VOUCHER IN CUSTOMER'S CART *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want the voucher to be displayed in the cart summary, if it is not yet in the cart' mod='btmailchimpecommerce'}">
							<strong>{l s='Highlight the voucher?' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="bt_voucher_highlight" id="bt_voucher_highlight_on" value="1" {if !empty($aCurrentVoucher)}{if !empty($aCurrentVoucher.highlight)}checked="checked"{/if}{else}checked="checked"{/if} />
								<label for="bt_voucher_highlight_on" class="radioCheck">
									{l s='Yes' mod='btmailchimpecommerce'}
								</label>
								<input type="radio" name="bt_voucher_highlight" id="bt_voucher_highlight_off" value="0" {if !empty($aCurrentVoucher)}{if empty($aCurrentVoucher.highlight)}checked="checked"{/if}{/if} />
								<label for="bt_voucher_highlight_off" class="radioCheck">
									{l s='No' mod='btmailchimpecommerce'}
								</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want the voucher to be displayed in the cart summary, if it is not yet in the cart' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
						</div>
					</div>
				</div>

				{* CUMULATIVE VOUCHER WITH OTHERS *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4"><strong>{l s='Make the voucher cumulable with other vouchers?' mod='btmailchimpecommerce'}</strong></label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_cumulative_other" id="bt_cumulative_other_on" value="1" {if !empty($aCurrentVoucher)}{if !empty($aCurrentVoucher.cumulativeOther)}checked="checked"{/if}{else}checked="checked"{/if} />
							<label for="bt_cumulative_other_on" class="radioCheck">
								{l s='Yes' mod='btmailchimpecommerce'}
							</label>
							<input type="radio" name="bt_cumulative_other" id="bt_cumulative_other_off" value="0" {if !empty($aCurrentVoucher)}{if empty($aCurrentVoucher.cumulativeOther)}checked="checked"{/if}{/if} />
							<label for="bt_cumulative_other_off" class="radioCheck">
								{l s='No' mod='btmailchimpecommerce'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>

				{* CUMULATIVE VOUCHER WITH PRICE REDUCTION *}
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4"><strong>{l s='Make the voucher cumulable with reduced prices?' mod='btmailchimpecommerce'}</strong></label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_cumulative_reduc" id="bt_cumulative_reduc_on" value="1" {if !empty($aCurrentVoucher)}{if !empty($aCurrentVoucher.cumulativeReduc)}checked="checked"{/if}{else}checked="checked"{/if} />
							<label for="bt_cumulative_reduc_on" class="radioCheck">
								{l s='Yes' mod='btmailchimpecommerce'}
							</label>
							<input type="radio" name="bt_cumulative_reduc" id="bt_cumulative_reduc_off" value="0" {if !empty($aCurrentVoucher)}{if empty($aCurrentVoucher.cumulativeReduc)}checked="checked"{/if}{/if} />
							<label for="bt_cumulative_reduc_off" class="radioCheck">
								{l s='No' mod='btmailchimpecommerce'}
							</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="clr_20"></div>

		<div class="center">
			<button class="btn btn-success btn-lg" onclick="oMailchimp.form('bt_voucher_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_voucher_form', 'bt_voucher_form', false, true, oVoucherCallBack, 'voucher_form', 'voucher_form');return false;">{if !empty($aCurrentVoucher)}{l s='Update' mod='btmailchimpecommerce'}{else}{l s='Create' mod='btmailchimpecommerce'}{/if}</button>
		</div>
	</form>

	<div class="clr_10"></div>

	<div id="bt_loading_div_voucher_form" style="display: none;">
		<div class="alert alert-info">
			<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
			<p style="text-align: center !important;">{l s='Your voucher creation/update is in progress...' mod='btmailchimpecommerce'}</p>
		</div>
	</div>

	<div class="clr_10"></div>

	<div id="bt_error_voucher_form"></div>

	{literal}
	<script type="text/javascript">
		//bootstrap components init
		$(document).ready(function() {
			$('.label-tooltip, .help-tooltip').tooltip();
			$.fancybox.resize;

			// handle cart rule type
			$("#bt_discount_type").bind('change', function (event)
			{
				$("#bt_discount_type option:selected").each(function ()
				{
					switch ($(this).val()) {
						case 'percentage' :
							$("#bt_apply_discount_percent_div").show();
							$("#bt_apply_discount_amount_div").hide();
							$("#bt_div_features_display").show();
							break;
						case 'amount' :
							$("#bt_apply_discount_percent_div").hide();
							$("#bt_apply_discount_amount_div").show();
							$("#bt_div_features_display").show();
							break;
						default:
							$("#bt_apply_discount_percent_div").hide();
							$("#bt_apply_discount_amount_div").hide();
							$("#bt_div_features_display").hide();
							break;
					}
				});
			}).change();

			// handle automation type
			$("#bt_voucher_automation").bind('change', function (event)
			{
				$("#bt_voucher_automation option:selected").each(function ()
				{
					switch ($(this).val()) {
						case '0' :
							$("#bt_div_options_display").hide();
							$("#bt_div_other_automation").hide();
							break;
						case 'other' :
							$("#bt_div_options_display").show();
							$("#bt_div_other_automation").show();
							break;
						default:
							$("#bt_div_options_display").show();
							$("#bt_div_other_automation").hide();
							break;
					}
				});
			});
		});
	</script>
	{/literal}
</div>
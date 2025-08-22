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

<div class="form-horizontal">
	{* USE CASE - SEARCH IS AVAILABLE ONLY IF ONE LIST IS ACTIVE  *}
	{if !empty($bActiveNewsletter) && !empty($aListStatus)}
		
		<div class="clr_10"></div>
		
		<div class="form-group">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="alert alert-info">
					{l s='This diagnostic tool allows you to accurately check the synchronization of a specific data (e.g specific e-mail, specific product, etc...). Moreover if you note that the synchronization has failed, you can restart it directly from this tab.' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>

		<div class="clr_10"></div>

		<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_search_form" name="bt_search_form" onsubmit="javascript: checkSearchForm();return false;">
			<input type="hidden" name="sAction" value="{$aQueryParams.search.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.search.type|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sTpl" value="{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{/if}" />
			{if !empty($bActiveEcommerce) && !empty($aListStatus['store_id']) && !empty($aListStatus['store_name'])}
				<input type="hidden" name="bt_store_id" value="{$aListStatus['store_id']|escape:'htmlall':'UTF-8'}" />
			{/if}

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<b>{l s='Check sync of the following data:' mod='btmailchimpecommerce'}</b>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
					<table class="table table-striped bt-table-info">
						<tr>
							<td class="center col-xs-12 col-sm-12 col-md-5 col-lg-5"><i class="icon icon-filter"></i>&nbsp;<b>{l s='Data type' mod='btmailchimpecommerce'}</b></td>
							<td class="center col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display: none;" id="bt_search_language_label"><b>{l s='Language' mod='btmailchimpecommerce'}</b></td>
							<td class="center col-xs-12 col-sm-12 col-md-4 col-lg-4"><b>{l s='Data reference' mod='btmailchimpecommerce'}</b>&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Enter the information indicated between brackets in the previous filter. Example: if you have selected "User (fill : e-mail address)" in the previous filter, fill the e-mail of the user you want to check the synchronization' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></td>
							<td class="center col-xs-12 col-sm-12 col-md-3 col-lg-3"><b>{l s='Action' mod='btmailchimpecommerce'}</b></td>
						</tr>
						<tr>
							<td class="center">
								<div class="filter">
									<select name="bt_search_data_type" id="bt_search_data_type">
										<option value="0"> -- </option>
										<option value="member">{l s='User (fill: e-mail address)' mod='btmailchimpecommerce'}</option>
										<option value="mergefield">{l s='Merge field (fill: merge field name)' mod='btmailchimpecommerce'}</option>
										{if !empty($bActiveEcommerce) && !empty($aListStatus['store_id']) && !empty($aListStatus['store_name'])}
										<option value="product">{l s='Product (fill: product ID)' mod='btmailchimpecommerce'}</option>
										<option value="variant">{l s='Product combination (fill: product ID + C + attribute ID)' mod='btmailchimpecommerce'}</option>
										<option value="cart">{l s='Cart (fill: cart ID)' mod='btmailchimpecommerce'}</option>
										<option value="order">{l s='Order (fill: order ID)' mod='btmailchimpecommerce'}</option>
										<option value="customer">{l s='Customer (fill: customer ID)' mod='btmailchimpecommerce'}</option>
										{/if}
									</select>
								</div>
							</td>
							<td class="center" style="display: none;" id="bt_search_language_row">
								<div class="filter">
									<select name="bt_elt_lang_id" id="bt_elt_lang_id">
										{foreach from=$aLanguages name=lang key=iPos item=aLanguage}
											<option value="{$aLanguage.id_lang|intval}">{$aLanguage.name|escape:'htmlall':'UTF-8'}</option>
										{/foreach}
									</select>
								</div>
							</td>
							<td class="center">
								<div class="filter">
									<input type="text" name="bt_search_elt_id"  id="bt_search_elt_id" value="" />
								</div>
							</td>
							<td class="center">
								<input type="button" name="bt_diagnostic_tool_button" value="{l s='Search' mod='btmailchimpecommerce'}" class="btn btn-success btn-mini" onclick="checkSearchForm();return false;" />
								<a class="btn btn-default btn-mini" id="bt_reset_search_form"> <i class="icon icon-minus-square-o"></i>&nbsp;{l s='Reset' mod='btmailchimpecommerce'} </a>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">&nbsp;</label>
				<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
					<div id="bt_error_search_filter" class="alert alert-danger" style="display: none;">
						<button type="button" class="close" onclick="$(this).parent().slideUp()">×</button>
						{l s='You have to select / fill this filter: ' mod='btmailchimpecommerce'}<span id="bt_error_search_filter_html"></span>
					</div>
				</div>
			</div>

			<div class="form-group" id="bt_search_result">
				<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"><b>{l s='Result' mod='btmailchimpecommerce'}</b> :</label>
				<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 center">
					<div class="alert alert-info">{l s='The result will be displayed here!' mod='btmailchimpecommerce'}</div>
				</div>
			</div>

			<div id="bt_loading_div_search" style="display: none;">
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The search is in progress...' mod='btmailchimpecommerce'}</p>
				</div>
			</div>

			<div class="clr_20"></div>
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
					<div id="bt_error_search"></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"></div>
			</div>
		</form>
	{else}
		<div class="form-group">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="alert alert-warning">
					{l s='To be able to use this tab: please first select a list (or create a new one).' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>

		<div class="clr_20"></div>
	{/if}
</div>

{literal}
<script type="text/javascript">
	//handle reset form
	$( "#bt_reset_search_form" ).click(function() {
		$('#bt_search_data_type').val("0");
		$('#bt_search_elt_id').val("");
	});

	// handle sign-up form display type
	$("#bt_search_data_type").bind('change', function(event)
	{
		$("#bt_search_data_type option:selected").each(function()
		{
			switch ($(this).val()) {
				case 'product' :
					$("#bt_search_language_label").show();
					$("#bt_search_language_row").show();
					break;
				case 'variant' :
					$("#bt_search_language_label").show();
					$("#bt_search_language_row").show();
					break;
				default:
					$("#bt_search_language_label").hide();
					$("#bt_search_language_row").hide();
					break;
			}
		});
	}).change();

	function checkSearchForm() {
		var bError = false;

		if ($('#bt_search_data_type').val() == '0') {
			$('#bt_error_search_filter_html').text('{/literal}{l s='Data type' mod='btmailchimpecommerce'}{literal}');
			$('select[name="bt_search_data_type"]').parent().addClass('has-error has-feedback');
			$('select[name="bt_search-type"]').parent().removeClass('has-error has-feedback');
			bError = true;
		}
		else if ($('#bt_search_elt_id').val() == '') {
			$('#bt_error_search_filter_html').text('{/literal}{l s='Element reference' mod='btmailchimpecommerce'}{literal}');
			$('select[name="bt_search_elt_id"]').parent().addClass('has-error has-feedback');
			$('select[name="bt_search_data_type"]').parent().removeClass('has-error has-feedback');
			bError = true;
		}
		else {
			$('select[name="bt_search_elt_id"]').parent().removeClass('has-error has-feedback');
		}

		if (bError) {
			$('#bt_error_search_filter').css('display', 'block');
		}
		else {
			oMailchimp.form('bt_search_form', '{/literal}{$sURI nofilter}{literal}', null, 'bt_search_result', 'bt_search_result', false, false, null, 'search', 'search');
		}
	}

	$(document).ready(function() {
		$('.label-tooltip, .help-tooltip').tooltip();
	});
</script>
{/literal}

<div class="clr_20"></div>
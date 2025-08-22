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

<div class="bootstrap">
	{if !empty($sMCApiKey)}
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_vouchers_form" name="bt_vouchers_form" onsubmit="javascript: return false;">
		{*<input type="hidden" name="sAction" value="{$aQueryParams.basic.action|escape:'htmlall':'UTF-8'}" />*}
		{*<input type="hidden" name="sType" value="{$aQueryParams.basic.type|escape:'htmlall':'UTF-8'}" />*}
		{*<input type="hidden" name="sDisplay" id="sVoucherDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}voucher{/if}" />*}

		<h3><i class="icon icon-AdminPriceRule"></i>&nbsp;{l s='Discount vouchers settings' mod='btmailchimpecommerce'}</h3>
		<div class="clr_10"></div>
		{if !empty($bUpdate)}
			{include file="`$sConfirmInclude`"}
			<div class="clr_10"></div>
		{elseif !empty($aErrors)}
			{include file="`$sErrorInclude`" aErrors=$aErrors}
			<div class="clr_10"></div>
		{/if}

		<div class="alert alert-info">
			{l s='In this section you can create customized vouchers to insert into your e-mail templates (newsletters or automated e-mails). Click "Add a voucher" to start.'  mod='btmailchimpecommerce'}
			<div class="clr_10"></div>
			{l s='Once the voucher is created, you just have to select the language in which your e-mail will be written and copy the corresponding link. To know more about this tool, please' mod='btmailchimpecommerce'}&nbsp;<strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/189" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='read our FAQ'  mod='btmailchimpecommerce'}</a></strong>
		</div>

		<div class="clr_20"></div>

		<h4><i class="icon icon-AdminPriceRule"></i>&nbsp;{l s='Vouchers list' mod='btmailchimpecommerce'}</h4>
		
		<div class="clr_10"></div>
		
		{if !empty($aVouchers)}
			<div class="form-group">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="btn-actions">
						<a class="fancybox.ajax" id="bt_create_voucher" href="{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.voucherForm.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.voucherForm.type|escape:'htmlall':'UTF-8'}"><div class="btn btn-success"><i class="icon-plus-square"></i>&nbsp;{l s='Add a voucher' mod='btmailchimpecommerce'}</div></a>
						<div class="clr_10"></div>
					</div>
					<div class="clr_hr_blue"></div>
					<table class="table table-striped table-responsive">
						<thead>
						<tr>
							<th><span class="title_box center">{l s='Campaign' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='URL to copy' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Modify' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Delete' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Be careful, if you delete this voucher, customers who have received the e-mail with the old link will get an error by clicking on it' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
						</tr>
						</thead>
						<tbody>
							{foreach from=$aVouchers name=voucher key=sType item=aVoucher}
								<tr>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{$aVoucher.name|escape:'htmlall':'UTF-8'}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-5 col-lg-5 center">
										<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
											<select id="bt_module_links_{$smarty.foreach.voucher.index}" name="bt_module-links">
												{foreach from=$aVoucher.aModuleLangLinks name=links key=iKey item=aLink}
													<option value="{$aLink.link nofilter}&email=*|EMAIL|*">{l s='Link for:' mod='btmailchimpecommerce'} {$aLink.lang|escape:'htmlall':'UTF-8'}</option>
												{/foreach}
											</select>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<button id="bt_copy_module_link_{$smarty.foreach.voucher.index}" type="button" class="btn btn-info btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{$aVoucher.aModuleLangLinks[0].link nofilter}&email=*|EMAIL|*"  title="{$aVoucher.aModuleLangLinks[0].link nofilter}&email=*|EMAIL|*">&nbsp;<i class="fa fa-copy"></i>&nbsp;{l s='Copy to clipboard' mod='btmailchimpecommerce'} </button>
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='URL to include in your e-mail template. Be careful, the "EMAIL" parameter is formatted as a "merge field", as MailChimp requires' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										<a class="fancybox.ajax" id="bt_voucher_update_{$aVoucher.type nofilter}" href="{$sURI nofilter}&sAction={$aQueryParams.voucherForm.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.voucherForm.type|escape:'htmlall':'UTF-8'}&bt_voucher={$aVoucher.type nofilter}"><button type="button" class="btn btn-success btn-mini"><i class="icon icon-pencil"></i></button></a>
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										<a id="bt_voucher-delete-{$aVoucher.type nofilter}" onclick="check = confirm('{l s='Are you sure you want to delete this voucher?' mod='btmailchimpecommerce'} {l s='It will be definitively removed from your database. Be careful: if you delete this voucher, customers who have received the e-mail with the old link will get an error by clicking on it.' mod='btmailchimpecommerce'}');if(!check)return false; oMailchimp.ajax('{$sURI|escape:'htmlall':'UTF-8'}', 'sAction=delete&sType=voucher&bt_voucher={$aVoucher.type nofilter}', 'bt_settings_voucher', 'bt_settings_voucher');"><button type="button" class="btn btn-danger btn-mini"><i class="icon icon-trash"></i></button></a>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>

			<div class="clr_10"></div>
			<div class="alert alert-warning">
				<strong>{l s='IMPORTANT:'  mod='btmailchimpecommerce'}</strong>
				{l s='Please do not modify the copied links. They already include all necessary parameters, such as the "EMAIL" merge field' mod='btmailchimpecommerce'}
			</div>
		{else}
			<div class="alert alert-warning">
				{l s='You didn\'t create any incentive voucher rules!'  mod='btmailchimpecommerce'}
			</div>

			<div class="btn-actions">
				<a class="fancybox.ajax" id="bt_create_voucher" href="{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.voucherForm.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.voucherForm.type|escape:'htmlall':'UTF-8'}"><div class="btn btn-success"><i class="icon-plus-square"></i>&nbsp;{l s='Add a voucher' mod='btmailchimpecommerce'}</div></a>
				<div class="clr_10"></div>
			</div>
		{/if}

		<div class="clr_20"></div>
		<div class="clr_hr"></div>
		<div class="clr_20"></div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
				<div id="bt_error_voucher"></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
				{*<button class="btn btn-default pull-right" onclick="oMailchimp.form('bt_vouchers-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_voucher-settings', 'bt_voucher-settings', false, false, oVouchersCallBack, 'voucher', 'voucher');return false;"><i class="process-icon-save"></i>{l s='Update' mod='btmailchimpecommerce'}</button>*}
			</div>
		</div>
	</form>
	{else}
		<div class="clr_20"></div>
		<div class="form-group">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="alert alert-warning">
					{l s='You don\'t have filled the MailChimp API Key yet!' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>
	{/if}
</div>

{literal}
<script type="text/javascript">

	$("a#bt_create_voucher").fancybox({
		'hideOnContentClick' : false,
		'autoSize': false,
		'autoDimensions': false,
		'scrolling': 'auto',
		'centerOnScroll': true,
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'height': 1200,
	});

	//bootstrap components init
	$(document).ready(function() {
		$('.label-tooltip, .help-tooltip').tooltip();

		// Copy to clipboard
		$('.js-copy').click(function() {
			var text = $(this).attr('data-copy');
			var el = $(this);
			oMailchimp.copyToClipboard(text, el);
		});

		// handle module links for each automation voucher and set the appropriate language
		$("#bt_module-links").bind('change', function (event)
		{
			$("#bt_module-links option:selected").each(function ()
			{
				var sLink = decodeURIComponent($(this).val());
				$('#bt_copy-module-link').attr('data-copy', sLink);
				$('#bt_copy-module-link').attr('title', sLink);
			});
		});

		{/literal}
		{if !empty($aVouchers)}
		{foreach from=$aVouchers name=voucher key=sType item=aVoucher}
		{literal}
		$("a#bt_voucher_update_{/literal}{$aVoucher.type|escape:'UTF-8'}{literal}").fancybox({
			'hideOnContentClick' : false
		});

		// handle module links for each automation voucher and ste the appropriate language
		$("#bt_module_links_{/literal}{$smarty.foreach.voucher.index}{literal}").bind('change', function (event)
		{
			$("#bt_module_links_{/literal}{$smarty.foreach.voucher.index}{literal} option:selected").each(function ()
			{
				var sLink = decodeURIComponent($(this).val());
				$('#bt_copy_module_link_{/literal}{$smarty.foreach.voucher.index}{literal}').attr('data-copy', sLink);
				$('#bt_copy_module_link_{/literal}{$smarty.foreach.voucher.index}{literal}').attr('title', sLink);
			});
		});

		{/literal}
		{/foreach}
		{/if}
		{literal}
	});
</script>
{/literal}
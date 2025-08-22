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
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI nofilter}" method="post" id="bt_form_ecommerce_sync" name="bt_form_ecommerce_sync">

		{if !empty($bActiveEcommerce)
			&& !empty($aListEcommerce.store_id)
		}
			<div class="form-group">
				<div class="alert alert-info">
				{l s='After configuring the e-commerce feature in the previous tab, do the synchronizations below a first time manually in order to activate the automatic synchronization process.' mod='btmailchimpecommerce'}
				<div class="clr_5"></div>
				{l s='To know more' mod='btmailchimpecommerce'}&nbsp;<strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/290" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='read our FAQ' mod='btmailchimpecommerce'}</a></strong>
				</div>
				<div class="clr_5"></div>
				
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table class="table table-striped table-responsive">
						<thead>
						<tr>
							<th><span class="title_box center">{l s='Type' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Synchronized?' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Date' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Period' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Action' mod='btmailchimpecommerce'}</span></th>
						</tr>
						</thead>
						<tbody>
							{foreach from=$aSynchronizedData name=ecommerce key=type item=param}
								<tr>
									<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center {if isset($param.data.active_catalog)}{if $param.data.active_catalog == 1}success{elseif $param.data.active_catalog == 2}info{else}warning{/if}{/if}">
										{$param.title}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										<i class="{if isset($param.data.active_catalog)}{if $param.data.active_catalog == 1}icon-check list-action-enable action-enabled{elseif $param.data.active_catalog == 2}icon icon-2x icon-time icon-time-red{else}icon-remove  list-action-enable action-disabled{/if}{else}icon-remove  list-action-enable action-disabled{/if}"></i>
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{if isset($param.data.active_catalog)}{if $param.data.active_catalog == 1}{l s='done!' mod='btmailchimpecommerce'} ({l s='last synching on' mod='btmailchimpecommerce'} {$param.data.sync_date_last|escape:'htmlall':'UTF-8'}){elseif $param.data.active_catalog == 2}{l s='in progress...' mod='btmailchimpecommerce'} ({l s='created on' mod='btmailchimpecommerce'} {$param.data.sync_date|escape:'htmlall':'UTF-8'}){else}{l s='never done!' mod='btmailchimpecommerce'}{/if}{else}{l s='never done!' mod='btmailchimpecommerce'}{/if}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center">
										{if $type == 'order'}
										<div class="row">
											<div class="input-group">
												<span class="input-group-addon">{l s='From' mod='btmailchimpecommerce'}:</span>
												<input type="text" class="datepicker input-medium" name="bt_order_date_from" value="{$sPastYear|escape:'htmlall':'UTF-8'}" id="bt_order_date_from">
												<span class="input-group-addon">{l s='To:' mod='btmailchimpecommerce'}</span>
												<input type="text" class="datepicker input-medium" name="bt_order_date_to" value="{$sToday|escape:'htmlall':'UTF-8'}" id="bt_order_date_to">
												<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
											</div>
										</div>
										{/if}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										<button id="bt_btn_ecommerce_sync_{$type}" type="button" class="btn btn-info btn-lg" onclick="oMailchimp.formatFancyboxUrl('{$type}', '{$sURI|escape:'htmlall':'UTF-8'}&sAction=display&sType=syncForm&type={$type}{if !empty($param.data.batches)}&batch=true{/if}', 'a#bt_list_sync_{$type}', '#bt_list_order_select_error');return false;"><i class="icon icon-refresh"></i></button>
										<a class="fancybox.ajax" id="bt_list_sync_{$type}" href="#"></a>
									</td>
								</tr>
								{if $type == 'order'}
								<tr id="bt_list_order_select_error" style="display: none;" class="danger">
									<td class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center" colspan="5">
										<div class="clr_10"></div>
										<button type="button" class="close" onclick="$('#bt_list_order_select_error').slideUp();">×</button>
										{l s='The start date is not specified. Please fill it in first.' mod='btmailchimpecommerce'}
										<div class="clr_10"></div>
									</td>
								</tr>
								{/if}
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		{else}
			<div class="alert alert-warning">
				{l s='You can\'t synchronize customers / products or past orders until you\'ve activated the e-commerce feature in the previous "Configuration" tab.' mod='btmailchimpecommerce'}
			</div>
		{/if}

		<div class="clr_20"></div>
		<div class="clr_hr"></div>
		<div class="clr_20"></div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
				<div id="bt_error_ecommerce_sync"></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
				{*<button class="btn btn-default pull-right" onclick="oMailchimp.form('bt_form_ecommerce_config', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_div_ecommerce_sync', 'bt_div_ecommerce_sync', false, false, null, 'ecommerce_sync', 'ecommerce');return false;"><i class="process-icon-save"></i>Search</button>*}
			</div>
		</div>
	</form>
</div>
<div class="clr_20"></div>

{literal}
<script type="text/javascript">
	//bootstrap components init
	$(document).ready(function() {
		$('.label-tooltip, .help-tooltip').tooltip();

		{/literal}
		{if !empty($aSynchronizedData)}
		{foreach from=$aSynchronizedData name=ecommerce key=type item=data}
		{literal}
		$("a#bt_list_sync_{/literal}{$type}{literal}").fancybox({
			'hideOnContentClick' : false,
			afterClose : function() {
				oMailchimp.clearTimeOut();
				return;
			}
		});
		{/literal}
		{/foreach}
		{/if}
		{literal}

		if ($('.datepicker').length > 0) {
			var date = new Date();
			var hours = date.getHours();
			if (hours < 10)
				hours = '0' + hours;
			var mins = date.getMinutes();
			if (mins < 10)
				mins = '0' + mins;
			var secs = date.getSeconds();
			if (secs < 10)
				secs = '0' + secs;
			$('.datepicker').datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd ' + hours + ':' + mins + ':' + secs
			});
		}
	});
</script>
{/literal}
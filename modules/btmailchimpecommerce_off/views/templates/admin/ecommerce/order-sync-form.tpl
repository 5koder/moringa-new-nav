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

{* USE CASE - FIRST SYNCHRO, NO BATCH DETAILS *}
{if empty($bDisplayDetails)}
<div id="bt_div_order_sync_form">
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
		<script type="text/javascript">
			{literal}
			var aOrderSync = {
				'sURI' : '{/literal}{$sURI nofilter}{literal}',
				'sParams' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.orderSync.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.orderSync.type|escape:'htmlall':'UTF-8'}{literal}',
				'sSyncType' : '{/literal}{$sSyncType|escape:'htmlall':'UTF-8'}{literal}',
				'sListId' : '{/literal}{$aListSyncForm.id}{literal}',
				'sStoreId' : '{/literal}{$aListSyncForm.store_id}{literal}',
				'iStep' : 0,
				'iFloor' : 0,
				'iTotal' : {/literal}{$iTotal|intval}{literal},
				'iProcess' : 0,
				'sDateFrom' : '{/literal}{$sDateFrom|escape:'htmlall':'UTF-8'}{literal}',
				'sDateTo' : '{/literal}{$sDateTo|escape:'htmlall':'UTF-8'}{literal}',
				'sDisplayedCounter' : '#bt_regen_counter',
				'sDisplayedBlock' : '#bt_content_sync',
				'sDisplayTotal' : '#bt_total_processed',
				'sLoaderBar' : '#bt_loader_bar',
				'sErrorContainer' : 'ajax_sync_error',
				'oCallback' : [{
					'name' : 'updateSyncForm',
					'url' : '{/literal}{$sURI nofilter}{literal}',
					'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=syncForm&type=order&sDateFrom={/literal}{$sDateFrom|escape:'htmlall':'UTF-8'}{literal}&sDateTo={/literal}{$sDateTo|escape:'htmlall':'UTF-8'}{literal}',
					'toShow' : 'bt_div_order_sync_form',
					'toHide' : 'bt_div_order_sync_form',
					'bFancybox' : false,
					'bFancyboxActivity' : false,
					'sLoadbar' : null,
					'sScrollTo' : null,
					'oCallBack' : {}
				}]
			};
			{/literal}
		</script>
		<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_order_sync_form" name="bt_order_sync_form">
			<input type="hidden" name="sAction" value="{$aQueryParams.orderSync.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.orderSync.type|escape:'htmlall':'UTF-8'}" />

			{* use case - first sync *}
			<h2>{l s='Past orders synchronization' mod='btmailchimpecommerce'}</h2>

			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			{if empty($aErrors)}
				{if !empty($bAlreadySync)}
					<div class="clr_10"></div>
					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-warning">
								{l s='Past orders list has already been synchronized to MailChimp on the:' mod='btmailchimpecommerce'}&nbsp;<b>{$sDateCreated|escape:'htmlall':'UTF-8'}</b>  ({l s='Last update:' mod='btmailchimpecommerce'} {$sDateUpdated|escape:'htmlall':'UTF-8'})
								<div class="clr_10"></div>
								{l s='However, you can always manually resynchronize the past orders by clicking on the green button below.' mod='btmailchimpecommerce'}
							</div>
						</div>
					</div>

					<div class="clr_10"></div>
				{/if}

				<div class="clr_20"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-3"><strong>{l s='Selected period' mod='btmailchimpecommerce'}</strong> : </label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="input-group">
							<span class="input-group-addon">{l s='From' mod='btmailchimpecommerce'}:</span>
							<input type="text" class="datepicker input-medium" name="bt_order_date_from" disabled="disabled" value="{$sDateFrom|escape:'htmlall':'UTF-8'}" id="bt_order_date_from">
							<span class="input-group-addon">{l s='To:' mod='btmailchimpecommerce'}</span>
							<input type="text" class="datepicker input-medium" name="bt_order_date_to" disabled="disabled" value="{$sDateTo|escape:'htmlall':'UTF-8'}" id="bt_order_date_to">
							<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Your MailChimp list name:' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$aListSyncForm.name|escape:'htmlall':'UTF-8'}" class="disabled" />
					</div>
				</div>
				
				<div class="clr_10"></div>

				{* USE CASE - display the form settings only if there are some orders in this language to synchronize *}
				{if !empty($iTotal) && !empty($aStatusSelection)}
				<div id="bt_div-display-details" style="display: block">
					<h3>{l s='Preferences for past orders synchronization' mod='btmailchimpecommerce'}</h3>
					<div class="clr_hr"></div>
					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4" for="bt_order-statuses">
						<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='This is a reminder of selected statuses. Only orders that match one of these statuses will be synchronized' mod='btmailchimpecommerce'}">
						<strong>{l s='Selected Order statuses:' mod='btmailchimpecommerce'}</strong></span></label>
						<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
							<table cellspacing="0" cellpadding="0" class="table table-responsive table-bordered table-striped">
								<tr class="nodrag nodrop">
									<td class="center"><strong>{l s='Order state' mod='btmailchimpecommerce'}</strong></td>
									<td class="center"></td>
								</tr>
								{foreach from=$aOrderStatuses key=id item=aOrder}
									{foreach from=$aStatusSelection key=key item=iIdSelect}
										{if $iIdSelect == $id}
											<tr>
												<td>
													{$aOrder[$iCurrentLang]|escape:'htmlall':'UTF-8'}
												</td>
												<td class="center">
													<input type="checkbox" name="{$sModuleName|escape:'htmlall':'UTF-8'}OrderStatus[]" id="{$sModuleName|escape:'htmlall':'UTF-8'}OrderStatus" value="{$id|escape:'htmlall':'UTF-8'}" checked="checked" disabled="disabled" />
												</td>
											</tr>
										{/if}
									{/foreach}
								{/foreach}
							</table>
						</div>
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The count here indicates the number of orders placed in the defined period and that match the selected statuses'  mod='btmailchimpecommerce'}"><strong>{l s='Total number of orders:' mod='btmailchimpecommerce'}</strong></span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<input type="text" id="bt_nb_orders" name="bt_nb_orders" size="25" value="{$iTotal|intval}" class="disabled" />
						</div>
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='To avoid server resources limitations issues, the orders are sent in batches. Each batch contains the number of orders indicated here.' mod='btmailchimpecommerce'}"><strong>{l s='Number of orders per batch:' mod='btmailchimpecommerce'}</strong></span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<input type="text" id="bt_item_per_cycle" name="bt_item_per_cycle" size="25" value="{if $iTotal < $iItemCycle}{$iTotal|intval}{else}{$iItemCycle|intval}{/if}" />
							</div>
							<div class="clr_5"></div>
							<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='First, leave the default value indicated and, if you get synch errors, lower the value.' mod='btmailchimpecommerce'}</span>
						</div>
					</div>
					
					<div class="clr_10"></div>

					<div id="bt_content_sync" style="display: none;" class="alert alert-info col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="button" class="close" onclick="$('#bt_content_sync').hide();">×</button>
						<h4>{l s='Synchronize your past orders' mod='btmailchimpecommerce'}</h4>
						<table>
							<tr>
								<td><b>{l s='Number of synchronized orders:' mod='btmailchimpecommerce'}</b>&nbsp;</td>
								<td>&nbsp;</td>
								<td><input type="text" size="5" name="bt_regen_counter" id="bt_regen_counter" value="0" />&nbsp;</td>
								<td>&nbsp;</td>
								<td>{l s='out of' mod='btmailchimpecommerce'}&nbsp;{$iTotal|intval} ({l s='orders in total on this period' mod='btmailchimpecommerce'})</td>
							</tr>
						</table>
						<div class="clr_5"></div>
						<div class="center">
							<div class="reloader" id="bt_loader_img"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></div>
						</div>
						<div class="clr_20"></div>
					</div>

					<div id="bt_error_ajax_sync_error" style="display: none;"></div>

					<div id="bt_content_sync-success" style="display: none;">
						<div class="clr_5"></div>
						<button type="button" class="close" data-dismiss="alert">×</button>
						<div id="bt_total_processed" style="font-weight: bold; display: none; margin-left:20px; vertical-align:text-top;"></div>
					</div>

					<div class="clr_20"></div>

					<div class="center">
						<a class="btn btn-success btn-lg" id="bt_sync_button" href="javascript:void(0);" onclick="if($('#bt_item_per_cycle').val() == 0 || $('#bt_item_per_cycle').val() == ''){literal}{{/literal}alert('{l s='You must fill in a value for the number of orders per batch!' mod='btmailchimpecommerce'}'); return false;{literal}}{/literal};aOrderSync.iStep=$('#bt_item_per_cycle').val();$('#bt_content_sync').show();oMailchimp.synchronizeData(aOrderSync);"><span class="icon-refresh"></span>&nbsp;{l s='Synchronize' mod='btmailchimpecommerce'}</a>
					</div>
				</div>
				{else}
					{* use case - any status has been selected *}
					{if empty($aStatusSelection)}
						<div class="form-group">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="alert alert-warning">
									{l s='You didn\'t select any order status in the "Configuration" tab!'  mod='btmailchimpecommerce'}
								</div>
							</div>
						</div>
					{* use case - any order is matching to the period and language *}
					{else}
						<div class="form-group">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="alert alert-warning">
									{l s='For the defined period, there is no order with one of the statuses selected in the "Configuration" tab!'  mod='btmailchimpecommerce'}
								</div>
							</div>
						</div>
					{/if}
				{/if}
			{else}
				{include file="`$sErrorInclude`" aErrors=$aErrors}
				<div class="clr_10"></div>
			{/if}
		</form>

		<div class="clr_10"></div>

		<div id="bt_loading_div_sync" style="display: none;">
			<div class="clr_5"></div>
			<div class="alert alert-info">
				<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
				<p style="text-align: center !important;">{l s='Your past orders list synchronization is in progress...' mod='btmailchimpecommerce'}</p>
			</div>
		</div>
		{literal}
		<script type="text/javascript">
			//bootstrap components init
			$(document).ready(function() {
				$('.label-tooltip, .help-tooltip').tooltip();
			});
		</script>
		{/literal}
	</div>
</div>
{* USE CASE - SYNCHRO IN PROGRESS, BATCH DETAILS *}
{else}
<div id="bt_div_past_orders_batch" class="autoscroll">
	<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap autoscroll">
		<script type="text/javascript">
			{literal}
			var oBatchCallBack = [{
				'name' : 'updateEcommerceSync',
				'url' : '{/literal}{$sURI nofilter}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=ecommerce&sTpl=sync&sSubTpl=sync',
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
		<form class="form-horizontal col-xs-12  col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_batch_form" name="bt_batch_form">
			<input type="hidden" name="sAction" value="{$aQueryParams.batch.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.batch.type|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="bt_sync_type" value="order" />

			<h2>{l s='Past orders synchronization' mod='btmailchimpecommerce'}</h2>
			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			{if empty($bCanClose)}
				<div class="clr_5"></div>

				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-warning">
							<b>{l s='The past orders synchronization has begun, please just wait for the end of batches sending: the window will refresh itself when it\'s finished.' mod='btmailchimpecommerce'}</b>
							<div class="clr_5"></div>
							<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
							<div class="center"><strong><span id="bt_countdown_order">{$iRefreshWaitingTime|intval}</span></strong> {l s='seconds' mod='btmailchimpecommerce'}</div>
						</div>
					</div>
				</div>
			{/if}

			{if !empty($iProcessed)}
				<div class="clr_5"></div>

				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-success">
							<b>{$iTotal|intval}&nbsp;{l s='past orders sent to MailChimp for processing' mod='btmailchimpecommerce'}</b>
						</div>
					</div>
				</div>
			{/if}

			<h3>{l s='Details' mod='btmailchimpecommerce'}</h3>
			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-3"><strong>{l s='Selected period' mod='btmailchimpecommerce'}</strong> : </label>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="input-group">
						<span class="input-group-addon">{l s='From' mod='btmailchimpecommerce'}:</span>
						<input type="text" class="datepicker input-medium" name="bt_order-date-from" value="{$sDateFrom|escape:'htmlall':'UTF-8'}" id="bt_order-date-from">
						<span class="input-group-addon">{l s='To:' mod='btmailchimpecommerce'}</span>
						<input type="text" class="datepicker input-medium" name="bt_order-date-to" value="{$sDateTo|escape:'htmlall':'UTF-8'}" id="bt_order-date-to">
						<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
					</div>
				</div>
			</div>

			<div class="clr_10"></div>

			<h2>{l s='Past orders processing' mod='btmailchimpecommerce'}</h2>
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			{if !empty($aBatches)}
				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<table class="table table-striped table-responsive">
							<thead>
							<tr>
								<th><span class="title_box center">{l s='Batch ID' mod='btmailchimpecommerce'}&nbsp;</span></th>
								<th><span class="title_box center">{l s='Status' mod='btmailchimpecommerce'}&nbsp;</span></th>
								<th><span class="title_box center">{l s='Total' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This is the total number of operations requested.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
								<th><span class="title_box center">{l s='Finished' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This is the total number of operations finished including those in error.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
								<th><span class="title_box center">{l s='Range of orders processed' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This allows you to know which range of orders you have to synchronize again if errors are returned.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
							</tr>
							</thead>
							<tbody>
							{foreach from=$aBatches name=batch key=iKey item=aBatch}
								<tr>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{$aBatch.id|escape:'htmlall':'UTF-8'}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center {if $aBatch.status == 'finished'}success{else}info{/if}">
										<i class="{if $aBatch.status == 'finished'}icon-check list-action-enable action-enabled{else}icon-time icon-time-blue{/if}"></i>&nbsp;{if $aBatch.status == 'finished'}{l s='Done!' mod='btmailchimpecommerce'}{else}{l s='In progress' mod='btmailchimpecommerce'}{/if}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{if $aBatch.total != 0}{$aBatch.total|intval}{else}{l s='pending ...' mod='btmailchimpecommerce'}{/if}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{$aBatch.finished|intval}
									</td>
									<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
										{$aBatch.floor+1|intval} - {$aBatch.next|intval}
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			{else}
				<div class="clr_20"></div>
				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-info">
							{l s='There isn\'t any batch related to this store!' mod='btmailchimpecommerce'}
						</div>
					</div>
				</div>
			{/if}

			<div class="clr_5"></div>

			<div id="bt_error_batch"></div>

			<div class="clr_5"></div>
			{if empty($bCanClose)}
				<div class="alert alert-warning">
					{l s='Some data batches are still in progress in MailChimp side, simply wait and refresh the progress details by clicking on the button below until you\'ve got all the batches flagged as "done". If you close the popup by accident, you will find the progress of data processing by clicking again on the synching button in the "Action" column.' mod='btmailchimpecommerce'}
				</div>
			{else}
				{l s='The execution of all the batches is finished. HOWEVER, to fully finish the process, please click on the "Finalize past orders sync" green button below.' mod='btmailchimpecommerce'}
			{/if}

			<div id="bt_loading_div_batch_reload" style="display: none;">
				<div class="clr_5"></div>
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The batches list is about to be refreshed...' mod='btmailchimpecommerce'}</p>
				</div>
			</div>

			<div id="bt_loading_div_batch_resync" style="display: none;">
				<div class="clr_5"></div>
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The form to resynchronize past orders in error will soon be displayed...' mod='btmailchimpecommerce'}</p>
				</div>
			</div>

			<div class="clr_5"></div>

			<div class="center">
				{if !empty($bCanClose)}
					<button id="bt_btn_finalize_order" class="btn btn-success btn-lg" onclick="oMailchimp.form('bt_batch_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_batch_form', 'bt_batch_form', false, false, oBatchCallBack, 'batch', 'batch-finalize');return false;">{l s='Finalize past orders sync' mod='btmailchimpecommerce'}</button>
				{else}
					<button id="bt_btn_refresh_order" class="btn btn-success btn-lg" onclick="oMailchimp.clearTimeOut();$('#bt_loading_div_batch_reload').show();oMailchimp.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sReloadUrlParams|escape:'htmlall':'UTF-8'}', 'bt_div_past_orders_batch', 'bt_div_past_orders_batch', false, false, 'batch_reload', null, null);return false;">{l s='Refresh details' mod='btmailchimpecommerce'}</button>
				{/if}
			</div>
		</form>

		<div class="clr_5"></div>

		<div id="bt_loading_div_batch-finalize" style="display: none;">
			<div class="clr_5"></div>
			<div class="alert alert-info">
				<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
				<p style="text-align: center !important;">{l s='The finalization of past orders synchronization is in progress...' mod='btmailchimpecommerce'}</p>
			</div>
		</div>

		{literal}
		<script type="text/javascript">
			//bootstrap components init
			$(document).ready(function() {
				$('.label-tooltip, .help-tooltip').tooltip();
				$.fancybox.resize;
			});
			{/literal}
			{if empty($bCanClose)}
			{literal}
			oMailchimp.chrono('oMailchimp',{/literal}{$iRefreshWaitingTime|intval}{literal}, '#bt_countdown_order', '#bt_btn_refresh_order', 'click');
			{/literal}
			{else}
			$('#bt_btn_finalize_order').trigger('click');
			{/if}
			{literal}
		</script>
		{/literal}
	</div>
</div>
{/if}
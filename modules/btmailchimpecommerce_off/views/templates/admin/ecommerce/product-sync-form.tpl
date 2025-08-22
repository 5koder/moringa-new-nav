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
<div id="bt_div_product_sync_form">
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
		<script type="text/javascript">
			{literal}
			var aProductSync = {
				'sURI' : '{/literal}{$sURI nofilter}{literal}',
				'sParams' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.productSync.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.productSync.type|escape:'htmlall':'UTF-8'}{literal}',
				'sSyncType' : '{/literal}{$sSyncType|escape:'htmlall':'UTF-8'}{literal}',
				'sListId' : '{/literal}{$aListSyncForm.id}{literal}',
				'sStoreId' : '{/literal}{$aListSyncForm.store_id}{literal}',
				'iStep' : 0,
				'iFloor' : 0,
				'iTotal' : {/literal}{$iTotal|intval}{literal},
				'iProcess' : 0,
				'sNewSyncType' : 'new',
				'sDisplayedCounter' : '#bt_regen_counter',
				'sDisplayedBlock' : '#bt_content_sync',
				'sDisplayTotal' : '#bt_total_processed',
				'sLoaderBar' : '#bt_loader_bar',
				'sErrorContainer' : 'ajax_sync_error',
				'oCallback' : [{
					'name' : 'updateSyncForm',
					'url' : '{/literal}{$sURI nofilter}{literal}',
					'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=syncForm&type=product',
					'toShow' : 'bt_div_product_sync_form',
					'toHide' : 'bt_div_product_sync_form',
					'bFancybox' : false,
					'bFancyboxActivity' : false,
					'sLoadbar' : null,
					'sScrollTo' : null,
					'oCallBack' : {}
				}]
			};
			{/literal}
		</script>
		<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_product_sync_form" name="bt_product_sync_form">
			<input type="hidden" name="sAction" value="{$aQueryParams.productSync.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.productSync.type|escape:'htmlall':'UTF-8'}" />

			{* use case - first sync *}
			<h2>{l s='Products synchronization' mod='btmailchimpecommerce'}</h2>

			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			{if !empty($bAlreadySync)}
				<div class="clr_10"></div>
				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-warning">
							{l s='Your products have already been synchronized to MailChimp on the:' mod='btmailchimpecommerce'}&nbsp;<b>{$sDateCreated|escape:'htmlall':'UTF-8'}</b>  ({l s='Last update:' mod='btmailchimpecommerce'} {$sDateUpdated|escape:'htmlall':'UTF-8'})
							<div class="clr_10"></div>
							{l s='However, you can always manually resynchronize your products by clicking on the green button below.' mod='btmailchimpecommerce'}
						</div>
					</div>
				</div>

				<div class="clr_10"></div>
			{/if}

			<div id="bt_div-display-details" style="display: block">
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Your MailChimp list name:' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$aListSyncForm.name|escape:'htmlall':'UTF-8'}" class="disabled" />
					</div>
				</div>

				<h3>{l s='Preferences for products synchronization' mod='btmailchimpecommerce'}</h3>
				<div class="clr_hr"></div>
				<div class="clr_20"></div>

				<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='The count here indicates, in the first column, the number of products without taking into account the combinations and, in the second column, taking them into account '  mod='btmailchimpecommerce'}"><strong>{l s='Total of products:' mod='btmailchimpecommerce'}</strong></span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
					<table class="table table-responsive">
						<thead>
						<tr>
							<th><span class="title_box center">{l s='Total' mod='btmailchimpecommerce'}</span></th>
							<th><span class="title_box center">{l s='Total of combinations' mod='btmailchimpecommerce'}</span></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td class="col-xs-12 col-sm-12 col-md-6 col-lg-6 center">{$iTotal|intval}</td>
							<td class="col-xs-12 col-sm-12 col-md-6 col-lg-6 center">{$iTotalAll|intval}</td>
						</tr>
						</tbody>
					</table>
					<div class="clr_5"></div>
					<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Products with many combinations may take more time to be synchronized.' mod='btmailchimpecommerce'}</span>
				</div>

				<div class="clr_20"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='To avoid server resources limitations issues, the products are sent in batches. Each batch contains the number of products indicated here.' mod='btmailchimpecommerce'}"><strong>{l s='Number of products per batch:' mod='btmailchimpecommerce'}</strong></span>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<input type="text" id="bt_item_per_cycle" name="bt_item_per_cycle" size="25" value="{if $iTotal < $iItemCycle}{$iTotal|intval}{else}{$iItemCycle|intval}{/if}" />
						</div>
						<div class="clr_5"></div>
					<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='First, leave the default value indicated and, if you get synch errors, lower the value.' mod='btmailchimpecommerce'}</span>
					</div>
				</div>

								
				{* USE CASE - WE OFFER TO THE MERCHANT TO UPDATE HIS PRODUCT CATLAOG AND NOT CREATE NEW PRODUCT ONLY INTO MAILCHIMP *}
				<div style="display: {if !empty($bAlreadySync)}block{else}none{/if};">
					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose if you only want to synchronize new products or if you only want to update existing products (in that second case, new ones won\'t be synchronized)' mod='btmailchimpecommerce'}">
							<strong>{l s='During this resynching:' mod='btmailchimpecommerce'}</strong>
						</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<select name="bt_new_sync_way" id="bt_new_sync_way">
									<option value="new" selected="selected">{l s='Only sync the new products' mod='btmailchimpecommerce'}</option>
									<option value="update">{l s='Only update existing products' mod='btmailchimpecommerce'}</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="clr_10"></div>

				<div id="bt_content_sync" style="display: none;" class="alert alert-info col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="button" class="close" onclick="$('#bt_content_sync').hide();">×</button>
					<h4>{l s='Synchronize your products' mod='btmailchimpecommerce'}</h4>
					<table>
						<tr>
							<td><b>{l s='Number of synchronized products:' mod='btmailchimpecommerce'}</b>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="text" size="5" name="bt_regen_counter" id="bt_regen_counter" value="0" />&nbsp;</td>
							<td>&nbsp;</td>
							<td>{l s='out of' mod='btmailchimpecommerce'}&nbsp;{$iTotal|intval} ({l s='products in total' mod='btmailchimpecommerce'})</td>
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
					<a class="btn btn-success btn-lg" id="bt_sync-button" href="javascript:void(0);" onclick="if($('#bt_item_per_cycle').val() == 0 || $('#bt_item_per_cycle').val() == ''){literal}{{/literal}alert('{l s='You must fill in a value for the number of products per batch!' mod='btmailchimpecommerce'}'); return false;{literal}}{/literal};aProductSync.iStep=$('#bt_item_per_cycle').val();aProductSync.sNewSyncType=$('#bt_new_sync_way option:selected').val();$('#bt_content_sync').show();oMailchimp.synchronizeData(aProductSync);"><span class="icon-refresh"></span>&nbsp;{l s='Synchronize' mod='btmailchimpecommerce'}</a>
				</div>
			</div>
		</form>

		<div class="clr_10"></div>

		<div id="bt_loading_div_sync" style="display: none;">
			<div class="clr_5"></div>
			<div class="alert alert-info">
				<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
				<p style="text-align: center !important;">{l s='Your products synchronization is in progress...' mod='btmailchimpecommerce'}</p>
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
<div id="bt_div_product_catalog_batch" class="autoscroll">
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
		<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_batch_form" name="bt_batch_form">
			<input type="hidden" name="sAction" value="{$aQueryParams.batch.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.batch.type|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="bt_sync_type" value="product" />

			<h2>{l s='Products synchronization' mod='btmailchimpecommerce'}</h2>
			<div class="clr_hr"></div>
			<div class="clr_10"></div>

			{if empty($bCanClose)}
				<div class="clr_5"></div>

				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-warning">
							<b>{l s='The products synchronization has begun, please just wait for the end of batches sending: the window will refresh itself when it\'s finished.' mod='btmailchimpecommerce'}</b>
							<div class="clr_10"></div>
							<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
							<div class="center"><strong><span id="bt_countdown_product">{$iRefreshWaitingTime|intval}</span></strong> {l s='seconds' mod='btmailchimpecommerce'}</div>
						</div>
					</div>
				</div>
			{/if}

			{if !empty($iProcessed)}
				<div class="clr_5"></div>

				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="alert alert-success">
							<b>{$iProcessed|intval}&nbsp;{l s='products sent to MailChimp for processing' mod='btmailchimpecommerce'}</b>
						</div>
					</div>
				</div>
			{/if}

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<strong>{l s='Your MailChimp list name:' mod='btmailchimpecommerce'}</strong>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$aListSyncForm.sListName|escape:'htmlall':'UTF-8'}" class="disabled" />
					</div>
				</div>
			</div>

			<h2>{l s='Products processing' mod='btmailchimpecommerce'}</h2>
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
								<th><span class="title_box center">{l s='Range of products processed' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This allows you to know which range of products you have to synchronize again if errors are returned.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
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
							{l s='There isn\'t any batch related to this list!' mod='btmailchimpecommerce'}
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
				<div class="alert alert-warning">
					{l s='The execution of all the batches is finished. HOWEVER, to fully finish the process, please click on the "Finalize products sync" green button below.' mod='btmailchimpecommerce'}
				</div>
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
					<p style="text-align: center !important;">{l s='The form to resynchronize products in error will soon be displayed...' mod='btmailchimpecommerce'}</p>
				</div>
			</div>

			<div class="clr_5"></div>

			<div class="center">
				{if !empty($bCanClose)}
					<button id="bt_btn_finalize_product" class="btn btn-success btn-lg" onclick="oMailchimp.form('bt_batch_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_batch_form', 'bt_batch_form', false, false, oBatchCallBack, 'batch', 'batch_finalize');return false;">{l s='Finalize products sync' mod='btmailchimpecommerce'}</button>
				{else}
					<button id="bt_btn_refresh_product" class="btn btn-success btn-lg" onclick="$('#bt_loading_div_batch_reload').show();oMailchimp.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sReloadUrlParams|escape:'htmlall':'UTF-8'}', 'bt_div_product_catalog_batch', 'bt_div_product_catalog_batch', false, false, 'batch_reload', null, null);return false;">{l s='Refresh details' mod='btmailchimpecommerce'}</button>
				{/if}
			</div>
		</form>

		<div class="clr_5"></div>

		<div id="bt_loading_div_batch_finalize" style="display: none;">
			<div class="clr_5"></div>
			<div class="alert alert-info">
				<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
				<p style="text-align: center !important;">{l s='The finalization of products synchronization is in progress...' mod='btmailchimpecommerce'}</p>
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
			oMailchimp.chrono('oMailchimp',{/literal}{$iRefreshWaitingTime|intval}{literal}, '#bt_countdown_product', '#bt_btn_refresh_product', 'click');
			{/literal}
			{else}
			$('#bt_btn_finalize_product').trigger('click');
			{/if}
			{literal}
		</script>
		{/literal}
	</div>
</div>
{/if}
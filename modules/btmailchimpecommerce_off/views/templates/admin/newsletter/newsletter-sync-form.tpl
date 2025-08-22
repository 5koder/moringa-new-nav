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
	<div id="bt_div_newsletter_sync_form">
		<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
			<script type="text/javascript">
				{literal}
				var aNewsletterSync = {
					'sURI' : '{/literal}{$sURI nofilter}{literal}',
					'sParams' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.newsletterSync.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.newsletterSync.type|escape:'htmlall':'UTF-8'}{literal}',
					'sSyncType' : '{/literal}{$sSyncType|escape:'htmlall':'UTF-8'}{literal}',
					'sListId' : '{/literal}{$aListSyncForm.id}{literal}',
					'sStoreId' : '{/literal}{$aListSyncForm.store_id}{literal}',
					'iStep' : 0,
					'iFloor' : 0,
					'iTotal' : {/literal}{$iTotal|intval}{literal},
					'iProcess' : 0,
					'sNewSyncType' : '',
					'bOldListSync' : {/literal}{if !empty($bOldSync) && !empty($aOldLists)}1{else}0{/if}{literal},
					'sDisplayedCounter' : '#bt_regen_counter',
					'sDisplayedBlock' : '#bt_content_sync',
					'sDisplayTotal' : '#bt_total_processed',
					'sLoaderBar' : '#bt_loader_bar',
					'sErrorContainer' : 'ajax_sync_error',
					'oCallback' : [{
						'name' : 'updateSyncForm',
						'url' : '{/literal}{$sURI nofilter}{literal}',
						'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType=syncForm&type=newsletter',
						'toShow' : 'bt_div_newsletter_sync_form',
						'toHide' : 'bt_div_newsletter_sync_form',
						'bFancybox' : false,
						'bFancyboxActivity' : false,
						'sLoadbar' : null,
						'sScrollTo' : null,
						'oCallBack' : {}
					}]
				};
				{/literal}
			</script>

			<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_newsletter_sync_form" name="bt_newsletter_sync_form">
				<input type="hidden" name="sAction" value="{$aQueryParams.newsletterSync.action|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sType" value="{$aQueryParams.newsletterSync.type|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="bt_sync_type" value="newsletter" />

				{* use case - first sync *}
				<h2>{if !empty($bOldSync) && !empty($aOldLists)}{l s='Old lists members migration' mod='btmailchimpecommerce'}{else}{l s='Users e-mail addresses synchronization' mod='btmailchimpecommerce'}{/if}</h2>

				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				{if !empty($bAlreadySync)}
					<div class="clr_10"></div>
					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-warning">
								{* USE CASE - migration of the old list members *}
								{if !empty($bOldSync) && !empty($aOldLists)}
									{l s='Here are your old lists configured with the previous module version:' mod='btmailchimpecommerce'}
									<div class="clr_5"></div>
									{foreach from=$aOldLists name=list key=iPos item=aList}
										<strong>{$aList.name|escape:'htmlall':'UTF-8'} ({$aList.language|escape:'htmlall':'UTF-8'})</strong>
										<div class="clr_5"></div>
									{/foreach}
									{* USE CASE - message for giving the possibility to redo the users export *}
								{else}
									{l s='Your users e-mail addresses have already been synchronized to MailChimp on the:' mod='btmailchimpecommerce'}&nbsp;<b>{$sDateCreated|escape:'htmlall':'UTF-8'}</b>  ({l s='Last update:' mod='btmailchimpecommerce'} {$sDateUpdated|escape:'htmlall':'UTF-8'})
									<div class="clr_10"></div>
									{l s='However, you can always manually resynchronize your users e-mail addresses by clicking on the green button below.' mod='btmailchimpecommerce'}
								{/if}
							</div>
						</div>
					</div>

					<div class="clr_10"></div>
				{/if}

				{* USE CASE - display the form settings only if there are some customers in this language to synchronize *}
				{if !empty($iTotal)}
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<strong>{l s='Your MailChimp list name:' mod='btmailchimpecommerce'}</strong>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$aListSyncForm.name|escape:'htmlall':'UTF-8'}" class="disabled" />
						</div>
					</div>

					<div class="clr_5"></div>

					<h3>{l s='Preferences for users e-mail addresses synchronization' mod='btmailchimpecommerce'}</h3>
					<div class="clr_hr"></div>
					<div class="clr_10"></div>

					<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='The count here indicates the number of e-mail addresses to be exported'  mod='btmailchimpecommerce'}"><strong>{l s='Total of e-mail addresses:' mod='btmailchimpecommerce'}</strong></span>
					</label>
					
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<input type="text" id="bt_nb_users" name="bt_nb_users" size="25" value="{$iTotal|intval}" class="disabled" />
					</div>

					<div class="clr_10"></div>

					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<span class="label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='To avoid server resources limitations issues, e-mail addresses are sent in batches. Each batch contains the number of e-mail addresses indicated here.' mod='btmailchimpecommerce'}"><strong>{l s='Number of e-mail addresses per batch:' mod='btmailchimpecommerce'}</strong></span>
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
						<h4>{l s='Users e-mail addresses synchronization' mod='btmailchimpecommerce'}</h4>
						<table>
							<tr>
								<td><b>{l s='Number of synchronized e-mail addresses:' mod='btmailchimpecommerce'}</b>&nbsp;</td>
								<td>&nbsp;</td>
								<td><input type="text" size="5" name="bt_regen_counter" id="bt_regen_counter" value="0" />&nbsp;</td>
								<td>&nbsp;</td>
								<td>{l s='out of' mod='btmailchimpecommerce'}&nbsp;{$iTotal|intval} ({l s='e-mail addresses in total' mod='btmailchimpecommerce'})</td>
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
						<a class="btn btn-success btn-lg" id="bt_sync_button" href="javascript:void(0);" onclick="if($('#bt_item_per_cycle').val() == 0 || $('#bt_item_per_cycle').val() == ''){literal}{{/literal}alert('{l s='You must fill in a value for the number of e-mail addresses per batch!' mod='btmailchimpecommerce'}'); return false;{literal}}{/literal};aNewsletterSync.iStep=$('#bt_item_per_cycle').val();$('#bt_content_sync').show();oMailchimp.synchronizeData(aNewsletterSync);"><span class="icon-refresh"></span>&nbsp;{l s='Synchronize' mod='btmailchimpecommerce'}</a>
					</div>
				{else}
					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-warning">
								{l s='There are no e-mail addresses to synchronize!'  mod='btmailchimpecommerce'}
							</div>
						</div>
					</div>
				{/if}
			</form>

			<div class="clr_10"></div>

			<div id="bt_loading_div_sync" style="display: none;">
				<div class="clr_5"></div>
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='Your e-mail addresses synchronization is in progress...' mod='btmailchimpecommerce'}</p>
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
	<div id="bt_div_newsletter_list_batch">
		<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap autoscroll">
			<script type="text/javascript">
				{literal}
				var oBatchCallBack =
					[{
						'name' : 'updateNewsletter',
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
							'name' : 'updateSignup',
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
						},
						{
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
						}];
				{/literal}
			</script>

			<form class="form-horizontal col-xs-12  col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_batch_form" name="bt_batch_form">
				<input type="hidden" name="sAction" value="{$aQueryParams.batch.action|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sType" value="{$aQueryParams.batch.type|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="bt_sync_type" value="newsletter" />

				<h2>{l s='Users e-mail addresses synchronization' mod='btmailchimpecommerce'}</h2>
				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				{if empty($bCanClose)}
					<div class="clr_5"></div>

					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-warning">
								<b>{l s='The e-mail addresses synchronization has begun, please just wait for the end of batches sending: the window will refresh itself when it\'s finished.' mod='btmailchimpecommerce'}</b>
								<div class="clr_10"></div>
								<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
								<div class="center"><strong><span id="bt_countdown_newsletter">{$iRefreshWaitingTime|intval}</span></strong> {l s='seconds' mod='btmailchimpecommerce'}</div>
							</div>
						</div>
					</div>
				{/if}

				{if !empty($iProcessed)}
					<div class="clr_5"></div>

					<div class="form-group">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="alert alert-success">
								<b>{$iTotal|intval}&nbsp;{l s='e-mail addresses sent to MailChimp for processing' mod='btmailchimpecommerce'}</b>
							</div>
						</div>
					</div>
				{/if}

				<div class="clr_5"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Your MailChimp list name:' mod='btmailchimpecommerce'}</strong>
					</label>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$aListSyncForm.name|escape:'htmlall':'UTF-8'}" class="disabled" />
						</div>
					</div>
				</div>

				<div class="clr_5"></div>

				<h2>{l s='E-mail addresses processing' mod='btmailchimpecommerce'}</h2>
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
									<th><span class="title_box center">{l s='Range of e-mail addresses processed' mod='btmailchimpecommerce'}&nbsp;<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This allows you to know which range of e-mail addresses you have to synchronize again if errors are returned.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span></span></th>
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
						{l s='Some data batches are still in progress in MailChimp side, simply wait and refresh the progress details by clicking on the button below until you\'ve got all the batches flagged as "done". If you close the popup by accident, you will find the progress of data processing by clicking again on the synching button in the previous window.' mod='btmailchimpecommerce'}
					</div>
				{else}
					{l s='The execution of all the batches is finished. HOWEVER, to fully finish the process, please click on the "Finalize e-mail addresses sync" green button below.' mod='btmailchimpecommerce'}
				{/if}

				<div id="bt_loading_div_batch_reload" style="display: none;">
					<div class="clr_5"></div>
					<div class="alert alert-info">
						<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
						<p style="text-align: center !important;">{l s='The batches list is about to be refreshed...' mod='btmailchimpecommerce'}</p>
					</div>
				</div>

				<div id="bt_loading_div_batch-resync" style="display: none;">
					<div class="clr_5"></div>
					<div class="alert alert-info">
						<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
						<p style="text-align: center !important;">{l s='The form to resynchronize e-mail addresses in error will soon be displayed...' mod='btmailchimpecommerce'}</p>
					</div>
				</div>

				<div class="clr_5"></div>

				<div class="center">
					{if !empty($bCanClose)}
						<button id="bt_btn_finalize_newsletter" class="btn btn-success btn-lg" onclick="oMailchimp.form('bt_batch_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_batch_form', 'bt_batch_form', false, false, oBatchCallBack, 'batch', 'batch_finalize');return false;">{l s='Finalize e-mail addresses sync' mod='btmailchimpecommerce'}</button>
					{else}
						<button id="bt_btn_refresh_newsletter" class="btn btn-success btn-lg" onclick="oMailchimp.clearTimeOut();$('#bt_loading_div_batch_reload').show();oMailchimp.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sReloadUrlParams|escape:'htmlall':'UTF-8'}', 'bt_div_newsletter_list_batch', 'bt_div_newsletter_list_batch', false, false, 'batch_reload', null, null);return false;">{l s='Refresh details' mod='btmailchimpecommerce'}</button>
					{/if}
				</div>
			</form>

			<div class="clr_5"></div>

			<div id="bt_loading_div_batch_finalize" style="display: none;">
				<div class="clr_5"></div>
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The finalization of e-mail addresses synchronization is in progress...' mod='btmailchimpecommerce'}</p>
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
				oMailchimp.chrono('oMailchimp',{/literal}{$iRefreshWaitingTime|intval}{literal}, '#bt_countdown_newsletter', '#bt_btn_refresh_newsletter', 'click');
				{/literal}
				{else}
				$('#bt_btn_finalize_newsletter').trigger('click');
				{/if}
				{literal}
			</script>
			{/literal}
		</div>
	</div>
{/if}
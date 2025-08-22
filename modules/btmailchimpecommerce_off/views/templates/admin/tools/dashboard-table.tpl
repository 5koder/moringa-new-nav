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

{if isset($bTableDisplay) && $bTableDisplay != false}
	<div class="clr_hr"></div>
	<div class="clr_10"></div>

	<h4><b>{l s='Summary of e-commerce data syncronizations for the past' mod='btmailchimpecommerce'} {$iDelay|intval} {l s='days' mod='btmailchimpecommerce'}</b></h4>

	<div class="clr_10"></div>

	{if !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
		<div class="clr_10"></div>
	{/if}

	<div class="form-group">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			{if !empty($aListDashboard)}
				<table class="table table-striped table-responsive">
					<thead>
					<tr>
						<th><span class="title_box center">{l s='Sync type' mod='btmailchimpecommerce'}</span></th>
						<th><span class="title_box center">{l s='Total OK' mod='btmailchimpecommerce'}</span></th>
						<th><span class="title_box center">{l s='Total KO' mod='btmailchimpecommerce'}</span></th>
						<th><span class="title_box center">{l s='See details' mod='btmailchimpecommerce'}</span></th>
					</tr>
					</thead>
					<tbody>

					{foreach from=$aListDashboard.aSyncData name=sync key=sType item=aSync}
						<tr>
							<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center">
								{$sType|ucfirst|escape:'htmlall':'UTF-8'}
							</td>
							<td class="col-xs-12 col-sm-12 col-md-3 col-lg-2 center {if !empty($aSync.ok)}success{/if}">
								{if !empty($aSync.ok)}{$aSync.ok|intval}{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
							</td>
							<td class="col-xs-12 col-sm-12 col-md-3 col-lg-2 center {if !empty($aSync.ko)}danger{/if}">
								{if !empty($aSync.ko)}{$aSync.ko|intval}{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
							</td>
							<td class="col-xs-12 col-sm-12 col-md-3 col-lg-2 center info pointer">
								<span onclick="$('#bt_sync_detail_{$smarty.foreach.store.iteration|intval}{$smarty.foreach.sync.iteration|intval}').toggle(800);"><i class="icon icon-search"></i></span>
							</td>
						</tr>
						<tr id="bt_sync_detail_{$smarty.foreach.store.iteration|intval}{$smarty.foreach.sync.iteration|intval}" style="display: none;">
							{if empty($aSync.details.ok) && empty($aSync.details.ko)}
								<td colspan="5" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center warning">
									{l s='There isn\'t any data synchonized to MailChimp for this kind of data.' mod='btmailchimpecommerce'}
								</td>
							{else}
								<td colspan="5" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center">
									<table class="table table-striped table-responsive" style="margin-top: 10px !important;">
										<thead>
										<tr>
											<th><span class="title_box center">{l s='ID' mod='btmailchimpecommerce'}</span></th>
											<th><span class="title_box center">{l s='Details' mod='btmailchimpecommerce'}</span></th>
											<th><span class="title_box center">{l s='Adding date' mod='btmailchimpecommerce'}</span></th>
											<th><span class="title_box center">{l s='Updating date' mod='btmailchimpecommerce'}</span></th>
											<th><span class="title_box center">{l s='Response of the MC API' mod='btmailchimpecommerce'}</span></th>
										</tr>
										</thead>
										<tbody>
										{if !empty($aSync.details.ok)}
											{foreach from=$aSync.details.ok name=ok key=iDetailKey item=aDetailOk}
												<tr class="center success">
													<td class="col-xs-12 col-sm-12 col-md-1 col-lg-1 center">
														{$aDetailOk.id|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center">
														{if !empty($aDetailOk.name)}{$aDetailOk.name|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
														{if !empty($aDetailOk.link)}{if !empty($aDetailOk.name)}- {/if}<a href="{$aDetailOk.link nofilter}" target="_blank"><i class="icon icon-eye"></i></a>{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
														{$aDetailOk.date_add|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
														{$aDetailOk.date_upd|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-4 col-lg-4 center">
														{$aDetailOk.detail|escape:'htmlall':'UTF-8'}
													</td>
												</tr>
											{/foreach}
										{/if}
										{if !empty($aSync.details.ko)}
											{foreach from=$aSync.details.ko name=ko key=iDetailKey item=aDetailKo}
												<tr class="center danger">
													<td class="col-xs-12 col-sm-12 col-md-1 col-lg-1 center">
														{$aDetailKo.id|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center">
														{if !empty($aDetailKo.name)}{$aDetailKo.name|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
														{if !empty($aDetailKo.link)}{if !empty($aDetailKo.name)}- {/if}<a href="{$aDetailKo.link nofilter}" target="_blank"><i class="icon icon-eye"></i></a>{else}{l s='N/A' mod='btmailchimpecommerce'}{/if}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
														{$aDetailKo.date_add|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-2 col-lg-2 center">
														{$aDetailKo.date_upd|escape:'htmlall':'UTF-8'}
													</td>
													<td class="col-xs-12 col-sm-12 col-md-4 col-lg-4 center">
														{if $sType == 'product' && empty($aDetailKo.detail)}{l s='The product was created only but not already synchronized to MC' mod='btmailchimpecommerce'}{else}{$aDetailKo.detail|escape:'htmlall':'UTF-8'}{/if}
													</td>
												</tr>
											{/foreach}
										{/if}
										</tbody>
									</table>
								</td>
							{/if}
						</tr>
					{/foreach}
					</tbody>
				</table>
			{else}
				<div class="alert alert-warning">
					{l s='Oops, there aren\'t any data synchonized to MailChimp!. You may have not finished to do your manual synching, please follow our documentation to understand when the automatic data will be synchronized as orders, carts, customers and products.' mod='btmailchimpecommerce'}
				</div>
			{/if}
		</div>
	</div>
{/if}
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
		<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI nofilter}" method="post" id="bt_form_dashboard" name="bt_form_dashboard" onsubmit="oMailchimp.form('bt_form_dashboard', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_dashboard_table', 'bt_dashboard_table', false, false, null, 'dashboard_table', 'dashboard_table');return false;">
			<input type="hidden" name="sAction" value="{$aQueryParams.synchingHistory.action|escape:'htmlall':'UTF-8'}" />
			<input type="hidden" name="sType" value="{$aQueryParams.synchingHistory.type|escape:'htmlall':'UTF-8'}" />
			
			<div class="clr_10"></div>
			
			<div class="form-group">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="alert alert-info">
						{l s='This dashboard allows you to see which synchronizations have been successfully performed over a given period of time and which have failed. You will be able to see the MailChimp API responses for each synchronization.' mod='btmailchimpecommerce'}
					</div>
				</div>
			</div>

			<div class="clr_10"></div>

			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select the time period (in days, from today) on which to check the synchronizations.' mod='btmailchimpecommerce'}".>
					<strong>{l s='Number of days to check synchronizations on' mod='btmailchimpecommerce'}</strong>
					</span>
				</label>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<div class="input-group">
							<span class="input-group-addon"><i class="icon icon-clock-o">&nbsp;</i></span>
							<select name="bt_dashboard_delay" id="bt_dashboard_delay">
								<option value="0"> -- </option>
								<option value="15">{l s='the last 15 days' mod='btmailchimpecommerce'}</option>
								<option value="30">{l s='the last 30 days' mod='btmailchimpecommerce'}</option>
								<option value="45">{l s='the last 45 days' mod='btmailchimpecommerce'}</option>
								<option value="60">{l s='the last 60 days' mod='btmailchimpecommerce'}</option>
								<option value="75">{l s='the last 75 days' mod='btmailchimpecommerce'}</option>
								<option value="90">{l s='the last 90 days' mod='btmailchimpecommerce'}</option>
							</select>
						</div>
					</div>
					&nbsp;<input type="button" name="bt_dash-board-button" value="{l s='Search' mod='btmailchimpecommerce'}" class="btn btn-success" onclick="oMailchimp.form('bt_form_dashboard', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_dashboard_table', 'bt_dashboard_table', false, false, null, 'dashboard_table', 'dashboard_table');return false;" />
				</div>
			</div>

			<div class="clr_20"></div>

			<div id="bt_dashboard_table">
				{include file="`$sDashboardTableInclude`" bTableDisplay=false}
			</div>

			<div id="bt_loading_div_dashboard_table" style="display: none;">
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The dashboard data will soon be displayed...' mod='btmailchimpecommerce'}</p>
				</div>
			</div>

			<div class="clr_20"></div>
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
					<div id="bt_error_dashboard_table"></div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"><button class="btn btn-default pull-right" onclick="oMailchimp.form('bt_form_dashboard', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_dashboard_table', 'bt_dashboard_table', false, false, null, 'dashboard_table', 'dashboard_table');return false;"><i class="process-icon-save"></i>{l s='Search' mod='btmailchimpecommerce'}</button></div>
			</div>
		</form>
	{else}
		<div class="form-group">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="alert alert-warning">
					{l s='To be able to use this tab: please first select a list (or create a new one), do the users synchronization and wait for the module to do the first automatic synchronizations of new e-commerce data or new subscribers.' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>

		<div class="clr_20"></div>
	{/if}
</div>
<div class="clr_20"></div>
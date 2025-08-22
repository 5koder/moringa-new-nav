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

{if !empty($aErrors)}
	<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"></label>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
		{include file="`$sErrorInclude`" aErrors=$aErrors}
	</div>
	<div class="clr_20"></div>
{/if}
{if !empty($aEmailExclusions)}
	{if !empty($bUpdate)}
		<div class="validate-message">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"></label>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<div class="alert alert-success">
					{l s='Your new domain name to be excluded has been added with success' mod='btmailchimpecommerce'}
				</div>
			</div>
		</div>
		<div class="clr_20"></div>
		{literal}
		<script type="text/javascript">
			$(".validate-message").delay(5000).slideUp();
		</script>
		{/literal}
	{/if}
	{if !empty($bDelete) && !empty($sDeletedName)}
		<div class="validate-message">
			<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2"></label>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<div class="alert alert-success">
					{l s='The following domain name has been deleted with success:' mod='btmailchimpecommerce'} "{$sDeletedName|escape:'htmlall':'UTF-8'}"
				</div>
			</div>
		</div>
		<div class="clr_20"></div>
		{literal}
		<script type="text/javascript">
			$(".validate-message").delay(5000).slideUp();
		</script>
		{/literal}
	{/if}
	<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
		<b>{l s='List of excluded domain names' mod='btmailchimpecommerce'}</b>
	</label>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
		<table class="table table-striped table-responsive">
			<thead>
			<tr>
				<th><span class="title_box center">{l s='domain name' mod='btmailchimpecommerce'}</span></th>
				<th><span class="title_box center">{l s='Delete it?' mod='btmailchimpecommerce'}</span></th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$aEmailExclusions name=store key=iKey item=sExclusionDomain}
				<tr>
					<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center">
						{$sExclusionDomain|escape:'htmlall':'UTF-8'}
					</td>
					<td class="col-xs-12 col-sm-12 col-md-3 col-lg-3 center pointer">
						<i class="icon-trash" style="font-size:20px;" title="{l s='delete' mod='btmailchimpecommerce'}" onclick="check = confirm('{l s='Are you sure you want to delete this excluded domain name' mod='btmailchimpecommerce'} ? {l s='It will be definitively removed from your database' mod='btmailchimpecommerce'}');if(!check)return false;$('#bt_loading_div_exclusion_mails').show();$('#bt_exclusion_mails_error').slideUp();oMailchimp.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionDelete.type|escape:'htmlall':'UTF-8'}&iExcludedMail={$iKey|intval}', 'bt_div_exclusion_mails', 'bt_div_exclusion_mails', false, false, 'exclusion_mails', false, null);" ></i>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">
		<b>{l s='List of excluded domain names' mod='btmailchimpecommerce'}</b>
	</label>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
		<div class="alert alert-warning xerror-message">
			{l s='There isn\'t any excluded domain name' mod='btmailchimpecommerce'}
		</div>
	</div>
	{literal}
	<script type="text/javascript">
		$(".error-message").delay(5000).slideUp();
	</script>
	{/literal}
{/if}
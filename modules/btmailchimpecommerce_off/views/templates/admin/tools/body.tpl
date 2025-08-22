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

{if !empty($bUpdate)}
	<div class="clr_10"></div>
	{include file="`$sConfirmInclude`"}
{elseif !empty($aErrors)}
	<div class="clr_10"></div>
	{include file="`$sErrorInclude`"}
{/if}

<ul class="nav nav-tabs" id="bt_sync_status">
	<li {if empty($sTpl) || (!empty($sTpl) && $sTpl == 'history') && empty($sSubTpl)}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}history{/if}').val('');">
		<a data-toggle="tab" href="#history"><i class="icon-bar-chart"></i>&nbsp;{l s='Synching history' mod='btmailchimpecommerce'}</a>
	</li>
	<li {if !empty($sSubTpl) && $sSubTpl == 'diagnostic'}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}history{/if}').val('diagnostic');">
		<a data-toggle="tab" href="#diagnostic"><i class="icon icon-refresh"></i>&nbsp;{l s='Diagnostic tool' mod='btmailchimpecommerce'}</a>
	</li>
</ul>

<div class="tab-content bt_my_tabs_content background-white">
	{************ SYNC HISTORY TAB ************}
	<div class="tab-pane{if empty($sTpl) || (!empty($sTpl) && $sTpl == 'history') && empty($sSubTpl)} active{/if}" id="history">
		{include file="`$sHistoryInclude`"}
	</div>

	{************ DIAGNOSTIC TOOL TAB ************}
	<div class="tab-pane {if !empty($sSubTpl) && $sSubTpl == 'diagnostic'} active{/if}" id="diagnostic">
		{include file="`$sDiagnosticInclude`"}
	</div>

	<div class="clr_20"></div>
</div>
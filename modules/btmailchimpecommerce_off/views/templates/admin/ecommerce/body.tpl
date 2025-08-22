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

<ul class="nav nav-tabs" id="bt_ecommerce">
	<li id="bt_sub_tab_ecommerce_config" {if empty($sTpl) || (!empty($sTpl) && $sTpl != 'sync') && empty($sSubTpl)}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl) && $sTpl == 'config'}{$sTpl|escape:'htmlall':'UTF-8'}{else}config{/if}').val('');">
		<a data-toggle="tab" href="#bt_div_ecommerce_config"><i class="icon-wrench"></i>&nbsp;{l s='Configuration' mod='btmailchimpecommerce'}</a>
	</li>
	<li id="bt_sub_tab_ecommerce_sync" {if !empty($sSubTpl) && $sSubTpl == 'sync'}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}sync{/if}').val('sync');">
		<a data-toggle="tab" href="#bt_div_ecommerce_sync"><i class="icon icon-refresh"></i>&nbsp;{l s='Synching' mod='btmailchimpecommerce'}</a>
	</li>
</ul>

<div class="tab-content bt_my_tabs_content background-white">
	{************ CONFIGURATION TAB ************}
	<div class="tab-pane{if empty($sTpl) || (!empty($sTpl) && $sTpl != 'sync') && empty($sSubTpl)} active{/if}" id="bt_div_ecommerce_config">
		{include file="`$sEcommerceConfigInclude`"}
	</div>

	{************ SYNC TAB ************}
	<div class="tab-pane {if !empty($sSubTpl) && $sSubTpl == 'sync'} active{/if}" id="bt_div_ecommerce_sync">
		{include file="`$sEcommerceSyncInclude`"}
	</div>

	<div class="clr_20"></div>
</div>
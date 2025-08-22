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
	{include file="`$sConfirmInclude`" sConfirmId="bt_nl_module_config_success"}
{elseif !empty($aErrors)}
	<div class="clr_10"></div>
	{include file="`$sErrorInclude`"}
{/if}

<ul class="nav nav-tabs" id="bt_signup_forms">
	<li id="bt_sub_tab_nl_module_config" {if empty($sTpl) || (!empty($sTpl) && $sTpl == 'module') && empty($sSubTpl)}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}module{/if}').val('');">
		<a data-toggle="tab" href="#signup_module"><i class="fa fa-sign-in"></i>&nbsp;{l s='newsletter module' mod='btmailchimpecommerce'}</a>
	</li>
	<li id="bt_sub_tab_signup_mc_config" {if !empty($sSubTpl) && $sSubTpl == 'mailchimp'}class="active"{/if} onclick="$('#bt_sub-template-{if !empty($sTpl)}{$sTpl|escape:'htmlall':'UTF-8'}{else}module{/if}').val('mailchimp');">
		<a data-toggle="tab" href="#signup_mailchimp"><i class="fa fa-sign-in"></i>&nbsp;{l s='mailchimp sign-up form' mod='btmailchimpecommerce'}</a>
	</li>
</ul>

<div class="tab-content bt_my_tabs_content background-white">
	{************ NEWSLETTER MODULE FORM TAB ************}
	<div class="tab-pane{if empty($sTpl) || (!empty($sTpl) && $sTpl == 'module') && empty($sSubTpl)} active{/if}" id="signup_module">
		{include file="`$sNewsletterModuleInclude`"}
	</div>

	{************ MAILCHIMP FORM TAB ************}
	<div class="tab-pane {if !empty($sSubTpl) && $sSubTpl == 'mailchimp'} active{/if}" id="signup_mailchimp">
		{include file="`$sMcFormInclude`"}
	</div>

	<div class="clr_20"></div>
</div>
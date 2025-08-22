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

<!-- MCE - MC NL Signup form -->
{extends file='page.tpl'}

{block name='page_content'}
	<div id="{$sMceModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
		{* USE CASE - GOT ERRORS *}
		{if !empty($aErrors)}
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				{l s='Our apologies! Something went wrong and we are not able to display our newsletter sign-up form. Please try later!' mod='btmailchimpecommerce'}
			</div>
		{* USE CASE - DISPLAY THE NL SIGNUP FORM *}
		{else}
			<div id="mce">
				<style type="text/css">
					#mce label {literal}{text-align: left !important;}{/literal}
				</style>
				{$sNewsletterForm nofilter}
			</div>
		{/if}
	</div>
{/block}
<!-- /MCE - MC NL Signup form  -->
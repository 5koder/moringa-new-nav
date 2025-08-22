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

<link rel="stylesheet" type="text/css" href="{$smarty.const._MCE_URL_CSS|escape:'htmlall':'UTF-8'}admin.css">
<link rel="stylesheet" type="text/css" href="{$smarty.const._MCE_URL_CSS|escape:'htmlall':'UTF-8'}font-awesome.css">
<link rel="stylesheet" type="text/css" href="{$smarty.const._MCE_URL_CSS|escape:'htmlall':'UTF-8'}top.css">

<script type="text/javascript" src="{$smarty.const._MCE_URL_JS|escape:'htmlall':'UTF-8'}module.js"></script>
<script type="text/javascript" src="{$smarty.const._MCE_URL_JS|escape:'htmlall':'UTF-8'}top.js"></script>
<script type="text/javascript">
	// instantiate object
	var oMailchimp = oMailchimp || new oMceModule('{$sModuleName|escape:'htmlall':'UTF-8'}');
	var oMcaTop = oMcaTop || new oMcaTop('{$sModuleName|escape:'htmlall':'UTF-8'}');

	// get errors translation
	oMailchimp.msgs = {$oJsTranslatedMsg nofilter};

	// set URL of admin img
	oMailchimp.sImgUrl = '{$smarty.const._MCE_URL_IMG|escape:'htmlall':'UTF-8'}';

	{if !empty($sModuleURI)}
	// set URL of module's web service
	oMailchimp.sWebService = '{$sModuleURI nofilter}';
	{/if}
</script>



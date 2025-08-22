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

<div id="mce" class="bootstrap">
	<button type="button" class="close right" onclick="$(this).parent().slideUp()">×</button>
	<div class="clr_hr"></div>
	<div class="clr_10"></div>

	{if !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
		<div class="clr_10"></div>
	{else}
		<h4><b>{l s='Result of the request:' mod='btmailchimpecommerce'}</b></h4>

		{if empty($aResult.error)}
			<div class="alert alert-success">
				{l s='Your query returned the element requested with success, it means this information has been synchronized well by the module.' mod='btmailchimpecommerce'}
				<div class="clr_10"></div>
				{foreach from=$aResult key=sKey item=mElt}
					{if $sKey != '_links'}
						{if is_array($mElt)}
							<b>{$sKey|escape:'htmlall':'UTF-8'}</b>:<br />
							{foreach from=$mElt key=sSubKey item=mSubElt}
								{if !is_array($mSubElt)}
									<div class="padding-left-20"><b>{$sSubKey|escape:'htmlall':'UTF-8'}</b>: {$mSubElt|escape:'htmlall':'UTF-8'}</div>
								{else}
									<div class="padding-left-20">
										<b>{$sSubKey|escape:'htmlall':'UTF-8'}</b>:<br />
										{foreach from=$mSubElt key=sSubSubKey item=mSubSubElt}
											{if !is_array($mSubSubElt)}
											<div class="padding-left-20"><b>{$sSubSubKey|escape:'htmlall':'UTF-8'}</b>: {$mSubSubElt|escape:'htmlall':'UTF-8'}</div>
											{/if}
										{/foreach}
									</div>
								{/if}
							{/foreach}
						{else}
							<b>{$sKey|escape:'htmlall':'UTF-8'}</b>: {$mElt|escape:'htmlall':'UTF-8'}<br />
						{/if}
					{/if}
				{/foreach}
			</div>
		{else}
			<div class="alert alert-danger">
				{l s='The result of your query has returned an error, it means the data has never been synchronized to your Mailchimp API or something went wrong! Please find below the MailChimp API error or the internal server error. If you want to synchronize the data for the first time or one more time, you just need to click on the "Preview for synching" button below.' mod='btmailchimpecommerce'}
				<div class="clr_10"></div>
				<b>{$aResult.error|escape:'htmlall':'UTF-8'}</b> - {l s='code:' mod='btmailchimpecommerce'} {$aResult.code|intval}
			</div>

			<div class="clr_20"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center">
				<a class="btn btn-success fancybox.ajax" id="bt_search_resend" href="{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.searchPopup.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.searchPopup.type|escape:'htmlall':'UTF-8'}&bt_data_type={$sDataType|escape:'htmlall':'UTF-8'}&bt_elt_id={$sEltId|escape:'htmlall':'UTF-8'}&bt_elt_lang_id={$iLangId|intval}">{l s='Preview for synching' mod='btmailchimpecommerce'}</a>
			</div>

			{literal}
			<script type="text/javascript">
				$("#bt_search_resend").fancybox({
					'hideOnContentClick' : false,
					'autoDimensions' : true
				});
			</script>
			{/literal}
		{/if}
	{/if}
</div>
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

<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap">

	<div class="clr_10"></div>

	<h3>{l s='Result of your cURL over SSL test' mod='btmailchimpecommerce'}</h3>
	<div class="clr_hr"></div>
	<div class="clr_20"></div>

	<div class="form-group">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			{if !empty($bCurlSslCheck)}
				<div class="alert alert-success">
					{l s='Your test has been done with success!' mod='btmailchimpecommerce'}
				</div>
			{else}
				<div class="alert alert-danger">
					{l s='Your test failed. Ask your technical contact to install cURL over SSL on your server.' mod='btmailchimpecommerce'}
				</div>
			{/if}
		</div>
	</div>

	<div class="clr_20"></div>

	<div class="center">
		<button class="btn btn-success btn-lg" onclick="$.fancybox.close();location.reload();return false;">{l s='Close' mod='btmailchimpecommerce'}</button>
	</div>
</div>
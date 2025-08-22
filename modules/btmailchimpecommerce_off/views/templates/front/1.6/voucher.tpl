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

<!-- MCE - Automation voucher for one the voucher automation settings -->
<div id="{$sMceModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
	{* USE CASE - GOT ERRORS *}
	{if !empty($aErrors)}
		{include file="`$sErrorInclude`" aErrors=$aErrors}
		{if !empty(sLoginURI)}
			<div class="clr_10"></div>
			<p class="center"><a class="btn btn-primary" href="{$sLoginURI|escape:'htmlall':'UTF-8'}"><i class="icon-star-empty"></i> {l s='Log in/Sign up' mod='btmailchimpecommerce'}</a></p>
		{/if}
	{* USE CASE - DISPLAY THE VOUCHER CODE *}
	{else}
		<h1 class="page-subheading">{l s='Discount voucher:' mod='btmailchimpecommerce'} "{$aVoucher.name|escape:'htmlall':'UTF-8'}"</h1>

		<div class="clr_10"></div>

		<h2>{l s='About you:' mod='btmailchimpecommerce'}</h2>

		<div class="clr_10"></div>

		<div class="form-group">
			<label for="bt_firstname">{l s='First name:' mod='btmailchimpecommerce'}</label>
			<input class="form-control" id="disabledInput" type="text" placeholder="{$sFirstName|escape:'htmlall':'UTF-8'}" disabled style="width: 250px !important;">
			<div class="clr_20"></div>
			<label for="bt_lastname">{l s='Last name:' mod='btmailchimpecommerce'} </label>
			<input class="form-control" id="disabledInput" type="text" placeholder="{$sLastName|escape:'htmlall':'UTF-8'}" disabled style="width: 250px !important;">
		</div>

		<div class="clr_20"></div>

		<h2>{l s='Your voucher code:' mod='btmailchimpecommerce'}</h2>

		<div class="clr_10"></div>

		{* USE CASE - SOMETHING WENT WRONG AND THE CODE HAS NOT BEEN CREATED *}
		{if isset($aVoucherAdd.name) && empty($aVoucherAdd.name)}
			<div class="alert alert-danger col-xs-12 col-sm-12 col-md-12 col-lg-12" id="bt_confirm">
				{$aVoucherAdd.status|escape:'htmlall':'UTF-8'}
			</div>
		{else}
			{* USE CASE - CODE ALREADY CREATED *}
			{if !empty($bAlreadyCreated)}
				<div class="alert alert-info col-xs-12 col-sm-12 col-md-12 col-lg-12" id="bt_confirm">
					{l s='You already got this voucher code! You can find it in the vouchers page of your customer account. As a reminder, you will find the voucher\'s details below.' mod='btmailchimpecommerce'}
					{if !empty($bEmailSent)}
					<div class="clr_5"></div>
					{l s='An e-mail was just sent with the voucher code, you should receive it in a few moments.' mod='btmailchimpecommerce'}
					{/if}
				</div>
				{* USE CASE - CODE CREATED WITH SUCCESS *}
			{else}
				<div class="alert alert-success col-xs-12 col-sm-12 col-md-12 col-lg-12" id="bt_confirm">
					{l s='Your voucher code has just been created with success. You\'ll find the details below.' mod='btmailchimpecommerce'}
					{if !empty($bEmailSent)}
					<div class="clr_5"></div>
					{l s='An e-mail was just sent with the voucher code, you should receive it in a few moments.' mod='btmailchimpecommerce'}
					{/if}
				</div>
			{/if}

			<div class="clr_10"></div>

			<div class="form-group">
				<label for="bt_voucher-code">{l s='Code:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" type="text" value="{$aVoucher.code|escape:'htmlall':'UTF-8'}" style="width: 250px !important;">
				<div class="clr_20"></div>
				<label for="bt_voucher-name">{l s='Description:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" id="disabledInput" type="text" placeholder="{$aVoucher.name|escape:'htmlall':'UTF-8'}" disabled style="width: 250px !important;">
				<div class="clr_20"></div>
				<label for="bt_voucher-amount">{l s='Amount:' mod='btmailchimpecommerce'}</label>
				<input class="form-control" id="disabledInput" type="text" placeholder="{$aVoucher.displayAmount|escape:'htmlall':'UTF-8'}" disabled style="width: 250px !important;">
				<div class="clr_20"></div>
				<label for="bt_voucher-amount">{l s='Validity:' mod='btmailchimpecommerce'}</label>
				<input class="form-control col-xs-12 col-sm-12 col-md-12 col-lg-12" id="disabledInput" type="text" placeholder="{$aVoucher.validity|intval} {l s='days' mod='btmailchimpecommerce'}" disabled>
			</div>

			{* USE CASE - if the minimum amount filled or the cumulative with others or kind of reduction to false *}
			{if !empty($aVoucher.minimum)
				|| empty($aVoucher.cumulativeOther)
				|| empty($aVoucher.cumulativeReduc)
			}
				<div class="clr_10"></div>
				<div class="alert alert-warning col-xs-12 col-sm-12 col-md-12 col-lg-12" id="bt_confirm">
					{l s='Please just note your voucher code has some restrictions, please find them below:' mod='btmailchimpecommerce'}
					{if !empty($aVoucher.minimum)}
						<div class="clr_5"></div>
						- {l s='The voucher code is only valid for a minimum purchase of:' mod='btmailchimpecommerce'} "{if $aVoucher.sign != '€'}{$aVoucher.sign|escape:'htmlall':'UTF-8'}{/if}{$aVoucher.minimum|floatval}{if $aVoucher.sign == '€'}{$aVoucher.sign|escape:'htmlall':'UTF-8'}{/if}"
					{/if}
					{if empty($aVoucher.cumulativeOther)}
						<div class="clr_5"></div>
						- {l s='The voucher code is not cumulative with others codes' mod='btmailchimpecommerce'}
					{/if}
					{if empty($aVoucher.cumulativeReduc)}
						<div class="clr_5"></div>
						- {l s='The voucher code is not cumulative with another kind of reduction applied on the catalog' mod='btmailchimpecommerce'}
					{/if}
				</div>
			{/if}
		{/if}
	{/if}
</div>
<!-- /MCE - Automation voucher for one the voucher automation settings -->
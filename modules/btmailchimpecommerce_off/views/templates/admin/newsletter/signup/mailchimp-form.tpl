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
	<script type="text/javascript">
		{literal}
		var oMCSignupCallBack = [];
		{/literal}
	</script>
	<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12" action="{$sURI|escape:'htmlall':'UTF-8'}#tab-10|tab-11" enctype="multipart/form-data" method="post" id="bt_mc_sign_up_form" name="bt_mc_sign_up_form" onsubmit="javascript: oMailchimp.form('bt_mc_sign_up_form', '{$sURI|escape:'htmlall':'UTF-8'}#tab-10|tab-11', null, 'bt_settings_newsletter_sign_up', 'bt_settings_newsletter_sign_up', true, false, oMCSignupCallBack, 'newsletter_signup_mailchimp', 'newsletter');return false;">
		<input type="hidden" name="sAction" value="{$aQueryParams.signupMailchimp.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.signupMailchimp.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sSubTpl" value="mailchimp" />

		{* Check if the MC API key is filled up *}
		{if !empty($sApiKey)}
			{if !empty($bActiveNewsletter) && !empty($aListSignup)}
				<div class="clr_10"></div>
				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to use a MailChimp sign-up form. You will then have to copy / paste the HTML code of the form to be embedded given by MailChimp (MailChimp "embedded form")' mod='btmailchimpecommerce'}">
							<strong>{l s='Do you want to use a MailChimp sign-up form?' mod='btmailchimpecommerce'}</strong>
						</span>
					</label>
					
					<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="bt_mc_signup_form_use" id="bt_mc_signup_form_use_on" value="1" {if !empty($bActiveSignup)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_signup_form', null, null, true, true);" />
							<label for="bt_mc_signup_form_use_on" class="radioCheck">
								{l s='Yes' mod='btmailchimpecommerce'}
							</label>
							<input type="radio" name="bt_mc_signup_form_use" id="bt_mc_signup_form_use_off" value="0" {if empty($bActiveSignup)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_signup_form', null, null, true, false);" />
							<label for="bt_mc_signup_form_use_off" class="radioCheck">
								{l s='No' mod='btmailchimpecommerce'}
							</label>
							<a class="slide-button btn"></a>
						</span>
						<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to use a MailChimp sign-up form. You will then have to copy / paste the HTML code of the form to be embedded given by MailChimp (MailChimp "embedded form")' mod='btmailchimpecommerce'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
						<a class="badge badge-info" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/294" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about MailChimp sign-up forms' mod='btmailchimpecommerce'}</a>
					</div>
				</div>

				<div id="bt_div_signup_form" style="display: {if !empty($bActiveSignup)}block{else}none{/if};">
					<div class="alert alert-info">
						<strong>{l s='IMPORTANT NOTE:' mod='btmailchimpecommerce'}</strong>&nbsp;
						{l s='If you have installed many languages on your shop, you have to translate the form text directly into the Mailchimp form HTML code pasted below.' mod='btmailchimpecommerce'}&nbsp;
						<strong><a href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}faq/296" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Follow the procedure' mod='btmailchimpecommerce'}</a></strong>
					</div>
					<div class="clr_10"></div>
					<div class="form-group ">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Copy/paste here the HTML code of the form you have configured in your MailChimp account for the corresponding list' mod='btmailchimpecommerce'}">
								<strong>{l s='Paste here the form HTML code given by MailChimp:' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							{foreach from=$aLanguages item=aLang}
								<div id="bt_div_mc_signup_code_{$aLang.id_lang|intval}" class="translatable-field row lang-{$aLang.id_lang|intval}" {if $aLang.id_lang != $iCurrentLang}style="display:none"{/if}>
									<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
										<textarea id="bt_mc_signup_code_{$aLang.id_lang|intval}" name="bt_mc_signup_code[{$aLang.id_lang|intval}]" {if empty($aMcSignupForm)}placeholder="{l s='Paste the form HTML code given by MailChimp' mod='btmailchimpecommerce'}"{/if} class="full-width" style="height: 350px;">{if !empty($aMcSignupForm)}{foreach from=$aMcSignupForm key=idLang item=sHtmlCode}{if $idLang == $aLang.id_lang}{$sHtmlCode nofilter}{/if}{/foreach}{/if}</textarea>
									</div>
									<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
										<ul class="dropdown-menu">
											{foreach from=$aLanguages item=aDropdownLang}
												<li><a href="javascript:hideOtherLanguage({$aDropdownLang.id_lang|intval});" tabindex="-1">{$aDropdownLang.name|escape:'htmlall':'UTF-8'}</a></li>
											{/foreach}
										</ul>
										<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Copy/paste here the HTML code of the form you have configured in your MailChimp account for the corresponding list' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
									</div>
								</div>
							{/foreach}
						</div>
					</div>

					{* USE CASE - define which way to integrate the MC Sign-up form *}
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose the way to display your custom MailChimp sign-up form on your site and complete the related options'  mod='btmailchimpecommerce'}">
								<strong>{l s='Sign-up form display:' mod='btmailchimpecommerce'}</strong>
							</span>
						</label>
						<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
							<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
								<select name="bt_mc_signup_form_display" id="bt_mc_signup_form_display">
									<option value="0" {if empty($sSignupDisplay)}selected="selected"{/if}>-- {l s='Select the way to display it' mod='btmailchimpecommerce'} --</option>
									<option value="dedicated" {if !empty($sSignupDisplay) && $sSignupDisplay == 'dedicated'}selected="selected"{/if}>{l s='Dedicated page' mod='btmailchimpecommerce'}</option>
									<option value="popup" {if !empty($sSignupDisplay) && $sSignupDisplay == 'popup'}selected="selected"{/if}>{l s='Pop-up form' mod='btmailchimpecommerce'}</option>
									<option value="form" {if !empty($sSignupDisplay) && $sSignupDisplay == 'form'}selected="selected"{/if}>{l s='Embedded block form (will replace the native block)' mod='btmailchimpecommerce'}</option>
									<option value="shortcode" {if !empty($sSignupDisplay) && $sSignupDisplay == 'shortcode'}selected="selected"{/if}>{l s='Use the shortcode' mod='btmailchimpecommerce'}</option>
								</select>
							</div>
						</div>
					</div>

					{* USE CASE - DEDICATED PAGE *}
					<div id="bt_div_signup_display_dedicated" style="display: {if !empty($sSignupDisplay) && $sSignupDisplay == 'dedicated'}block{else}none{/if};">
						<div class="clr_10"></div>
						<div class="clr_hr"></div>
						<div class="clr_10"></div>

						<h4>{l s='Option for the dedicated page' mod='btmailchimpecommerce'}</h4>

						<div class="clr_10"></div>
						
						<div class="alert alert-info">
							{l s='When you display your sign up form on a dedicated page, the module will display, where the PrestaShop native newsletter block is normally positioned, a link to this dedicated page. You can define here, for each language, the text of the link:' mod='btmailchimpecommerce'}
						</div>
						
						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Define the text of the link to the dedicated page' mod='btmailchimpecommerce'}">
								<strong>{l s='Label of the dedicated page link:' mod='btmailchimpecommerce'}</strong>
							</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								{foreach from=$aLanguages item=aLang}
									<div id="bt_div_mc_signup_page_link_label_{$aLang.id_lang|intval}" class="translatable-field row lang-{$aLang.id_lang|intval}" {if $aLang.id_lang != $iCurrentLang}style="display:none"{/if}>
										<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-link"></i></span>
												<input type="text" id="bt_mc_signup_page_link_label_{$aLang.id_lang|intval}" name="bt_mc_signup_page_link_label[{$aLang.id_lang|intval}]" size="35" value="{if !empty($aSignupLinkLabel)}{foreach from=$aSignupLinkLabel key=idLang item=sLinkLabel}{if $idLang == $aLang.id_lang}{if !empty($sLinkLabel)}{$sLinkLabel}{else}{l s='Interested in the products of our shop? Subscribe to our newsletter!' mod='btmailchimpecommerce'}{/if}{/if}{/foreach}{else}{l s='Interested in the products of our shop? Subscribe to our newsletter!' mod='btmailchimpecommerce'}{/if}" />
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
											<ul class="dropdown-menu">
												{foreach from=$aLanguages item=aDropdownLang}
													<li><a href="javascript:hideOtherLanguage({$aDropdownLang.id_lang|intval});" tabindex="-1">{$aDropdownLang.name|escape:'htmlall':'UTF-8'}</a></li>
												{/foreach}
											</ul>
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Define the text of the link to the dedicated page' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
									</div>
								{/foreach}
							<div class="clr_5"></div>
							<div style="clear: both;"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Don\'t forget to fill in a text for each language' mod='btmailchimpecommerce'}</span>
							</div>
						</div>
					</div>

					{* USE CASE - POPUP *}
					<div id="bt_div_signup_display_popup" style="display: {if !empty($sSignupDisplay) && $sSignupDisplay == 'popup'}block{else}none{/if};">
						<div class="clr_10"></div>
						<div class="clr_hr"></div>
						<div class="clr_10"></div>

						<h4>{l s='Options for the pop-up form' mod='btmailchimpecommerce'}</h4>

						<div class="clr_10"></div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<strong>{l s='Popup width and height:' mod='btmailchimpecommerce'}</strong>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-4">
									<select name="bt_mc_signup_popup_width" id="bt_mc_signup_popup_width">
										{foreach from=$aPixelValues item=value}
										<option value="{$value|intval}" {if !empty($iPopupWidth) && $iPopupWidth == $value}selected="selected"{/if}>{$value|intval}{l s='px' mod='btmailchimpecommerce'}</option>
										{/foreach}
									</select>
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-4">
									<select name="bt_mc_signup_popup_height" id="bt_mc_signup_popup_height">
										{foreach from=$aPixelValues item=value}
											<option value="{$value|intval}" {if !empty($iPopupHeight) && $iPopupHeight == $value}selected="selected"{/if}>{$value|intval}{l s='px' mod='btmailchimpecommerce'}</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>

						<div class="clr_10"></div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Define how many times you want the pop-up to appear on your site'  mod='btmailchimpecommerce'}">
									<strong>{l s='How many times you want the pop-up to display?' mod='btmailchimpecommerce'}</strong>
								</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
									<select name="bt_mc_signup_form_times" id="bt_mc_signup_form_times">
										<option value="1" {if !empty($iPopupTimes) && $iPopupTimes == 1}selected="selected"{/if}>1 {l s='time' mod='btmailchimpecommerce'}</option>
										<option value="2" {if !empty($iPopupTimes) && $iPopupTimes == 2}selected="selected"{/if}>2 {l s='times' mod='btmailchimpecommerce'}</option>
										<option value="3" {if !empty($iPopupTimes) && $iPopupTimes == 3}selected="selected"{/if}>3 {l s='times' mod='btmailchimpecommerce'}</option>
										<option value="4" {if !empty($iPopupTimes) && $iPopupTimes == 4}selected="selected"{/if}>4 {l s='times' mod='btmailchimpecommerce'}</option>
										<option value="5" {if !empty($iPopupTimes) && $iPopupTimes == 5}selected="selected"{/if}>5 {l s='times' mod='btmailchimpecommerce'}</option>
										<option value="0" {if empty($iPopupTimes)}selected="selected"{/if}>{l s='Always' mod='btmailchimpecommerce'}</option>
									</select>
								</div>
							</div>
						</div>

						<div class="clr_10"></div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select on which pages you want the pop-up to be displayed. We advise you to choose the pages carefully so that the pop-up window is not intrusive and makes your visitors go'  mod='btmailchimpecommerce'}">
									<strong>{l s='Page(s) where the pop-up should appear:' mod='btmailchimpecommerce'}</strong>
								</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
									<select name="bt_mc_signup_form_pages[]" id="bt_mc_signup_form_pages" multiple="multiple">
										<option value="home" {if !empty($aPopupPages)}{foreach from=$aPopupPages key=id item=sType}{if $sType == 'home'}selected="selected"{/if}{/foreach}{/if}>{l s='Home page' mod='btmailchimpecommerce'}</option>
										<option value="category" {if !empty($aPopupPages)}{foreach from=$aPopupPages key=id item=sType}{if $sType == 'category'}selected="selected"{/if}{/foreach}{/if}>{l s='Category page' mod='btmailchimpecommerce'}</option>
										<option value="brand" {if !empty($aPopupPages)}{foreach from=$aPopupPages key=id item=sType}{if $sType == 'brand'}selected="selected"{/if}{/foreach}{/if}>{l s='Brand page' mod='btmailchimpecommerce'}</option>
										<option value="product" {if !empty($aPopupPages)}{foreach from=$aPopupPages key=id item=sType}{if $sType == 'product'}selected="selected"{/if}{/foreach}{/if}>{l s='Product page' mod='btmailchimpecommerce'}</option>
										<option value="other" {if !empty($aPopupPages)}{foreach from=$aPopupPages key=id item=sType}{if $sType == 'other'}selected="selected"{/if}{/foreach}{/if}>{l s='Other pages' mod='btmailchimpecommerce'}</option>
									</select>
								</div>
							</div>
						</div>

						<div class="clr_10"></div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to offer the visitors, through a checkbox, the possibility to stop displaying the pop-up' mod='btmailchimpecommerce'}">
									<strong>{l s='Offer your visitors to stop displaying the pop-up' mod='btmailchimpecommerce'}</strong>
								</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="bt_mc_signup_form_not_display" id="bt_mc_signup_form_not_display_on" value="1" {if !empty($bPopupNotDisplayButton)}checked="checked"{/if} />
									<label for="bt_mc_signup_form_not_display_on" class="radioCheck">
										{l s='Yes' mod='btmailchimpecommerce'}
									</label>
									<input type="radio" name="bt_mc_signup_form_not_display" id="bt_mc_signup_form_not_display_off" value="0" {if empty($bPopupNotDisplayButton)}checked="checked"{/if} />
									<label for="bt_mc_signup_form_not_display_off" class="radioCheck">
										{l s='No' mod='btmailchimpecommerce'}
									</label>
									<a class="slide-button btn"></a>
								</span>
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to offer the visitors, through a checkbox, the possibility to stop displaying the pop-up' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
							</div>
						</div>

						<div class="clr_10"></div>

						<div class="form-group ">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You can include a promotional text to encourage your visitors to subscribe to your newsletter. It will be displayed at the top of the pop-up, over the image if you add one (see next option)' mod='btmailchimpecommerce'}">
								<strong>{l s='Display a promotional text:' mod='btmailchimpecommerce'}</strong>
							</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								{foreach from=$aLanguages item=aLang}
									<div class="translatable-field row lang-{$aLang.id_lang|intval}" {if $aLang.id_lang != $iCurrentLang}style="display:none"{/if}>
										<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
											<textarea id="bt_mc_signup_popup_text_{$aLang.id_lang|intval}" name="bt_mc_signup_popup_text[{$aLang.id_lang|intval}]" class="textarea-autosize rte autoload_rte">{if !empty($aPopupText)}{foreach from=$aPopupText key=idLang item=sText}{if $idLang == $aLang.id_lang}{$sText nofilter}{/if}{/foreach}{/if}</textarea>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
											<ul class="dropdown-menu">
												{foreach from=$aLanguages item=aDropdownLang}
													<li><a href="javascript:hideOtherLanguage({$aDropdownLang.id_lang|intval});" tabindex="-1">{$aDropdownLang.name|escape:'htmlall':'UTF-8'}</a></li>
												{/foreach}
											</ul>
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='You can include a promotional text to encourage your visitors to subscribe to your newsletter. It will be displayed at the top of the pop-up, over the image if you add one (see next option)' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
									</div>
								{/foreach}
							</div>
						</div>

						<div class="clr_10"></div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to include a banner with or without a promotional text' mod='btmailchimpecommerce'}">
									<strong>{l s='Do you want to include a banner?' mod='btmailchimpecommerce'}</strong>
								</span>
							</label>
							<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="bt_mc_signup_popup_image" id="bt_mc_signup_popup_image_on" value="1" {if !empty($bPopupImage)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_signup_popup_image', null, null, true, true);" />
									<label for="bt_mc_signup_popup_image_on" class="radioCheck">
										{l s='Yes' mod='btmailchimpecommerce'}
									</label>
									<input type="radio" name="bt_mc_signup_popup_image" id="bt_mc_signup_popup_image_off" value="0" {if empty($bPopupImage)}checked="checked"{/if} onclick="oMailchimp.changeSelect(null, 'bt_div_signup_popup_image', null, null, true, false);" />
									<label for="bt_mc_signup_popup_image_off" class="radioCheck">
										{l s='No' mod='btmailchimpecommerce'}
									</label>
									<a class="slide-button btn"></a>
								</span>
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select YES if you want to include a banner with or without a promotional text' mod='btmailchimpecommerce'}">&nbsp;&nbsp;<span class="icon-question-sign"></span></span>&nbsp;&nbsp;
							</div>
						</div>
						<div class="clr_10"></div>

						<div id="bt_div_signup_popup_image" style="display: {if !empty($bPopupImage)}block{else}none{/if};">
							<div class="form-group">
								<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose an image to make your pop-up form attractive' mod='btmailchimpecommerce'}">
										<strong>{l s='Choose an image to be displayed in the pop-up:' mod='btmailchimpecommerce'}</strong>
									</span>
								</label>
								<div class="row col-xs-12 col-sm-12 col-md-9 col-lg-9">
									{foreach from=$aLanguages item=aLang}
									<div class="translatable-field lang-{$aLang.id_lang|intval}" {if $aLang.id_lang != $iCurrentLang}style="display:none"{/if}>
										<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
											{if !empty($aPopupImages[$aLang.id_lang])}
												<img src="{$aPopupImages[$aLang.id_lang]}" class="img-thumbnail" />
											{/if}
											<div class="dummyfile input-group">
												<input id="bt_signup_popup_image_{$aLang.id_lang|intval}" type="file" name="bt_signup_popup_image_{$aLang.id_lang|intval}" class="hide-file-upload">
												<span class="input-group-addon"><i class="icon-file"></i></span>
												<input id="bt_signup_popup_image_name_{$aLang.id_lang|intval}" type="text" class="disabled" name="filename" readonly="">
												<span class="input-group-btn">
													<button id="bt_signup_popup_image_button_{$aLang.id_lang|intval}" type="button" name="bt_submit_add_attachments" class="btn btn-default">
														<i class="icon-folder-open"></i> {l s='Choose a file' mod='btmailchimpecommerce'}
													</button>
												</span>
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
											<ul class="dropdown-menu">
												{foreach from=$aLanguages item=aDropdownLang}
													<li><a href="javascript:hideOtherLanguage({$aDropdownLang.id_lang|intval});" tabindex="-1">{$aDropdownLang.name|escape:'htmlall':'UTF-8'}</a></li>
												{/foreach}
											</ul>
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose an image to make your pop-up form attractive' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
									</div>
									{literal}
									<script type="text/javascript">
										$(document).ready(function(){
											$('#bt_signup_popup_image_button_{/literal}{$aLang.id_lang|intval}{literal}').click(function(e){
												$('#bt_signup_popup_image_{/literal}{$aLang.id_lang|intval}{literal}').trigger('click');
											});
											$('#bt_signup_popup_image_{/literal}{$aLang.id_lang|intval}{literal}').change(function(e){
												var val = $(this).val();
												var file = val.split(/[\\/]/);
												$('#bt_signup_popup_image_name_{/literal}{$aLang.id_lang|intval}{literal}').val(file[file.length-1]);
											});
										});
									</script>
									{/literal}
									{/foreach}
								</div>
							</div>

							<div class="clr_10"></div>

							<div class="form-group">
								<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Define the vertical position of the text'  mod='btmailchimpecommerce'}">
									<strong>{l s='Vertical alignment of promotional text:' mod='btmailchimpecommerce'}</strong>
								</span>
								</label>
								<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<select name="bt_mc_signup_popup_text_valign" id="bt_mc_signup_popup_text_valign">
											<option value="top" {if !empty($sTextValign) && $sTextValign == 'top'}selected="selected"{/if}>{l s='at the top' mod='btmailchimpecommerce'}</option>
											<option value="middle" {if !empty($sTextValign) && $sTextValign == 'middle'}selected="selected"{/if}>{l s='at the middle' mod='btmailchimpecommerce'}</option>
											<option value="bottom" {if !empty($sTextValign) && $sTextValign == 'bottom'}selected="selected"{/if}>{l s='at the bottom' mod='btmailchimpecommerce'}</option>
										</select>
									</div>
								</div>
							</div>

							<div class="clr_10"></div>

							<div id="bt_div_mc_signup_popup_text_valign_custom" style="display: {if !empty($sTextValign) && $sTextValign == 'middle'}block{else}none{/if};">
								<div class="form-group">
									<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
										<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Depending on the styles chosen for your promotional text and its length, you may need to modify the high and low margins to obtain an exact vertical centering of the text. Enter a value between 1 and 100 (the unit is %). If you leave the box empty, a default value of 50% will be set.' mod='btmailchimpecommerce'}">
											<strong>{l s='Adjust the vertical central alignment:' mod='btmailchimpecommerce'}</strong>
										</span>
									</label>
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-file-code-o"></i></span>
												<input type="text" id="bt_mc_signup_popup_text_valign_custom" name="bt_mc_signup_popup_text_valign_custom" size="35" value="{if !empty($sPopupTextVCenterCustom)}{$sPopupTextVCenterCustom}{/if}" />
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Depending on the styles chosen for your promotional text and its length, you may need to modify the high and low margins to obtain an exact vertical centering of the text. Enter a value between 1 and 100 (the unit is %). If you leave the box empty, a default value of 50% will be set.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
										<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Enter a numeric value between 1 to 100 (the unit is %).' mod='btmailchimpecommerce'}</span>
									</div>
								</div>
								<div class="clr_10"></div>
							</div>

							<div class="form-group">
								<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Define the horizontal position of the text'  mod='btmailchimpecommerce'}">
									<strong>{l s='Horizontal alignment of promotional text:' mod='btmailchimpecommerce'}</strong>
								</span>
								</label>
								<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<select name="bt_mc_signup_popup_text_halign" id="bt_mc_signup_popup_text_halign">
											<option value="left" {if !empty($sTextHalign) && $sTextHalign == 'left'}selected="selected"{/if}>{l s='at the left' mod='btmailchimpecommerce'}</option>
											<option value="middle" {if !empty($sTextHalign) && $sTextHalign == 'middle'}selected="selected"{/if}>{l s='at the middle' mod='btmailchimpecommerce'}</option>
											<option value="right" {if !empty($sTextHalign) && $sTextHalign == 'right'}selected="selected"{/if}>{l s='at the right' mod='btmailchimpecommerce'}</option>
										</select>
									</div>
								</div>
							</div>

							<div id="bt_div_mc_signup_popup_text_halign_custom" style="display: {if !empty($sTextHalign) && $sTextHalign == 'middle'}block{else}none{/if};">
								<div class="form-group">
									<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
										<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Depending on the styles chosen for your promotional text and its length, you may need to modify the left and right margins to obtain an exact horizontal centering of the text. Enter a value between 1 and 100 (the unit is %). If you leave the box empty, a default value of 50% will be set.' mod='btmailchimpecommerce'}">
											<strong>{l s='Adjust the horizontal central alignment:' mod='btmailchimpecommerce'}</strong>
										</span>
									</label>
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-file-code-o"></i></span>
												<input type="text" id="bt_mc_signup_popup_text_halign_custom" name="bt_mc_signup_popup_text_halign_custom" size="35" value="{if !empty($sPopupTextHCenterCustom)}{$sPopupTextHCenterCustom}{/if}" />
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Depending on the styles chosen for your promotional text and its length, you may need to modify the left and right margins to obtain an exact horizontal centering of the text. Enter a value between 1 and 100 (the unit is %). If you leave the box empty, a default value of 50% will be set.' mod='btmailchimpecommerce'}">&nbsp;<span class="icon-question-sign"></span></span>
										</div>
										<div class="clr_5"></div><span class="help-block"><i class="icon-warning-sign text-primary">&nbsp;</i>{l s='Enter a numeric value between 1 to 100 (the unit is %).' mod='btmailchimpecommerce'}</span>
									</div>
								</div>
								<div class="clr_10"></div>
							</div>
						</div>
					</div>

					{* USE CASE - SHORTCODE *}
					<div id="bt_div_signup_display_shortcode" style="display: {if !empty($sSignupDisplay) && $sSignupDisplay == 'shortcode'}block{else}none{/if};">
						<div class="clr_10"></div>
						<div class="clr_hr"></div>
						<div class="clr_10"></div>

						<h4>{l s='Option for the shortcode' mod='btmailchimpecommerce'}</h4>

						<div class="clr_10"></div>
						
						<div class="alert alert-info">
						{l s='Copy this shortcode and paste it at the right place into your theme template files to make your form appear exactly where you want.' mod='btmailchimpecommerce'}
						</div>

						<div class="form-group">
							<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<strong>{l s='Shortcode to be copied:' mod='btmailchimpecommerce'}</strong>
							</label>
							
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
								<input type="text" id="bt_mc_signup_shortcode" name="bt_mc_signup_shortcode" value="{$sShortCode nofilter}"/>
								{*<div class="clr_5"></div>*}
								{*<button id="bt_copy_short_code" type="button" class="btn btn-info btn-copy js-tooltip js-copy" data-toggle="tooltip" data-placement="bottom" data-copy="{$sShortCode nofilter}" title="">&nbsp;<i class="fa fa-copy"></i>&nbsp;{l s='Click to copy the shortcode' mod='btmailchimpecommerce'} </button>*}
							</div>
						</div>
					</div>
				</div>
			{else}
				<div class="alert alert-warning">
					{l s='To be able to configure your newsletter form, you must first choose a list (or create one) in the "General settings > Users list choice" tab and do a first export of your users through the "Users list sync & newsletter forms > Users list synching" tab.' mod='btmailchimpecommerce'}
				</div>
			{/if}
		{else}
			<div class="alert alert-warning">
				{l s='You don\'t have filled in the MailChimp API key yet! Please do it by clicking on the "Configure" button at the step 2 above or go to the "General settings > Mailchimp settings" tab.' mod='btmailchimpecommerce'}
			</div>
		{/if}

		<div class="clr_20"></div>
		<div class="clr_hr"></div>
		<div class="clr_20"></div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
				<div id="bt_error_newsletter_signup_mailchimp"></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
				<button class="btn btn-success btn-lg pull-right" id="bt_btn_signup_form_mc" onclick="tinymce.triggerSave();oMailchimp.form('bt_mc_sign_up_form', '{$sURI|escape:'htmlall':'UTF-8'}#tab-10|tab-11', null, 'bt_settings_newsletter_sign_up', 'bt_settings_newsletter_sign_up', true, false, oMCSignupCallBack, 'newsletter_signup_mailchimp', 'newsletter');return false;">{if !empty($aLocalList.data.mc.form.content)}{l s='Save' mod='btmailchimpecommerce'}{else}{l s='Save' mod='btmailchimpecommerce'}{/if}</button>
			</div>
		</div>
	</form>

	<div class="clr_10"></div>

	{literal}
	<script type="text/javascript">
		//bootstrap components init
		$(document).ready(function() {
			{/literal}{if !empty($bAjaxMode)}{literal}
			$('.label-tooltip, .help-tooltip').tooltip();
			{/literal}
			{/if}
			{literal}

			// Copy to clipboard
			$('.js-copy').click(function() {
				var text = $(this).attr('data-copy');
				var el = $(this);
				oMailchimp.copyToClipboard(text, el);
			});

			// handle sign-up form display type
			$("#bt_mc_signup_form_display").bind('change', function(event)
			{
				$("#bt_mc_signup_form_display option:selected").each(function()
				{
					switch ($(this).val()) {
						case 'dedicated' :
							$("#bt_div_signup_display_dedicated").show();
							$("#bt_div_signup_display_popup").hide();
							$("#bt_div_signup_display_shortcode").hide();
							break;
						case 'popup' :
							$("#bt_div_signup_display_dedicated").hide();
							$("#bt_div_signup_display_popup").show();
							$("#bt_div_signup_display_shortcode").hide();
							break;
						case 'shortcode' :
							$("#bt_div_signup_display_dedicated").hide();
							$("#bt_div_signup_display_popup").hide();
							$("#bt_div_signup_display_shortcode").show();
							break;
						default:
							$("#bt_div_signup_display_dedicated").hide();
							$("#bt_div_signup_display_popup").hide();
							$("#bt_div_signup_display_shortcode").hide();
							break;
					}
				});
			}).change();

			// handle sign-up form popup vertical align text
			$("#bt_mc_signup_popup_text_valign").bind('change', function(event)
			{
				$("#bt_mc_signup_popup_text_valign option:selected").each(function()
				{
					switch ($(this).val()) {
						case 'middle' :
							$("#bt_div_mc_signup_popup_text_valign_custom").show();
							break;
						default:
							$("#bt_div_mc_signup_popup_text_valign_custom").hide();
							break;
					}
				});
			}).change();

			// handle sign-up form popup horizontal align text
			$("#bt_mc_signup_popup_text_halign").bind('change', function(event)
			{
				$("#bt_mc_signup_popup_text_halign option:selected").each(function()
				{
					switch ($(this).val()) {
						case 'middle' :
							$("#bt_div_mc_signup_popup_text_halign_custom").show();
							break;
						default:
							$("#bt_div_mc_signup_popup_text_halign_custom").hide();
							break;
					}
				});
			}).change();

			// fix for PS 1.6 as the tinySetup function is using 2 globals JS variables but they are defined in the specific admin controller, and not in the global admin context
			ad = "{/literal}{$sBaseAdminDir}{literal}";
			iso = "{/literal}{$iso}{literal}";

			// set the tiny MCE for the popup text
			tinySetup({
				editor_selector :"autoload_rte"
			});

			$(".textarea-autosize").autosize();
		});
	</script>
	{/literal}
</div>


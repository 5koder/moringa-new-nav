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

<div class="row header_bar bg-white">
	<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
				<img class="img-responsive" src="{$smarty.const._MCE_MODULE_URL|escape:'htmlall':'UTF-8'}logo.png" width="57" height="57" style="margin-top: 15px;" />
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<img class="img-responsive" src="{$smarty.const._MCE_URL_IMG|escape:'htmlall':'UTF-8'}admin/bt_logo.jpg" style="margin-top: 10px;" />
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9" id="bt_prerequesite_steps">
		<div class="text-center">
			<div id="step-by-step" class="row bs-wizard text-center" style="border-bottom:0;">
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 bs-wizard-step step-1 {if $iCurlTest == 1}complete{elseif $iCurlTest == 0}disabled{else}danger{/if} text-center">
					<div class="text-center bs-wizard-stepnum">
						{if $iCurlTest == 1}
							{l s='1 - Connection' mod='btmailchimpecommerce'}
						{else}
							<strong>{l s='1 - Connection' mod='btmailchimpecommerce'}</strong>
						{/if}
						<span class="pointer label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='The MailChimp API uses cURL over SSL. Therefore, this MUST be enabled on your server. If you encounter connection problems to the Mailchimp API, you will need to contact your web host as the module needs cURL over SSL' mod='btmailchimpecommerce'}">
							<i class="icon icon-question-sign">&nbsp;</i>
						</span>
					</div>
					<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
					<div class="clr_5"></div>
					<div class="bt_work_tabs">
						{if $iCurlTest != 1}
							<a class="btn btn-sm btn-warning fancybox.ajax" id="bt_curl_ssl" href="{$sURI|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.curl.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.curl.type|escape:'htmlall':'UTF-8'}">{if $iCurlTest == 0}{l s='Test it!' mod='btmailchimpecommerce'}{else}{l s='Test it again!' mod='btmailchimpecommerce'}{/if}</a>
							{literal}
							<script type="text/javascript">
								$(document).ready(function() {
									$("#bt_curl_ssl").fancybox({
										'hideOnContentClick' : false,
										'scrolling' : 'no',
										'autoDimensions' : true
									});
								});
							</script>
							{/literal}
						{/if}
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 bs-wizard-step step-2 {if empty($sApiKey)}disabled{else}complete{/if} text-center">
					<div class="text-center bs-wizard-stepnum">
						{if $iCurlTest == 1 && empty($sApiKey)}
						<strong>{l s='2 - General settings' mod='btmailchimpecommerce'}</strong>
						{else}
							{l s='2 - General settings' mod='btmailchimpecommerce'}
						{/if}
						<span class="pointer label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='The MailChimp API key must be filled in before moving on the module configuration' mod='btmailchimpecommerce'}">
							<i class="icon icon-question-sign">&nbsp;</i>
						</span>
					</div>
					<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
					<div class="clr_5"></div>
					<div class="bt_top_tabs">
						{if empty($sApiKey)}
							<a class="btn btn-sm btn-warning btn-step-2" onclick="javascript: window.location.href = '{$sURI}#tab-01';window.location.reload(true);"><i class="fa fa-cog"></i> {l s='Configure' mod='btmailchimpecommerce'}</a>
						{/if}
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 bs-wizard-step step-3 {if empty($bNewsletterConf)}disabled{else}complete{/if} text-center">
					<div class="text-center bs-wizard-stepnum">
						{if $iCurlTest == 1 && !empty($sApiKey) && empty($bNewsletterConf)}
							<strong>{l s='3 - Users list sync' mod='btmailchimpecommerce'}</strong>
						{else}
							{l s='3 - Users list sync' mod='btmailchimpecommerce'}
						{/if}
						<span class="pointer label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='Export your users list to be able to use the newsletter and/or e-commerce features' mod='btmailchimpecommerce'}">
							<i class="icon icon-question-sign">&nbsp;</i>
						</span>
					</div>
					<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
					<div class="clr_5"></div>
					<div class="bt_top_tabs">
						{if empty($bNewsletterConf)}
							<a class="btn btn-sm btn-warning btn-step-3" onclick="javascript: window.location.href = '{$sURI}#tab-10';window.location.reload(true);"><i class="fa fa-cog"></i> {l s='Configure' mod='btmailchimpecommerce'}</a>
						{/if}
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 bs-wizard-step step-4 {if $iCurlTest == 1 && !empty($sApiKey) && !empty($bNewsletterConf) && (empty($bEcommerceConf) || (!empty($bEcommerceConf) && empty($aSynchronizedData.customer.data.active_catalog) && empty($aSynchronizedData.product.data.active_catalog)))}warning{elseif empty($bEcommerceConf)}disabled{else}complete{/if} text-center">
					<div class="text-center bs-wizard-stepnum">
						{if $iCurlTest == 1
							&& !empty($sApiKey)
							&& !empty($bNewsletterConf)
							&& empty($bEcommerceConf)
						}
							<strong>{l s='4 - E-commerce' mod='btmailchimpecommerce'}</strong>
						{else}
							{l s='4 - E-commerce' mod='btmailchimpecommerce'}
						{/if}
						<span class="pointer label-tooltip" data-toggle="tooltip" data-placement="bottom" title data-original-title="{l s='To use the e-commerce feature, enable it in the related tab. After a first manual synchronization of customers information, product catalog and, if you want, past orders, then next synchronizations will be done automatically!' mod='btmailchimpecommerce'}">
							<i class="icon icon-question-sign">&nbsp;</i>
						</span>
					</div>
					<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
					<div class="clr_5"></div>
					<div class="bt_top_tabs">
						{if empty($bEcommerceConf)}
							<a class="btn btn-sm btn-warning btn-step-4" onclick="javascript: window.location.href = '{$sURI}#tab-2';window.location.reload(true);"><i class="fa fa-cog"></i> {l s='Configure' mod='btmailchimpecommerce'} </a>
						{elseif empty($aSynchronizedData.customer.data.active_catalog) && empty($aSynchronizedData.product.data.active_catalog)}
							<a class="btn btn-sm btn-warning btn-step-4" onclick="javascript: window.location.href = '{$sURI}&sTpl=sync&sSubTpl=sync#tab-2';window.location.reload(true);"><i class="fa fa-cog"></i> {l s='Finalize' mod='btmailchimpecommerce'} </a>
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
		<a class="btn btn-info btn-md col-xs-12" target="_blank" href="{$smarty.const._MCE_BT_FAQ_MAIN_URL|escape:'htmlall':'UTF-8'}product/74"><span class="fa fa-question-circle"></span>&nbsp;{l s='Online FAQ' mod='btmailchimpecommerce'}</a>
	</div>
</div>

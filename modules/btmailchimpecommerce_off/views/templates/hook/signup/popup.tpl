{*
* 2003-2019 Business Tech
*
* @author Business Tech SARL
* @copyright  2003-2019 Business Tech SARL
*}
<div class="mce">
	<style type="text/css">
		.mce label {literal}{text-align: left !important;}{/literal}
	</style>

	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		{if !empty($image) || !empty($text)}
			<div class="popup-noscrollable">
				<div style="position: inherit">
					{* USE CASE - text alone *}
					{if !empty($text) && empty($image)}
						{$text nofilter}
					{* USE CASE - image alone *}
					{elseif !empty($image) && empty($text)}
						<img src="{$image}" alt="{l s='Subscribe to our newsletter!' mod='btmailchimpecommerce'}" class="img-responsive">
					{* USE CASE - text and image *}
					{else}
						<figure class="popup-figure">
							<img src="{$image}" alt="{l s='Subscribe to our newsletter!' mod='btmailchimpecommerce'}" class="img-responsive">
							<figcaption class="popup-caption valign-{$text_valign} halign-{$text_halign}" {if ($text_valign == 'middle' || $text_halign == 'middle') && (!empty($text_valign_custom) || !empty($text_halign_custom))}style="{if $text_valign == 'middle' && !empty($text_valign_custom)}top: {$text_valign_custom}% !important;{/if}{if $text_halign == 'middle' && !empty($text_halign_custom)}left: {$text_halign_custom}% !important;{/if}"{/if}>
								<div class="caption-description">{$text nofilter}</div>
							</figcaption>
						</figure>
					{/if}
				</div>
			</div>
			<div class="clr_20"></div>
		{/if}

		<div>
			{$form nofilter}
			{if !empty($button)}
			<div class="clr_20"></div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left">
					{l s='Stop displaying this popup' mod='btmailchimpecommerce'} <input type="checkbox" name="bt_signup_hide" id="bt_signup_hide" value="0" />
				</div>
			</div>
			{/if}
		</div>
	</div>
</div>

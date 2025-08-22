{*
* 2003-2019 Business Tech
*
* @author Business Tech SARL
* @copyright  2003-2019 Business Tech SARL
*}

<script type="text/javascript">
	// manage 
	var bt_nl_form = {literal}{}{/literal};
	bt_nl_form.hide = {if !empty($render_js.hide)}true{else}false{/if};
	bt_nl_form.module_selector = '{if !empty($render_js.module_selector)}{$render_js.module_selector}{/if}';
	bt_nl_form.shortcode = {if !empty($render_js.shortcode)}true{else}false{/if};
	bt_nl_form.shortcode_selector = '{if !empty($render_js.shortcode_selector)}{$render_js.shortcode_selector}{/if}';
	bt_nl_form.popup = {if !empty($render_js.popup)}true{else}false{/if};
	bt_nl_form.popup_selector = '{if !empty($render_js.popup_selector)}#{$render_js.popup_selector}{/if}';
	bt_nl_form.popup_width = {if !empty($render_js.popup_width)}{$render_js.popup_width}{else}800{/if};
	bt_nl_form.popup_height = {if !empty($render_js.popup_height)}{$render_js.popup_height}{else}600{/if};
	bt_nl_form.html_content = {if !empty($render_html)}true{else}false{/if};
	bt_nl_form.html_selector = '{if !empty($render_html)}#mce_html_form_hide{/if}';
	bt_nl_form.ajax = {if !empty($render_js.ajax)}true{else}false{/if};
	bt_nl_form.ajax_selector = '{if !empty($render_js.ajax_selector)}{$render_js.ajax_selector}{/if}';
	bt_nl_form.ajax_url = '{if !empty($render_js.ajax_url)}{$render_js.ajax_url nofilter}{/if}';
	bt_nl_form.ajax_params = '{if !empty($render_js.ajax_params)}{$render_js.ajax_params nofilter}{/if}';

	// manage the NL module
	var bt_nl_module = {literal}{}{/literal};
	bt_nl_module.ajax = {if !empty($nl_module_ajax)}true{else}false{/if};
	bt_nl_module.field_submit = '{if !empty($nl_module_submit)}{$nl_module_submit}{/if}';
	bt_nl_module.field_email = '{if !empty($nl_module_email)}{$nl_module_email}{/if}';
	bt_nl_module.ajax_url = '{if !empty($nl_module_url)}{$nl_module_url nofilter}{/if}';
	bt_nl_module.ajax_params = '{if !empty($nl_module_params)}{$nl_module_params nofilter}{/if}';

</script>

{* USE CASE - MC External JS integration for MC features *}
{if !empty($sMailChimpJS)}
	{$sMailChimpJS nofilter}
{/if}

{* use case - we hide MC form HTML to place it where we want via the JS code *}
{if !empty($render_html)}
	<div id="mce_html_form_hide" style="display: none;">
        {$render_html nofilter}
	</div>
{/if}
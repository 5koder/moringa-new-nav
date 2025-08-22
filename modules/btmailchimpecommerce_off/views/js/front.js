/*
 * 2003-2019 Business Tech
 *
 *  @author    Business Tech SARL
 *  @copyright 2003-2019 Business Tech SARL
 */

bt_front_ajax = function(sURI, sParams) {
	sParams = 'sMode=xhr' + ((sParams == null || sParams == undefined) ? '' : '&' + sParams) ;
	// configure XHR
	$.ajax({
		type : 'POST',
		url : sURI,
		data : sParams,
		dataType : 'html',
		success: function(data) {
			console.log('The request was done with success!');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log('The request went wrong!');
		}
	});
};

$(document).ready(function() {
	// use case - we hide the native block newsletter if needed: add a label/link or MC form directly (MC signup decidated page or integrated form)
	if (bt_nl_form.module_selector != ''
		&& bt_nl_form.html_content == true
	) {
		// use case - hide the NL module block
		if (bt_nl_form.hide) {
			$(bt_nl_form.module_selector).css('display', 'none');
			$(bt_nl_form.module_selector).empty();
		}
		$(bt_nl_form.html_selector).prependTo($(bt_nl_form.module_selector));
		$(bt_nl_form.module_selector).show();
		$(bt_nl_form.module_selector + ' :first-child').show();
	}

	// use case - display the shortcode
	if (bt_nl_form.shortcode
		&& bt_nl_form.html_content == true
		&& $(bt_nl_form.shortcode_selector).length != 0
	) {
		$(bt_nl_form.html_selector).prependTo($(bt_nl_form.shortcode_selector));
		$(bt_nl_form.shortcode_selector + ' :first-child').show();
	}

	// use case - display the popup
	if (bt_nl_form.popup) {
		// set the popup attributes
		$.extend($.fancybox.defaults, {
			minWidth : bt_nl_form.popup_width,
			minHeight : bt_nl_form.popup_height
		});
		$.fancybox.helpers.overlay.open({parent: $('body')});
		$.fancybox($(bt_nl_form.html_selector));

		// use case - display the "not display anymore" button
		if (bt_nl_form.ajax
			&& bt_nl_form.ajax_url != ''
			&& bt_nl_form.ajax_params != ''
			&& bt_nl_form.ajax_selector != ''
		) {
			$("#"+bt_nl_form.ajax_selector).bind('click', function (event) {
				var not_display = document.getElementById(bt_nl_form.ajax_selector).checked ? 1 : 0;
				bt_front_ajax(bt_nl_form.ajax_url, bt_nl_form.ajax_params + not_display);
			});
		}
	}

	// use case - display the popup
	if (bt_nl_module.ajax
		&& bt_nl_module.ajax_url != ''
		&& bt_nl_module.ajax_params != ''
		&& bt_nl_module.field_email != ''
	) {
		$('*[name="'+bt_nl_module.field_submit+'"]').bind('click', function (event) {
			if ($('*[name="'+bt_nl_module.field_email+'"]').val() != '') {
				bt_front_ajax(bt_nl_module.ajax_url, bt_nl_module.ajax_params + $('*[name="'+bt_nl_module.field_email+'"]').val());
			}
		});
	}
});
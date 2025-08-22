/*
 * 2003-2019 Business Tech
 *
 *  @author    Business Tech SARL
 *  @copyright 2003-2019 Business Tech SARL
 */
// declare main object of module
var oMceModule = function(sName){
	// set name
	this.name = sName;

	// set name
	this.oldVersion = false;

	// set translated js msgs
	this.msgs = {};

	// stock error array
	this.aError = [];

	// set url of admin img
	this.sImgUrl = '';

	// set url of module's web service
	this.sWebService = '';

	// get the timer box object to handle chrono and setimeout
	this.oTimer = {};

	// set this in obj context
	var oThis = this;

	// variable to control the synchronization of product catalog
	this.bSyncFlag = false;

	/**
	 * show() method show effect and assign HTML in
	 *
	 * @param string sId : container to show in
	 * @param string sHtml : HTML to display
	 */
	this.show = function(sId, sHtml){
		$("#" + sId).html(sHtml).css('style', 'none');
		$("#" + sId).show('fast');
	};

	/**
	 * hide() method hide effect and delete html
	 *
	 * @param string sId : container to hide in
	 */
	this.hide = function(sId, bOnlyHide){
		$('#' + sId).hide('fast');
		if (bOnlyHide == null) {
			$('#' + sId).empty();
		}
	};

	/**
	 * form() method check all fields of current form and execute : XHR or submit => used for update all admin config
	 *
	 * @see ajax
	 * @param string sForm : form
	 * @param string sURI : query params used for XHR
	 * @param string sRequestParam : param action and type in order to send with post mode
	 * @param string sToDisplay :
	 * @param string sToHide : force to hide specific ID
	 * @param bool bSubmit : used only for sending main form
	 * @param bool bFancyBox : used only for fancybox in xhr
	 * @param string oCallBack : used only for callback to execute as ajax request
	 * @param string sErrorType :
	 * @param string sLoadBar :
	 * @param string sScrollTo :
	 * @return string : HTML returned by smarty
	 */
	this.form = function(sForm, sURI, sRequestParam, sToDisplay, sToHide, bSubmit, bFancyBox, oCallBack, sErrorType, sLoadBar, sScrollTo){
		// set return validation
		var aError = [];

		// get all fields of form
		var fields = $("#" + sForm).serializeArray();

		// set counter
		var iCounter = 0;

		// set bIsError
		var bIsError = false;
		var bCheckOrderStatus = false;
		var bCheckedOrderStatus = false;

		// check element form
		jQuery.each(fields, function(i, field) {
			bIsError = false;

			switch(field.name) {
				case 'bt_mc_api_key' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.apikey;
						bIsError = true;
					}
					break;
				case 'bt_mc_cookie_ttl':
					if (field.value == '0' || field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.cookie;
						bIsError = true;
					}
					break;
				case 'bt_list_company_name' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.listCompanyName;
						bIsError = true;
					}
					break;
				case 'bt_list_company_address1' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.listCompanyAddress;
						bIsError = true;
					}
					break;
				case 'bt_list_company_city' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.listCompanyCity;
						bIsError = true;
					}
					break;
				case 'bt_list_company_zip' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.listCompanyZip;
						bIsError = true;
					}
					break;
				case 'bt_list_company_country' :
					if (field.value == '') {
						oThis.aError[iCounter] = oThis.msgs.listCompanyCountry;
						bIsError = true;
					}
					break;
				case 'bCheckOrderStatus' :
					if (field.value == 1) {
						bCheckOrderStatus = true;
					}
					break;
				case 'bt_order_status[]' :
					if ($('input:checked[name="bt_order_status[]"]').val() == 0) {
						oThis.aError[iCounter] = oThis.msgs.statuses;
						bIsError = true;
					}
					else {
						bCheckedOrderStatus = true;
					}
					break;
				case 'bt_dashboard_delay':
					if (field.value == '0') {
						oThis.aError[iCounter] = oThis.msgs.delay;
						bIsError = true;
					}
					break;
				case 'bt_voucher_automation':
					if ($('#bt_voucher_automation :checked').val() == 0) {
						oThis.aError[iCounter] = oThis.msgs.automation;
						bIsError = true;
					}
					break;
				case 'bt_custom_automation':
					if (field.value == ''
						&& $('#bt_voucher_automation :checked').val() == 'other'
					) {
						oThis.aError[iCounter] = oThis.msgs.customAutomation;
						bIsError = true;
					}
					break;
				case 'bt_discount_type':
					if ($('#bt_discount_type :checked').val() == 'none'
						&& $('#bt_voucher_automation :checked').val() != 0
					) {
						oThis.aError[iCounter] = oThis.msgs.voucherDiscountType;
						bIsError = true;
					}
					break;
				case 'bt_voucher_percent':
					if ($('#bt_discount_type :checked').val() == 'percentage'
						&& (isNaN(field.value) || field.value == 0)
					) {
						oThis.aError[iCounter] = oThis.msgs.voucherPercent;
						bIsError = true;
					}
					break;
				case 'bt_voucher_amount':
					if ($('#bt_discount_type :checked').val() == 'amount'
						&& (isNaN(field.value) || field.value == 0)
					) {
						oThis.aError[iCounter] = oThis.msgs.voucherAmount;
						bIsError = true;
					}
					break;
				case 'bt_voucher_minimum':
					if (isNaN(field.value)) {
						oThis.aError[iCounter] = oThis.msgs.voucherMinimum;
						bIsError = true;
					}
					break;
				case 'bt_voucher_validity':
					if (isNaN(field.value) || field.value == 0) {
						oThis.aError[iCounter] = oThis.msgs.voucherValidity;
						bIsError = true;
					}
					break;
				case 'bt_mc_signup_form_display':
					if ($('input:checked[name="bt_mc_signup_form_use"]').val() == 1
						&& field.value == '0'
					) {
						oThis.aError[iCounter] = oThis.msgs.signupDisplay;
						bIsError = true;
					}
					break;
				case 'bt_mc_signup_popup_text_valign_custom':
				case 'bt_mc_signup_popup_text_halign_custom':
					if (field.value != ''
						&& (isNaN(field.value)
						|| field.value == 0
						|| field.value > 100)
					) {
						oThis.aError[iCounter] = oThis.msgs.signupPopupTextAlign;
						bIsError = true;
					}
					break;
				default:
					break;
			}

			if (($('input[name="' + field.name + '"]') != undefined
				|| $('textarea[name="' + field.name + '"]') != undefined
				|| $('select[name="' + field.name + '"]').length != undefined)
				&& bIsError == true
			) {
				if ($('input[name="' + field.name + '"]').length != 0) {
					$('input[name="' + field.name + '"]').parent().addClass('has-error');
					$('input[name="' + field.name + '"]').append('<span class="icon-remove-sign"></span>');
				}
				if ($('textarea[name="' + field.name + '"]').length != 0) {
					$('textarea[name="' + field.name + '"]').parent().addClass('has-error');
					$('textarea[name="' + field.name + '"]').append('<span class="icon-remove-sign"></span>');
				}
				if ($('select[name="' + field.name + '"]').length != 0) {
					$('select[name="' + field.name + '"]').parent().addClass('has-error');
					$('select[name="' + field.name + '"]').append('<span class="icon-remove-sign"></span>');
				}
				++iCounter;
			}
		});

		if (bCheckOrderStatus && !bCheckedOrderStatus) {
			oThis.aError[iCounter] = oThis.msgs.statuses;
			bIsError = true;
		}

		// use case - no errors in form
		if (oThis.aError.length == 0 && !bIsError) {
			// set loading bar
			if (sLoadBar) {
				// hide the elt to hide in any case
				if (bSubmit && sToHide != null) {
					oThis.hide(sToHide, true);
				}
				$('#bt_loading_div_'+sLoadBar).show();
			}

			// use case - Ajax request
			if (bSubmit == undefined || bSubmit == null || !bSubmit) {
				if (sLoadBar && sToHide != null) {
					oThis.hide(sToHide, true);
				}

				// format object of fields in string to execute Ajax request
				var sFormParams = $.param(fields);

				if (sRequestParam != null && sRequestParam != '') {
					sFormParams = sRequestParam + '&' + sFormParams;
				}

				// execute Ajax request
				this.ajax(sURI, sFormParams, sToDisplay, sToHide, bFancyBox, null, sLoadBar, sScrollTo, oCallBack);

				return true;
			}
			// use case - send form
			else {
				document.forms[sForm].submit();
				return true;
			}
		}
		// display errors
		this.displayError(sErrorType);

		// set loading bar
		if (sLoadBar) {
			$('#bt_loading_div_'+sLoadBar).hide();
		}

		return false;
	};


	/**
	 * ajax() method execute XHR
	 *
	 * @param string sURI : query params used for XHR
	 * @param string sParams :
	 * @param string sToShow :
	 * @param string sToHide :
	 * @param bool bFancyBox : used only for fancybox in xhr
	 * @param bool bFancyBoxActivity : used only for fancybox in xhr
	 * @param string sLoadBar : used only for loading
	 * @param string sScrollTo : used only for scrolling
	 * @param obj oCallBack : used only for callback to execute as ajax request
	 * @return string : HTML returned by smarty
	 */
	this.ajax = function(sURI, sParams, sToShow, sToHide, bFancyBox, bFancyBoxActivity, sLoadBar, sScrollTo, oCallBack) {
		sParams = 'sMode=xhr' + ((sParams == null || sParams == undefined) ? '' : '&' + sParams) ;

		// configure XHR
		$.ajax({
			type : 'POST',
			url : sURI,
			data : sParams,
			dataType : 'html',
			success: function(data) {
				// hide loading bar
				if (sLoadBar) {
					$('#bt_loading_div_'+sLoadBar).hide();
				}
				if (bFancyBox) {
					// update fancybox content
					$.fancybox(data);
				}
				else if (sToShow != null && sToHide != null) {
					// same hide and show
					if (sToShow == sToHide) {
						oThis.hide(sToHide);
						setTimeout('', 1000);
						oThis.show(sToShow, data);
					}
					else {
						oThis.hide(sToHide);
						setTimeout('', 1000);
						oThis.show(sToShow, data);
					}
				}
				else if (sToShow != null) {
					oThis.show(sToShow, data);
				}
				else if (sToHide != null) {
					oThis.hide(sToHide);
				}

				if (sScrollTo !== null && typeof sScrollTo !== 'undefined' && $(sScrollTo).length != 0) {
					var iPosTop = $(sScrollTo).offset().top-30;
					if(iPosTop < 0) iPosTop = 0;

					$(document).scrollTop(iPosTop);
				}

				// execute others ajax request if needed. In this case, we can update any other tab from the module in the same time
				if (oCallBack != null && oCallBack.length != 0) {
					for (var fx in oCallBack) {
						oThis.ajax(oCallBack[fx].url, oCallBack[fx].params, oCallBack[fx].toShow, oCallBack[fx].toHide, oCallBack[fx].bFancybox, oCallBack[fx].bFancyboxActivity, oCallBack[fx].sLoadbar, oCallBack[fx].sScrollTo , oCallBack[fx].oCallBack);
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				oThis.aError[0] = 'Internal server error';
				// display errors
				this.displayError(sLoadBar);
			}
		});
	};

	/**
	 * displayError() method display errors
	 *
	 * @param string sType : type of container
	 * @return bool
	 */
	this.displayError = function(sType){
		if (oThis.aError.length != 0) {
			var sError = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button><ul class="list-unstyled">';
			for (var i = 0; i < oThis.aError.length;++i) {
				sError += '<li>' + oThis.aError[i] + '</li>';
			}
			sError += '</ul></div>';
			$("#bt_error_" + sType).html(sError);
			$("#bt_error_" + sType).slideDown();

			// flush errors
			oThis.aError = [];

			return false;
		}
	};


	/**
	 * changeSelect() method displays or hide related option form
	 *
	 * @param string sId : type of container
	 * @param mixed mDestId
	 * @param string sDestId2
	 * @param string sType of second dest id
	 * @param bool bForce
	 * @param bool mVal
	 */
	this.changeSelect = function(sId, mDestId, sDestId2, sDestIdToHide, bForce, mVal){
		if (bForce) {
			if (typeof mDestId == 'string') {
				mDestId = [mDestId];
			}
			for (var i = 0; i < mDestId.length; ++i) {
				if (mVal) {
					$("#" + mDestId[i]).fadeIn('fast', function() {$("#" + mDestId[i]).css('display', 'block')});
				}
				else {
					$("#" + mDestId[i]).fadeOut('fast');
				}
			}
		}
		else {
			$("#" + sId).bind('change', function (event){
				$("#" + sId + " input:checked").each(function (){
					switch ($(this).val()) {
						case 'true' :
							// display option features
							$("#" + sDestId).fadeIn('fast', function() {$("#" + sDestId).css('display', 'block')});
							break;
						default:
							// hide option features
							$("#" + sDestId).fadeOut('fast');

							// set to false
							if (sDestId2 && sDestIdToHide) {
								$("#" + sDestId2 + " input").each(function (){
										switch ($(this).val()) {
											case 'false' :
												$(this).attr('checked', 'checked');
												// hide option features
												$("#" + sDestIdToHide).fadeOut('fast');
												break;
											default:
												$(this).attr('checked', '');
												break;
										}
									}
								);
							}
							break;
					}
				});
			});
		}
	};

	/**
	 * selectAll() method select / deselect all checkbox
	 *
	 * @param string sTarget : all checkbox to process
	 * @param string sType : type of elt to check
	 */
	this.selectAll = function(sTarget, sType){
		if (sType == 'check') {
			$(sTarget).attr('checked', true);
		}
		else{
			$(sTarget).attr('checked', false);
		}
	};

	/**
	 * synchronizeData() method synchronize customers or products or orders data to MC
	 *
	 * @param array aParams
	 * @param json
	 */
	this.synchronizeData = function(aParams) {
		var sURI = aParams.sURI;
		var sParams = aParams.sParams;
		var sSyncType = aParams.sSyncType;
		var sListId = aParams.sListId;
		var sStoreId = aParams.sStoreId;
		var iStep = aParams.iStep;
		var iFloor = aParams.iFloor;
		var iTotal = aParams.iTotal;
		var iProcess = aParams.iProcess;
		var sDisplayedCounter = aParams.sDisplayedCounter;
		var sDisplayedBlock = aParams.sDisplayedBlock;
		var sDisplayTotal = aParams.sDisplayTotal;
		var sLoaderBar = aParams.sLoaderBar;
		var sErrorContainer = aParams.sErrorContainer;
		var oCallback = aParams.oCallback;

		if (iFloor == 0) {
			$(sDisplayTotal).css('display','none');
			$(sDisplayTotal).html('');
		}
		// hide
		$(sErrorContainer).hide();

		// variable to control the XHR synchro
		oThis.bSyncFlag = true;

		// concatenate common params
		sParams += '&sSyncType='+sSyncType+'&sListId='+sListId+'&sStoreId='+sStoreId+'&iStep='+iStep +'&iFloor='+iFloor+'&iTotal='+iTotal+'&iProcess='+iProcess;

		// use case - newsletter synching
		if (sSyncType == 'newsletter') {
			// case of migrating old list members
			sParams += '&bOldListSync=' + aParams.bOldListSync;
		}

		// use case - product synching
		if (sSyncType == 'product') {
			if (aParams.sNewSyncType != '') {
				sParams += '&sNewSyncType=' + aParams.sNewSyncType;
			}
		}

		// use case - order synching
		if (sSyncType == 'order') {
			if (aParams.sDateFrom != '') {
				sParams += '&sDateFrom='+aParams.sDateFrom;
			}
			if (aParams.sDateTo != '') {
				sParams += '&sDateTo='+aParams.sDateTo;
			}
		}

		$.ajax({
			type : 'POST',
			url : sURI,
			data : sParams,
			dataType : 'json',
			async : true,
			success: function(data) {
				// use case - no error
				if (data.status == 'ok') {
					var iProcessedItem = data.counter;
					// modify the displayed counter value
					$(sDisplayedCounter).val(iProcessedItem);
					$(sLoaderBar).attr('width', parseInt(iProcessedItem / iTotal * iStep));

					// use case - recursive ajax query
					if (iProcessedItem < iTotal) {
						aParams.iFloor = data.counter;
						aParams.iProcess = data.process;
						oThis.synchronizeData(aParams);
					}
					// use case - finalize the recursive ajax query
					else {
						$(sDisplayedCounter).val(iTotal);
						$(sLoaderBar).attr('width', 1);
						$(sDisplayedBlock).hide();
						$(sLoaderBar).hide();
						$('#bt_sync_button').hide();
						$('#bt_loader_img').hide();

						// execute others ajax request if needed. In this case, we can update any other tab from the module in the same time
						if (oCallback != null && oCallback.length != 0) {
							for (var fx in oCallback) {
								if (oCallback[fx].name == 'updateSyncForm') {
									oCallback[fx].params += '&processed='+data.process
								}
								oThis.ajax(oCallback[fx].url, oCallback[fx].params, oCallback[fx].toShow, oCallback[fx].toHide, oCallback[fx].bFancybox, oCallback[fx].bFancyboxActivity, oCallback[fx].sLoadbar, oCallback[fx].sScrollTo , oCallback[fx].oCallback);
							}
						}

						// variable to control the XHR data feed and reset all control params
						oThis.bSyncFlag = false;
						aParams.iStep = 0;
						aParams.iProcess = 0;
						aParams.iFloor = 0;
					}
				}
				// use case - errors
				else {
					$(sDisplayedBlock).hide();
					oThis.bSyncFlag = false;
					for (key in data.error) {
						oThis.aError.push(data.error[key].msg);
					}
					oThis.displayError(sErrorContainer);
					// flush errors
					oThis.aError = [];
					//setTimeout(function () {$(sDisplayedBlock).hide();}, 1000);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				$(sDisplayedBlock).hide();
				oThis.bSyncFlag = false;
				oThis.aError[0] = 'Internal Ajax error';
				oThis.displayError(sErrorContainer);
				// flush errors
				oThis.aError = [];
				//setTimeout(function () {$(sDisplayedBlock).hide();}, 1000);
			}
		});
	};


	/**
	 * clearTimeOut() method clear the current timeout
	 *
	 */
	this.clearTimeOut = function() {
		if (this.oTimer.length != 0) {
			clearTimeout(this.oTimer.timerOut);
		}
	};


	/**
	 * timingBox() method define a time box
	 *
	 * @param int sec
	 * @param int min
	 * @param int hour
	 */
	this.timingBox = function(sec, min, hour) {
		this.secondes = sec;
		this.minutes = min;
		this.hours = hour;
		this.timerOut = null;
	};


	/**
	 * chrono() method define a time box
	 *
	 * @param string jsObjName
	 * @param int time
	 * @param string tagId
	 * @param string tagClickId
	 * @param string eventType
	 */
	this.chrono = function(jsObjName, time, tagId, tagClickId, eventType) {
		this.oTimer = new this.timingBox(0, 0, 0);
		this.oTimer.secondes = time;

		if (this.oTimer.secondes > 60) {
			this.oTimer.minutes = Math.floor(this.oTimer.secondes / 60);
			this.oTimer.secondes = this.oTimer.secondes - this.oTimer.minutes * 60;
		}
		if (this.oTimer.minutes > 60) {
			this.oTimer.hours = Math.floor(this.oTimer.minutes / 60);
			this.oTimer.minutes = Math.floor(this.oTimer.minutes - this.oTimer.hours * 60);
		}

		// adjust values according to numbers < 10 to add a zero as prefix
		if (this.oTimer.hours < 10) rhours = '0'+this.oTimer.hours; else rhours = this.oTimer.hours;
		if (this.oTimer.minutes < 10) rminutes = '0'+this.oTimer.minutes; else rminutes = this.oTimer.minutes;
		if (this.oTimer.secondes < 10) rsecondes = '0'+this.oTimer.secondes; else rsecondes = this.oTimer.secondes;

		time = time - 1;
		$(tagId).html(''+rsecondes+'');
		if (time > 0){
			this.oTimer.timerOut = setTimeout(""+jsObjName+".chrono('"+jsObjName+"', "+time+", '"+tagId+"', '"+tagClickId+"', '"+eventType+"')", 1000);
		}
		else{
			this.clearTimeOut();
			$(tagClickId).trigger(''+eventType+'');
		}
	};

	/**
	 * copyToClipboard() method copy text to the clipboard
	 *
	 * @param string text
	 * @param string el
	 */
	this.copyToClipboard = function(text, el) {
		var copyTest = document.queryCommandSupported('copy');
		var elOriginalText = el.attr('data-original-title');

		if (copyTest === true) {
			var copyTextArea = document.createElement("textarea");
			copyTextArea.value = text;
			document.body.appendChild(copyTextArea);
			copyTextArea.select();
			try {
				var successful = document.execCommand('copy');
				var msg = successful ? 'Copied!' : 'Whoops, not copied!';
				el.attr('data-original-title', msg).tooltip('show');
			}
			catch (err) {
				console.log('Oops, unable to copy');
			}
			document.body.removeChild(copyTextArea);
			el.attr('data-original-title', elOriginalText);
		}
		else {
			// Fallback if browser doesn't support .execCommand('copy')
			window.prompt("Copy to clipboard: Ctrl+C or Command+C, Enter", text);
		}
	}

	/**
	 * activateBadge() method allows you to activate a badge into a table
	 *
	 * @param string elt
	 */
	this.activateBadge = function(elt) {
		if ($(elt).is('.action-enabled')){
			$(elt).removeClass('action-enabled');
			$(elt).addClass('action-disabled');
			$(elt).children('i').removeClass('icon-check');
			$(elt).parent().removeClass('success');
			$(elt).children('i').addClass('icon-remove');
			$(elt).parent().addClass('danger');
			$(elt).children('input').removeAttr('checked', 'checked');
			$(elt).children('input').val(0);
		}
		else {
			$(elt).removeClass('action-disabled');
			$(elt).addClass('action-enabled');
			$(elt).children('i').removeClass('icon-remove');
			$(elt).parent().removeClass('danger');
			$(elt).children('i').addClass('icon-check');
			$(elt).parent().addClass('success');
			$(elt).children('input').attr('checked', 'checked');
			$(elt).children('input').val(1);
		}
	};

	/**
	 * formatFancyboxUrl() method allows you to format fancybox URL to take into account form field values
	 *
	 * @param string type
	 * @param string uri
	 * @param string triggerElt
	 */
	this.formatFancyboxUrl = function(type, uri, triggerElt, errorElt) {
		switch (type) {
			case 'newsletter':
				var user_type = $('#bt_customer_export_type option:selected').val();
				var use_nl_module = $('input[name="bt_use_nl_module"]').val();
				var user_language = $('#bt_user_language option:selected').val();
				var nl_module = $('#bt_nl_module option:selected').val();
				var nl_form_submit = $('#bt_nl_form_submit').val();
				var nl_form_email = $('#bt_nl_form_email').val();
				var nl_form_ajax = $('input[name="bt_nl_form_ajax"]').val();

				uri += '&bt_customer_export_type=' + user_type + '&bt_use_nl_module=' + use_nl_module + '&bt_user_language=' + user_language + '&bt_nl_module=' + nl_module + '&bt_nl_form_submit=' + nl_form_submit + '&bt_nl_form_email=' + nl_form_email + '&bt_nl_form_ajax=' + nl_form_ajax;
				break;
			case 'order':
				if ($(bt_order_date_from).val() == '') {
					$(errorElt).slideDown();
					return false;
				}
				else {
					$(errorElt).slideUp();
					uri += "&sDateFrom=" + encodeURI($('#bt_order_date_from').val()) + "&sDateTo=" + encodeURI($('#bt_order_date_to').val());
				}
				break;
			default:
				break;
		}

		$(triggerElt).attr('href', uri);

		// trigger the click event
		$(triggerElt).click();
	};
};
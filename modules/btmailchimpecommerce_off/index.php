<?php
/**
 * index.php file execute module for Front Office in order to redirect links of the return checkout URLs from the abandoned cart e-mails
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/btmailchimpecommerce.php');

/* instantiate */
$oModule = new BTMailchimpEcommerce();

// get the current checkout URL
if (!empty(\BTMailchimpEcommerce::$bCompare17)) {
	$sCheckOutCtrl = 'cart';
}
else {
	$sCheckOutCtrl = Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 ? 'order-opc' : 'order';
}
$sCheckoutUrl = Context::getContext()->link->getPageLink($sCheckOutCtrl);

if (empty($_SERVER['PATH_INFO'])) {
	$aPathInfo = explode('index.php', $_SERVER['REQUEST_URI']);
	$_SERVER['PATH_INFO'] = $aPathInfo[1];
}

// use case - path_info set to on in the http.conf
if (!empty($_SERVER['PATH_INFO'])) {
	// explode the path_info
	list ($iCustomerId, $iCartId, $sSecureHashKey) = explode('/', substr($_SERVER['PATH_INFO'], 1));

	// use case - only if we detect the arguments needed for the checkout URL from the abandoned cart e-mails
	if (!empty($iCustomerId)
		&& !empty($iCartId)
		&& !empty($sSecureHashKey)
		&& $sSecureHashKey == \MCE\Tools::setSecureKey(_MCE_SECURE_HASH, $iCustomerId, $iCartId)
	) {
		if (strstr($sCheckoutUrl, '?') === false) {
			$sCheckoutUrl .= '?';
		}
		else {
			$sCheckoutUrl .= '&';
		}
		// use case - on PS 1,7, the cart controller URL needs apparently to add the "action=show" parameter to display the cart page because on ps 1.7.1.2 for a customer his shop redirected to the home page without the action parameter
		if (!empty(\BTMailchimpEcommerce::$bCompare17)
			&& !strstr($sCheckoutUrl, 'action=show')
		) {
			$sCheckoutUrl .= 'action=show&';
		}

		$sCheckoutUrl .= 'bt_cl=' . $iCustomerId . '&bt_ct=' . $iCartId . '&bt_mcsid=' . $sSecureHashKey;
	}
}

// whatever the referrer is, we redirect on the check out URL because this index.php should be requested only by the links coming from the abandoned cart e-mails.
header("Location: " . $sCheckoutUrl);
exit(0);
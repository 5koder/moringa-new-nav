<?php
/**
 * Mailchimp Pro - Newsletter sync and eCommerce Automation
 *
 * @author    BusinessTech.fr - https://www.businesstech.fr
 * @copyright Business Tech 2021 - https://www.businesstech.fr
 * @license   Commercial
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

class BTMailchimpEcommerceListModuleFrontController extends ModuleFrontController
{

	/**
	 * method manage post data
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function postProcess()
	{
		// get the data returned by MC
		$token = Tools::getValue('token');
		$type = Tools::getValue('type');
		$data = Tools::getValue('data');
		$response = false;

		// test event from MC
		if (
			!empty($token)
			&& !empty($type)
			&& $token = BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN']
		) {
			// execute a custom module's hook
			if ($this->module->hookActionListWebhookProcess(array('type' => $type, 'data' => $data))) {
				$response = true;
			}
		}

		die(Tools::jsonEncode($response));
	}
}

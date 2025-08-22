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

class BTMailchimpEcommerceBatchModuleFrontController extends ModuleFrontController
{
	/**
	 * method manage post data
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function postProcess()
	{
		$token = Tools::getValue('token');
		$data = Tools::getValue('data');
		$response = false;

		if (
			!empty($data)
			&& is_array($data)
		) {
			// get the batch id
			if (!empty($data['id'])) {
				$params['id'] = $data['id'];
			}

			// get the response body url
			if (!empty($data['response_body_url'])) {
				$params['response_body_url'] = $data['response_body_url'];
			}
			// manage the status
			if (
				!empty($data['status'])
				&& $data['status'] == 'finished'
			) {
				$params['status'] = 'finished';
			}
		}

		// test if the batch comes from the automatic synching process - if it comes from the manual synching that the merchant must do first then we don't do anything
		if (
			!empty($token)
			&& $token == BTMailchimpEcommerce::$conf['MCE_CRON_TOKEN']
			&& !empty($params['status'])
			&& $params['status'] == 'finished'
			&& !empty($params['id'])
			&& !empty($params['response_body_url'])
		) {
			// execute a custom module's hook
			if ($this->module->hookActionBatchWebhookProcess($params)) {
				$response = true;	
			}
		}

		die(Tools::jsonEncode($response));
	}
}

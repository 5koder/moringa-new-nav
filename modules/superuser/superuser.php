<?php
/**
* Super User Module
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2017 idnovate
*  @license   See above
*/

class SuperUser extends Module
{
    protected $errors = array();
    protected $success;

    public function __construct()
    {
        $this->name = 'superuser';
        $this->tab = 'front_office_features';
        $this->version = '2.3.2';
        $this->author = 'idnovate';
        $this->module_key = '7ef93903ff837a50dd1702bcc2553ab7';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Super User');
        $this->description = $this->l('Log in to your shop as one of your customers!');
        $this->addons_id_product = '7280';

        /* Backward compatibility */
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
            $this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        }
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('adminCustomers') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('adminOrder')) {
            return false;
        }
        return true;
    }

    public function hookDisplayHeader()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return false;
        }
        if (!$this->isBoLogged()) {
            return false;
        }
        if (Module::isEnabled($this->name) &&
            $this->context->customer->id &&
            $this->context->customer->logged == '1'
        ) {
            $this->context->controller->addCSS($this->_path.'views/css/front.css');
            $params = array('id_customer' => $this->context->customer->id, 'secure_key' => $this->context->customer->passwd, 'use_last_cart' => '1');
            $this->context->smarty->assign(array(
                'controller_superuser' => version_compare(_PS_VERSION_, '1.5', '<') ? (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/superuser/setuser.php?'.http_build_query($params) : $this->context->link->getModuleLink('superuser', 'setuser', $params, true),
                'controller_logout' => $this->context->link->getPageLink('index', true, NULL, 'mylogout'),
                'su_customer' => $this->context->customer
            ));
            return $this->display(__FILE__, 'front_banner.tpl');
        }
        return false;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return true;
        }
        if (Tools::getValue('configure') === $this->name) {
            if (version_compare(_PS_VERSION_, '1.7', '>=') ||
                version_compare(_PS_VERSION_, '1.6', '<')
            ) {
                $this->context->controller->setMedia();
            }
            $this->context->controller->addJqueryPlugin(array('typewatch', 'fancybox', 'autocomplete'));
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        if (Module::isEnabled($this->name) &&
            ((version_compare(_PS_VERSION_, '1.5', '>=') && (Tools::strtolower(Dispatcher::getInstance()->getController()) == 'admincustomers') || Tools::strtolower(Dispatcher::getInstance()->getController()) == 'adminorders') ||
             (version_compare(_PS_VERSION_, '1.5', '<') && Tools::strtolower(Tools::getValue('tab')) == 'admincustomers') ||
             Tools::getValue('configure') == $this->name || Tools::getValue('tab') == $this->name)
        ) {
            $params = array();
            $customer = new Customer();
            $this->context->smarty->assign(array(
                'action_su' => $this->l('Connect as'),
                'action_superuser' => $this->l('Connect as this customer'),
                'this_path_bo' => $this->_path,
                'iso_code' => $this->context->language->iso_code,
                'url' => false,
                'show_button' => true,
                'superuser_token' => Tools::getAdminTokenLite($this->name)
            ));
            if ((Tools::getIsset('viewcustomer') && Tools::getValue('id_customer') > 0) ||
                (Tools::getIsset('vieworder') && Tools::getValue('id_order') > 0)) {
                $this->context->controller->addCSS($this->_path.'views/css/back.css');
                if (Tools::getIsset('vieworder') && Tools::getValue('id_order') > 0) {
                    $order = new Order((int)Tools::getValue('id_order'));
                    $id_customer = $order->id_customer;
                } else {
                    $id_customer = Tools::getValue('id_customer');
                }
                $customer = new Customer((int)$id_customer);
                $params = array('id_customer' => $customer->id, 'secure_key' => $customer->passwd, 'use_last_cart' => '1');
            }
            if ($customer->is_guest === '1') {
                $this->context->smarty->assign(array(
                    'controller_superuser' => 'javascript:isGuest();',
                ));
            } else {
                $this->context->smarty->assign(array(
                    'controller_superuser' => version_compare(_PS_VERSION_, '1.5', '<') ? (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/superuser/setuser.php?'.http_build_query($params) : $this->context->link->getModuleLink('superuser', 'setuser', $params, true),
                ));
            }
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                return $this->display(__FILE__, '/views/templates/hook/bo_customers.tpl');
            } else {
                if (Tools::strtolower(Dispatcher::getInstance()->getController()) == 'adminorders') {
                    return $this->display(__FILE__, 'bo_orders.tpl');
                } else {
                    return $this->display(__FILE__, 'bo_customers.tpl');
                }
            }
        }
        return false;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitSuperuserModule') == true) {
            if ($customer = new Customer(Tools::getValue('SUPERUSER_CUSTOMERS'))) {
                $params = array('id_customer' => $customer->id, 'secure_key' => Tools::encrypt($customer->passwd), 'use_last_cart' => '1');
                $this->context->smarty->assign(array(
                    'su_path'                   => $this->_path,
                    'errors'                    => $this->errors,
                    'success'                   => $this->success,
                    'cookie_id_customer'        => $customer->id,
                    'cookie_customer_firstname' => $customer->firstname,
                    'cookie_customer_lastname'  => $customer->lastname,
                    'customers'                 => Customer::getCustomers(),
                    'displayName'               => $this->displayName,
                    'shop_ori'                  => Context::getContext()->shop->id,
                    'customer'                  => (array)$customer,
                    'customers_controller'      => ((version_compare(_PS_VERSION_, '1.5', '<') && strtolower(Tools::getValue('tab')) == 'admincustomers') || (version_compare(_PS_VERSION_, '1.5', '>=') && strtolower(Dispatcher::getInstance()->getController()) == "admincustomers")) ? true : false,
                    'frontoffice_url'           => version_compare(_PS_VERSION_, '1.5', '<') ? (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/superuser/setuser.php?'.http_build_query($params) : $this->context->link->getModuleLink('superuser', 'setuser', $params, true),

                ));
                $modulePretty = false;
                if ($this->isModuleActive('prettyurls') || $this->isModuleActive('purls') || $this->isModuleActive('fsadvancedurl')) {
                    $modulePretty = true;
                }
		        if ($modulePretty !== false) {
		        	$this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
			        $this->context->smarty->assign(array(
			            'frontoffice_url' => $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=setuser&'.http_build_query($params),
			        ));
		        }
            }
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $this->renderForm14();
        } else {
            return $this->renderForm();
        }
    }

    protected function renderForm()
    {
        $html = '';

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
        );

        $html .= $helper->generateForm(array($this->getConfigForm()));
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->context->smarty->assign(array(
                'this_path'     => $this->_path,
                'support_id'    => $this->addons_id_product
            ));

            $available_iso_codes = array('en', 'es');
            $default_iso_code = 'en';
            $template_iso_suffix = in_array($this->context->language->iso_code, $available_iso_codes) ? $this->context->language->iso_code : $default_iso_code;
            $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/company/information_'.$template_iso_suffix.'.tpl');
        }

        return $html;
    }

    protected function renderForm14()
    {
        $html = '';
        $html .= $this->_displayIdnovateHeader();

        $helper = new Helper();

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
        );

        $html .= $helper->generateForm(array($this->getConfigForm()));

        return $html;
    }

    protected function getConfigForm()
    {
        /*
        $customers_list = Customer::getCustomers();

        foreach ($customers_list as &$customer) {
            $customer['id'] = $customer['id_customer'];
            $customer['name'] = $customer['id_customer'].' - '.$customer['firstname'].' '.$customer['lastname'];
        }

        $customers_list = $this->orderMultiDimensionalArray($customers_list, 'id', true);
        */

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Customer'),
                    'icon' => 'icon-user',
                ),
                'input' => array(
                    /*
                    array(
                        'col' => 9,
                        'type' => 'free',
                        'label' => '',
                        'name' => 'SUPERUSER_CONNECT_FORM',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customers'),
                        'name' => 'SUPERUSER_CUSTOMERS',
                        'options' => array(
                            'query' => $customers_list,
                            'id' => 'id_customer',
                            'name' => 'name',
                        ),
                        'hint' => 'Select the customer that you want to log with in frontoffice'
                    ),
                    */
                    array(
                        'type' => 'free',
                        'label' => '',
                        'name' => 'SUPERUSER_CUSTOMERS',
                    ),
                    /*
                    array(
                        'col' => 9,
                        'type' => 'free',
                        'label' => '',
                        'name' => 'SUPERUSER_CONNECT_BUTTON',
                    ),
                    */
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        $params = array('use_last_cart' => '1');
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'controller' => version_compare(_PS_VERSION_, '1.5', '<') ? (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/superuser/setuser.php?'.http_build_query($params) : $this->context->link->getModuleLink('superuser', 'setuser', $params, true),
            )
        );
        $fields = array();
        //$fields['SUPERUSER_CONNECT_BUTTON'] = '<div class="margin-form"><button type="submit" class="btn btn-primary button" name="submitSuperuserModule"><i class="icon-key"></i>'.$this->l('Connect as customer').'</button></div>';
        $fields['SUPERUSER_CUSTOMERS'] = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/customers.tpl');
        //$fields['SUPERUSER_CONNECT_FORM'] = $this->context->smarty->fetch($this->local_path.'views/templates/admin/admin.tpl');

        return $fields;
    }

    protected function _displayIdnovateHeader()
    {
        $this->context->smarty->assign('this_path', $this->_path);

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            return $this->display(__FILE__, '/views/templates/hook/info.tpl');
        } else {
            return $this->display(__FILE__, 'info.tpl');
        }
    }

    public function hookAdminCustomers($param)
    {
        if (isset($param['id_customer'])) {
            //Customer block
            $id_customer = (int)$param['id_customer'];
        } else {
            //Order block
            $order = new Order($param['id_order']);
            $id_customer = (int)$order->id_customer;
        }

        $customer = new Customer($id_customer);
        $params = array('id_customer' => $id_customer, 'secure_key' => Tools::encrypt($customer->passwd), 'use_last_cart' => '1');
        $this->context->smarty->assign(array(
            'displayName'               => $this->displayName,
            'su_path'                   => $this->_path,
            'shop_ori'                  => Context::getContext()->shop->id,
            'customer'                  => (array)$customer,
            'customers_controller'      => ((version_compare(_PS_VERSION_, '1.5', '<') && strtolower(Tools::getValue('tab')) == 'admincustomers') || (version_compare(_PS_VERSION_, '1.5', '>=') && strtolower(Dispatcher::getInstance()->getController()) == "admincustomers")) ? true : false,
            'frontoffice_url'           => version_compare(_PS_VERSION_, '1.5', '<') ? (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/superuser/setuser.php?'.http_build_query($params) : $this->context->link->getModuleLink('superuser', 'setuser', $params, true),
        ));

        $modulePretty = false;
        if ($this->isModuleActive('prettyurls') || $this->isModuleActive('purls') || $this->isModuleActive('fsadvancedurl')) {
            $modulePretty = true;
        }
        if ($modulePretty !== false) {
        	$this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__;
	        $this->context->smarty->assign(array(
	            'frontoffice_url' => $this_path_ssl.'index.php?fc=module&module='.$this->name.'&controller=setuser&'.http_build_query($params),
	        ));
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            echo $this->display(__FILE__, 'views/templates/hook/backoffice_block.tpl');
        } else {
            echo $this->display(__FILE__, 'backoffice_block.tpl');
        }
    }

    public function hookAdminOrder($param)
    {
        $this->hookAdminCustomers($param);
    }

    public function isModuleActive($name_module, $function_exist = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.2', '>=')) {
            return false;
        }
        if (Module::isInstalled($name_module)) {
            $module = Module::getInstanceByName($name_module);
            if ((Validate::isLoadedObject($module) && $module->active) 
                || (Validate::isLoadedObject($module) && $name_module == 'prettyurls')
                || (Validate::isLoadedObject($module) && $name_module == 'purls')
                || (Validate::isLoadedObject($module) && $name_module == 'fsadvancedurl')
            ) {
                if ($function_exist) {
                    if (method_exists($module, $function_exist)) {
                        return $module;
                    } else {
                        return false;
                    }
                }
                return $module;
            }
        }
        return false;
    }

	public function orderMultiDimensionalArray($toOrderArray, $field, $inverse = false)
	{
	    $position = array();
	    $newRow = array();
	    foreach ($toOrderArray as $key => $row) {
	            $position[$key]  = $row[$field];
	            $newRow[$key] = $row;
	    }
	    if ($inverse) {
	        arsort($position);
	    }
	    else {
	        asort($position);
	    }
	    $returnArray = array();
	    foreach ($position as $key => $pos) {     
	        $returnArray[] = $newRow[$key];
	    }
	    return $returnArray;
	}

    private function isBoLogged()
    {
        $cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));
        $employee = new Employee((int)$cookie->id_employee);
        if (Validate::isLoadedObject($employee) &&
            $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd) &&
            (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))
        ) {
            return true;
        } else {
            return false;
        }
    }
}

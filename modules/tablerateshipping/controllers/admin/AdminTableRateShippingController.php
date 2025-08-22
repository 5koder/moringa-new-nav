<?php
/**
 * Overrides carrier shipping with Table Rate Shipping
 *
 * Table Rate Shipping by Kahanit(https://www.kahanit.com/) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at https://www.kahanit.com/.
 * Permissions beyond the scope of this license may be available at https://www.kahanit.com/.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2016 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 */

require_once dirname(__FILE__) . '/../../libraries/kahanit/Products.php';
require_once dirname(__FILE__) . '/../../libraries/TRSCarrierTableRate.php';
require_once dirname(__FILE__) . '/../../libraries/TRSUploadHandler.php';

/**
 * Class AdminTableRateShippingController
 */
class AdminTableRateShippingController extends ModuleAdminController
{
    public function init()
    {
        parent::init();

        if (!Shop::getContextShopID()) {
            $controller = 'AdminTableRateShipping';
            $id_lang = $this->context->language->id;
            $params = array(
                'token'          => Tools::getAdminTokenLite($controller),
                'setShopContext' => 's-' . current(Shop::getContextListShopID())
            );
            $link = Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
            Tools::redirectAdmin($link);
            die();
        }

        $this->bootstrap = true;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/dataTables.bootstrap.min.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/select2.min.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/jquery.fileupload.min.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/jquery.fileupload-ui.min.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/bootstrap-editable.min.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/admin.css');

        $this->addJquery();
        $this->addJqueryUI('ui.widget');
        $this->addJqueryUI('ui.tabs');
        $this->addJqueryUI('ui.sortable');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.dataTables.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/dataTables.bootstrap.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/select2.full.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/tmpl.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.iframe-transport.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.fileupload.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.fileupload-process.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.fileupload-validate.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.fileupload-ui.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/bootstrap-editable.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.serializejson.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/jquery.admintablerateshipping.js');
    }

    public function initContent()
    {
        parent::initContent();

        $ajax = (bool)Tools::getValue('ajax', false);
        if ($ajax) {
            return;
        }

        $this->context->smarty->assign(array(
            'module_name'         => $this->module->name,
            'module_display_name' => $this->module->displayName,
            'module_version'      => $this->module->version,
            'module_views_dir'    => _PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/views/',
            'carriers'            => Carrier::getCarriers($this->context->language->id, true, false, false, null, ALL_CARRIERS),
            'zones'               => array_merge(array(array('id_zone' => 0, 'name' => 'All Zones')), Zone::getZones())
        ));

        $this->setTemplate('configuration.tpl');
    }

    public function displayAjax()
    {
        $method = Tools::getValue('method', false);
        $header = Tools::getValue('header', 'json');

        if ($method !== false && method_exists($this, $method)) {
            if ($header == 'json') {
                header('Content-Type: application/json');
                echo Tools::jsonEncode($this->$method());
            } else {
                echo $this->$method();
            }
        }
        die();
    }

    public function getRuleGroups()
    {
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_zone = (int)Tools::getValue('id_zone', -1);
        $start = (int)Tools::getValue('start', 0);
        $length = (int)Tools::getValue('length', 20);
        $where = '';
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        $records = TRSCarrierTableRate::getRuleGroups($id_carrier, $id_zone, $start, $length, $where, $id_lang, $id_shop);
        $total_records = TRSCarrierTableRate::getRuleGroupsNumRows($id_carrier, $id_zone, $where, $id_shop);

        return array(
            'recordsTotal'    => $total_records,
            'recordsFiltered' => $total_records,
            'data'            => $records
        );
    }

    public function getRules()
    {
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_zone = (int)Tools::getValue('id_zone', -1);
        $id_group = (int)Tools::getValue('id_group', 0);
        $start = (int)Tools::getValue('start', 0);
        $length = (int)Tools::getValue('length', 20);
        $where = '';
        $id_shop = $this->context->shop->id;

        $records = TRSCarrierTableRate::getRules($id_carrier, $id_zone, $id_group, $start, $length, $where, $id_shop);
        $total_records = TRSCarrierTableRate::getRulesNumRows($id_carrier, $id_zone, $id_group, $where, $id_shop);

        return array(
            'recordsTotal'    => $total_records,
            'recordsFiltered' => $total_records,
            'data'            => $records
        );
    }

    public function saveRules()
    {
        $action = Tools::getValue('action', '');
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $records = Tools::getValue('records', array());
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::saveRules($action, $id_carrier, $records, $id_lang, $id_shop);
    }

    public function import()
    {
        $file = Tools::getValue('file', '');
        $file = _PS_ADMIN_DIR_ . '/import/' . $this->module->name . '/' . $file;
        $csv_separator = Tools::getValue('csv_separator', ',');
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::import($file, $csv_separator, $id_carrier, $id_lang, $id_shop);
    }

    public function export()
    {
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $csv_separator = Tools::getValue('csv_separator', ',');
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        TRSCarrierTableRate::export($id_carrier, $csv_separator, $id_lang, $id_shop);
        die();
    }

    public function updateCountryStateCity()
    {
        $update = Tools::getValue('update', false);
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_zone = (int)Tools::getValue('id_zone', -1);
        $id_group = (int)Tools::getValue('id_group', 0);
        $value = Tools::getValue('value', false);
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::updateCountryStateCity(
            $update,
            $id_carrier,
            $id_zone,
            $id_group,
            $value,
            $id_lang,
            $id_shop
        );
    }

    public function updateConditionPriceComment()
    {
        $update = Tools::getValue('update', false);
        $id_carrier_table_rate = Tools::getValue('id_carrier_table_rate', false);
        $value = Tools::getValue('value', false);
        $id_lang = $this->context->language->id;

        return TRSCarrierTableRate::updateConditionPriceComment(
            $update,
            $id_carrier_table_rate,
            $value,
            $id_lang
        );
    }

    public function updateStatus()
    {
        $id_carrier_table_rate = Tools::getValue('id_carrier_table_rate', false);
        $active = (int)Tools::getValue('active', 1);

        return TRSCarrierTableRate::updateStatus($id_carrier_table_rate, $active);
    }

    public function updateOrder()
    {
        $entity = Tools::getValue('entity', '');
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_zone = (int)Tools::getValue('id_zone', -1);
        $order = Tools::getValue('order', array());
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::updateOrder($entity, $id_carrier, $id_zone, $order, $id_shop);
    }

    public function updateProductSelected()
    {
        $id_product = (int)Tools::getValue('id_product', 0);
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $select = (int)Tools::getValue('select', 1);
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::updateProductSelected($id_product, $id_carrier, $select, $id_shop);
    }

    public function deleteRules()
    {
        $entity = Tools::getValue('entity', '');
        $type = Tools::getValue('type', '');
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $data = Tools::getValue('data', false);
        $id_zone = (int)Tools::getValue('id_zone', -1);
        $id_carrier_table_rate = Tools::getValue('id_carrier_table_rate', false);
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::deleteRules(
            $entity,
            $type,
            $id_carrier,
            $data,
            $id_zone,
            $id_carrier_table_rate,
            $id_shop
        );
    }

    public function processAttachment()
    {
        $id_employee = $this->context->cookie->id_employee;
        $token = Tools::getAdminToken('AdminTableRateShipping' . (int)Tab::getIdFromClassName('AdminTableRateShipping') . (int)$id_employee);
        $url = 'index.php?controller=AdminTableRateShipping&token=' . $token . '&ajax=1';

        $uploads = new TRSUploadHandler(
            array(
                'upload_dir' => _PS_ADMIN_DIR_ . '/import/' . $this->module->name . '/',
                'script_url' => $url . '&method=processAttachment',
                'upload_url' => $url . '&method=processAttachment&header=csv&download=1&file=',
                'param_name' => 'files'
            )
        );

        return $uploads->getResponse();
    }

    public function getProducts()
    {
        $id_lang = $this->context->language->id;
        $id_category = (int)Tools::getValue('id_category_default', null);
        $search_get = Tools::getValue('search', array('value' => ''));
        $search = $search_get['value'];
        $start = (int)Tools::getValue('start', 0);
        $length = (int)Tools::getValue('length', 25);
        $order = Tools::getValue('order', array(array('column' => 0, 'dir' => 'ASC')));
        $columns = Tools::getValue('columns', array());
        $orderfld = Tools::strtolower($columns[$order[0]['column']]['data']);
        $orderfld = (($orderfld == 'name') ? 'l.' . $orderfld : 'p.' . $orderfld);
        $orderdir = Tools::strtoupper($order[0]['dir']);
        $id_shop = $this->context->shop->id;
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);

        $products = KIProducts::getProducts($id_lang, $id_category, $search, $start, $length, $orderfld, $orderdir, $id_shop);
        $products_total = KIProducts::getNumProducts($id_category, $search, $id_shop);

        $records = array();
        foreach ($products as $product) {
            $producttemp = new Product($product['id_product'], false, $id_lang);
            $categorytempid = ((!$producttemp->id_category_default)
                ? $this->context->shop->id_category
                : $producttemp->id_category_default);
            $categorytemp = new Category($categorytempid, $id_lang);
            $records[] = array(
                'id_product'          => $producttemp->id,
                'name'                => $producttemp->name,
                'reference'           => $producttemp->reference,
                'id_category_default' => $categorytemp->id,
                'name_category'       => $categorytemp->name,
                'active'              => $producttemp->active == '1',
                'hascarrier'          => TRSCarrierTableRate::hasProductCarrier($producttemp->id, $id_carrier, $id_shop)
            );
        }

        return array(
            'recordsTotal'    => $products_total,
            'recordsFiltered' => $products_total,
            'data'            => $records
        );
    }

    public function getImportStatus()
    {
        $import_status = Tools::jsonDecode(Tools::file_get_contents(_PS_MODULE_DIR_ . $this->module->name . '/data/ImportStatus.json'));
        if (is_object($import_status) && isset($import_status->rowImported)) {
            return $import_status;
        } else {
            return (object)array('rowImported' => 'Wait..');
        }
    }

    public function getZonesOrder()
    {
        $id_carrier = (int)Tools::getValue('id_carrier', 0);
        $id_carrier = TRSCarrierTableRate::getCarrierIdFromCarrierId($id_carrier);
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::getZonesOrder($id_carrier, $id_shop);
    }

    public function getCountries()
    {
        $search = Tools::getValue('q', '');
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        return TRSCarrierTableRate::getCountries($search, $id_lang, $id_shop);
    }

    public function getStates()
    {
        $search = Tools::getValue('q', '');
        $id_country = (int)Tools::getValue('c', 0);

        return TRSCarrierTableRate::getStates($search, $id_country);
    }
}

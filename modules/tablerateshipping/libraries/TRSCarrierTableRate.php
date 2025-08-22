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

ini_set('max_execution_time', 0);

require_once(dirname(__FILE__) . '/autoload.php');
require_once(dirname(__FILE__) . '/kahanit/Helpers.php');
require_once(dirname(__FILE__) . '/TRSHelper.php');
require_once(dirname(__FILE__) . '/TRSExpressionFunctionProvider.php');

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class TRSCarrierTableRate
 */
class TRSCarrierTableRate extends ObjectModel
{
    public $id_shop;

    public $id_carrier;

    public $order;

    public $active;

    public $id_zone;

    public $id_group;

    public $id_country;

    public $id_state;

    public $dest_city;

    public $dest_zip;

    public $condition_weight_from;

    public $condition_weight_to;

    public $condition_price_from;

    public $condition_price_to;

    public $condition_ptprice_from;

    public $condition_ptprice_to;

    public $condition_quantity_from;

    public $condition_quantity_to;

    public $condition_volume_from;

    public $condition_volume_to;

    public $price;

    public $comment;

    public static $id_group_static = 0;

    public static $error_message
        = [
            'id_shop'                                                 => 'Invalid `Products`',
            'id_carrier'                                              => 'Invalid `Carrier Id`',
            'id_zone'                                                 => 'Invalid `Zone`',
            'id_group'                                                => 'Invalid `Group`',
            'id_country'                                              => 'Invalid `Country`',
            'id_state'                                                => 'Invalid `State`',
            'dest_city'                                               => 'Invalid `City`',
            'dest_zip'                                                => 'Invalid `Zip/Post code`',
            'dest_zip_from'                                           => 'Invalid `Zip/Post code from`',
            'dest_zip_to'                                             => 'Invalid `Zip/Post code to`',
            'dest_zip_from_less_then_dest_zip_to'                     => '`Zip/Post code from` should be less than `Zip/Post code to`',
            'no_range_if_wildcard'                                    => 'You cannot specify range if wildcard is used in zip/post code',
            'condition_weight_from_less_than_condition_weight_to'     => '`Weight from` should be less than `Weight to`',
            'condition_price_from_less_than_condition_price_to'       => '`Price from` should be less than `Price to`',
            'condition_ptprice_from_less_than_condition_ptprice_to'   => '`Price(pretax) from` should be less than `Price(pretax) to`',
            'condition_quantity_from_less_than_condition_quantity_to' => '`Quantity from` should be less than `Quantity to`',
            'condition_volume_from_less_than_condition_volume_to'     => '`Volume from` should be less than `Volume to`',
            'price'                                                   => 'Invalid `Price`',
            'comment'                                                 => 'Invalid `Comment`',
            'rule_exists'                                             => 'Duplicate table rate found',
            'csv_invalid_format'                                      => 'CSV not properly formatted',
            'no_records_found'                                        => 'No records found'
        ];

    public static $definition
        = [
            'table'     => 'carrier_table_rate',
            'primary'   => 'id_carrier_table_rate',
            'multishop' => true,
            'fields'    => [
                'id_shop'                 => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'id_carrier'              => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'id_zone'                 => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'id_group'                => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'id_country'              => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'id_state'                => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
                'dest_city'               => ['type' => self::TYPE_STRING, 'validate' => 'isCityName', 'size' => 64],
                'dest_zip'                => ['type' => self::TYPE_STRING, 'size' => 25],
                'condition_weight_from'   => ['type' => self::TYPE_FLOAT],
                'condition_weight_to'     => ['type' => self::TYPE_FLOAT],
                'condition_price_from'    => ['type' => self::TYPE_FLOAT],
                'condition_price_to'      => ['type' => self::TYPE_FLOAT],
                'condition_ptprice_from'  => ['type' => self::TYPE_FLOAT],
                'condition_ptprice_to'    => ['type' => self::TYPE_FLOAT],
                'condition_quantity_from' => ['type' => self::TYPE_FLOAT],
                'condition_quantity_to'   => ['type' => self::TYPE_FLOAT],
                'condition_volume_from'   => ['type' => self::TYPE_FLOAT],
                'condition_volume_to'     => ['type' => self::TYPE_FLOAT],
                'price'                   => ['type' => self::TYPE_HTML],
                'comment'                 => ['type' => self::TYPE_STRING],
                'active'                  => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
                'order'                   => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt']
            ]
        ];

    public function validateFields($die = false, $error_return = true)
    {
        $message = parent::validateFields($die, $error_return);

        /* core validation */
        if ($message != 1) {
            foreach (self::$definition['fields'] as $field_name => $field_rules) {
                if (array_key_exists('validate', $field_rules)) {
                    preg_match('/->' . $field_name . ' /', $message, $matches);

                    if (count($matches) != 0) {
                        return self::$error_message[$field_name];
                    }
                }
            }
        }

        /* validate zone */
        if ($this->id_zone != 0 && $this->id_zone != '' && (!($zone = new Zone($this->id_zone)) || !Validate::isLoadedObject($zone))) {
            return self::$error_message['id_zone'];
        }

        /* validate country */
        if ($this->id_country != 0 && $this->id_country != '' && (!($country = new Country($this->id_country)) || !Validate::isLoadedObject($country))) {
            return self::$error_message['id_country'];
        }

        /* validate state */
        if ($this->id_state != 0 && $this->id_state != '' && ((!($state = new State($this->id_state)) || !Validate::isLoadedObject($state)) || $state->id_country != $this->id_country)) {
            return self::$error_message['id_state'];
        }

        /* validate zip codes */
        $dest_zip = explode('-', $this->dest_zip);
        $dest_zip_from = (isset($dest_zip[0])) ? trim($dest_zip[0]) : '';
        $dest_zip_from = ($dest_zip_from === '*' || $dest_zip_from === '') ? '' : $dest_zip_from;
        $dest_zip_to = (isset($dest_zip[1])) ? trim($dest_zip[1]) : '';
        $dest_zip_to = ($dest_zip_to === '*' || $dest_zip_to === '') ? '' : $dest_zip_to;
        $dest_zip_to = ($dest_zip_to === '') ? $dest_zip_from : $dest_zip_to;
        if (strcasecmp($dest_zip_from, $dest_zip_to) == 0) {
            $this->dest_zip = $dest_zip_from;
        } else {
            $this->dest_zip = $dest_zip_from . '-' . $dest_zip_to;
        }

        /* validate zip from */
        if ($dest_zip_from != '' && isset($country) && $country->zip_code_format
            && !TRSHelper::checkZipCode($dest_zip_from, $country->zip_code_format, $country->iso_code)
        ) {
            return self::$error_message['dest_zip_from'];
        }

        /* validate zip to */
        if ($dest_zip_to != '' && isset($country) && $country->zip_code_format
            && !TRSHelper::checkZipCode($dest_zip_to, $country->zip_code_format, $country->iso_code)
        ) {
            return self::$error_message['dest_zip_to'];
        }

        /* validate zip from and to range */
        if ($dest_zip_from != '' && $dest_zip_to != '' && $dest_zip_to < $dest_zip_from) {
            return self::$error_message['dest_zip_from_less_then_dest_zip_to'];
        }

        /* validate to cannot specify range if wildcard is used */
        if ($dest_zip_from != '' && $dest_zip_to != '' && strcasecmp($dest_zip_from, $dest_zip_to) != 0
            && ((strpos($dest_zip_from, '*') !== false || strpos($dest_zip_to, '*') !== false)
                || (strpos($dest_zip_from, '%') !== false || strpos($dest_zip_to, '%') !== false)
                || (strpos($dest_zip_from, '_') !== false || strpos($dest_zip_to, '_') !== false))
        ) {
            return self::$error_message['no_range_if_wildcard'];
        }

        /* validate condition value from and to range */
        $condition_types = ['weight', 'price', 'ptprice', 'quantity', 'volume'];
        foreach ($condition_types as $condition_type) {
            if ($this->{'condition_' . $condition_type . '_to'} < $this->{'condition_' . $condition_type . '_from'}) {
                return self::$error_message['condition_' . $condition_type . '_from_less_than_condition_' . $condition_type . '_to'];
            }
        }

        /* validate price */
        $vars = [
            'tw'     => 1,
            'tp'     => 1,
            'tptp'   => 1,
            'tq'     => 1,
            'tv'     => 1,
            'ctwf'   => 1,
            'ctwt'   => 1,
            'ctwi'   => 1,
            'ctpf'   => 1,
            'ctpt'   => 1,
            'ctpi'   => 1,
            'ctptpf' => 1,
            'ctptpt' => 1,
            'ctptpi' => 1,
            'ctqf'   => 1,
            'ctqt'   => 1,
            'ctqi'   => 1,
            'ctvf'   => 1,
            'ctvt'   => 1,
            'ctvi'   => 1,
            'skip'   => -1,
            'stop'   => false
        ];
        $this->price = str_replace('$', '', $this->price); // for bc with old formula variables with $
        $language = new ExpressionLanguage();
        $language->registerProvider(new TRSExpressionFunctionProvider());
        try {
            $language->evaluate($this->price, $vars);
        } catch (Exception $e) {
            return self::$error_message['price'];
        }

        /* validate duplicate entry */
        $rule_exists = self::ruleExists($this->id_carrier, get_object_vars($this), $this->id_shop);
        if ($rule_exists !== 0 && $rule_exists !== $this->id) {
            return self::$error_message['rule_exists'];
        }

        return $message;
    }

    public static function ruleExists($id_carrier, $record, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('id_carrier_table_rate');
        $sql->from('carrier_table_rate');
        $sql->where(
            '   id_carrier = ' . (int)$id_carrier . '
            AND id_zone = ' . (int)$record['id_zone'] . '
            AND id_country = ' . (int)$record['id_country'] . '
            AND id_state = ' . (int)$record['id_state'] . '
            AND dest_city = \'' . KIHelpers::addslashes($record['dest_city']) . '\'
            AND dest_zip = \'' . KIHelpers::addslashes($record['dest_zip']) . '\'
            AND condition_weight_from = ' . (float)$record['condition_weight_from'] . '
            AND condition_weight_to = ' . (float)$record['condition_weight_to'] . '
            AND condition_price_from = ' . (float)$record['condition_price_from'] . '
            AND condition_price_to = ' . (float)$record['condition_price_to'] . '
            AND condition_ptprice_from = ' . (float)$record['condition_ptprice_from'] . '
            AND condition_ptprice_to = ' . (float)$record['condition_ptprice_to'] . '
            AND condition_quantity_from = ' . (float)$record['condition_quantity_from'] . '
            AND condition_quantity_to = ' . (float)$record['condition_quantity_to'] . '
            AND condition_volume_from = ' . (float)$record['condition_volume_from'] . '
            AND condition_volume_to = ' . (float)$record['condition_volume_to'] . '
            AND price = \'' . KIHelpers::addslashes($record['price']) . '\'
            AND id_shop = ' . (int)$id_shop
        );

        return (int)Db::getInstance()->getValue($sql);
    }

    public static function getRuleGroups($id_carrier, $id_zone = null, $start = null, $length = null, $where = '', $id_lang, $id_shop)
    {
        // select query
        $sql = new DbQuery();
        $sql->select('ct.id_zone, z.name AS zone, ct.id_group, ct.id_country, cl.name AS country, c.iso_code AS c_iso_code,
            ct.id_state, s.name AS state, s.iso_code AS s_iso_code, ct.dest_city, ct.dest_zip,
            MIN(ct.order) as `order`');
        $sql->from('carrier_table_rate', 'ct');
        $sql->leftJoin('zone', 'z', 'z.id_zone = ct.id_zone');
        $sql->leftJoin('zone_shop', 'zs', 'zs.id_zone = ct.id_zone AND zs.id_shop = ' . (int)$id_shop);
        $sql->leftJoin('country', 'c', 'c.id_country = ct.id_country');
        $sql->leftJoin('country_lang', 'cl', 'cl.id_country = ct.id_country AND cl.id_lang = ' . (int)$id_lang);
        $sql->leftJoin('country_shop', 'cs', 'cs.id_country = ct.id_country AND cs.id_shop = ' . (int)$id_shop);
        $sql->leftJoin('state', 's', 's.id_state = ct.id_state');
        $sql->where('ct.id_carrier = ' . (int)$id_carrier
            . (($id_zone !== null) ? ' AND ct.id_zone = ' . (int)$id_zone : '')
            . (($id_shop !== false) ? ' AND ct.id_shop = ' . (int)$id_shop : ''));
        $sql->groupBy('ct.id_group, ct.dest_zip');
        $sql->orderBy('MIN(ct.order) ASC');

        // outer query
        $sql_outer = new DbQuery();
        $sql_outer->select('id_zone, zone, id_group, id_country, country, c_iso_code, id_state, state, s_iso_code, dest_city,
	        GROUP_CONCAT(dest_zip ORDER BY `order` ASC SEPARATOR \',\') AS dest_zip');
        $sql_outer->from('{{placeholder}}');
        $sql_outer->groupBy('id_group');
        $sql_outer->orderBy('MIN(`order`) ASC');
        if ($start !== null && $length !== null) {
            $sql_outer->limit($length, $start);
        }

        $sql_outer
            = 'SELECT id_zone, zone, id_group, id_country, country, c_iso_code,
            id_state, state, s_iso_code, dest_city, GROUP_CONCAT(dest_zip ORDER BY `order` ASC SEPARATOR \',\') AS dest_zip
            FROM (' . $sql->build() . ') as SubTable
            GROUP BY id_group
            ORDER BY MIN(`order`) ASC';
        if ($start !== null && $length !== null) {
            $sql_outer .= ' LIMIT ' . $start . ', ' . $length;
        }

        $results = Db::getInstance()->executeS($sql_outer);

        foreach ($results as &$result) {
            $result['id_zone'] = ($result['id_zone'] == 0) ? '*' : $result['id_zone'];
            $result['zone'] = ($result['zone'] === '') ? '*' : $result['zone'];
            $result['id_country'] = ($result['id_country'] == 0) ? '*' : $result['id_country'];
            $result['c_iso_code'] = ($result['id_country'] == 0) ? '*' : $result['c_iso_code'];
            $result['country'] = ($result['id_country'] == 0) ? '*' : $result['country'];
            $result['id_state'] = ($result['id_state'] == 0) ? '*' : $result['id_state'];
            $result['s_iso_code'] = ($result['id_state'] == 0) ? '*' : $result['s_iso_code'];
            $result['state'] = ($result['id_state'] == 0) ? '*' : $result['state'];
            $result['dest_city'] = KIHelpers::stripslashes(($result['dest_city'] === '') ? '*' : $result['dest_city']);
            $result['dest_zip'] = KIHelpers::stripslashes(($result['dest_zip'] === '') ? '*' : $result['dest_zip']);
        }

        return $results;
    }

    public static function getRuleGroupsNumRows($id_carrier, $id_zone = null, $where = '', $id_shop)
    {
        // select query
        $sql = new DbQuery();
        $sql->select('id_carrier_table_rate');
        $sql->from('carrier_table_rate', 'ct');
        $sql->where('ct.id_carrier = ' . (int)$id_carrier
            . (($id_zone !== null) ? ' AND ct.id_zone = ' . (int)$id_zone : '')
            . (($id_shop !== false) ? ' AND ct.id_shop = ' . (int)$id_shop : ''));
        $sql->groupBy('ct.id_group');

        // count query
        $sql_count = 'SELECT COUNT(*) AS total FROM (' . $sql . ') AS tbl';

        return (int)Db::getInstance()->getValue($sql_count);
    }

    public static function getRules($id_carrier, $id_zone, $id_group, $start = null, $length = null, $where = '', $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('*, GROUP_CONCAT(`id_carrier_table_rate` ORDER BY `order` ASC SEPARATOR \',\') AS `id_carrier_table_rate`,
            GROUP_CONCAT(`dest_zip` ORDER BY `order` ASC SEPARATOR \',\') AS `dest_zip`');
        $sql->from('carrier_table_rate');
        $sql->where(
            '   `id_carrier` = ' . (int)$id_carrier . '
            AND `id_zone` = ' . (int)$id_zone . '
            AND `id_group` = ' . (int)$id_group . '
            AND `id_shop` = ' . (int)$id_shop
        );
        $sql->groupBy(
            '`condition_weight_from`, `condition_weight_to`,
            `condition_price_from`, `condition_price_to`,
            `condition_ptprice_from`, `condition_ptprice_to`,
            `condition_quantity_from`, `condition_quantity_to`,
            `condition_volume_from`, `condition_volume_to`,
            `price`, `active`'
        );
        $sql->orderBy('MIN(`order`) ASC');
        if ($start !== null && $length !== null) {
            $sql->limit($length, $start);
        }

        $results = Db::getInstance()->executeS($sql);
        $condition_types = ['weight', 'price', 'ptprice', 'quantity', 'volume'];
        foreach ($results as &$result) {
            $result['id_country'] = ($result['id_country'] == 0) ? '*' : $result['id_country'];
            $result['id_state'] = ($result['id_state'] == 0) ? '*' : $result['id_state'];
            $result['dest_city'] = KIHelpers::stripslashes(($result['dest_city'] === '') ? '*' : $result['dest_city']);
            $result['dest_zip'] = KIHelpers::stripslashes(($result['dest_zip'] === '') ? '*' : $result['dest_zip']);
            foreach ($condition_types as $condition_type) {
                $result['condition_' . $condition_type . '_from'] = ($result['condition_' . $condition_type . '_from'] == -1) ? '*' : $result['condition_' . $condition_type . '_from'];
                $result['condition_' . $condition_type . '_to'] = ($result['condition_' . $condition_type . '_to'] == -1) ? '*' : $result['condition_' . $condition_type . '_to'];
            }
            $result['price'] = KIHelpers::stripslashes($result['price']);
            $result['comment'] = KIHelpers::stripslashes($result['comment']);
        }

        return $results;
    }

    public static function getRulesNumRows($id_carrier, $id_zone, $id_group, $where, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('GROUP_CONCAT(`id_carrier_table_rate` ORDER BY `order` ASC SEPARATOR \',\') AS `id_carrier_table_rate`');
        $sql->from('carrier_table_rate');
        $sql->where(
            ' `id_carrier` = ' . (int)$id_carrier . '
            AND `id_zone` = ' . (int)$id_zone . '
            AND `id_group` = ' . (int)$id_group . '
            AND `id_shop` = ' . (int)$id_shop
        );
        $sql->groupBy(
            '`condition_weight_from`, `condition_weight_to`,
            `condition_price_from`, `condition_price_to`,
            `condition_ptprice_from`, `condition_ptprice_to`,
            `condition_quantity_from`, `condition_quantity_to`,
            `condition_volume_from`, `condition_volume_to`,
            `price`, `active`'
        );
        $sql->orderBy('MIN(`order`) ASC');
        $sql_count = 'SELECT COUNT(id_carrier_table_rate) AS total FROM (' . $sql . ') AS tbl';

        return (int)Db::getInstance()->getValue($sql_count);
    }

    public static function saveRules($action, $id_carrier, $records, $id_lang, $id_shop)
    {
        $status = [
            'error'   => 0,
            'success' => 0,
            'status'  => '',
            'message' => []
        ];
        $fields = [
            'id_shop'                 => 0,
            'id_carrier'              => 0,
            'id_zone'                 => 0,
            'id_group'                => 0,
            'id_country'              => 0,
            'id_state'                => 0,
            'dest_city'               => '',
            'dest_zip'                => '',
            'condition_weight_from'   => -1,
            'condition_weight_to'     => -1,
            'condition_price_from'    => -1,
            'condition_price_to'      => -1,
            'condition_ptprice_from'  => -1,
            'condition_ptprice_to'    => -1,
            'condition_quantity_from' => -1,
            'condition_quantity_to'   => -1,
            'condition_volume_from'   => -1,
            'condition_volume_to'     => -1,
            'price'                   => '',
            'comment'                 => '',
            'active'                  => 0,
            'order'                   => 0
        ];
        $fields_group = [
            'id_shop'    => 0,
            'id_carrier' => 0,
            'id_zone'    => 0,
            'id_country' => 0,
            'id_state'   => 0,
            'dest_city'  => ''
        ];
        $condition_types = [
            'weight',
            'price',
            'ptprice',
            'quantity',
            'volume'
        ];
        $insert_carrier_table_rates = [];
        $insert_carrier_table_rates_flat = [];
        $insert_carrier_table_groups_flat = [];
        $groups_numrows = self::getRuleGroupsNumRows($id_carrier, null, '', false);

        if (is_array($records) && count($records) > 0) {
            foreach ($records as $record_index => &$record) {
                // assign shop and carrier
                $record = ['id_shop' => $id_shop, 'id_carrier' => $id_carrier] + $record;

                // prepare condition from and to values
                foreach ($condition_types as $condition_type) {
                    if (isset($record['condition_' . $condition_type])) {
                        if (is_numeric($record['condition_' . $condition_type])) {
                            $record['condition_' . $condition_type . '_from'] = (float)$record['condition_' . $condition_type];
                            $record['condition_' . $condition_type . '_to'] = (float)$record['condition_' . $condition_type];
                        } else {
                            $condition_values = explode('-', $record['condition_' . $condition_type]);
                            if (isset($condition_values[0]) && is_numeric($condition_values[0])
                                && isset($condition_values[1])
                                && is_numeric($condition_values[1])
                            ) {
                                $record['condition_' . $condition_type . '_from'] = (float)$condition_values[0];
                                $record['condition_' . $condition_type . '_to'] = (float)$condition_values[1];
                            }
                        }
                        unset($record['condition_' . $condition_type]);
                    }
                }

                // prepare record
                foreach ($fields as $field_key => $field_default) {
                    if (isset($record[$field_key])) {
                        $record[$field_key] = ($record[$field_key] === '*' || $record[$field_key] === '') ? $field_default : $record[$field_key];
                    } else {
                        $record[$field_key] = $field_default;
                    }
                }
                $record = array_merge($fields, $record);

                // prepare list of zipcodes
                if (isset($record['dest_zip'])) {
                    $record['dest_zip'] = (($record['dest_zip'] === '*' || $record['dest_zip'] === '') ? '' : $record['dest_zip']);
                } else {
                    $record['dest_zip'] = '';
                }
                $record['dest_zip'] = preg_replace('/[\r\n,]+/', '{{separator}}', $record['dest_zip']);
                $record['dest_zip'] = explode('{{separator}}', $record['dest_zip']);

                // get group rules
                if (!isset($record['id_group']) || $record['id_group'] == 0) {
                    $record_temp = $record;
                    $record_temp['dest_zip'] = implode(',', $record_temp['dest_zip']);
                    $insert_carrier_table_group_flat = KIHelpers::addslashes(implode(',', array_intersect_key($record_temp, $fields_group + ['dest_zip' => ''])));
                    if (count($insert_carrier_table_rates) > 0 && in_array($insert_carrier_table_group_flat, $insert_carrier_table_groups_flat)) {
                        $record['id_group'] = array_search($insert_carrier_table_group_flat, $insert_carrier_table_groups_flat);
                    } else {
                        $record['id_group'] = self::getIdGroup($record['id_carrier'], $record['id_zone'], $record['id_country'],
                            $record['id_state'], KIHelpers::addslashes($record['dest_city']), KIHelpers::addslashes(implode(',', $record['dest_zip'])), $record['id_shop']);
                    }
                }
                $rules = self::getRules($record['id_carrier'], $record['id_zone'], $record['id_group'], null, null, '', $record['id_shop']);
                $group_zips = [];
                if (is_array($rules) && count($rules) > 0) {
                    $group_zips = explode(',', reset($rules)['dest_zip']);
                }

                // prepare delete rules with `group zips` not in `dest zips sent`
                $delete_zips = [];
                if ($action == 'edit') {
                    $delete_zips = array_diff($group_zips, $record['dest_zip']);
                    if (count($delete_zips) > 0) {
                        $group_zips = array_diff($group_zips, $delete_zips);
                    }
                }

                // insert `all rules in database` for `dest zips sent`
                if (is_array($rules) && count($rules) > 0) {
                    foreach ($rules as $rule) {
                        $group_zips_temp = '';
                        foreach ($record['dest_zip'] as $dest_zip) {
                            // if dest zip is in database continue
                            if (in_array($dest_zip, $group_zips)) {
                                continue;
                            }

                            // setup object
                            $carrier_table_rate = new TRSCarrierTableRate();
                            foreach ($record as $record_key => $record_value) {
                                if (array_key_exists($record_key, $fields)) {
                                    if (is_int($fields[$record_key])) {
                                        $carrier_table_rate->{$record_key} = (float)$record_value;
                                    } else {
                                        $carrier_table_rate->{$record_key} = KIHelpers::addslashes($record_value);
                                    }
                                }
                            }

                            // assign rule data
                            foreach ($condition_types as $condition_type) {
                                $carrier_table_rate->{'condition_' . $condition_type . '_from'}
                                    = (($rule['condition_' . $condition_type . '_from'] === '*' || $rule['condition_' . $condition_type . '_from'] === '')
                                    ? -1 : $rule['condition_' . $condition_type . '_from']);
                                $carrier_table_rate->{'condition_' . $condition_type . '_to'}
                                    = (($rule['condition_' . $condition_type . '_to'] === '*' || $rule['condition_' . $condition_type . '_to'] === '')
                                    ? -1 : $rule['condition_' . $condition_type . '_to']);
                            }
                            $carrier_table_rate->price = KIHelpers::addslashes($rule['price']);
                            $carrier_table_rate->comment = KIHelpers::addslashes($rule['comment']);
                            $carrier_table_rate->active = (int)$rule['active'];

                            // assign dest zip
                            $carrier_table_rate->dest_zip = KIHelpers::addslashes($dest_zip);

                            // validate object
                            $message = $carrier_table_rate->validateFields(false, true);

                            // if duplicate record found continue
                            if (strcasecmp($message, self::$error_message['rule_exists']) === 0) {
                                continue;
                            }

                            // save object of validated successfully
                            if ($message == 1) {
                                $insert_carrier_table_rate = array_intersect_key(get_object_vars($carrier_table_rate), $fields);
                                $insert_carrier_table_rate_flat = implode(',', $insert_carrier_table_rate);
                                if (!in_array($insert_carrier_table_rate_flat, $insert_carrier_table_rates_flat)) {
                                    $insert_carrier_table_rates_flat[] = $insert_carrier_table_rate_flat;
                                    $group_zips_temp .= ',' . $carrier_table_rate->dest_zip;
                                    $insert_carrier_table_group_flat = implode(',', array_intersect_key(get_object_vars($carrier_table_rate), $fields_group));
                                    $insert_carrier_table_groups_flat[$insert_carrier_table_rate['id_group']] = $insert_carrier_table_group_flat . $group_zips_temp;
                                    $insert_carrier_table_rates[] = $insert_carrier_table_rate;
                                    $status['success']++;
                                }
                            } else {
                                $status['error']++;
                                $status['message'][] = $message;
                            }
                        }
                    }
                    $group_zips = array_unique(array_merge(array_diff($group_zips, $record['dest_zip']), $record['dest_zip']));
                } else {
                    $group_zips = $record['dest_zip'];
                }

                // insert `rule sent` for `database dest zips`
                if (is_array($group_zips) && count($group_zips) > 0) {
                    $group_zips_temp = '';
                    foreach ($group_zips as $group_zip) {
                        // if group exists and sent rule is dummy continue
                        if (is_array($rules) && count($rules) > 0 && strcasecmp($record['comment'], '{{dummy}}') === 0) {
                            continue;
                        }

                        // setup object
                        $carrier_table_rate = new TRSCarrierTableRate();
                        foreach ($record as $record_key => $record_value) {
                            if (array_key_exists($record_key, $fields)) {
                                if (is_int($fields[$record_key])) {
                                    $carrier_table_rate->{$record_key} = (float)$record_value;
                                } else {
                                    $carrier_table_rate->{$record_key} = KIHelpers::addslashes($record_value);
                                }
                            }
                        }

                        // assign dest zip
                        $carrier_table_rate->dest_zip = KIHelpers::addslashes($group_zip);

                        // validate object
                        $message = $carrier_table_rate->validateFields(false, true);

                        // if duplicate record found continue
                        if (strcasecmp($message, self::$error_message['rule_exists']) === 0) {
                            continue;
                        }

                        // save object of validated successfully
                        if ($message == 1) {
                            $insert_carrier_table_rate = array_intersect_key(get_object_vars($carrier_table_rate), $fields);
                            $insert_carrier_table_rate_flat = implode(',', $insert_carrier_table_rate);
                            if (!in_array($insert_carrier_table_rate_flat, $insert_carrier_table_rates_flat)) {
                                $insert_carrier_table_rates_flat[] = $insert_carrier_table_rate_flat;
                                $group_zips_temp .= ',' . $carrier_table_rate->dest_zip;
                                $insert_carrier_table_group_flat = implode(',', array_intersect_key(get_object_vars($carrier_table_rate), $fields_group));
                                $insert_carrier_table_groups_flat[$insert_carrier_table_rate['id_group']] = $insert_carrier_table_group_flat . $group_zips_temp;
                                $insert_carrier_table_rates[] = $insert_carrier_table_rate;
                                $status['success']++;
                            }
                        } else {
                            $status['error']++;
                            $status['message'][] = $message;
                        }
                    }
                }

                // execute delete rules with `group zips` not in `dest zips sent`
                if ($action == 'edit' && $status['error'] <= 0 && count($delete_zips) > 0) {
                    Db::getInstance()->delete(
                        'carrier_table_rate',
                        '   id_carrier = ' . (int)$record['id_carrier'] . '
                        AND id_zone = ' . (int)$record['id_zone'] . '
                        AND id_group = ' . (int)$record['id_group'] . '
                        AND dest_zip IN (\'' . implode("','", $delete_zips) . '\')
                        AND id_shop = ' . (int)$id_shop
                    );
                }

                // write import status
                file_put_contents(_PS_MODULE_DIR_ . 'tablerateshipping/data/ImportStatus.json', '{"rowImported":' . ((int)$record_index + 1) . '}');
            }
        }

        if ($status['error'] > 0) {
            $status['status'] = 'danger';
            $status['message'] = implode(', ', array_unique($status['message']));

            return $status;
        } else {
            // insert records
            if (count($insert_carrier_table_rates) > 0) {
                $insert_carrier_table_rates = KIHelpers::addslashes($insert_carrier_table_rates); // as prestashop strips slashes
                Db::getInstance()->insert('carrier_table_rate', $insert_carrier_table_rates);
                Db::getInstance()->query('
                    UPDATE `' . _DB_PREFIX_ . 'carrier_table_rate`
                    SET `order` = `id_carrier_table_rate` WHERE `order` = 0
                ');
            }

            // sort zones
            $zones = self::getZonesOrder($id_carrier, $id_shop);
            self::updateOrder('zone', $id_carrier, null, $zones, $id_shop);

            // sort rule groups
            foreach ($zones as $id_zone) {
                $results = self::getRuleGroups($id_carrier, $id_zone, null, null, '', $id_lang, $id_shop);
                $id_group_order = array_map(function ($item) {
                    return $item['id_group'];
                }, $results);
                self::updateOrder('rulegroup', $id_carrier, $id_zone, $id_group_order, $id_shop);
            }

            // sort rules only on edit
            if ($action == 'edit' && isset($record['id_zone']) && isset($record['id_group']) && isset($group_zips)) {
                $results = self::getRules($id_carrier, $record['id_zone'], $record['id_group'], null, null, '', $id_shop);
                $rules = [];
                foreach ($results as $result) {
                    $id_carrier_table_rate_explode = explode(',', $result['id_carrier_table_rate']);
                    $dest_zip_explode = explode(',', $result['dest_zip']);
                    $id_carrier_table_rate_explode_sorted = [];
                    foreach ($group_zips as $group_zip) {
                        $id_carrier_table_rate_explode_sorted[]
                            = $id_carrier_table_rate_explode[array_search($group_zip, $dest_zip_explode)];
                    }
                    $rules[] = implode(',', $id_carrier_table_rate_explode_sorted);
                }
                self::updateOrder('rule', null, null, $rules, $id_shop);
            }

            // set success status
            $status['status'] = 'success';
            $status['message'] = 'Records saved successfully';

            // set carrier as table rate
            if ($groups_numrows == 0 && count($insert_carrier_table_rates) > 0) {
                self::setCarrierAsTableRate($id_carrier);
            }

            return $status;
        }
    }

    public static function import($file, $csv_separator = ',', $id_carrier, $id_lang, $id_shop)
    {
        $first_record = true;
        $valid_columns = [
            'zone',
            'country',
            'state',
            'city',
            'zip',
            'condition_weight',
            'condition_price',
            'condition_ptprice',
            'condition_quantity',
            'condition_volume',
            'price',
            'active',
            'comment'
        ];
        $column_numbers = [];
        $records = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, $csv_separator)) !== false) {
                $data = array_map("utf8_encode", $data);
                if ($first_record) {
                    foreach ($data as $key => $value) {
                        if (in_array($value, $valid_columns)) {
                            $column_numbers[$value] = $key;
                        }
                    }

                    if (count($column_numbers) != count($valid_columns)) {
                        return [
                            'status'  => 'danger',
                            'message' => self::$error_message['csv_invalid_format']
                        ];
                    }

                    $first_record = false;
                    continue;
                }

                $id_zone = ($data[$column_numbers['zone']] === '*' || $data[$column_numbers['zone']] === '')
                    ? 0 : Zone::getIdByName($data[$column_numbers['zone']]);
                $id_country = ($data[$column_numbers['country']] === '*' || $data[$column_numbers['country']] === '')
                    ? 0 : Country::getByIso($data[$column_numbers['country']]);
                $id_state = ($data[$column_numbers['state']] === '*' || $data[$column_numbers['state']] === '')
                    ? 0 : State::getIdByIso($data[$column_numbers['state']], $id_country);

                $records[] = [
                    'id_zone'            => $id_zone,
                    'id_country'         => $id_country,
                    'id_state'           => $id_state,
                    'dest_city'          => ($data[$column_numbers['city']] === '*' || $data[$column_numbers['city']] === '')
                        ? '' : $data[$column_numbers['city']],
                    'dest_zip'           => ($data[$column_numbers['zip']] === '*' || $data[$column_numbers['zip']] === '')
                        ? '' : preg_replace('/[\r\n,]+/', '{{separator}}', $data[$column_numbers['zip']]),
                    'condition_weight'   => ($data[$column_numbers['condition_weight']] === '*' || $data[$column_numbers['condition_weight']] === '')
                        ? -1 : $data[$column_numbers['condition_weight']],
                    'condition_price'    => ($data[$column_numbers['condition_price']] === '*' || $data[$column_numbers['condition_price']] === '')
                        ? -1 : $data[$column_numbers['condition_price']],
                    'condition_ptprice'  => ($data[$column_numbers['condition_ptprice']] === '*' || $data[$column_numbers['condition_ptprice']] === '')
                        ? -1 : $data[$column_numbers['condition_ptprice']],
                    'condition_quantity' => ($data[$column_numbers['condition_quantity']] === '*' || $data[$column_numbers['condition_quantity']] === '')
                        ? -1 : $data[$column_numbers['condition_quantity']],
                    'condition_volume'   => ($data[$column_numbers['condition_volume']] === '*' || $data[$column_numbers['condition_volume']] === '')
                        ? -1 : $data[$column_numbers['condition_volume']],
                    'price'              => $data[$column_numbers['price']],
                    'active'             => $data[$column_numbers['active']],
                    'comment'            => $data[$column_numbers['comment']]
                ];
            }

            fclose($handle);
        }

        return self::saveRules('add', $id_carrier, $records, $id_lang, $id_shop);
    }

    public static function export($id_carrier, $csv_separator = ',', $id_lang, $id_shop)
    {
        $collection = [];
        $rule_groups = self::getRuleGroups($id_carrier, null, null, null, '', $id_lang, $id_shop);
        foreach ($rule_groups as $rule_group) {
            $rules = self::getRules($id_carrier, $rule_group['id_zone'], $rule_group['id_group'], null, null, '', $id_shop);

            foreach ($rules as $rule) {
                $weight = ($rule['condition_weight_from'] == $rule['condition_weight_to']) ? $rule['condition_weight_from'] : $rule['condition_weight_from'] . '-' . $rule['condition_weight_to'];
                $price = ($rule['condition_price_from'] == $rule['condition_price_to']) ? $rule['condition_price_from'] : $rule['condition_price_from'] . '-' . $rule['condition_price_to'];
                $ptprice = ($rule['condition_ptprice_from'] == $rule['condition_ptprice_to']) ? $rule['condition_ptprice_from'] : $rule['condition_ptprice_from'] . '-' . $rule['condition_ptprice_to'];
                $quantity = ($rule['condition_quantity_from'] == $rule['condition_quantity_to']) ? $rule['condition_quantity_from'] : $rule['condition_quantity_from'] . '-' . $rule['condition_quantity_to'];
                $volume = ($rule['condition_volume_from'] == $rule['condition_volume_to']) ? $rule['condition_volume_from'] : $rule['condition_volume_from'] . '-' . $rule['condition_volume_to'];

                $collection[] = [
                    'zone'               => ($rule_group['zone'] === '') ? '*' : $rule_group['zone'],
                    'country'            => ($rule_group['c_iso_code'] === '') ? '*' : $rule_group['c_iso_code'],
                    'state'              => ($rule_group['s_iso_code'] === '') ? '*' : $rule_group['s_iso_code'],
                    'city'               => ($rule_group['dest_city'] === '') ? '*' : $rule_group['dest_city'],
                    'zip'                => ($rule_group['dest_zip'] === '') ? '*' : implode(PHP_EOL, explode(',', $rule_group['dest_zip'])),
                    'condition_weight'   => ($weight == -1) ? '*' : $weight,
                    'condition_price'    => ($price == -1) ? '*' : $price,
                    'condition_ptprice'  => ($ptprice == -1) ? '*' : $ptprice,
                    'condition_quantity' => ($quantity == -1) ? '*' : $quantity,
                    'condition_volume'   => ($volume == -1) ? '*' : $volume,
                    'price'              => $rule['price'],
                    'comment'            => $rule['comment'],
                    'active'             => $rule['active']
                ];
            }
        }

        if (count($collection) > 0) {
            header('Content-type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header('Content-disposition: attachment; filename="export.csv"');
            $csv = fopen("php://output", 'w');
            fputcsv($csv, array_keys(reset($collection)));
            foreach ($collection as $row) {
                fputcsv($csv, $row, $csv_separator);
            }
            fclose($csv);
        } else {
            echo self::$error_message['no_records_found'];
        }

        die();
    }

    public static function updateCountryStateCity($update, $id_carrier, $id_zone, $id_group, $value, $id_lang, $id_shop)
    {
        if ($update !== false && in_array($update, ['id_country', 'id_state', 'dest_city']) && $id_carrier !== 0 && $id_zone !== -1) {
            if ($update == 'id_country' || $update == 'id_state') {
                $value = (int)(($value === '*' || $value === '') ? 0 : $value);
            } else {
                $value = KIHelpers::addslashes(($value === '*' || $value === '') ? '' : $value);
            }

            // validate group already exists
            $rules = self::getRules($id_carrier, $id_zone, $id_group, null, null, '', $id_shop);
            foreach ($rules as $rule) {
                $id_carrier_table_rate_explode = array_map('intval', explode(',', $rule['id_carrier_table_rate']));
                foreach ($id_carrier_table_rate_explode as $id_carrier_table_rate_explode_item) {
                    $carrier_table_rate = new TRSCarrierTableRate($id_carrier_table_rate_explode_item);
                    $carrier_table_rate->{$update} = $value;
                    if ($update == 'id_country') {
                        $carrier_table_rate->id_state = 0;
                    }
                    $message = $carrier_table_rate->validateFields(false, true);
                    if ($message != 1) {
                        return [
                            'status'  => 'danger',
                            'message' => $message
                        ];
                    }
                }
            }

            // update values
            $update_values = [$update => $value];
            if ($update == 'id_country') {
                $update_values['id_state'] = 0;
            }
            $update_values = KIHelpers::addslashes($update_values); // as prestashop strips slashes
            Db::getInstance()->update(
                'carrier_table_rate',
                $update_values,
                '    id_carrier = ' . (int)$id_carrier . '
                AND id_zone = ' . (int)$id_zone . '
                AND id_group = ' . (int)$id_group . '
                AND id_shop = ' . (int)$id_shop
            );

            if ((int)Db::getInstance()->getValue('SELECT ROW_COUNT()') > 0) {
                $rules = self::getRules($id_carrier, $id_zone, $id_group, 0, 1, '', $id_shop);
                if (count($rules) > 0) {
                    $country = new Country($rules[0]['id_country'], $id_lang, $id_shop);
                    $state = new State($rules[0]['id_state']);

                    return [
                        'status'     => 'success',
                        'id_shop'    => $id_shop,
                        'id_carrier' => $id_carrier,
                        'id_zone'    => $id_zone,
                        'id_country' => ($rules[0]['id_country'] == 0) ? '*' : $rules[0]['id_country'],
                        'country'    => ($country->name && $country->iso_code) ? $country->name . ' (' . $country->iso_code . ')' : '*',
                        'id_state'   => ($rules[0]['id_state'] == 0) ? '*' : $rules[0]['id_state'],
                        'state'      => ($state->name && $state->iso_code) ? $state->name . ' (' . $state->iso_code . ')' : '*',
                        'dest_city'  => KIHelpers::stripslashes(($rules[0]['dest_city'] === '') ? '*' : $rules[0]['dest_city']),
                        'dest_zip'   => KIHelpers::stripslashes(($rules[0]['dest_zip'] === '') ? '*' : $rules[0]['dest_zip']),
                        'message'    => 'Record updated successfully'
                    ];
                }
            }
        }

        return [
            'status'  => 'danger',
            'message' => 'Error updating record'
        ];
    }

    public static function updateConditionPriceComment($update, $id_carrier_table_rate, $value, $id_lang)
    {
        $fields = [
            'condition_weight',
            'condition_price',
            'condition_ptprice',
            'condition_quantity',
            'condition_volume',
            'price',
            'comment'
        ];
        $condition_types = [
            'weight',
            'price',
            'ptprice',
            'quantity',
            'volume'
        ];

        if ($update !== false && in_array($update, $fields) && $id_carrier_table_rate !== false) {
            if ($update == 'price' || $update == 'comment') {
                $update_values = [
                    $update => KIHelpers::addslashes($value)
                ];
            } else {
                if (is_numeric($value)) {
                    $value_from = $value_to = $value;
                } else {
                    $value_range = explode('-', $value);
                    if (isset($value_range[0]) && is_numeric($value_range[0]) && isset($value_range[1]) && is_numeric($value_range[1])) {
                        $value_from = $value_range[0];
                        $value_to = $value_range[1];
                    } else {
                        $value_from = $value_to = -1;
                    }
                }
                $update_values = [
                    $update . '_from' => (float)(($value_from === '*' || $value_from === '') ? -1 : $value_from),
                    $update . '_to'   => (float)(($value_to === '*' || $value_to === '') ? -1 : $value_to)
                ];
            }

            // validate records
            $id_carrier_table_rate_explode = array_map('intval', explode(',', $id_carrier_table_rate));
            foreach ($id_carrier_table_rate_explode as $id_carrier_table_rate_explode_item) {
                $carrier_table_rate = new TRSCarrierTableRate($id_carrier_table_rate_explode_item);
                foreach ($update_values as $update_value_key => $update_value_value) {
                    $carrier_table_rate->{$update_value_key} = $update_value_value;
                }
                $message = $carrier_table_rate->validateFields(false, true);
                if ($message != 1) {
                    return [
                        'status'  => 'danger',
                        'message' => $message
                    ];
                }
            }

            // update values
            $update_values = KIHelpers::addslashes($update_values); // as prestashop strips slashes
            Db::getInstance()->update(
                'carrier_table_rate',
                $update_values,
                'id_carrier_table_rate IN (' . implode(',', array_map('intval', explode(',', $id_carrier_table_rate))) . ')'
            );

            if ((int)Db::getInstance()->getValue('SELECT ROW_COUNT()') > 0) {
                $id_carrier_table_rate = array_map('intval', explode(',', $id_carrier_table_rate));
                if (isset($id_carrier_table_rate[0])) {
                    $carrier_table_rate = new TRSCarrierTableRate();
                    $carrier_table_rate->clearCache(true);
                    $carrier_table_rate = new TRSCarrierTableRate((int)$id_carrier_table_rate[0]);
                    $status = [
                        'status'  => 'success',
                        'price'   => KIHelpers::stripslashes($carrier_table_rate->price),
                        'comment' => KIHelpers::stripslashes($carrier_table_rate->comment),
                        'message' => 'Record updated successfully'
                    ];
                    foreach ($condition_types as $condition_type) {
                        $status['condition_' . $condition_type . '_from'] = ($carrier_table_rate->{'condition_' . $condition_type . '_from'} == -1)
                            ? '*' : $carrier_table_rate->{'condition_' . $condition_type . '_from'};
                        $status['condition_' . $condition_type . '_to'] = ($carrier_table_rate->{'condition_' . $condition_type . '_to'} == -1)
                            ? '*' : $carrier_table_rate->{'condition_' . $condition_type . '_to'};
                    }

                    return $status;
                }
            }
        }

        return [
            'status'  => 'danger',
            'message' => 'Error updating record'
        ];
    }

    public static function updateStatus($id_carrier_table_rate, $active)
    {
        if ($id_carrier_table_rate !== false) {
            Db::getInstance()->update(
                'carrier_table_rate', ['active' => (int)$active],
                'id_carrier_table_rate IN (' . implode(',', array_map('intval', explode(',', $id_carrier_table_rate))) . ')'
            );

            return [
                'status'                => 'success',
                'message'               => 'Status updated successfully',
                'id_carrier_table_rate' => $id_carrier_table_rate,
                'active'                => $active
            ];
        }

        return [
            'status'                => 'danger',
            'message'               => 'Status update failed',
            'id_carrier_table_rate' => $id_carrier_table_rate,
            'active'                => $active
        ];
    }

    public static function updateOrder($entity = '', $id_carrier = null, $id_zone = null, $order = [], $id_shop)
    {
        if ($entity !== '' && $order !== []) {
            switch ($entity) {
                case 'zone':
                    if ($id_carrier !== 0) {
                        $order_ids = [];
                        foreach ($order as $order_item) {
                            $order_ids[] = (int)preg_replace('/^zone-/', '', $order_item);
                        }
                        $sql
                            = 'SET @rank := 0;
                        UPDATE `' . _DB_PREFIX_ . 'carrier_table_rate`
                            SET `order` = @rank := @rank + 1
                            WHERE `id_carrier` = ' . (int)$id_carrier . ' AND `id_shop` = ' . (int)$id_shop . '
                            ORDER BY FIELD(`id_zone`, ' . implode(',', $order_ids) . '), `order` ASC';
                        Db::getInstance()->execute($sql);
                    }
                    break;
                case 'rulegroup':
                    if ($id_carrier !== 0 && $id_zone !== -1) {
                        $order = array_map('intval', $order);

                        // get min order rank
                        $sql = new DbQuery();
                        $sql->select('MIN(`order`) AS `minorder`');
                        $sql->from('carrier_table_rate');
                        $sql->where('`id_group` IN (' . implode(',', $order) . ')');
                        $minorder = (int)Db::getInstance()->getValue($sql);
                        if (!$minorder) {
                            $minorder = 1;
                        }
                        $minorder--;

                        // update order
                        $sql = 'SET @rank := ' . (int)$minorder . ';
                        UPDATE `' . _DB_PREFIX_ . 'carrier_table_rate`
                            SET `order` = @rank := @rank + 1
                            WHERE
                                    `id_carrier` = ' . (int)$id_carrier . '
                                AND `id_zone` = ' . (int)$id_zone . '
                                AND `id_group` IN (' . implode(',', $order) . ')
                                AND `id_shop` = ' . (int)$id_shop . '
                            ORDER BY FIELD(`id_group`, ' . implode(',', $order) . '), `order` ASC';
                        Db::getInstance()->execute($sql);
                    }
                    break;
                case 'rule':
                    $order_item_count = count(explode(',', reset($order)));
                    $order_temp = [];
                    for ($i = 0; $i < $order_item_count; $i++) {
                        foreach ($order as $order_item) {
                            $order_item_explode = array_map('intval', explode(',', $order_item));
                            $order_temp[] = $order_item_explode[$i];
                        }
                    }
                    $id_carrier_table_rate = implode(',', $order_temp);

                    // get min order rank
                    $sql = new DbQuery();
                    $sql->select('MIN(`order`) AS `minorder`');
                    $sql->from('carrier_table_rate');
                    $sql->where('`id_carrier_table_rate` IN (' . $id_carrier_table_rate . ')');
                    $minorder = (int)Db::getInstance()->getValue($sql);
                    if (!$minorder) {
                        $minorder = 1;
                    }
                    $minorder--;

                    // update order
                    $sql = 'SET @rank := ' . (int)$minorder . ';
                    UPDATE `' . _DB_PREFIX_ . 'carrier_table_rate`
                        SET `order` = @rank := @rank + 1
                        WHERE `id_carrier_table_rate` IN (' . $id_carrier_table_rate . ')
                        ORDER BY FIELD(`id_carrier_table_rate`, ' . $id_carrier_table_rate . '), `order` ASC';
                    Db::getInstance()->execute($sql);
                    break;
            }
        }

        return [
            'status'  => 'success',
            'message' => 'Order saved successfully'
        ];
    }

    public static function updateProductSelected($id_product, $id_carrier, $select, $id_shop)
    {
        if ($id_product !== 0 && $id_carrier !== 0) {
            if ($select && !self::hasProductCarrier($id_product, $id_carrier, $id_shop)) {
                $carrier = new Carrier($id_carrier);

                Db::getInstance()->insert(
                    'product_carrier',
                    [
                        'id_product'           => (int)$id_product,
                        'id_carrier_reference' => (int)$carrier->id_reference,
                        'id_shop'              => (int)$id_shop
                    ]
                );

                return [
                    'status'     => 'success',
                    'message'    => 'Product selected',
                    'id_product' => $id_product,
                    'id_carrier' => $id_carrier,
                    'select'     => self::hasProductCarrier($id_product, $id_carrier, $id_shop)
                ];
            }

            if (!$select && self::hasProductCarrier($id_product, $id_carrier, $id_shop)) {
                $carrier = new Carrier($id_carrier);

                Db::getInstance()->delete(
                    'product_carrier',
                    '   id_product = ' . (int)$id_product . '
                    AND id_carrier_reference = ' . (int)$carrier->id_reference . '
                    AND id_shop = ' . (int)$id_shop);

                return [
                    'status'     => 'success',
                    'message'    => 'Product deselected',
                    'id_product' => $id_product,
                    'id_carrier' => $id_carrier,
                    'select'     => self::hasProductCarrier($id_product, $id_carrier, $id_shop)
                ];
            }
        }

        return [
            'status'     => 'danger',
            'message'    => 'Product select failed',
            'id_product' => $id_product,
            'id_carrier' => $id_carrier,
            'select'     => $select
        ];
    }

    public static function deleteRules($entity, $type, $id_carrier, $data, $id_zone, $id_carrier_table_rate, $id_shop)
    {
        if ($entity == 'rule') {
            if ($id_carrier_table_rate !== false && ($type == 'single' || $type == 'selected')) {
                $id_carrier_table_rate = array_map('intval', explode(',', $id_carrier_table_rate));
                if (isset($id_carrier_table_rate[0]) && is_numeric($id_carrier_table_rate[0])) {
                    $carrier_table_rate = new TRSCarrierTableRate((int)$id_carrier_table_rate[0]);
                    $id_carrier = $carrier_table_rate->id_carrier;
                }
                $id_carrier_table_rate = implode(',', $id_carrier_table_rate);
                Db::getInstance()->delete('carrier_table_rate',
                    'id_carrier_table_rate IN (' . $id_carrier_table_rate . ')');
            }
        } elseif ($entity == 'rulegroup') {
            if ($data !== false && ($type == 'single' || $type == 'selected')) {
                foreach ($data as $line) {
                    Db::getInstance()->delete('carrier_table_rate',
                        '   id_carrier = ' . (int)$id_carrier . '
                        AND id_zone = ' . (int)$line['id_zone'] . '
                        AND id_group = ' . (int)$line['id_group'] . '
                        AND id_shop = ' . (int)$id_shop);
                }
            } elseif ($id_zone !== -1 && $type == 'all') {
                Db::getInstance()->delete('carrier_table_rate',
                    '   id_carrier = ' . (int)$id_carrier . '
                    AND id_zone = ' . (int)$id_zone . '
                    AND id_shop = ' . (int)$id_shop);
            }
        }

        // clear carrier as module carrier if no records
        $records_total = self::getRuleGroupsNumRows($id_carrier, null, '', false);
        if ($records_total == 0) {
            self::clearCarrierAsTableRate($id_carrier);
        }

        return [
            'status'  => 'success',
            'message' => 'Table rates deleted successfully'
        ];
    }

    public static function deleteRulesByCarrier($id_carrier)
    {
        Db::getInstance()->delete('carrier_table_rate', 'id_carrier = ' . (int)$id_carrier);
    }

    public static function deleteRulesByZone($id_zone)
    {
        Db::getInstance()->delete('carrier_table_rate', 'id_zone = ' . (int)$id_zone);
    }

    public static function getIdGroup($id_carrier, $id_zone, $id_country, $id_state, $dest_city, $dest_zip, $id_shop)
    {
        // select query
        $sql = new DbQuery();
        $sql->select('id_group, dest_zip, MIN(`order`) AS `order`');
        $sql->from('carrier_table_rate');
        $sql->where(
            '   id_carrier = ' . (int)$id_carrier . '
            AND id_zone = ' . (int)$id_zone . '
            AND id_country = ' . (int)$id_country . '
            AND id_state = ' . (int)$id_state . '
            AND dest_city = \'' . KIHelpers::addslashes($dest_city) . '\'
            AND id_shop = ' . (int)$id_shop
        );
        $sql->groupBy('id_group, dest_zip');
        $sql->orderBy('MIN(`order`) ASC');

        // final query
        $sql_final
            = 'SELECT id_group
            FROM (' . $sql->build() . ') as SubTable
            GROUP BY id_group
            HAVING GROUP_CONCAT(dest_zip ORDER BY `order` ASC SEPARATOR \',\') = \'' . KIHelpers::addslashes($dest_zip) . '\'
            ORDER BY MIN(`order`) ASC';
        $id_group = (int)Db::getInstance()->getValue($sql_final);

        if ($id_group) {
            return $id_group;
        }

        if (self::$id_group_static == 0) {
            $sql = new DbQuery();
            $sql->select('MAX(id_group) AS id_group');
            $sql->from('carrier_table_rate');

            self::$id_group_static = (int)Db::getInstance()->getValue($sql) + 1;
        } else {
            self::$id_group_static++;
        }

        return self::$id_group_static;
    }

    public static function getZonesOrder($id_carrier, $id_shop)
    {
        $order = [];

        if ($id_carrier !== 0) {
            $sql = new DbQuery();
            $sql->select('DISTINCT id_zone');
            $sql->from('carrier_table_rate');
            $sql->where('id_carrier = ' . (int)$id_carrier . ' AND id_shop = ' . (int)$id_shop);
            $sql->orderBy('`order` ASC');
            $results = Db::getInstance()->executeS($sql);
            foreach ($results as $result) {
                $order[] = $result['id_zone'];
            }
        }

        return $order;
    }

    public static function getCountries($search, $id_lang, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('c.`id_country` as `id`, cl.`name` as `text`');
        $sql->from('country', 'c');
        $sql->leftJoin('country_shop', 'cs', 'cs.`id_country` = c.`id_country`');
        $sql->leftJoin('country_lang', 'cl', 'cl.`id_country` = c.`id_country`');
        $sql->where('cs.`id_shop` = ' . (int)$id_shop . '
            AND cl.`id_lang` = ' . (int)$id_lang . '
            AND `name` LIKE \'%' . KIHelpers::addslashes($search, true) . '%\'
            AND `active` = 1');
        $countries = Db::getInstance()->executeS($sql);

        return $countries;
    }

    public static function getStates($search, $id_country)
    {
        $sql = new DbQuery();
        $sql->select('`id_state` as `id`, `name` as `text`');
        $sql->from('state');
        $sql->where('`id_country` = ' . (int)$id_country . '
            AND `name` LIKE \'%' . KIHelpers::addslashes($search, true) . '%\'
            AND `active` = 1');
        $states = Db::getInstance()->executeS($sql);

        return $states;
    }

    public static function getCarrierIdFromCarrierId($id_carrier)
    {
        $sql
            = 'SELECT `id_carrier`
                FROM `' . _DB_PREFIX_ . 'carrier`
                WHERE `id_reference` = (
                    SELECT `id_reference`
                    FROM `' . _DB_PREFIX_ . 'carrier`
                    WHERE `id_carrier` = ' . (int)$id_carrier . '
                ) AND `deleted` = 0';

        return (int)Db::getInstance()->getValue($sql);
    }

    public static function isCarrierTableRate($id_carrier)
    {
        $sql = new DbQuery();
        $sql->select('COUNT(id_carrier)');
        $sql->from('carrier');
        $sql->where('id_carrier = ' . (int)$id_carrier . ' AND external_module_name = \'tablerateshipping\'');

        return (int)Db::getInstance()->getValue($sql);
    }

    public static function setCarrierAsTableRate($id_carrier)
    {
        return Db::getInstance()->update('carrier', [
            'is_module'            => 1,
            'shipping_external'    => 1,
            'need_range'           => 1,
            'external_module_name' => 'tablerateshipping'
        ], 'id_carrier = ' . (int)$id_carrier);
    }

    public static function clearCarrierAsTableRate($id_carrier)
    {
        return Db::getInstance()->update('carrier', [
            'is_module'            => 0,
            'shipping_external'    => 0,
            'need_range'           => 0,
            'external_module_name' => ''
        ], 'id_carrier = ' . (int)$id_carrier);
    }

    public static function clearAllCarriersAsTableRate()
    {
        return Db::getInstance()->update('carrier', [
            'is_module'            => 0,
            'shipping_external'    => 0,
            'need_range'           => 0,
            'external_module_name' => ''
        ], 'external_module_name = \'tablerateshipping\'');
    }

    public static function updateIdCarrier($old_id_carrier = null, $new_id_carrier = null)
    {
        Db::getInstance()->update('carrier_table_rate', [
            'id_carrier' => (int)$new_id_carrier
        ], 'id_carrier = "' . (int)$old_id_carrier . '"');
    }

    public static function hasProductCarrier($id_product, $id_carrier, $id_shop)
    {
        $sql = new DbQuery();
        $sql->select('COUNT(c.id_carrier) as count');
        $sql->from('product_carrier', 'pc');
        $sql->leftjoin('carrier', 'c', 'pc.id_carrier_reference = c.id_reference');
        $sql->where('deleted = 0
            AND pc.id_product = ' . (int)$id_product . '
            AND pc.id_shop = ' . (int)$id_shop . '
            AND c.id_carrier = ' . (int)$id_carrier);

        return (int)((int)Db::getInstance()->getValue($sql) > 0);
    }

    public static function getShippingPrice($id_carrier, $id_zone, $id_country, $id_state, $dest_city, $dest_zip, $vars, $id_shop)
    {
        $dest_zip = str_replace('-', '', str_replace(' ', '', $dest_zip));

        $sql = new DbQuery();
        $sql->select('*, SUBSTRING_INDEX(dest_zip, "-", 1) AS dest_zip_from, SUBSTRING_INDEX(dest_zip, "-", -1) AS dest_zip_to');
        $sql->from('carrier_table_rate');
        $sql->where('
                id_shop     = ' . (int)$id_shop . '
            AND id_carrier  = ' . (int)$id_carrier . '
            AND (id_zone    = ' . (int)$id_zone . '                           OR id_zone = 0)
            AND (id_country = ' . (int)$id_country . '                        OR id_country = 0)
            AND (id_state   = ' . (int)$id_state . '                          OR id_state   = 0)
            AND (dest_city  = \'' . KIHelpers::addslashes($dest_city, true) . '\'   OR dest_city  = \'\')
            AND (' . (float)$vars['tw'] . '   BETWEEN condition_weight_from   AND condition_weight_to   OR (condition_weight_from   < 0 AND condition_weight_to   < 0))
            AND (' . (float)$vars['tp'] . '   BETWEEN condition_price_from    AND condition_price_to    OR (condition_price_from    < 0 AND condition_price_to    < 0))
            AND (' . (float)$vars['tptp'] . ' BETWEEN condition_ptprice_from  AND condition_ptprice_to  OR (condition_ptprice_from  < 0 AND condition_ptprice_to  < 0))
            AND (' . (float)$vars['tq'] . '   BETWEEN condition_quantity_from AND condition_quantity_to OR (condition_quantity_from < 0 AND condition_quantity_to < 0))
            AND (' . (float)$vars['tv'] . '   BETWEEN condition_volume_from   AND condition_volume_to   OR (condition_volume_from   < 0 AND condition_volume_to   < 0))
            AND active = 1
        ');
        $sql->having('
            (
                dest_zip_from = ""
                AND dest_zip_to = ""
            )
            OR (
                dest_zip_from = dest_zip_to
                AND "' . KIHelpers::addslashes($dest_zip, true) . '" LIKE REPLACE(REPLACE(REPLACE(dest_zip_to, "*", "%"), " ", ""), "-", "")
            )
            OR (
                dest_zip_from = dest_zip_to
                AND "' . KIHelpers::addslashes($dest_zip, true) . '" LIKE REPLACE(REPLACE(REPLACE(dest_zip_from, "*", "%"), " ", ""), "-", "")
            )
            OR (
                dest_zip_from != ""
                AND dest_zip_to != ""
                AND REPLACE(REPLACE(dest_zip_from, " ", ""), "-", "") <= \'' . KIHelpers::addslashes($dest_zip, true) . '\'
                AND REPLACE(REPLACE(dest_zip_to, " ", ""), "-", "") >= \'' . KIHelpers::addslashes($dest_zip, true) . '\'
            )
        ');
        $sql->orderBy('`order` ASC');
        $results = Db::getInstance()->executeS($sql);

        $language = new ExpressionLanguage();
        $language->registerProvider(new TRSExpressionFunctionProvider());
        $shipping_price = false;

        foreach ($results as $result) {
            $vars_temp = $vars;
            $vars_temp = array_merge($vars_temp, [
                'ctwf'   => $result['condition_weight_from'],
                'ctwt'   => $result['condition_weight_to'],
                'ctwi'   => $vars_temp['tw'] - $result['condition_weight_from'],
                'ctpf'   => $result['condition_price_from'],
                'ctpt'   => $result['condition_price_to'],
                'ctpi'   => $vars_temp['tp'] - $result['condition_price_from'],
                'ctptpf' => $result['condition_ptprice_from'],
                'ctptpt' => $result['condition_ptprice_to'],
                'ctptpi' => $vars_temp['tptp'] - $result['condition_ptprice_from'],
                'ctqf'   => $result['condition_quantity_from'],
                'ctqt'   => $result['condition_quantity_to'],
                'ctqi'   => $vars_temp['tq'] - $result['condition_quantity_from'],
                'ctvf'   => $result['condition_volume_from'],
                'ctvt'   => $result['condition_volume_to'],
                'ctvi'   => $vars_temp['tv'] - $result['condition_volume_from']
            ]);

            $result['price'] = str_replace('$', '', $result['price']); // for bc with old formula variables with $

            try {
                $shipping_price = $language->evaluate($result['price'], $vars_temp);
            } catch (Exception $e) {
                $shipping_price = false;
            }

            if ($shipping_price < 0) {
                continue;
            } else {
                break;
            }
        }

        return $shipping_price;
    }
}

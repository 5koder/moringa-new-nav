<?php

class dboptimization extends Module
{
    function __construct()
    {
        $this->name = 'dboptimization';
        $this->tab = 'administration';
        $this->author = 'MyPresta.eu';
        $this->version = '1.3.3';
        ini_set("display_errors", 0);
        error_reporting(0);
        parent::__construct();
        $this->displayName = $this->l('Database Optimization');
        $this->description = $this->l('Optimize your database, remove unnecessary informations, speed up your store!');
        $this->mkey = "freelicense";
        $this->mypresta_link = 'https://mypresta.eu/modules/administration-tools/database-optimization.html';
        $this->checkforupdates();
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = dboptimizationUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (dboptimizationUpdate::version($this->version) < dboptimizationUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = dboptimizationUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (dboptimizationUpdate::version($this->version) < dboptimizationUpdate::version(dboptimizationUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function inconsistency($ret)
    {
        return true;
    }

    function install()
    {
        if (parent::install() == false OR !Configuration::updateValue('update_' . $this->name, '0')) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        $output = "";
        if (Tools::isSubmit('dbtabletoclean')) {
            if (Tools::getValue('dbtabletoclean') == "cart") {
                Configuration::updateValue('dbopt_' . Tools::getValue('dbtabletoclean'), date("Y-m-d h:i:s"));
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM `' . _DB_PREFIX_ . Tools::getValue('dbtabletoclean') . '` WHERE id_cart not in (SELECT id_cart from ' . _DB_PREFIX_ . 'orders)');
            } else {
                Configuration::updateValue('dbopt_' . Tools::getValue('dbtabletoclean'), date("Y-m-d h:i:s"));
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM `' . _DB_PREFIX_ . Tools::getValue('dbtabletoclean') . '`');
            }
        }
        $output .= "";
        return $output . $this->displayForm() . $this->checkforupdates(0, 1);
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 0) {
            return $exp[0];
        }
        if ($part == 1) {
            if ($exp[0] >= 8) {
                return 7;
            }
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }
    public function dbcounter($table)
    {
        if ($table == "cart") {
            $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . $table . '` WHERE id_cart not in (SELECT id_cart from ' . _DB_PREFIX_ . 'orders)');
        } else {
            $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT count(*) AS count FROM `' . _DB_PREFIX_ . $table . '`');
        }
        return $query[0]['count'];
    }

    public function displayForm()
    {
        return '
        <div style="diplay:block; clear:both; margin-bottom:20px;">
		  <iframe src="//apps.facepages.eu/somestuff/onlyexample.html" width="100%" height="150" border="0" style="border:none;"></iframe>
		</div>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <input type="hidden" name="dbtabletoclean" value="cart" />
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <fieldset style=" margin-right:10px; vertical-align:top;">
        			<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('unwanted carts') . ' (' . $this->dbcounter('cart') . ')</legend>
                    <input type="submit" name="dbtableclean" value="' . $this->l('clean') . '" class="button"/>
                    ' . $this->l('date of last clean') . ': <b>' . (Configuration::get('dbopt_cart') == null ? $this->l('not cleaned yet') : Configuration::get('dbopt_cart')) . '</b>
                </fieldset>
            </div>
		</form>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="margin-top:20px">
            <input type="hidden" name="dbtabletoclean" value="connections" />
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <fieldset style=" margin-right:10px; vertical-align:top;">
        			<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Connection informations') . ' (' . $this->dbcounter('connections') . ')</legend>
                    <input type="submit" name="dbtableclean" value="' . $this->l('clean') . '" class="button"/>
                    ' . $this->l('date of last clean') . ': <b>' . (Configuration::get('dbopt_connections') == null ? $this->l('not cleaned yet') : Configuration::get('dbopt_connections')) . '</b>
                </fieldset>
            </div>
		</form>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="margin-top:20px">
            <input type="hidden" name="dbtabletoclean" value="connections_page" />
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <fieldset style=" margin-right:10px; vertical-align:top;">
        			<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Connection informations (page)') . ' (' . $this->dbcounter('connections_page') . ')</legend>
                    <input type="submit" name="dbtableclean" value="' . $this->l('clean') . '" class="button"/>
                    ' . $this->l('date of last clean') . ': <b>' . (Configuration::get('dbopt_connections_page') == null ? $this->l('not cleaned yet') : Configuration::get('dbopt_connections_page')) . '</b>
                </fieldset>
            </div>
		</form>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="margin-top:20px">
            <input type="hidden" name="dbtabletoclean" value="connections_source" />
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <fieldset style=" margin-right:10px; vertical-align:top;">
        			<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Connection informations (source)') . ' (' . $this->dbcounter('connections_source') . ')</legend>
                    <input type="submit" name="dbtableclean" value="' . $this->l('clean') . '" class="button"/>
                    ' . $this->l('date of last clean') . ': <b>' . (Configuration::get('dbopt_connections_source') == null ? $this->l('not cleaned yet') : Configuration::get('dbopt_connections_source')) . '</b>
                </fieldset>
            </div>
		</form>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="margin-top:20px">
            <input type="hidden" name="dbtabletoclean" value="guest" />
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <fieldset style=" margin-right:10px; vertical-align:top;">
        			<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Guests') . ' (' . $this->dbcounter('guest') . ')</legend>
                    <input type="submit" name="dbtableclean" value="' . $this->l('clean') . '" class="button"/>
                    ' . $this->l('date of last clean') . ': <b>' . (Configuration::get('dbopt_guest') == null ? $this->l('not cleaned yet') : Configuration::get('dbopt_guest')) . '</b>
                </fieldset>
            </div>
		</form>                
		<div style="diplay:block; clear:both; margin-bottom:20px;">
		</div>' . $this->l('like us on Facebook') . '</br><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fmypresta&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=true&amp;font=verdana&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=276212249177933" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px; margin-top:10px;" allowtransparency="true"></iframe>
        ' . '<div style="float:right; text-align:right; display:inline-block; margin-top:10px; font-size:10px;">
        ' . $this->l('Proudly developed by') . ' <a href="http://mypresta.eu" style="font-weight:bold; color:#B73737">MyPresta<font style="color:black;">.eu</font>.</a>
        </div>';
    }
}

class dboptimizationUpdate extends dboptimization
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }

        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);

        return $actual_version;
    }
}


?>
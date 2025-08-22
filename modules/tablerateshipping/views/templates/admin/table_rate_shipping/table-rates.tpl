{**
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
 *}

<div id="trs-panel-table-rates" class="panel panel-default">
    <div class="panel-heading">
        <img src="../modules/{$module_name|escape:'htmlall':'UTF-8'}/views/img/date.png"/>&nbsp;
        <span>{l s='Table Rates' mod='tablerateshipping'}</span>
        <span id="trs-table-loader"><i></i></span>
    </div>
    <div class="panel-body">
        <div class="alert alert-success">
            <a class="alert-close pull-right" href="javascript:void(0);"><i class="glyphicon glyphicon-remove"></i></a>
            <span class="alert-message"></span>
        </div>
        <div class="action_buttons">
            <div class="btn-group">
                <a id="trs-importcsv" class="btn btn-default" href="javascript:void(0);">
                    <i class="glyphicon glyphicon-import"></i>
                    <span>{l s='Import CSV' mod='tablerateshipping'}</span></a>
                <a id="trs-exportcsv" class="btn btn-default" href="javascript:void(0);">
                    <i class="glyphicon glyphicon-export"></i>
                    <span>{l s='Export CSV' mod='tablerateshipping'}</span></a>
                <a id="trs-selectproducts" class="btn btn-default" href="javascript:void(0);">
                    <i class="glyphicon glyphicon-list"></i>
                    <span>{l s='Select Products' mod='tablerateshipping'}</span></a>
                <a id="trs-reload" class="btn btn-default" href="javascript:void(0);">
                    <i class="glyphicon glyphicon-refresh"></i> <span>{l s='Reload' mod='tablerateshipping'}</span></a>
            </div>
        </div>
        <div class="toggle_columns">
            <span></span>
        </div>
        <div id="trs-zones">
            <ul>
                {foreach $zones as $zone}
                    <li id="zone-{$zone.id_zone|escape:'htmlall':'UTF-8'}"><a href="#tabs-{$zone.id_zone|escape:'htmlall':'UTF-8'}">{$zone.name|escape:'htmlall':'UTF-8'}</a></li>
                {/foreach}
            </ul>
            {foreach $zones as $zone}
                <div id="tabs-{$zone.id_zone|escape:'htmlall':'UTF-8'}" data-zone="{$zone.id_zone|escape:'htmlall':'UTF-8'}">
                    <table class="trs-table-rates table table-striped table-responsive-row clearfix" width="100%" data-zone="{$zone.id_zone|escape:'htmlall':'UTF-8'}">
                        <thead>
                        <tr>
                            <th class="td-id_country">
                                <span class="title_box">{l s='Country' mod='tablerateshipping'}</span>
                            </th>
                            <th class="td-id_state">
                                <span class="title_box">{l s='State' mod='tablerateshipping'}</span>
                            </th>
                            <th class="td-dest_city">
                                <span class="title_box">{l s='City' mod='tablerateshipping'}</span>
                            </th>
                            <th class="td-dest_zip">
                                <span class="title_box">{l s='Zip/Post Code' mod='tablerateshipping'}</span>
                            </th>
                            <th class="td-actions text-center">
                                <span class="title_box">{l s='Actions' mod='tablerateshipping'}</span>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            {/foreach}
        </div>
    </div>
</div>
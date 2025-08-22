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

<div id="trs-panel-select-carrier" class="panel panel-default">
    <div class="panel-heading">
        <img src="../modules/{$module_name|escape:'htmlall':'UTF-8'}/views/img/select-carrier.png"/>&nbsp;
        <span>{l s='Select Carrier' mod='tablerateshipping'}</span>
    </div>
    <div class="panel-body">
        <select id="trs-select-carrier" class="form-control">
            <option value="">{l s='-- Select carrier --' mod='tablerateshipping'}</option>
            {foreach from=$carriers item=carrier}
                <option value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}">{$carrier.name|escape:'htmlall':'UTF-8'} (ID: {$carrier.id_carrier|escape:'htmlall':'UTF-8'})</option>
            {/foreach}
        </select>
    </div>
</div>
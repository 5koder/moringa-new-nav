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

<div id="trs-content">
    <div class="bootstrap">
        {include file=$module_views_dir|cat:"templates/admin/common/header.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/table_rate_shipping/select-carriers.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/table_rate_shipping/table-rates.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/table_rate_shipping/select-products.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/table_rate_shipping/importexport-rate.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/table_rate_shipping/uploaddownload-files.tpl"}
        {include file=$module_views_dir|cat:"templates/admin/common/footer.tpl"}
    </div>
</div>
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

<div class="modal fade bs-example-modal-lg" id="modalselectproducts" role="dialog"
     aria-labelledby="modalselectproductslabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{l s='Close' mod='tablerateshipping'}</span>
                </button>
                <h4 class="modal-title" id="modalselectproductslabel">{l s='Select Products' mod='tablerateshipping'}</h4>
            </div>
            <div class="panel">
                <div class="panel-body">
                    <table id="trs-table-products" class="table table-striped table-responsive-row clearfix" width="100%">
                        <thead>
                        <tr>
                            <th><span class="title_box">{l s='ID' mod='tablerateshipping'}</span></th>
                            <th><span class="title_box">{l s='Name' mod='tablerateshipping'}</span></th>
                            <th><span class="title_box">{l s='Reference' mod='tablerateshipping'}</span></th>
                            <th><span class="title_box">{l s='Status' mod='tablerateshipping'}</span></th>
                            <th class="center">{l s='Selected' mod='tablerateshipping'}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
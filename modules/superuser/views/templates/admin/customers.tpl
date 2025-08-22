{**
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
*}
<script>
    $(document).ready(function() {
        $('#customer').typeWatch({
            captureLength: 0,
            highlight: true,
            wait: 50,
            callback: function () {
                searchCustomers();
            }
        });
        function searchCustomers() {
            $('#customers').html('<span class="{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}alert{/if} superuser"><h4>{l s='Loading... please, wait.' mod='superuser'}</h4></span>');
            $.ajax({
                type: "POST",
                url: "{$link->getAdminLink('AdminCustomers')}",
                async: true,
                dataType: "json",
                data: {
                    ajax: "1",
                    tab: "AdminCustomers",
                    action: "searchCustomers",
                    customer_search: $('#customer').val()
                },
                success: function (res) {
                    if (res.found) {
                        var html = '';
                        $.each(res.customers, function () {
                            html += '<div class="customerCard col-lg-3{if version_compare($smarty.const._PS_VERSION_,'1.6','<')} ps15{/if}">';
                            html += '<div class="panel">';
                            html += '<div class="panel-heading">' + this.firstname + ' ' + this.lastname;
                            html += '<span class="pull-right">#' + this.id_customer + '</span></div>';
                            html += '<span>' + this.email + '</span><br/>';
                            html += '<span class="text-muted">' + ((this.birthday != '0000-00-00') ? this.birthday : '') + '</span><br/>';
                            if (this.is_guest === '0') {
                                html += '<input type="checkbox" id="use_last_cart' + this.id_customer + '" title="" class="use-last-cart" onclick="setLastCart(this, \'superuser-controller' + this.id_customer + '\')" checked/><span class="label-tooltip">{l s='Use last cart' mod='superuser'}</span>';
                            } else {
                                html += '<br/>';
                            }
                            html += '<div class="panel-footer">';
                            if (this.is_guest === '1') {
                                html += '<a href="javascript:void(0);" class="btn btn-default fancybox connect-btn superuser-controller'+ this.id_customer + ' disabled" target="_blank"><i class="icon-warning"></i> {l s='Not a registered customer. You cannot connect as this user.' mod='superuser'}</a><br />';
                            } else {
                                html += '<a href="{$controller|escape:'htmlall':'UTF-8'}&id_customer=' + this.id_customer + '&secure_key=' + this.passwd+ '" class="btn btn-default fancybox connect-btn superuser-controller'+ this.id_customer + '" target="_blank"><i class="icon-home"></i> {l s='Connect homepage' mod='superuser'} <i class="icon-external-link-sign"></i></a><br />';
                                html += '<a href="{$controller|escape:'htmlall':'UTF-8'}&id_customer=' + this.id_customer + '&secure_key=' + this.passwd+ '&redir=myaccount" class="btn btn-default fancybox connect-btn superuser-controller'+ this.id_customer + ' myaccount" target="_blank"><i class="icon-user"></i> {l s='Connect customer account' mod='superuser'} <i class="icon-external-link-sign"></i></a><br />';
                                html += '<a href="{$controller|escape:'htmlall':'UTF-8'}&id_customer=' + this.id_customer + '&secure_key=' + this.passwd+ '&redir=cart" class="btn btn-default fancybox connect-btn superuser-controller'+ this.id_customer + ' cart" target="_blank"><i class="icon-cart-plus"></i> {l s='Connect customer cart' mod='superuser'} <i class="icon-external-link-sign"></i></a><br />';
                            }
                            html += '<a href="{$link->getAdminLink('AdminCustomers')}&id_customer=' + this.id_customer + '&viewcustomer" class="btn btn-default fancybox connect-btn superuser-controller'+ this.id_customer + '"><i class="icon-search"></i> {l s='Customer details' mod='superuser'}</a>';
                            //html += '<button type="button" data-customer="' + this.id_customer + '" class="setup-customer btn btn-default pull-right"><i class="icon-arrow-right"></i> {l s='Choose' mod='superuser'}</button>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });
                    } else {
                        html = '<div class="alert alert-warning">{l s='No customers found' mod='superuser'}</div>';
                    }
                    $('#customers').html(html);
                    resetBind();
                }
            });
        }
        function resetBind() {
            $('.fancybox_customer').fancybox({
                'type': 'iframe',
                'width': '90%',
                'height': '90%',
                'afterClose': function () {
                    searchCustomers();
                }
            });
        }
    });
    function setLastCart(el, id) {
        var href = $('a.' + id).attr('href');
        var hrefMyaccount = $('a.' + id + '.myaccount').attr('href');
        var hrefCart = $('a.' + id + '.cart').attr('href');
        if($(el).is(":checked")) {
            $('a.' + id).attr('href', href.replace('use_last_cart=0','use_last_cart=1'));
            $('a.' + id + '.myaccount').attr('href', hrefMyaccount.replace('use_last_cart=0','use_last_cart=1'));
            $('a.' + id + '.cart').attr('href', hrefCart.replace('use_last_cart=0','use_last_cart=1'));
        } else {
            $('a.' + id).attr('href', href.replace('use_last_cart=1','use_last_cart=0'));
            $('a.' + id + '.myaccount').attr('href', hrefMyaccount.replace('use_last_cart=1','use_last_cart=0'));
            $('a.' + id + '.cart').attr('href', hrefCart.replace('use_last_cart=1','use_last_cart=0'));
        }
    }
</script>
<div class="panel form-horizontal" id="customer_part">
    <div class="panel-heading{if version_compare($smarty.const._PS_VERSION_,'1.6','<')} ps15{/if}">
        <i class="icon-keyboard"></i>
        {l s='Search for an existing customer by typing the first letters of his/her name.' mod='superuser'}
    </div>
    <div id="search-customer-form-group" class="form-group">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" id="customer" title="" value="" />
                        <span class="input-group-addon">
        					<i class="icon-search"></i>
						</span>
                    </div>
                </div>
                <!-- <div class="col-lg-6"></div> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div id="customers"></div>
    </div>
</div>

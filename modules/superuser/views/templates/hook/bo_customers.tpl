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
{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}
    <!-- TODO PS14 -->
{literal}
    <script type="text/javascript">
        if (document.URL.indexOf('id_customer') > 0) {
            $(document).ready(function() {
                var id_customer = '{/literal}{$smarty.get.id_customer|default:0|escape:'htmlall':'UTF-8'}{literal}'
            });
        } else {
            $(document).ready(function() {
                $('.table.table tbody tr').each(function(){

                })
            });
        }
    </script>
{/literal}
{else}
{literal}
    <style>
        .icon-user.bo {font-size: 28px; width: 30px; height: 30px;}
    </style>
    <script type="text/javascript">
        var customers_super_user_list = {
            init: function() {
                customers_super_user_list.createSuperUserListDropdown();
            },
            createSuperUserListDropdown: function() {
                var parent = $('table.table.customer');
                if (parent.length) {
                    var items = parent.find('tbody tr');
                    if (items.length) {
                        items.each(function(){
                            var last_cell = $(this).find('td:last');
                            var checkbox = $(this).find('td:first input[type=checkbox]');
                            var id_customer = 0;
                            if (checkbox.length > 0) {
                                id_customer = parseInt(checkbox.attr('value'));
                            } else {
                                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}
                                id_customer = parseInt($(this).find('td:first').next().html());
                                {/literal}{else}{literal}
                                id_customer = parseInt($(this).find('td:first').html());
                                {/literal}{/if}{literal}
                            }
                            if (last_cell.length) {
                                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}
                                var html = '<a href="{/literal}{$controller_superuser|escape:'htmlall':'UTF-8'}{literal}?use_last_cart=1&id_customer=' + id_customer + '&secure_key={/literal}{$superuser_token|escape:'htmlall':'UTF-8'}{literal}" target="_blank" title="{/literal}{$action_su|escape:'htmlall':'UTF-8'}{literal}" class="btn btn-default"> <i class="icon-trash"></i> {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}<img src="{/literal}{$this_path_bo|escape:'htmlall':'UTF-8'}{literal}views/img/superuser.png" width="16px"/>{/literal}{else}{$action_su|escape:'htmlall':'UTF-8'}{/if}{literal}</a>';
                                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{literal}
                                $(this).find('td:last div').append(html);
                                {/literal}{elseif version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}
                                $(this).find('td:last').append(html);
                                {/literal}{/if}{literal}
                                {/literal}{else}{literal}
                                var button_container = last_cell.find('.btn-group'),
                                    button = customers_super_user_list.createSuperUserButton(id_customer);
                                if (last_cell.find('.btn-group-action').length) {
                                    button_container.find('ul.dropdown-menu').append($(document.createElement('li')).attr({'class': 'divider'}));
                                    button_container.find('ul.dropdown-menu').append(button);
                                } else {
                                    button_container.wrap($(document.createElement('div')).addClass('btn-group-action'));
                                    button_container.append(
                                        $(document.createElement('button')).addClass('btn btn-default dropdown-toggle').attr('data-toggle', 'dropdown')
                                            .append($(document.createElement('i')).addClass('icon-caret-down'))
                                    ).append($(document.createElement('ul')).addClass('dropdown-menu').append(button))
                                }
                                {/literal}{/if}{literal}
                            }
                        });
                    }
                }
            },
            createSuperUserButton: function(id_customer) {
                return $(document.createElement('li')).append($(document.createElement('a')).attr({'href': '{/literal}{$controller_superuser|escape:'htmlall':'UTF-8'}{literal}?use_last_cart=1&id_customer=' + id_customer + '&secure_key={/literal}{$superuser_token|escape:'htmlall':'UTF-8'}{literal}', 'title':'{/literal}{$action_su|escape:'htmlall':'UTF-8'}{literal}', 'target': '_blank'}).html('<i class="icon-user"></i> ' + customers_super_user_list.tr('{/literal}{$action_su|escape:'htmlall':'UTF-8'}{literal}')));
            },
            tr: function(str) {
                return str;
            }
        };
        $(function(){
            customers_super_user_list.init();
        });
        if (document.URL.indexOf('id_customer') > 0) {
            $(document).ready(function(){
                {/literal}{if $show_button !== false}{literal}
                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}{literal}
                $('ul#toolbar-nav').prepend('<li><a id="page-header-desc-customer-superuser" class="toolbar_btn" href="{/literal}{$controller_superuser|escape:'htmlall':'UTF-8'}{literal}" target="_blank" title="{/literal}{$action_superuser|escape:'htmlall':'UTF-8'}{literal}"><i class="icon-user bo"></i><div>{/literal}{$action_superuser|escape:'htmlall':'UTF-8'}{literal}</div></a></li>');
                {/literal}{/if}{literal}
                html = '<a href="{/literal}{$controller_superuser|escape:'htmlall':'UTF-8'}{literal}" target="_blank" title="{/literal}{$action_su|escape:'htmlall':'UTF-8'}{literal}" class="btn btn-default"> <i class="icon-user bo"></i> {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}<img src="{/literal}{$this_path_bo|escape:'htmlall':'UTF-8'}{literal}views/img/superuser.png" width="16px"/>{/literal}{else}{$action_su|escape:'htmlall':'UTF-8'}{/if}{literal}</a>';
                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{literal}
                $("#content div.col-lg-7 .panel:first .hidden-print:first").prepend(html);
                {/literal}{elseif version_compare($smarty.const._PS_VERSION_,'1.6','>=')}{literal}
                $("#content div.col-lg-7 .panel:first .hidden-print:first").prepend(html);
                {/literal}{else}{literal}
                html = '<a class="toolbar_btn" href="{/literal}{$controller_superuser|escape:'htmlall':'UTF-8'}{literal}" target="_blank"><span class="icon-user bo"><img src="{/literal}{$this_path_bo|escape:'htmlall':'UTF-8'}{literal}views/img/superuser32.png" /></span> <div>{/literal}{$action_superuser|escape:'htmlall':'UTF-8'}{literal}</div></a>';
                $('ul.cc_button').prepend('<li>' + html + '</li>');
                {/literal}{/if}{literal}
                {/literal}{/if}{literal}
            });
            function isGuest() {
                alert('{/literal}{l s='Not a registered customer. You cannot connect as this user.' mod='superuser'}{literal}');
            }
        }
    </script>
{/literal}
{/if}

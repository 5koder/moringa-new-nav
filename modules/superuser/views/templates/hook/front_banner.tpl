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

<div class="superuser-front-container">
    <span class="superuser-front-msg{if version_compare($smarty.const._PS_VERSION_,'1.6','<')} ps15{/if}">
        {if version_compare($smarty.const._PS_VERSION_,'1.7','>=')}
            <i class="material-icons">&#xE7FD;</i>
        {else}
            {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
                <img src="{$base_dir}/modules/superuser/views/img/icon-user.png" alt="" title="">
            {else}
                <i class="icon-user"></i>
            {/if}
        {/if}
        {l s='You are now logged as' mod='superuser'} <strong>{$su_customer->firstname|escape:'htmlall':'UTF-8'} {$su_customer->lastname|escape:'htmlall':'UTF-8'}</strong> ({$su_customer->email|escape:'htmlall':'UTF-8'})
        <a class="superuser-logout-btn" href="{$controller_logout|escape:'htmlall':'UTF-8'}" rel="nofollow" title="{l s='Sign out' mod='superuser'}">
            {if version_compare($smarty.const._PS_VERSION_,'1.7','>=')}
                <i class="material-icons">&#xE879;</i>
            {else}
                {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
                    <img src="{$base_dir}/modules/superuser/views/img/icon-sign-out.png" alt="" title="">
                {else}
                    <i class="icon-sign-out"></i>
                {/if}
            {/if}
        </a>
    </span>
</div>

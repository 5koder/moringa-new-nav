{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if !$tab} active{/if}"
    data-toggle="list" href="#list-settings">
    <i class="material-icons-outlined text-primary">settings</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Settings' mod='advancedemailguard'}</span>
</a>

<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if $tab === 'forms'} active{/if}"
    data-toggle="list" href="#list-forms">
    <i class="material-icons-outlined text-primary">assignment_turned_in</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Forms' mod='advancedemailguard'}</span>
</a>

<a class="list-group-item list-group-item-action font-weight-bold text-left text-md-center{if $tab === 'logs'} active{/if}"
    data-toggle="list" href="#list-logs">
    <i class="material-icons-outlined text-primary">policy</i>
    <span class="d-inline-block d-md-block px-1 pt-md-1">{l s='Validations' mod='advancedemailguard'}</span>
</a>
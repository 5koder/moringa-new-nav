{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

<header id="header" class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-md-0">
    <div class="container-fluid px-md-4">
        <div class="navbar-brand">
            <img src="{$basePath|escape:'html':'UTF-8'}logo.png" width="32px" height="32px" alt="" class="mr-2">
            <span class="app-name">{$appName|escape:'html':'UTF-8'}</span>
            <span class="app-version badge bg-white border ml-1 d-none d-sm-inline" title="{l s='Current version' mod='advancedemailguard'}"
                data-toggle="tooltip">{$appVersion|escape:'html':'UTF-8'}</span>
            {if $isDemo}
                <span class="app-demo badge badge-dark ml-1" style="background-color: #e83e8c" title="{l s='This module is a demo. Some configuration will be unavailable. After purchasing and downloading the module all settings will be available.' mod='advancedemailguard'}"
                    data-toggle="tooltip">
                    <i class="material-icons-outlined md-18">block</i>
                    {$trans.demo|escape:'html':'UTF-8'}
                </span>
            {/if}
        </div>
        <button class="navbar-toggler btn btn-light" type="button" data-toggle="collapse" data-target="#extra">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="extra">
            <ul class="navbar-nav ml-auto">
                {if !$isDemo}
                    <li class="nav-item">
                        <a class="nav-link" href="{$psAddonsLinks.ratings|escape:'html':'UTF-8'}" target="_blank">
                            <i class="material-icons-outlined" style="color: #e83e8c">grade</i>
                            {l s='Rate our module' mod='advancedemailguard'}</a>
                    </li>
                {/if}
                <li class="nav-item">
                    <a class="nav-link" href="{$psAddonsLinks.profile|escape:'html':'UTF-8'}" target="_blank">
                        <i class="material-icons-outlined text-primary">layers</i>
                        {l s='Discover more modules' mod='advancedemailguard'}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="material-icons-outlined text-primary">help_outline</i>
                        {l s='Help' mod='advancedemailguard'}</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{$docsLink|escape:'html':'UTF-8'}" target="_blank" class="dropdown-item">
                            {l s='Documentation' mod='advancedemailguard'}</a>
                        <a href="{$psAddonsLinks.contact|escape:'html':'UTF-8'}" target="_blank" class="dropdown-item">
                            {l s='Contact us' mod='advancedemailguard'}</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>

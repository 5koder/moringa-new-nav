{**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*}

{extends file='./layouts/main.tpl'}

{block name='content'}
    {if $canConfig}
        <div class="row no-gutters row-main flex-md-nowrap">
            <div class="col-md col-nav bg-white shadow">
                <div class="list-group list-group-flush">
                    {include file='./_partials/nav.tpl'}
                </div>
            </div>
            <div class="col-md col-content">
                {include file='./_partials/rating.tpl'}
                {include file="./_partials/alerts.tpl"}
                <div class="tab-content pt-4">
                    <div class="tab-pane fade show{if !$tab} active{/if}" id="list-settings">
                        <div class="container-md px-md-4">
                            {include file='./pages/settings/recaptcha.tpl'}
                            {include file='./pages/settings/email.tpl'}
                            {include file='./pages/settings/message.tpl'}
                        </div>
                    </div>
                    <div class="tab-pane fade show{if $tab === 'forms'} active{/if}" id="list-forms">
                        <div class="container-md px-md-4">
                            {include file='./pages/forms/settings.tpl'}
                            {include file='./pages/forms/preview.mode.tpl'}
                        </div>
                    </div>
                    <div class="tab-pane fade show{if $tab === 'logs'} active{/if}" id="list-logs">
                        <div class="container{if $logsType !== null}-fluid{else}-md{/if} px-md-4">
                            {include file='./pages/logs/index.tpl'}
                        </div>
                    </div>
                </div>

                {include file='./_partials/footer.tpl'}
            </div>
        </div>
    {else}
        {include file="./_partials/alerts.tpl"}
    {/if}
{/block}
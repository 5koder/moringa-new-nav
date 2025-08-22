{*
* 2003-2019 Business Tech
*
* @author Business Tech SARL
* @copyright  2003-2019 Business Tech SARL
*}
{* cases - dedicated signup page / MC signup included form *}
{if !$hide_nl_module}
<div class="container">
	<div class="row">
		<div class="block_newsletter col-xs-12 col-sm-12 col-md-12 col-lg-8">
{else}
	<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
{/if}
		<p>{$label}</p>
		<a href="{$link_dedicated}" class="btn btn-primary">{l s='Subscribe' mod='btmailchimpecommerce'}</a>
{if !$hide_nl_module}
		</div>
	</div>
</div>
{else}
	</div>
{/if}
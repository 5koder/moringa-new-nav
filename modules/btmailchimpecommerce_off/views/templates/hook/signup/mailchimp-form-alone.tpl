{*
* 2003-2019 Business Tech
*
* @author Business Tech SARL
* @copyright  2003-2019 Business Tech SARL
*}
<style type="text/css">
	.mce label {literal}{text-align: left !important;}{/literal}
	.block_newsletter form input[type=text] {literal}{min-width: inherit !important}{/literal}
</style>
{* cases - dedicated signup page / MC signup included form *}
{if !$hide_nl_module}
<div class="container">
	<div class="row">
		<div class="block_newsletter col-xs-12 col-sm-12 col-md-12 col-lg-8 mce">
{else}
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 mce">
{/if}
	{$form nofilter}
	{if !$hide_nl_module}
			</div>
		</div>
	</div>
{else}
</div>
{/if}
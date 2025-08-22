{*
* 2003-2019 Business Tech
*
* @author Business Tech SARL
* @copyright  2003-2019 Business Tech SARL
*}
{if !empty($sMCNewsletterForm)}
	{$sMCNewsletterForm nofilter}
	{if !empty($sModNLSelector)}
		<script type="text/javascript">
			bt_sHideNewsletterForm = '{$sModNLSelector}';
		</script>
	{/if}
{/if}

{* USE CASE - MC External JS integration for remarketing *}
{if !empty($sMailChimpExternalJS)}
	<!-- Begin - MailChimp E-Commerce Automation - includes the MC JS library -->
	{$sMailChimpExternalJS nofilter}
	<!-- End - MailChimp E-Commerce Automation - includes the MC JS library -->
{/if}
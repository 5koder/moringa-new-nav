{*
*
* Mailchimp Pro - Newsletter sync and eCommerce Automation
*
* @author BusinessTech.fr
* @copyright Business Tech
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}

<div class="autoscroll">
	<div id="mce" class="bootstrap autoscroll">

		{if !empty($aErrors)}
			{include file="`$sErrorInclude`" aErrors=$aErrors}
			<div class="clr_10"></div>
		{else}
			<form class="form-horizontal col-xs-12 col-sm-12 col-md-12 col-lg-12 fancyform" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_data_to_synch_form" name="bt_data_to_synch_form">
				<input type="hidden" name="sAction" value="{$aQueryParams.dataToSynch.action|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sType" value="{$aQueryParams.dataToSynch.type|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" id="bt_list_id" name="bt_list_id" size="45" value="{$sListId|escape:'htmlall':'UTF-8'}"  />
				<input type="hidden" id="bt_store_id" name="bt_store_id" size="45" value="{if !empty($sStoreId)}{$sStoreId|escape:'htmlall':'UTF-8'}{/if}" />

				<div class="clr_10"></div>

				<h2>{l s='Preview data to synch' mod='btmailchimpecommerce'}</h2>
				<div class="clr_hr"></div>
				<div class="clr_30"></div>

				<h3>{l s='Requested details' mod='btmailchimpecommerce'}</h3>
				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Your list name' mod='btmailchimpecommerce'}</strong> :
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<input type="text" id="bt_list_name" name="bt_list_name" size="45" value="{$sListName|escape:'htmlall':'UTF-8'}" class="disabled"  />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Data type' mod='btmailchimpecommerce'}</strong> :
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<input type="text" id="bt_data_type" name="bt_data_type" size="45" value="{$sDataType|escape:'htmlall':'UTF-8'}" class="disabled" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<strong>{l s='Element' mod='btmailchimpecommerce'}</strong> :
					</label>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<input type="text" id="bt_elt_id" name="bt_elt_id" size="45" value="{$sEltId|escape:'htmlall':'UTF-8'}" class="disabled"  />
					</div>
				</div>

				<input type="hidden" id="bt_language_id" name="bt_language_id" size="45" value="{$iLangId|intval}" class="disabled" />
				{if !empty($sLangName) && $sDataType == 'product' || $sDataType == 'variant'}
					<div class="form-group">
						<label class="control-label col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<strong>{l s='Language' mod='btmailchimpecommerce'}</strong> :
						</label>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<input type="text" id="bt_data_language" name="bt_data_language" size="45" value="{$sLangName|escape:'htmlall':'UTF-8'}" class="disabled" />
						</div>
					</div>
				{/if}

				<h3>{l s='You can check the data before synching them' mod='btmailchimpecommerce'}</h3>

				<div class="clr_hr"></div>
				<div class="clr_10"></div>

				<div class="form-group">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						{if !empty($aDataToSync)}
							<div class="alert alert-info">
								{foreach from=$aDataToSync key=sKey item=mElt}
									{if is_array($mElt)}
										<b>{$sKey|escape:'htmlall':'UTF-8'}</b>:
										<div class="clr_5"></div>
										{foreach from=$mElt key=sSubKey item=mSubElt}
											{if is_array($mSubElt)}
												{foreach from=$mSubElt key=sSubSubKey item=mSubSubElt}
													{if !is_array($mSubSubElt)}
														&nbsp;&nbsp;&nbsp;<b>{$sSubSubKey|escape:'htmlall':'UTF-8'}</b>: {$mSubSubElt|escape:'htmlall':'UTF-8'}
														<div class="clr_5"></div>
													{/if}
												{/foreach}
											{else}
												&nbsp;&nbsp;<b>{$sSubKey|escape:'htmlall':'UTF-8'}</b>: {$mSubElt|escape:'htmlall':'UTF-8'}
												<div class="clr_5"></div>
											{/if}
										{/foreach}
									{else}
										<b>{$sKey|escape:'htmlall':'UTF-8'}</b>: {$mElt|escape:'htmlall':'UTF-8'}
										<div class="clr_5"></div>
									{/if}
								{/foreach}
							</div>
						{else}
							<div class="alert alert-danger">
								{l s='Something went wrong, the data you want to synchronize not appear valid as well as nothing was formatted right.' mod='btmailchimpecommerce'}
							</div>
						{/if}
					</div>
				</div>

				<div class="clr_20"></div>
				<div class="clr_hr"></div>
				<div class="clr_20"></div>

				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
						<div id="bt_error_synch_data"></div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1"><button class="btn btn-success btn-lg pull-right" onclick="oMailchimp.form('bt_data_to_synch_form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_data_to_synch_form', 'bt_data_to_synch_form', false, false, null, 'synch_data', 'synch_data');return false;"><i class="icon-refresh"></i>&nbsp;{l s='Sync it' mod='btmailchimpecommerce'}</button></div>
				</div>
			</form>

			<div class="clr_10"></div>

			<div id="bt_loading_div_synch_data" style="display: none;">
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sLoader|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='Your current data to synch are in progress' mod='btmailchimpecommerce'}</p>
				</div>
			</div>
		{/if}
	</div>
</div>
{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{block name='product_list_subcategories'}
    {if isset($subcategories)}
        <!-- Subcategories -->
        <div class="product-list-subcategories {if $iqitTheme.cat_hide_mobile} hidden-sm-down{/if}">
            <div class="row">
                {foreach from=$subcategories item=subcategory}
                    <div class="col-{$iqitTheme.cat_sub_thumbs_p} col-md-{$iqitTheme.cat_sub_thumbs_t} col-lg-{$iqitTheme.cat_sub_thumbs_d}">
                        <a href="{$subcategory.url}">
                            <div class="subcategory-image">
                                <img src="{$urls.img_cat_url}{$subcategory.id_category}-small_default.jpg" alt="{$subcategory.name}" width="{$subcategory.image.bySize.medium_default.width}"
                                     height="{$subcategory.image.bySize.medium_default.height}" class="img-fluid"/>
                            </div>
                        </a>
                        <a class="subcategory-name" href="{$subcategory.url}">{$subcategory.name}</a>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
    {*custom*}
    {if $category.id == 2}
        <h2 class="shop_best_sellers"><span>Best Sellers</span></h2>
    {else}
    <a href="/shop"><button class="btn btn-primary btn-md">< Back to Shop</button></a>
    {/if}
{/block}

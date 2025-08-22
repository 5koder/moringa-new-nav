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
{extends file='page.tpl'}

{block name='page_title'}
  {$cms.meta_title}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-cms page-cms-{$cms.id}">

    {block name='hook_cms_dispute_information'}
      {hook h='displayCMSDisputeInformation'}
    {/block}

    {block name='hook_cms_print_button'}
      {hook h='displayCMSPrintButton'}
    {/block}
    
    {if $cms.id==13}
    {literal}    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
          var quoteElement = document.querySelector('.quote_scroll');
          if (quoteElement) {
            var links = document.querySelectorAll('.cms-id-13 .elementor-button-link');
            links.forEach(function(link) {
              link.addEventListener('click', function(event) {
                event.preventDefault();
                var offset = 140;
                var elementPosition = quoteElement.offsetTop;
                var offsetPosition = elementPosition - offset;
                document.documentElement.scrollTop = offsetPosition;
                document.body.scrollTop = offsetPosition;
              });
            });
          }
        });
    </script>
    {/literal}

    {*exit intent popup*}
    
    <div class="exit-popup-modal">
    <div class="exit-popup-wrapper">
    <h2 class="heading">Get 10% off Your First Bulk Order!</h2><a href="#" class="exit-popup-close btn-close-custom btn">X</a>
    <p class="paragraph ep-paragraph">Request a quote to receive your voucher code and get 10% off your first bulk order</p>
    <div class="exit-form">
        <a href="#" class="elementor-button-link elementor-button btn elementor-size-medium btn-secondary btn-traditional exit-popup-close">
            <span class="elementor-button-content-wrapper">
                <span class="elementor-button-text">Request a quote and get 10% off</span>
            </span>
        </a>
    </div>
    </div>
    </div>
    {/if}

  </section>
{/block}

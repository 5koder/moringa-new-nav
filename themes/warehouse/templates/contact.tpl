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


{block name='page_header_container'}
  <header class="page-header">
    <h1 class="h1 page-title"><span>{l s='Contact us' d='Shop.Theme.Global'}</span></h1>
  </header>
  {widget name="iqitcontactpage" hook='displayContactMap'}
{/block}

{block name='page_content'}
  <div class="row">
    <div class="col-sm-4 contact-page-info">
        <div class="contact-rich">
            <strong>Moringa World</strong>
            {*
            <div class="part">
                <div class="icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                <div class="data">Unit 4, Gallagher Place North<br>
                    Corner of Richards Drive and Suttie Avenue<br>
                    Halfway House<br>
                    Midrand, 1685</div>
            </div>
            <hr>
            *}
            <div class="part">
                <div class="icon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                <div class="data">
                    <a href="tel:+27 11 568 7136">+27 11 568 7136</a>
                </div>
            </div>
            <hr>
            <div class="part">
                <div class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                <div class="data email">
                    <a href="mailto:sales@moringaworld.co.za">sales@moringaworld.co.za</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="contact-rich">
            <strong>Enquiry Form</strong>
        </div>
        {hook h='displayGform' id='2'}
    </div>
    {*
    <div class="col-sm-12 contact_map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14343.239948727267!2d28.1276093!3d-26.0070411!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xf2c51ad0ac239dbe!2sMoringa%20World!5e0!3m2!1sen!2sza!4v1624668696855!5m2!1sen!2sza" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
    *}
{/block}



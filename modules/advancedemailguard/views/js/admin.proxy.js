/**
 * Advanced Anti Spam Google reCAPTCHA PrestaShop Module.
 *
 * @author      ReduxWeb <contact@reduxweb.net>
 * @copyright   2017-2021
 * @license     LICENSE.txt
*/

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    $('body').css('overflow', 'hidden');
    var $iframe = $('#reduxWebAdmin');
    var adjustHeight = function () {
        $iframe.css('height', Math.abs($iframe.offset().top - $(window).height()));
    };
    adjustHeight();
    $(window).resize(adjustHeight);
});
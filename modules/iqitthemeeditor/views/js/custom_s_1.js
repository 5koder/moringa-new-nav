 //move links to dektop menu
$('#_desktop_iqitmegamenu-mobile ul:first-child').appendTo("#append_links");

//replace menu text
$("#append_links > ul > li:nth-of-type(2) > a").text( "Shop" );

//change product category menu url
var _href = $("#append_links > ul > li:nth-of-type(2) > a").attr("href");
$("#append_links > ul > li:nth-of-type(2) > a").attr("href", _href + 'shop');

// menu close btn toggle
$(function() {
  $('#desktop_close_menu').on('click', function() {
    $('#_mobile_iqitmegamenu-mobile, .col-mobile-menu-push').removeClass('show');

    $('.col-mobile-menu-push a:first-child').attr('aria-expanded', 'false');

  });
});

// add header scroll animation
$(document).ready(function(){
  var scrollTop = 0;
  $(window).scroll(function(){
    scrollTop = $(window).scrollTop();
     $('.counter').html(scrollTop);
    
    if (scrollTop >= 100) {
      $('#desktop-header').addClass('scrolled-nav');
    } else if (scrollTop < 100) {
      $('#desktop-header').removeClass('scrolled-nav');
    } 
    
  }); 
  
});
// check newsletter for customer registration
if($('#authentication #customer-form #ff_newsletter').prop('checked') == false){
    $('#authentication #customer-form #ff_newsletter').prop('checked', true); 
}
// add datepicker to reg form
$('#customer-form [name=birthday]').datepicker({ 
    changeMonth: true,
    changeYear: true,
    yearRange: '-120:+0',
    dateFormat: 'dd/mm/yy'
});

// copyright year
$('#spanYear').html(new Date().getFullYear());

// append kg to inputs
$('.bulk_input').parent().append('Kg');
// toggle kg append
$('.input-group-append').hide();
// restrict input to numeric
$('.bulk_input').keyup(function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
});

// toggle bulk form inputs
$('#input_70717').hide();

//show it when the checkbox is clicked
$('#checkbox_checkbox_44317_0').on('click', function() {
  if ($(this).prop('checked')) {
    $('#input_70717').show();
    $('#input_70717').next('.input-group-append').show();
    $('#input_70717').prop('required', true);
  } else {
    $('#input_70717').hide();
    $('#input_70717').next('.input-group-append').hide();
    $('#input_70717').prop('required', false);    
  }
});

$('#input_22661').hide();
//show it when the checkbox is clicked
$('#checkbox_checkbox_77543_0').on('click', function() {
  if ($(this).prop('checked')) {
    $('#input_22661').show();
    $('#input_22661').next('.input-group-append').show();
    $('#input_22661').prop('required', true);
  } else {
    $('#input_22661').hide();
	$('#input_22661').next('.input-group-append').hide();
    $('#input_22661').prop('required', false);
  }
});

$('#input_22270').hide();

//show it when the checkbox is clicked
$('#checkbox_checkbox_80782_0').on('click', function() {
  if ($(this).prop('checked')) {
    $('#input_22270').show();
	$('#input_22270').next('.input-group-append').show();
    $('#input_22270').prop('required', true);
  } else {
    $('#input_22270').hide();
	$('#input_22270').next('.input-group-append').hide();
    $('#input_22270').prop('required', false);
  }
});

// add req class
$('#gformbuilderpro_27 label[for="checkbox_44317"]').addClass('required_label');

//scroll to quote form on bulk page
var quoteElement = document.querySelector('.elementor-element-5pqwmd2');
if (quoteElement) {
  document.querySelectorAll('.cms-id-13 .elementor-button-link').forEach(link => {
    link.addEventListener('click', event => {
      event.preventDefault();
      var offset = 90;
      var elementPosition = quoteElement.offsetTop;
      var offsetPosition = elementPosition - offset;
      document.documentElement.scrollTop = offsetPosition;
      document.body.scrollTop = offsetPosition;
    });
  });
}
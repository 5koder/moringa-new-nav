/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

/*cookie function - Set and read cookie */
  function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

/* Detecting mouse out event */
  document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("mouseout", (event) => {
    if (!event.toElement && !event.relatedTarget) {
      setTimeout(() => {
        var ifcookie = readCookie("exitpopup");
    	if(ifcookie !="hide"){
      		$('.exit-popup-modal').fadeIn();
    	}
      }, 100);
    }
  });
});
/* Closing the popup and setting up cookie so it won't show it again.*/
$('.exit-popup-close').on('click', function(){
  createCookie("exitpopup","hide","1"); //You can set any number of days here.
   $('.exit-popup-modal').fadeOut();
});

var submitFormElements = document.querySelectorAll('#submitForm');
if (submitFormElements.length > 0) {
  submitFormElements.forEach(element => {
    element.classList.add('exit-popup-close');
  });
}
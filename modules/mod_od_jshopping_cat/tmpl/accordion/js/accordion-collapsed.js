/* 
   Simple JQuery Collapsing menu.
   HTML structure to use:

Copyright 2007 by Marco van Hylckama Vlieg

web: http://www.i-marco.nl/weblog/
email: marco@i-marco.nl

Free for non-commercial use
*/
  
function initMenu() {
  jQuery(".vmenu li.parent > a").click(function(eventObject){return false});
  jQuery('.vmenu ul').hide();
  jQuery('.vmenu li.active ul').show();
  jQuery('.vmenu li a').click(
    function() {
        jQuery(this).next().slideToggle('normal');	
      }
    );
  }
jQuery(document).ready(function() {initMenu();});

/*------------------------------------
* Product Video Extention
* Author  CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support: http://cmsmart.net/support_ticket/
* Forum - http://cmsmart.net/forum
-----------------------------------------------------*/

/* AJAX Star Rating : v1.0.3 : 2008/05/06 */
/* http://www.nofunc.com/AJAX_Star_Rating/ */
define([
    "jquery",
    "jquery/ui"
], function($) {
	$(document).ready(function() {
 		// $('.ratings_stars').hover(
   //          function() { 
   //              $(this).prevAll().andSelf().addClass('ratings_over');
   //              $(this).nextAll().removeClass('ratings_vote');  
   //          },
   //          function() { 
   //              $(this).prevAll().andSelf().removeClass('ratings_over'); 
   //          } 
   //      );
        $('.ratings_stars').mouseover(
            function() { 
                $(this).prevAll().andSelf().addClass('ratings_over');
                $(this).nextAll().removeClass('ratings_over');  
                var id=$(this).parent().attr("id");
                var num=$(this).attr("id");
                $('#total_votes').val(num);

        });
    });
});
/*Init Accordion*/
(function ($) {
/* Venustheme Frontend Common Js */
var accordian = {
    init: function(element_id){

        var $current_obj = jQuery(element_id),
                rotate_in =       $current_obj.data('rotate-in'),
                rotate_in_speed =       $current_obj.data('rotate-in-speed'),
                rotate_out =       $current_obj.data('rotate-out'),
                rotate_out_speed =       $current_obj.data('rotate-out-speed'),
                bg_in_y =       $current_obj.data('bg-in-y'),
                bg_in_opacity =       $current_obj.data('bg-in-opacity'),
                bg_in_scale =       $current_obj.data('bg-in-scale'),
                bg_in_speed =       $current_obj.data('bg-in-speed'),
                bg_out_y =       $current_obj.data('bg-out-y'),
                bg_out_opacity =       $current_obj.data('bg-out-opacity'),
                bg_out_scale =       $current_obj.data('bg-out-scale'),
                bg_out_speed =       $current_obj.data('bg-out-speed');

       
        if(rotate_in === "" || typeof(rotate_in) == "undefined") {
            rotate_in = "0deg";
        } 
        if(rotate_in_speed === "" || typeof(rotate_in_speed) == "undefined") {
            rotate_in_speed = 350;
        } 
        if(rotate_out === "" || typeof(rotate_out) == "undefined") {
            rotate_out = "135deg";
        } 
        if(rotate_out_speed === "" || typeof(rotate_out_speed) == "undefined") {
            rotate_out_speed = 350;
        } 
        if(bg_in_y === "" || typeof(bg_in_y) == "undefined") {
            bg_in_y = 0;
        } 
        if(bg_in_scale === "" || typeof(bg_in_scale) == "undefined") {
            bg_in_scale = 1;
        } 
        if(bg_in_speed === "" || typeof(bg_in_speed) == "undefined") {
            bg_in_speed = 800;
        }
        if(bg_out_y === "" || typeof(bg_out_y) == "undefined") {
            bg_out_y = -80;
        }
        if(bg_out_opacity === "" || typeof(bg_out_opacity) == "undefined") {
            bg_out_opacity = .75;
        }
        if(bg_out_scale === "" || typeof(bg_out_scale) == "undefined") {
            bg_out_scale = 1.2;
        } 
        if(bg_out_speed === "" || typeof(bg_out_speed) == "undefined") {
            bg_out_speed = 800;
        }  
        accordian.max_height(element_id);

        jQuery(element_id).on('click', '.menu-title', function(){

            if ( !jQuery(element_id).hasClass('press') ){

                var $this =         jQuery(this),
                    menuCat =       $this.data('menu'),
                    bg_effect = $this.data('bg-effect'),
                    $foodItems =    jQuery('.panel-item-content').filter('[data-menu="'+ menuCat +'"]');
                
                if(bg_effect === "" || typeof(bg_effect) == "undefined") {
                    bg_effect = 1;
                }

                if ( $this.hasClass('open') ){
    
                    // Animate The Things
                    $this.find('i').transition({ rotate: rotate_in }, rotate_in_speed);
                    if(bg_effect) {
                        $this.find('.bg').transition({y: bg_in_y, opacity: bg_in_opacity, scale: bg_in_scale }, bg_in_speed, 'ease');
                    }
                    // Handle classes
                    setTimeout(function(){
                        $this.removeClass('open').addClass('closed');
                    }, 50);
    
                    // Hide the Menu
                    $foodItems.transition({ height: 0, complete: function(){
                        $foodItems.attr({ 'style' : '' });
                    } }, 400);
    
                    
                    
                } else {
                    
                    // $j('section#accordian .open').not(this).trigger('click');
                    // Hacky get height
                    $foodItems.show();
                    var foodItems_height = $foodItems.height();
                    $foodItems.hide();
                    var speed = foodItems_height * 1.05;
                    
                    // Animate The Things

                    $this.find('i').transition({ rotate: rotate_out }, rotate_out_speed);
                    if(bg_effect) {
                        $this.find('.bg').transition({ y: bg_out_y, opacity: bg_out_opacity, scale: bg_out_scale }, bg_out_speed, 'ease');
                    }
                    // Handle classes
                    $this.addClass('open').removeClass('closed');
                    
                    // Show the Menu
                    $foodItems.show().height(0)
                        .css({ 'opacity' : 0 })
                        .transition({ opacity: 1, height: foodItems_height }, speed);
                    
                }
            } // End if isn't press
            
        });
        
        // Hovers for Menu
        
            jQuery(element_id + ' .menu-title').on({
                mouseenter: function(e){
                    
                        var $this = jQuery(this),
                        bg_effect = $this.data('bg-effect'),
                        in_press_y =       $this.data('in-press-y'),
                        in_press_opacity =       $this.data('in-press-opacity'),
                        in_press_speed =       $this.data('in-press-speed');

                        in_y =       $this.data('in-y'),
                        in_opacity =       $this.data('in-opacity'),
                        in_speed =       $this.data('in-speed');

                        if(bg_effect === "" || typeof(bg_effect) == "undefined") {
                            bg_effect = 1;
                        }
                        if(in_press_y == "" || typeof(in_press_y) == "undefined") {
                            in_press_y = -80;
                        }

                        if(in_press_opacity == "" || typeof(in_press_opacity) == "undefined") {
                            in_press_opacity = .2;
                        }

                        if(in_press_speed == "" || typeof(in_press_speed) == "undefined") {
                            in_press_speed = 1200;
                        }

                        if(in_y == "" || typeof(in_y) == "undefined") {
                            in_y = -80;
                        }

                        if(in_opacity == "" || typeof(in_opacity) == "undefined") {
                            in_opacity = .75;
                        }

                        if(in_speed == "" || typeof(in_speed) == "undefined") {
                            in_speed = 1200;
                        }

                        if(bg_effect) {
                            if ( jQuery(element_id).hasClass('press') ){
                                if ( $this.hasClass('closed') ){
                                    $this.find('.bg').stop().transition({ y: in_press_y, opacity: in_press_opacity }, in_press_speed, 'ease');
                                }                   
                            } else {
                                if ( $this.hasClass('closed') ){
                                    $this.find('.bg').stop().transition({ y: in_y, opacity: in_opacity }, in_speed, 'ease');
                                }                   
                            }
                        }
                },
                mouseleave: function(e){
                    var $this = jQuery(this),
                    bg_effect = $this.data('bg-effect'),
                    leave_y =       $this.data('out-y'),
                    leave_opacity =       $this.data('out-opacity'),
                    leave_speed =       $this.data('out-speed');

                    if(bg_effect === "" || typeof(bg_effect) == "undefined") {
                        bg_effect = 1;
                    }

                    if(leave_y == "" || typeof(leave_y) == "undefined") {
                        leave_y = 0;
                    }
                    if(leave_opacity == "" || typeof(leave_opacity) == "undefined") {
                        leave_opacity = 0;
                    }
                    if(leave_speed == "" || typeof(leave_speed) == "undefined") {
                        leave_speed = 600;
                    }

                    if ( bg_effect && $this.hasClass('closed') ){
                        $this.find('.bg').stop().transition({ y: leave_y, opacity: leave_opacity }, leave_speed, 'ease');
                    }
                }
            });
        
    },
    max_height: function(element_id){
        jQuery(element_id + ' .panel-item-content').show();
        
        jQuery(element_id + ' .panel-item-content').hide();
    }
}

/** 
 * 
 * Automatic apply accordian
 */
if($(".accordian-play").length > 0) {
    $(".accordian-play").each( function(){
        var element_id = $(this).attr("id");
        accordian.init("#" + element_id);
    });
}
}(jQuery));
define([
    'jquery',
    'Ves_All/lib/owl.carousel/owl.carousel.min'
    ], function($){
        'use strict'

        $.widget('ves.testimonial', {
    tmlsOwlCarousel:function (eOwlData, eOwlCarousel) {
                var config = [];
                if(typeof(jQuery(eOwlData).data('nav'))!='underfined'){
                    config['nav'] = jQuery(eOwlData).data('nav');
                }
                if(typeof(jQuery(eOwlData).data('dots'))!='underfined'){
                    config['dots'] = jQuery(eOwlData).data('dots');
                }
                if(typeof(jQuery(eOwlData).data('autoplay'))!='underfined'){
                    config['autoplay'] = jQuery(eOwlData).data('autoplay');
                }
                if(jQuery(eOwlData).data('autoplay-timeout')){
                    config['autoplayTimeout'] = jQuery(eOwlData).data('autoplay-timeout');
                }
                if(typeof(jQuery(eOwlData).data('autoplay-hover-pause'))!='underfined'){
                    config['autoplayHoverPause'] = jQuery(eOwlData).data('autoplay-hover-pause');
                }
                if(typeof(jQuery(eOwlData).data('rtl'))!='underfined'){
                    config['rtl'] = jQuery(eOwlData).data('rtl');
                }
                if(typeof(jQuery(eOwlData).data('loop'))!='underfined'){
                    config['loop'] = jQuery(eOwlData).data('loop');
                }
                config['navText'] = [ 'prev', 'next' ];
                if(jQuery(eOwlData).data("nav-text-owlpre")){
                    config['navText'] = [ jQuery(eOwlData).data("nav-text-owlpre"), 'next' ];
                }
                if(jQuery(eOwlData).data("nav-text-owlnext")){
                    config['navText'] = [ 'pre', jQuery(eOwlData).data("nav-text-owlnext") ];
                }
                if(jQuery(eOwlData).data("nav-text-owlpre") && jQuery(eOwlData).data("nav-text-owlnext")){
                    config['navText'] = [ jQuery(eOwlData).data("nav-text-owlpre"), jQuery(eOwlData).data("nav-text-owlnext") ];   
                }
                var mobile_items = 1;
                if(jQuery(eOwlData).data('mobile-items')){
                    mobile_items = jQuery(eOwlData).data('mobile-items');
                }
                var tablet_small_items = 2;
                if(jQuery(eOwlData).data('tablet-small-items')){
                    tablet_small_items = jQuery(eOwlData).data('tablet-small-items');
                }
                var tablet_items = 2;
                if(jQuery(eOwlData).data('tablet-items')){
                    tablet_items = jQuery(eOwlData).data('tablet-items');
                }
                var portrait_items = 3;
                if(jQuery(eOwlData).data('portrait-items')){
                    portrait_items = jQuery(eOwlData).data('portrait-items');
                }
                var large_items = 3;
                if(jQuery(eOwlData).data('large-items')){
                    large_items = jQuery(eOwlData).data('large-items');
                }
                var large_max_items = 3;
                if(jQuery(eOwlData).data('large-max-items')){
                    large_max_items = jQuery(eOwlData).data('large-max-items');
                }
                config['responsive'] = {
                    0 : {items: mobile_items},
                    480 : {items: tablet_small_items},
                    640 : {items: tablet_items},
                    768 : {items: portrait_items},
                    980 : {items: large_items},
                    1200 : {items: large_max_items}
                };
                jQuery(eOwlData).find(eOwlCarousel).owlCarousel(config);
            },

        tmlsSliderPlay:function (tmls_slider) {
                tmls_slider.carouFredSel({
                    responsive: true,
                    width:'variable',
                    height:'variable',
                    prev: {
                        button: function() {
                            return jQuery(this).parents().children(".tmls_next_prev").children(".tmls_prev");
                        }
                    },
                    next: {
                        button: function() {
                            return jQuery(this).parents().children(".tmls_next_prev").children(".tmls_next");
                        }
                    },
                    pagination: {
                        container: function() {
                            return jQuery(this).parents('.tab-content').find('.tmls-paginationContainer');
                        },
                        anchorBuilder   : function(nr) {
                            return "<div class='tmls-image-container'><div class='tmls-image'> <img src='" + jQuery(this).data('bgimg') +"' </div><div class='tmls-image-overlay' style='background-color:#FFF'></div></div>";
                        }
                    },
                    scroll: {
                        items:1,          
                        duration: tmls_slider.data('scrollduration'),
                        fx: tmls_slider.data('transitioneffect')
                    },
                    auto: {
                        play: tmls_slider.data('autoplay'),
                        timeoutDuration:tmls_slider.data('pauseduration'),
                        pauseOnHover:tmls_slider.data('pauseonhover')
                    },
                    items: {
                        width:700
                    },
                    swipe: {
                        onMouse: false,
                        onTouch: true
                    }

                });
            }
       });
    return $.ves.testimonial;
})
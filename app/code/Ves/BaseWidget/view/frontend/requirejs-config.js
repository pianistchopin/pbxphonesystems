/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		"*": {
 			vesbaseOwlCarousel: "Ves_BaseWidget/js/owl/owl.carousel.min",
 			vesbaseBootstrap: "Ves_BaseWidget/js/bootstrap336/bootstrap.min",
 			vesAccordion: "Ves_BaseWidget/js/colorpicker/js/colorpicker",
 			vesbaseSwiper: "Ves_BaseWidget/js/swiper/swiper.min",
 			vesbaseSwiperCommon: "Ves_BaseWidget/js/swiper/common",
 			vesbaseTransition: "Ves_BaseWidget/js/jquery/jquery.transition",
 			vesbaseCountDown: "Ves_BaseWidget/js/countdown",
 			vesbaseCookie: "Ves_BaseWidget/js/jquery/jquery.cookie",
 			vesbaseColorbox: "Ves_BaseWidget/js/colorbox/jquery.colorbox.min",
 			vesbaseFancybox: "Ves_BaseWidget/js/fancybox/jquery.fancybox.pack",
 			vesbaseFancyboxMouseWheel: "Ves_BaseWidget/js/fancybox/jquery.mousewheel-3.0.6.pack",
 			vesbaseAnimate: "Ves_BaseWidget/js/animate/animate.min",
 			vesbaseHolder: "Ves_BaseWidget/js/jquery/holder.min",
 			vesParallax: "Ves_BaseWidget/js/jquery/jquery.parallax-1.1.3",
 			vesAccordion: "Ves_BaseWidget/js/jquery/accordion",
 			vesElevateZoom: "Ves_BaseWidget/js/jquery/jquery.elevateZoom-3.0.8.min"
 		}
 	},
 	shim: {
        'Ves_BaseWidget/js/swiper/swiper.min': {
            'deps': ['jquery']
        },
        'Ves_BaseWidget/js/bootstrap336/bootstrap.min': {
            'deps': ['jquery']
        },
        'Ves_BaseWidget/js/swiper/common': {
            'deps': ['jquery']
        },
        'Ves_BaseWidget/js/jquery/jquery.transition': {
            'deps': ['jquery']
        },
        'Ves_BaseWidget/js/countdown': {
        	'deps': ['jquery']
        },
        'Ves_BaseWidget/js/jquery/jquery.cookie': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/colorpicker/js/colorpicker': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/animate/animate.min': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/jquery/holder.min': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/jquery/jquery.parallax-1.1.3': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/jquery/accordion': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/colorbox/jquery.colorbox.min': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/fancybox/jquery.fancybox.pack': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/jquery/jquery.elevateZoom-3.0.8.min': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/fancybox/jquery.mousewheel-3.0.6.pack': {
            'deps': ['jquery']  
        },
        'Ves_BaseWidget/js/owl/owl.carousel.min': {
            'deps': ['jquery']  
        }
    }
 };
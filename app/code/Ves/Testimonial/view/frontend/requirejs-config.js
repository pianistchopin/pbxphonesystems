/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
	map: {
		"*": {
			testimonial_fancybox: 'Ves_Testimonial/js/jquery.fancybox.pack',
			rating : 'Ves_Testimonial/js/rating',
			testimonial_rating : 'Ves_Testimonial/js/testimonial_rating',
			testimonial_carousel : 'Ves_Testimonial/js/testimonial-carousel',
			testimonialtowlcarousel: "Ves_Testimonial/js/owl.carousel.min"
		}
	},
	shim: {
        'Ves_Testimonial/js/testimonial-carousel': {
            'deps': ['jquery']
        },
        'Ves_Testimonial/js/owl.carousel.min': {
            'deps': ['jquery']
        }
    }
};
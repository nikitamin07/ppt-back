var mailbox_ckeckbox = false;

$(document).ready(() => {
	var mapLoaded = false;
	$(document).on('click', '.btn-callback', function(event) {
		initCaptchaScript();
	});
	$(document).on('click', '.btn-buy-1click', function(event) {
		initCaptchaScript();
	});
	var tabs_slider_optins = {
		slidesToShow: 6,
		slidesToScroll: 1,
		centerMode: false,
		lazyLoad: 'ondemand',
		infinite:  false,
		prevArrow: '<button class="slick-prev tabs-slick-arrow"><i class="fas fa-chevron-left"></i></button>',
		nextArrow: '<button class="slick-next tabs-slick-arrow"><i class="fas fa-chevron-right"></i></button>',
		responsive: [
		{
		  breakpoint: 1024,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 1,			
		  }
		},
		{
		  breakpoint: 767,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 1
		  }
		}		
	  ]
	};

	$('.main-slider').on('init', function(s){
		$('.slick-active').addClass('anim');	
	});
	
	$('.main-slider').slick({
	  slidesToShow: 1,
	  slidesToScroll: 1,
	  arrows: true,
	  dots: true,
	  lazyLoad: 'ondemand',
	  prevArrow: '<button class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
	  nextArrow: '<button class="slick-next"><i class="fas fa-chevron-right"></i></button>',
	  autoplay: true
	 
	//  autoplaySpeed: 3000,
	}).on('afterChange', function(event, slick, currentSlide, nextSlide){
		$('.main-slider .anim').removeClass('anim');
	    $('.main-slider .slick-active').addClass('anim');
	});
	
	window.addEventListener('scroll', function() {
		if(pageYOffset > 100){
			$('.f_up').addClass('show');
			if (mapLoaded == false){
				$('.footer-map-yandex').html('<iframe title="ППТ.бел на карте yandex" src="https://yandex.by/map-widget/v1/-/CCU74CcJ1C" allowfullscreen style="position:relative; border-width: 0; width: 100%; height: 100%;"></iframe>');
				mapLoaded = true;
			}
		}else{
			$('.f_up').removeClass('show');
		}
	});
	
	
	$('.f_up').on('click', function(e){
		e.preventDefault();
		$('html, body').animate({
			scrollTop: 0
		}, 600);
	});
	
	$('.js-products-slider').slick(tabs_slider_optins);
	
	
	$('.tab-item.active').find('.tab-products-slider').slick(tabs_slider_optins);

	
	$(document).on('click', '.menu-mobile-switcher', function(e){
		let self = $(this);
		self.toggleClass('active');
		self.siblings('.menu-mobile-box').toggleClass('active').slideToggle(300);
	});
	
	$(document).on('click', '.menu-mobile-box .havesub', function(e){
        e.stopPropagation();
		let self = $(e.target);
        let el;        
        if ( self.parent().hasClass('item-link') ) {
            el = self.parents('.havesub');
            if (!el.hasClass('active') && $('.menu-item.havesub.active').length > 0) {
                $('.menu-item.havesub.active').find('.submenu').stop().slideToggle(300);
                $('.menu-item.havesub.active').removeClass('active');
            }            
            el.toggleClass('active');
            el.find('.submenu').stop().slideToggle(300);
            return false;
        }        
	});
		
	$(document).on('mouseover', '.tab-slider-item', function(e){
		$(this).addClass('hover');
		$(this).parents('.tab-products-slider').addClass('hover');
	});
	
	$(document).on('mouseout', '.tab-slider-item', function(e){
		$(this).removeClass('hover');
		$(this).parents('.tab-products-slider').removeClass('hover');
	});
	
	$(document).on('click', '.tabs-title-box span', function(e){		
		if($(this).hasClass('active')){
			return false;
		}		
		let t = $(this).attr('data-tab-toggle');
		$('.tabs-title-box .active').toggleClass('active');
		$(this).toggleClass('active');
		$('.tab-item[id="'+t+'"]').parent().find('.active').fadeOut(300, () => {
			$('.tab-item[id="'+t+'"]').parent().find('.active').removeClass('in active');
		});		
		$('.tab-item[id="'+t+'"]').fadeIn(300, () => {
			$('.tab-item[id="'+t+'"]').addClass('active in');
		});	
		if(!$('.tab-item[id="'+t+'"]').find('.tab-products-slider').hasClass('slick-initialized')){
			$('.tab-item[id="'+t+'"]').find('.tab-products-slider').slick(tabs_slider_optins);
		}else{
			$('.tab-item[id="'+t+'"]').find('.tab-products-slider').slick('refresh');
		}
	});
	
	
	
	if($('.bxe-list').length){
		$('.bxe-list li').prepend('<i class="fa fa-check"></i>');
	}
	
	$(document).on("change keyup input click", '.js-qty-input' , function() {
		if (this.value.match(/[^0-9]/g)) this.value = this.value.replace(/[^0-9]/g, '');
        if (Number(this.value) == 0 || this.value == '') this.value = 1;
	});
	
	
	$(document).click( function(e){
		Click_checker(e);
	});
	
    
    $(document).on('click', (e) => {
        if ( $('.header-col-user.hover').length > 0 ) {
            $('.header-col-user.hover').removeClass('hover');
        } 
    });
	
    
    $(window).on('resize', () => {     
        CatalogAutoHeight();
        if ( $('.header-col-user.hover').length > 0 ) {
            $('.header-col-user.hover').removeClass('hover');
        } 
    });
    CatalogAutoHeight();
});

function initCaptchaScript(e){
	loadRecaptchaScript();
	$('.recaptcha-container').html('<div class="g-recaptcha" data-sitekey="6LecDFYkAAAAAGEnxkDS61l9NUN-iAD-SabVzFos" data-callback="onSuccessCaptcha"></div>');
}
function loadRecaptchaScript() {
    var script = document.createElement('script');
	script.id = 'script-recaptcha';
    script.src = 'https://www.google.com/recaptcha/api.js';
    script.async = true;
	script.defer = true;
    document.head.appendChild(script);
}

function Click_checker(e){
	let t = e.target;
	if($(t).closest('.menu-mobile').length){
		return;
	}
	if($('.menu-mobile-switcher.active').length){
		$('.menu-mobile-switcher.active').removeClass('active');
		$('.menu-mobile-box.active').removeClass('active').slideToggle(300);
	}
}

function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function CatalogAutoHeight(){    
    if ($('.catalog-products-grid').length > 0 && $('.catalog-products-item').length > 0) {
        if (window.innerWidth > 567) {
            let maxHeight = 0;
            let curHeight = 0;
            $('.catalog-products-item').css('height', 'auto');
            $('.catalog-products-item').each((k, v) => {
                curHeight = $(v).innerHeight();
                if (maxHeight < curHeight) {
                    maxHeight = curHeight;                
                }
            });
            $('.catalog-products-item').css('height', `${maxHeight}px`);
        } else {
            $('.catalog-products-item').css('height', `50%`);
        }
    }
}

$('.product-title-label').on('click', function(e){
	if(!$(this).hasClass('active')){
		$('.product-title-label').toggleClass('active');
		$('.product-info-block').toggleClass('hidden-info-block');
	}
});

let o = new IntersectionObserver(
    (e, o) => {
        e.forEach((entry) => {
            if (entry.isIntersecting) {
                let t = entry.target;
                t.src = t.dataset.src;
                t.classList.remove('lazy');                
                o.unobserve(entry.target);
            }
        });
    },
    { rootMargin: '35px', threshold: 0.0015 }
);
Array.from(document.getElementsByClassName('lazy')).forEach((item) => o.observe(item));

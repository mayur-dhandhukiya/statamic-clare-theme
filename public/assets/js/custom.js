/* =====================================
    Template Name: Clare E Commerce
    Author Name: WebbyCrown
    Description: Clare E Commerce - HTML5 Template.
    Version:1.0
========================================*/

/*======================================
[ JS Table of contents ]
Home one js
01. General Open JS
    + Mobile menu
    + Mobile menu dropdown
    + all categories menu js
    + Cookie popup js
    + mini cart popup js
    + Countdown js
    + Plus Minus button js
    + Page scroll
    + Search Bar
 
02. Slider Open JS
    + Hero slider
    + hero slider home 2 slider
    + What we do slider
    + Testimonial slider
03. Tabs Open JS
04. Accordion Open JS
05. Isotope JS
06. All popup JS
07. Preloader JS

========================



========================================*/

(function ($) {
  eCommerce = {
    init: function () {
      this.general_open();
      this.all_slider();
      this.tabs_open();
      this.accordion_open();
      this.Isotope_js();
      this.all_popup_js();
      this.Preloader_js();
      this.Custom_js();
    },

        /*======================================
         01. General Open JS
        ========================================*/

        general_open: function () {

          /* Page scroll to Header sticky */
          $(window).scroll(function() {
            if ($(this).scrollTop() > 0){  
              $('.site-header').addClass("sticky");
            }
            else{
              $('.site-header').removeClass("sticky");
            }
          });

          /* Mobile menu*/
          $("body").on('click', '.header-button .toggle-menu, .mobile-menu-close a', function(){
                $('.mobile-menu').toggleClass('open');
                $(this).toggleClass('active');
                $('body, html').toggleClass('menu-open');
            });

        /* Mobile menu dropdown */
        if( $(window).width() <= 991 ) {
              $(".mobile-menu .menu > li").each(function (i) {
                  if ($(this).has("ul").length)
                  {
                      $(this).find('ul').addClass("sub-menu");
                      $(this).find('> a').after('<span class="caret-arrow"></span>');
                      $(this).find('> .sub-menu').css('display', 'none');
                  }
              });
              $('.mobile-menu .menu li .caret-arrow').click(function () {
                  var catSubUl = $(this).next('.sub-menu');
                  var catSubli = $(this).closest('li');
                  if (catSubUl.is(':hidden'))
                  {
                      //$("#window > ul > li .sub-menu").slideUp();
                      catSubUl.slideDown();
                      //$('.caret').removeClass('active');
                      $(this).addClass('active');
                      catSubli.addClass('active');
                  }
                  else
                  {
                      catSubUl.slideUp();
                      $(this).removeClass('active');
                      catSubli.removeClass('active');
                  }
              });
          }

          //TOGGLING NESTED ul
          $(".drop-down .selected").click(function() {
            $(".drop-down .search-categories").toggle();
          });

          //SELECT OPTIONS AND HIDE OPTION AFTER SELECTION
          $(".drop-down .search-categories li").click(function() {
            var text = $(this).html();
            $(".drop-down .selected span").html(text);
            $(".drop-down .search-categories").hide();
          }); 


          //HIDE OPTIONS IF CLICKED ANYWHERE ELSE ON PAGE
          $(document).bind('click', function(e) {
            var $clicked = $(e.target);
            if (! $clicked.parents().hasClass("drop-down"))
              $(".drop-down .search-categories").hide();
          });

          /* all categories menu js */
          $(document).on("click", ".all-categories .dropdown-toggle", function(){
            $('.departments-menu').slideToggle("");
          });

          /* Cookie popup js */
          $(document).on("click", ".cookie-popup .accept-all-btn", function(){
            $('.cookie-popup').removeClass("open");
          });


          /* mini cart popup js */
          $(document).on("click", ".header-button .cart-icon, .mini-cart-close a", function(){
            $('.mini-cart-dropdown').toggleClass("open");
            $('body').toggleClass("minicart-open");
          });

          /* Filter sidebar popup js */
          $(document).on("click", ".filter-shop-loop .filter-mobile-btn, .sidebar-inner .filter-close", function(){
            $('.sidebar').toggleClass("open");
          });

          
          /* countdown js */
          if ($('.product-countdown').length>0){
            const second = 1000,
                  minute = second * 60,
                  hour = minute * 60,
                  day = hour * 24;

            //I'm adding this section so I don't have to keep updating this pen every year :-)
            //remove this if you don't need it
            let today = new Date(),
                dd = String(today.getDate()).padStart(2, "0"),
                mm = String(today.getMonth() + 1).padStart(2, "0"),
                yyyy = today.getFullYear(),
                nextYear = yyyy + 1,
                dayMonth = "09/30/",
                birthday = dayMonth + yyyy;
            
            today = mm + "/" + dd + "/" + yyyy;
            if (today > birthday) {
              birthday = dayMonth + nextYear;
            }
            //end
            
            const countDown = new Date(birthday).getTime(),
                x = setInterval(function() {    

                  const now = new Date().getTime(),
                        distance = countDown - now;

                  document.getElementById("days").innerText = Math.floor(distance / (day)),
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

                  //do something later when date is reached
                  if (distance < 0) {
                    document.getElementById("headline").innerText = "It's my birthday!";
                    document.getElementById("countdown").style.display = "none";
                    document.getElementById("content").style.display = "block";
                    clearInterval(x);
                  }
                  //seconds
                }, 0)
              }

          /* Plus Minus button js */

          /*var buttonPlus  = $(".quantity .plus");
          var buttonMinus = $(".quantity .minus");

          var incrementPlus = buttonPlus.click(function() {
            var $n = $(this)
            .parent(".quantity")
            .find(".input-qty");
            $n.val(Number($n.val())+1 );
          });

          var incrementMinus = buttonMinus.click(function() {
            var $n = $(this)
            .parent(".quantity")
            .find(".input-qty");
            var amount = Number($n.val());
            if (amount > 0) {
              $n.val(amount-1);
            }
          });*/

          /* Page scroll */
          $(".scroll a").click(function (event) {
            $('.scroll a').removeClass("active");
            event.preventDefault();
            var full_url = this.href;
            var parts = full_url.split("#");
            var trgt = parts[1];
            var target_offset = $("#" + trgt).offset();
            var target_top = target_offset.top;
            $('html, body').animate({scrollTop: target_top - 100 }, 0);
            $(this).addClass("active");
          });

          /* Search Popup */
          $(document).on("click", ".search-icon, .close-search", function(){
            $('.search-bar').toggleClass("open");
          });
          
        },

        

        /*======================================
         02. Slider Open JS
        ========================================*/
      all_slider: function () {

      /*Trending Collection slider*/
      var swiper = new Swiper(".trending-collection-slider .swiper", {
        slidesPerView: 1,
        spaceBetween: 0,
        navigation: {
          nextEl: ".trending-collection-section .swiper-button-next",
          prevEl: ".trending-collection-section .swiper-button-prev",
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 3,
          },
          1024: {
            slidesPerView: 4,
          },
        },
      });

      
            /*hero slider*/
      var swiper = new Swiper(".hero-slider-section .swiper", {
        slidesPerView: 1,
        spaceBetween: 0,
        
        pagination: {
          el: ".hero-slider-section .swiper-pagination",
          clickable: true,
        },
      });

            /*Season Collection slider*/
      var swiper = new Swiper(".season-collection-slider .swiper", {
        slidesPerView: 1,
        spaceBetween: 15,
        navigation: {
          nextEl: ".season-collection-section .swiper-button-next",
          prevEl: ".season-collection-section .swiper-button-prev",
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 3,
            spaceBetween: 25,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 32,
          },
        },
      });

             /*new arrival slider*/
      var swiper = new Swiper(".new-arrival-slider .swiper", {
        slidesPerView: 1,
        spaceBetween: 15,
        navigation: {
          nextEl: ".new-arrival-slider .swiper-button-next",
          prevEl: ".new-arrival-slider .swiper-button-prev",
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 4,
            spaceBetween: 20,
          },
        },
      });

            /*testimonial slider*/
      var swiper = new Swiper(".testimonial-slider .mySwiper", {
        spaceBetween: 0,
        slidesPerView: 1,
        effect: "fade",
      });
      var swiper2 = new Swiper(".testimonial-slider .mySwiper2", {
        spaceBetween: 0,
        pagination: {
          el: ".testimonial-slider .swiper-pagination",
          clickable: true,
        },
        thumbs: {
          swiper: swiper,
        },
      });

            /*product gallery vertical*/
      var swiper = new Swiper(".product-gallery-vertical .mySwiper", {
        spaceBetween: 5,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        direction: "vertical",
      });
      var swiper2 = new Swiper(".product-gallery-vertical .mySwiper2", {
        spaceBetween: 0,
        navigation: {
          nextEl: ".product-gallery-vertical .swiper-button-next",
          prevEl: ".product-gallery-vertical .swiper-button-prev",
        },
        thumbs: {
          swiper: swiper,
        },
      });

            /*product gallery horizontal*/
      var swiper = new Swiper(".product-gallery-horizontal .mySwiper", {
        spaceBetween: 5,
        slidesPerView: 3,
        freeMode: true,
        watchSlidesProgress: true,

        breakpoints: {
          640: {
            slidesPerView: 3,
          },
          768: {
            slidesPerView: 4,
          },
          1024: {
            slidesPerView: 5,
          },
        },
      });
      var swiper2 = new Swiper(".product-gallery-horizontal .mySwiper2", {
        spaceBetween: 0,
        navigation: {
          nextEl: ".product-gallery-horizontal .swiper-button-next",
          prevEl: ".product-gallery-horizontal .swiper-button-prev",
        },
        thumbs: {
          swiper: swiper,
        },
      });

      
    },

    
    /*======================================
     03. Tabs Open JS
    ========================================*/
    tabs_open: function() {

      $('.wc-tabs li, .tab-link-title').click(function(){
        var tab_id = $(this).attr('data-tab');
        $('.wc-tabs li, .tab-link-title').removeClass('active');
        $('.tabs-entry-content').removeClass('active');
        $(this).addClass('active');
        $("#"+tab_id).addClass('active');
      });

    },

    /*======================================
     04. Accordion Open JS
    ========================================*/
    accordion_open: function() {

      $("body").on("click",".accordion .accordion-title",function(){
        $(".accordion-content").slideUp(),
        $(this).hasClass("active")?($(this).next(".accordion-content").slideUp(),
          $(this).removeClass("active")):(
          $(".accordion .accordion-title").removeClass("active"),
          $(this).addClass("active"),
          $(this).next(".accordion-content").slideDown())
        });

    },

    /*======================================
     05. Isotope JS
    ========================================*/
    Isotope_js: function() {
      // init Isotope

      $('.marquee-animation').imagesLoaded( function() {
        var $grid_masonary = $('.grid-masonary').isotope({
          itemSelector: '.grid-item',
          masonry: {
            horizontalOrder: false,
          }
        });
      });
      
      
    },

    /*======================================
     06. All popup JS
    ========================================*/
    all_popup_js: function() {
      
      /* Newsletter Popup JS */
        setTimeout(function() {
          $('body').find('.newsletter-popup-link').trigger('click');
        }, 2000);

      // Newsletter Popup
      // $('.newsletter-popup-link').magnificPopup({
      //   type: 'inline',
      //   preloader: false,
      //   focus: '#name',
      // });

      // product quick view Popup
      $('.quick-view-link').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#name',
      });
      // product quick view Popup
      $('.video-play-icon').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
      });
      
      
    },

    /*=====================================
    07. Preloader JS
    ======================================*/  
    Preloader_js: function() {
      //After 2s preloader is fadeOut
      $('.preloader').delay(2000).fadeOut('slow');
      setTimeout(function() {
      //After 2s, the no-scroll class of the body will be removed
        $('body').removeClass('no-scroll');
      }, 2000); //Here you can change preloader time
    },


    /*=====================================
    07. Custom JS
    ======================================*/  
    Custom_js: function() {
      // Target only the first .accordion group
      var $firstAccordion = $('.accordion').first();

      $firstAccordion.find('.accordion-title').first().addClass('active');
      $firstAccordion.find('.accordion-content').first().addClass('actives');

      /*Start Cookie JS*/
      const $cookiePopup = $('.cookie-popup');

      function setCookie(name, value, days) {
        let expires = "";
        if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 86400000));
          expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
      }

      function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
      }

      // Hide popup if already accepted
      if (localStorage.getItem('cookieConsent') || getCookie('OptanonConsent')) {
        $cookiePopup.removeClass('open').hide();
      }

      function handleConsent(type, days) {
        localStorage.setItem('cookieConsent', type);
        setCookie('OptanonAlertBoxClosed', new Date().toISOString(), days);

        let groups = 'C0004:0,C0002:0,C0001:0,C0003:0'; // all off
        if (type === 'all') groups = 'C0004:1,C0002:1,C0001:1,C0003:1';
        if (type === 'accept') groups = 'C0001:1'; // only essential

        const consent = `landingPath=NotLandingPage&datestamp=${encodeURIComponent(new Date().toString())}&version=202403.1.0&groups=${groups}&hosts=`;
        setCookie('OptanonConsent', consent, days);

        $cookiePopup.removeClass('open').fadeOut();
      }

      // Accept All → 365 days
      $(document).on('click', '.accept-all-btn', function (e) {
        handleConsent('all', 365);
      });

      // Decline All → 7 days
      $(document).on('click', '.decline-all-btn', function (e) {
        handleConsent('decline', 7);
      });
      /*End Cookie JS*/

      /* Start Newsletter Popup JS */
      const NEWSLETTER_COOKIE_NAME = 'newsletterPopupConsent';
      const $newsletterPopup = $('#newsletter-popup');

      // Show popup if cookie is not set or expired
      if (!getCookie(NEWSLETTER_COOKIE_NAME)) {
        setTimeout(() => {
          $.magnificPopup.open({
            items: {
              src: '#newsletter-popup',
              type: 'inline'
            },
            focus: 'input[type="email"]',
            callbacks: {
              close: function () {
                // If closed without subscribing
                if (!$newsletterPopup.data('subscribed')) {
                  setCookie(NEWSLETTER_COOKIE_NAME, 'closed', 7); // close = 7 days
                }
              }
            }
          });
        }, 2000);
      }

      // On form submit (simulate subscription)
      $(document).on('submit', '.newsletter-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const type = $form.find('input[name="type"]').val();
        const email = $form.find('input[name="email"]').val();
        // const $message = $form.find('.form-message');
        const $message = $form.siblings('.form-message');

        $.ajax({
          url: '/subscribe-newsletter',
          method: 'POST',
          data: {
            email: email,
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            $form.find('input[name="email"]').val('');
            
            $('#newsletter-popup').data('subscribed', true);
            $message.text('Thank you for subscribing!').css('color', 'green').fadeIn();

            if (type == 'popup') {
              setCookie('newsletterPopupConsent', 'subscribed', 365);
              setTimeout(() => {
                $.magnificPopup.close();
              }, 1500);
            }

          },
          error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'Something went wrong. Please try again.';

            // If already subscribed, treat as success
            if (xhr.status === 409) {
              $('#newsletter-popup').data('subscribed', true);
              $message.text('You are already subscribed.').css('color', (type == 'popup' ? 'green' : 'red')).fadeIn();

              if (type == 'popup') {
                setCookie('newsletterPopupConsent', 'already_subscribed', 365);
                setTimeout(() => {
                  $.magnificPopup.close();
                }, 1500);
              }

            } else {
              $message.text(msg).css('color', 'red').fadeIn();
            }
          }
        });

        setTimeout(() => {
          $message.text('');
        }, 3000);
      });
      /* End Newsletter Popup JS */

      /*Start Register JS*/
      $(document).ready(function () {
        const $form = $('#register-form');
        const $responseBox = $('#registerResponse');

        if (!$form.length) return;

        $form.on('submit', function (e) {
          e.preventDefault();

          // Clear previous field errors
          $('.field-error').remove();

          const formData = new FormData(this);

          $.ajax({
            url: "/customer/register",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
              $responseBox
                .show()
                .css('background-color', '#186f65')
                .text(data.message || 'Registration complete');

              $form[0].reset();

              setTimeout(() => {
                window.location.href = "/sign-in";
              }, 500);
            },
            error: function (xhr) {
              const data = xhr.responseJSON;
              
              if (data?.errors) {
                $.each(data.errors, function (field, messages) {
                  const $input = $(`[name="${field}"]`);
                  if ($input.length) {
                    const $error = $('<div class="field-error text-danger mt-1"></div>').text(messages[0]);
                    $input.closest('.form-group').append($error);
                  }
                });
              } else {
                $responseBox
                  .show()
                  .css('background-color', '#a94442')
                  .text(data?.message || 'Something went wrong.');
              }
            }
          });

          setTimeout(() => {
            $responseBox.hide();
          }, 3000);
        });
      });
      /*End Register JS*/

      /*Start Login JS*/
      $(document).ready(function () {
        const $form = $('#customerLoginForm');
        const $responseBox = $('#loginResponse');

        if (!$form.length) return;

        $form.on('submit', function (e) {
          e.preventDefault();

          const formData = new FormData(this);

          $.ajax({
            url: "/customer/login",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
              $responseBox
                .show()
                .css('background-color', '#186f65')
                .text(data.message || 'Login successful');

              // Redirect to dashboard or homepage after short delay
              setTimeout(() => {
                var redirectUrl = localStorage.getItem('redirectUrl') ?? "/";
                localStorage.setItem('redirectUrl', '');
                window.location.href = redirectUrl;
              }, 500);
            },
            error: function (xhr) {
              const data = xhr.responseJSON;

              if (data?.errors) {
                $.each(data.errors, function (field, messages) {
                  const $input = $(`[name="${field}"]`);
                  if ($input.length) {
                    const $error = $('<div class="field-error text-danger mt-1"></div>').text(messages[0]);
                    $input.closest('.form-group').append($error);
                  }
                });
              } else {
                $responseBox
                  .show()
                  .css('background-color', '#a94442')
                  .text(data?.message || 'Invalid credentials or error occurred');
              }
            }
          });

          setTimeout(() => {
            $responseBox.hide();
          }, 3000);
        });
      });
      /*End Login JS*/

      /*Start Forgot Password JS*/
      $(document).ready(function () {
        $(document).on('submit', '#forgotPasswordForm', function (e) {
            e.preventDefault();
            const form = $(this);
            const data = form.serialize();

            $.ajax({
                url: '/customer/forgot-password',
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (res) {
                    $('#forgotResponse').show().text(res.message).css('background-color', '#186f65');
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Error occurred.';
                    $('#forgotResponse').show().text(msg).css('background-color', '#a94442');
                }
            });

            setTimeout(() => {
              $('#forgotResponse').hide();
            }, 3000);
        });

      });
      /*End Forgot Password JS*/

      /*Start Reset Password JS*/
      $(document).ready(function () {
        $(document).on('submit', '#resetPasswordForm', function (e) {
            e.preventDefault();
            const form = $(this);
            const data = form.serialize();

            $.ajax({
                url: '/customer/reset-password',
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (res) {
                    $('#resetResponse').show().text(res.message).css('background-color', '#186f65');

                    setTimeout(() => {
                      window.location.href = '/sign-in';
                    }, 500);
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Error occurred.';
                    $('#resetResponse').show().text(msg).css('background-color', '#a94442');
                }
            });
        });

        setTimeout(() => {
          $('#resetResponse').hide();
        }, 3000);
      });
      /*End Reset Password JS*/

      /*Start Blogs & Category JS*/
      if ($('meta[name="segment-1"]').attr('content') == 'blogs') {
        fetchBlogs();
      }

      function fetchBlogs(url = '/blogs/list') {
        $('#blogListAjaxWrapper').html('Loading...');

        const $form = $('#blogFilterForm');
        const formData = $form.serialize();

        $.ajax({
          url: url,
          method: 'GET',
          data: formData,
          success: function (res) {

            // Update title/description block
            let title = '';
            let description = '';
            let breadcrumb = `<li class="breadcrumb-item"><a href="/">Home</a></li>`;

            if (res.category_title) {
              title = res.category_title;
              description = res.category_description;
              breadcrumb += `<li class="breadcrumb-item"><a href="/blogs">Blogs</a></li><li class="breadcrumb-item active" aria-current="page">Category</li>`;
            } else if (res.tag_title) {
              title = res.tag_title;
              description = res.tag_description;
              breadcrumb += `<li class="breadcrumb-item"><a href="/blogs">Blogs</a></li><li class="breadcrumb-item active" aria-current="page">Tag</li>`;
            } else {
              title = 'Blogs';
              breadcrumb += `<li class="breadcrumb-item active" aria-current="page">Blogs</li>`;
            }

            $('.heading-banner-wrap h1').text(title);
            $('.breadcrumb').html(breadcrumb);

            // Show title/description section before blog list
            if (res.category_title && title != 'Blogs') {
              $('.filter-intro-section').removeClass('d-none');
              $('.filter-intro-section .container:first h2').text(title);
              $('.filter-intro-section .container:first p').text(description);
            } else {
              $('.filter-intro-section').addClass('d-none');
            }

            // ✅ Push current filters to URL
            if ($('meta[name="segment-1"]').attr('content') == 'blogs' && $('meta[name="segment-2"]').attr('content') == 'category' && $('meta[name="segment-3"]').attr('content')) {
              $(`#blogFilterForm input[name="category"]`).prop('checked', false);
            }
            const formData = $('#blogFilterForm').serialize();
            const hasPushFilters = formData && formData.replace(/(s=|category=|tag=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');

            if (hasPushFilters) {
              const newUrl = window.location.pathname + '?' + formData;
              history.pushState(null, '', newUrl);
            } else {
              // No filters, just reset to base URL
              history.pushState(null, '', window.location.pathname);
            }

            if ($('meta[name="segment-1"]').attr('content') == 'blogs' && $('meta[name="segment-2"]').attr('content') == 'category' && $('meta[name="segment-3"]').attr('content')) {
              $(`#blogFilterForm input[name="category"][value="${$('meta[name="segment-3"]').attr('content')}"]`).prop('checked', true);
            }

            // ✅ Applied Filters UI
            const $filterList = $('#appliedFilterList').empty();
            const search = document.getElementById('filterSearch').value ?? $('[name="s"]').val();
            const category = $('[name="category"]:checked').val() ?? $('meta[name="segment-3"]').attr('content');
            const tag = $('[name="tag"]:checked').val();

            let hasFilterDisplay = false;

            if (search) {
              hasFilterDisplay = true;
              $filterList.append(`<span class="badge bg-dark text-white filter-badge" data-type="search">Search: "${search}" ✕</span>`);
            }

            if (category) {
              hasFilterDisplay = true;
              const categoryLabel = $(`input[name="category"][value="${category}"]`).siblings('a').text();
              $filterList.append(`<span class="badge bg-primary text-white filter-badge" data-type="category">Category: ${categoryLabel} ✕</span>`);
            }

            if (tag) {
              hasFilterDisplay = true;
              const tagLabel = $(`input[name="tag"][value="${tag}"]`).siblings('a').text();
              $filterList.append(`<span class="badge bg-success text-white filter-badge" data-type="tag">Tag: ${tagLabel} ✕</span>`);
            }

            $('#appliedFilters').toggle(hasFilterDisplay);

            // Update blog list section
            $('#blogListAjaxWrapper').html(res.html);

            // ✅ Reset all active classes
            $('#blogFilterForm .widget-categories a, #blogFilterForm .widget-popular-tags a').removeClass('active');

            // ✅ Add active class to selected category
            const selectedCategory = $form.find('input[name="category"]:checked').val() ?? $('meta[name="segment-3"]').attr('content');
            if (selectedCategory) {
              $(`input[name="category"][value="${selectedCategory}"]`).siblings('a').addClass('active');
            }

            // ✅ Add active class to selected tag
            const selectedTag = $form.find('input[name="tag"]:checked').val();
            if (selectedTag) {
              $(`input[name="tag"][value="${selectedTag}"]`).siblings('a').addClass('active');
            }
          },
          error: function () {
            alert('Failed to load blogs');
            $('#blogListAjaxWrapper').html('');
          }
        });
      }

      $('#blogFilterForm').on('submit', function (e) {
        e.preventDefault();
        fetchBlogs();
      });

      $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        fetchBlogs(url);
      });

      $(document).on('click', '#blogFilterForm li a', function (e) {
        e.preventDefault(); // prevent link behavior

        const $parent = $(this).closest('li');
        const $input = $parent.find('input[type="radio"]');

        // Select the radio and trigger change
        if ($input.attr('name') == 'category') {

          $(`#blogFilterForm input[name="category"]`).prop('checked', false);
          const formData = $('#blogFilterForm').serialize();
          const hasPushFilters = formData && formData.replace(/(s=|category=|tag=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');
          window.location.href = '/blogs/category/' + $input.val() + (hasPushFilters ? '?' + formData : '');

        } else {
          if ($('meta[name="segment-1"]').attr('content') == 'blogs' && $('meta[name="segment-2"]').attr('content') == 'category' && $('meta[name="segment-3"]').attr('content')) {
            $(`input[name="category"][value="${$('meta[name="segment-3"]').attr('content')}"]`).prop('checked', true);
          }
          $input.prop('checked', true).trigger('change');
        }
      });

      $(document).on('change', '#blogFilterForm input[type="radio"]', function () {
          $('#blogFilterForm').submit(); // Auto-submit on change
      });

      // Clear all filters
      $(document).on('click', '#clearAllFilters', function () {
        window.location.href = '/blogs';
      });

      // Remove individual filter
      $(document).on('click', '.filter-badge', function () {

        const type = $(this).data('type');
        $(`#blogFilterForm input[name="${type}"]`).prop('checked', false);
        
        if (type == 'search') {
          $('[name="s"]').val('')
        }
        
        if (type == 'category') {
          const formData = $('#blogFilterForm').serialize();
          const hasPushFilters = formData && formData.replace(/(s=|category=|tag=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');
          window.location.href = '/blogs'+ (hasPushFilters ? '?' + formData : '');
        } else {
          $('#blogFilterForm').submit();
        }
      });
      /*End Blogs & Category JS*/

      /*Start Blog Comments JS*/
      $('#blogCommentForm').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const responseBox = $('#commentResponse');

        // Clear previous messages
        responseBox.hide().removeClass('success error').text('');

        $.ajax({
          url: '/blog-comment/submit', // change to your correct controller route
          type: 'POST',
          data: formData,
          processData: false, // Important for FormData
          contentType: false, // Important for FormData
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function (res) {
            responseBox
              .css({ backgroundColor: '#186f65' })
              .text('Comment submitted successfully.')
              .fadeIn();
            $form[0].reset();
          },
          error: function (xhr) {
            let errorMsg = 'Something went wrong.';
            if (xhr.responseJSON?.errors) {
              const errors = xhr.responseJSON.errors;
              errorMsg = Object.values(errors).flat().join(' ');
            }
            responseBox
              .css({ backgroundColor: '#a94442' })
              .text(errorMsg)
              .fadeIn();
          }
        });

        setTimeout(() => {
            responseBox.hide();
          }, 3000);
      });

      /*End Blog Comments JS*/
      
      /*Start Shop & Category JS*/
      if ($('meta[name="segment-1"]').attr('content') == 'shop' || $('meta[name="segment-1"]').attr('content') == 'category') {
        fetchProducts();
      }

      function fetchProducts(url = '/products/list') {
        $('#product-list').html('<div class="text-center w-100 py-5">Loading...</div>');
        const formData = $('#shopFilterForm').serialize();

        $.ajax({
          url: url,
          method: 'GET',
          data: formData,
          success: function (res) {
            $('#product-result').html(res.html);

            $('.shop-result-count').html(`Showing <span id="current-count">${res?.products?.to ?? 0}</span> of <span id="total-count">${res?.products?.total ?? 0}</span> results`);

            // Update URL without reload
            let formFilterData = $('#shopFilterForm').serialize();
            let params = new URLSearchParams(formFilterData);
            params.delete('category');
            formFilterData = params.toString();

            const hasPushFilters = formFilterData && formFilterData.replace(/(s=|category=|colors=|sizes=|price_range=|sorting=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');
            const newUrl = window.location.pathname + ( hasPushFilters ? '?' + formFilterData : '' );
            history.pushState(null, '', newUrl);

            renderAppliedFilters();
          },
          error: function () {
            alert('Failed to load products');
            $('#product-list').html('<div class="text-center w-100 py-5 text-danger">Failed to load products</div>');
          }
        });
      }

      function renderAppliedFilters() {
        const $filterList = $('#appliedFilterList').empty();
        const formData = new FormData(document.getElementById('shopFilterForm'));
        let hasFilters = false;

        const filters = {
          s: 'Search',
          category: 'Category',
          'color[]': 'Color',
          'size[]': 'Size',
          'price_range': 'Price',
          'view_type': ''
        };
        const filterClasses = {
          s: 'badge bg-secondary text-white',
          category: 'badge bg-primary text-white',
          'color[]': 'badge bg-info text-white',
          'size[]': 'badge bg-light text-white',
          'price_range': 'badge bg-dark text-white',
          'view_type': 'badge bg-dark text-white'
        };

        for (const [key, label] of Object.entries(filters)) {
          const values = formData.getAll(key);

          values.forEach(value => {
            let displayValue = decodeURIComponent(value).replace(/_/g, ' ');
            if (displayValue && key != 'view_type') {

              if (key == 'price_range') {
                $(`input[name="price"]`).prop('checked', false);
                $(`input[name="price"][value="${value}"]`).prop('checked', true);
              }

              if ($(`[data-value="${displayValue}"]`).text()) {
                displayValue = $(`[data-value="${displayValue}"]`).text();
              }

              hasFilters = true;
              $filterList.append(`
                <span class="${filterClasses[key]} shop-filter-badge" data-type="${key}" data-value="${value}">
                  ${label}: ${displayValue} ✕
                </span>
              `);

            }

            if ( key == 'view_type' ) {
              $('.product-view-type').removeClass('active');
              $(`.product-view-type[data-view-type="${value}"]`).addClass('active');
              $('#view_type').val(value);
            }

          });
        }

        $('#appliedFilters').toggle(hasFilters);
      }

      let debounceTimer;
      $(document).on('keydown', '.search-input', function (e) {
          if (e.key === 'Enter' || e.keyCode === 13) {
              e.preventDefault();
              let s = $(this).val();
              let category = $('.search-categories .search-category.search-category-selected').data('cat-slug');
              $(`#shopFilterForm input[name="s"]`).val(s);
              $(`#shopFilterForm input[name="category"]`).val(category);

              const formData = $('#shopFilterForm').serialize();
              const hasPushFilters = formData && formData.replace(/(s=|category=|colors=|sizes=|price_range=|sorting=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');

              if (category) {

                window.location.href = '/category/' + category + (hasPushFilters ? '?' + formData : '?s=' + s);
              } else {
                window.location.href = '/shop' + (hasPushFilters ? '?' + formData : '?s=' + s);
              }
          }

          // clearTimeout(debounceTimer);
          // debounceTimer = setTimeout(() => {
          //   const s = $(this).val().trim();
          //   $(`#shopFilterForm input[name="s"]`).val(s);
          //   fetchProducts();
          // }, 1000);
      });

      $(document).on('click', '.search-categories li', function (e) {
          e.preventDefault();
          $('.search-categories .search-category').removeClass('search-category-selected');
          $(this).addClass('search-category-selected');
      });


      // Category filter click
      $(document).on('click', '.checkbox-categories-list li a', function (e) {
        e.preventDefault();

        const category = $(this).data('category-slug');

        if (category && category != '' && category != null && category != undefined) {
          const formData = $('#shopFilterForm').serialize();
          const hasPushFilters = formData && formData.replace(/(s=|category=|colors=|sizes=|price_range=|sorting=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');
          window.location.href = '/category/' + category + (hasPushFilters ? '?' + formData : '');
        } else {
          window.location.href = '/category';
        }

      });

      // Price filter click
      $(document).on('click', '.checkbox-price-list li label span.filter-price-span', function (e) {
        e.preventDefault();

        const price = $(this).data('value');
        $(`#shopFilterForm input[name="price_range"]`).val(price);
        fetchProducts();

      });

      // sorting-products filter click
      $(document).on('change', '.sorting-products .orderby', function (e) {
        e.preventDefault();

        const sorting = $(this).val();
        $(`#shopFilterForm input[name="sorting"]`).val(sorting);
        fetchProducts();

      });

      $(document).on('click', '.product-view-type', function () {
        $('.product-view-type').removeClass('active');
        $(this).addClass('active');

        const viewType = $(this).data('view-type');
        $('#view_type').val(viewType); // update hidden input

        fetchProducts(); // reload product list based on view
      });

      // Remove Individual Filter Badge
      $(document).on('click', '.shop-filter-badge', function () {
        const type = $(this).data('type');
        const value = $(this).data('value');

        // if (type.endsWith('[]')) {
        //   $(`#shopFilterForm input[name="${type}"][value="${value}"]`).prop('checked', false);
        // } else {
        //   $(`#shopFilterForm input[name="${type}"]`).prop('checked', false);
        // }
        // $('#shopFilterForm').submit();

        $(`#shopFilterForm input[name="${type}"]`).val('');

        if (type == 'category') {
          $(`#shopFilterForm input[name="${type}"]`).val('');
          const formData = $('#shopFilterForm').serialize();
          const hasPushFilters = formData && formData.replace(/(s=|category=|colors=|sizes=|price_range=|sorting=)([^&]*)/g, '$2').split('&').some(v => v.trim() !== '');
          window.location.href = '/shop' + (hasPushFilters ? '?' + formData : '');
        } else {
          fetchProducts();
        }
      });

      // Clear All Filters
      $(document).on('click', '#clearAllShopFilters', function () {
        if ($('meta[name="segment-1"]').attr('content') == 'category') {
          window.location.href = '/shop';
        } else {
          // Reset form to default
          $('#shopFilterForm')[0].reset();

          // Manually clear non-default or dynamic values
          $('#shopFilterForm').find('input, select, textarea').each(function () {
            if (this.type === 'checkbox' || this.type === 'radio') {
              this.checked = false;
            } else {
              this.value = '';
            }
          });
          fetchProducts();
        }
      });
      /*End Shop & Category JS*/
      
      /*Start Load More JS*/
      $(document).on('click', '.load-more-products', function(e) {
        e.preventDefault();

        var $btn = $(this);
        var nextPageUrl = $btn.attr('href');

        $btn.text('Loading...');

        const formData = $('#shopFilterForm').serialize();

        $.ajax({
          url: nextPageUrl,
          type: 'GET',
          data: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            $('.shop-result-count').html(`Showing <span id="current-count">${response?.products?.to ?? 0}</span> of <span id="total-count">${response?.products?.total ?? 0}</span> results`);

            var $response = $('<div>').html(response?.html);
            var newProducts = $response?.find('#product-list').html();
            var newLoadMore = $response?.find('.section-full-btn').html();

            $('#product-list').append(newProducts);

            if (newLoadMore) {
              $('.section-full-btn').html(newLoadMore);
            } else {
              $('.section-full-btn').remove(); // No more pages
            }

            // var currentCount = $('#current-count').text();
            // var newToCount = $response?.find('#current-count').text();
            // var newCurrentCount = parseInt(currentCount) + parseInt(newToCount);
            // $('#current-count').text(newCurrentCount);
          },
          error: function() {
            $btn.text('Error. Try again');
          }
        });
      });
      /*End Load More JS*/

      /*Start Product Quick View JS*/
      $(document).on('click', '.quick-view-link', function(e) {
        e.preventDefault();
        var $button = $(this);
        var get_view_url = $button.data('get_view_url');
        var $popup = $('#product-quick-popup');

        // Show a loading spinner or message
        $popup.html(`
          <div class="product-quick-body">
            <div class="loading-message">
              <div class="spinner"></div>
              <p>Processing, please wait...</p>
            </div>
          </div>
        `);

        $.ajax({
          url: get_view_url,
          type: 'GET',
          dataType: 'html',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            var $response = $('<div>').html(response);
            var $popupContent = $response.find('#product-quick-popup-content').html();

            if ($popupContent) {
              $popup.html($popupContent);
            } else {
              $popup.html('<div class="product-quick-body"><div class="error-message">No content found for this product.</div></div>');
            }

            $.magnificPopup.open({
              items: { src: '#product-quick-popup' },
              type: 'inline'
            });
          },
          error: function(xhr, status, error) {
            $popup.html(`
              <div class="product-quick-body">
                <div class="error-message">
                  <p>Error loading product details.</p>
                  <button class="retry-btn">Retry</button>
                </div>
              </div>
            `);

            $('.retry-btn').on('click', function() {
              $button.trigger('click');
            });
          }
        });
      });
      /*End Product Quick View JS*/
      
      /*Start Review JS*/
      $('#review-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
          url: '/submit-review',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            $('#review-message')
              .html('<div class="alert alert-success">Review submitted successfully!</div>')
              .fadeIn();

            $('#review-form')[0].reset();

            // Wait 1 second, then reload
            setTimeout(function () {
              location.reload();
            }, 1000);
          },
          error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Error submitting review.';
            $('#review-message')
              .html(`<div class="alert alert-danger">${errorMsg}</div>`)
              .fadeIn();
          }
        });
      });

      $(document).ready(function () {
        let total = 0;
        let count = 0;

        $('.review-ratings').each(function () {
          const rating = parseInt($(this).data('rating'));
          total += rating;
          count++;

          const maxStars = 5;
          const $el = $(this);
          $el.empty();

          for (let i = 1; i <= maxStars; i++) {
            if (i <= rating) {
                $el.append('<li><i class="fa-solid fa-star text-warning"></i></li>');
            } else {
                $el.append('<li><i class="fa-regular fa-star text-muted"></i></li>');
            }
          }
        });

        if (count > 0) {
          const average = (total / count).toFixed(1);
          const avgRounded = Math.round(average);
          const $avgStars = $('.average-ratings');
          const maxAvgStars = 5;

          // Output average stars
          $avgStars.empty();
          for (let j = 1; j <= maxAvgStars; j++) {
            if (j <= avgRounded) {
              $avgStars.append('<li><i class="fa-solid fa-star text-warning"></i></li>');
            } else {
              $avgStars.append('<li><i class="fa-regular fa-star text-muted"></i></li>');
            }
          }
        }
      });

      /*End Review JS*/

      $(document).ready(function () {

        let segment_1 = $('meta[name="segment-1"]').attr('content')
        
        /*Start AddToCart JS*/
        $(document).on('click', '.add-to-cart-btn', function (e) {
          e.preventDefault();

          let $row = $(this).closest('.product-info');
          let $input = $row.find('.single-input-qty');
          let qty = parseInt($input.val());
          if (qty < 1 || isNaN(qty)) {
            qty = 1;
            $input.val(qty);
          }

          let productId = $(this).data('product-id');
          let quantity = (parseInt(qty) > 0) ? parseInt(qty) : 1; // or get from input

          $.ajax({
            url: '/add-to-cart',
            method: 'POST',
            data: {
              product_id: productId,
              quantity: quantity,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                alert(response.message);
                refreshDataWithoutReload();
            },
            error: function () {
                alert('Something went wrong!');
            }
          });
        });

        // Handle quantity change via +/- buttons in single product page
        $(document).on('click', '.single-quantity-click', function (e) {
          e.preventDefault();
          let $row = $(this).closest('.product-info');
          let $input = $row.find('.wc-input-qty');
          let qty = parseInt($input.val());

          if ($(this).hasClass('plus')) {
            qty = qty + 1;
          } else if ($(this).hasClass('minus') && qty > 1) {
            qty = qty - 1;
          }

          $input.val(qty);
        });

        // Handle quantity change via +/- buttons
        $(document).on('click', '.quantity-click', function (e) {
          e.preventDefault();
          let $row = $(this).closest('.cart-item-row');
          let $input = $row.find('.wc-input-qty');
          let productId = $input.data('product-id');
          let qty = parseInt($input.val());

          if ($(this).hasClass('plus')) {
            qty = qty + 1;
          } else if ($(this).hasClass('minus') && qty > 1) {
            qty = qty - 1;
          }

          $input.val(qty);

          updateCartItem(productId, qty);
        });

        // Handle manual quantity input (on blur)
        $(document).on('blur', '.wc-input-qty', function (e) {
          e.preventDefault();
          let productId = $(this).data('product-id');
          let qty = parseInt($(this).val());

          if (qty < 1 || isNaN(qty)) {
            qty = 1;
            $(this).val(qty);
          }

          updateCartItem(productId, qty);
        });

        // Handle remove item
        $(document).on('click', '.remove-icon', function (e) {
          e.preventDefault();
          let productId = $(this).data('product-id');
          removeCartItem(productId);
        });

        function updateCartItem(productId, quantity) {
          $.ajax({
            url: '/cart/update-item',
            type: 'POST',
            data: {
              product_id: productId,
              quantity: quantity,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
              alert(response.message);
              // location.reload(); // Refresh cart list & total
              refreshDataWithoutReload();
            },
            error: function (xhr) {
              alert('Failed to update cart');
            }
          });
        }

        function removeCartItem(productId) {
          $.ajax({
            url: '/cart/remove-item',
            type: 'POST',
            data: {
              product_id: productId,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
              alert(response.message);
              // location.reload(); // Refresh cart after removal
              refreshDataWithoutReload();
            },
            error: function () {
              alert('Failed to remove item');
            }
          });
        }

        function refreshDataWithoutReload() {
            $.ajax({
                url: $('meta[name="current-full-url"]').attr('content'),
                type: 'GET',
                success: function (html) {

                    const newContent = $(html).find('.cart-detail-section').html();
                    $('.cart-detail-section').html(newContent);
                    
                    const newContentMini = $(html).find('.mini-cart-detail-section').html();
                    $('.mini-cart-detail-section').html(newContentMini);
                    
                    const headerCartItemCount = $(html).find('.header-cart-item-count').html();
                    $('.header-cart-item-count').html(headerCartItemCount);
                    
                    const wishlistListSection = $(html).find('.wishlist-list-section').html();
                    $('.wishlist-list-section').html(wishlistListSection);
                    
                    const headerWishlistItemCount = $(html).find('.header-wishlist-item-count').html();
                    $('.header-wishlist-item-count').html(headerWishlistItemCount);
                    
                    const compareListSection = $(html).find('.compare-list-section').html();
                    $('.compare-list-section').html(compareListSection);
                    
                    const headerCompareItemCount = $(html).find('.header-compare-item-count').html();
                    $('.header-compare-item-count').html(headerCompareItemCount);
                    
                    const checkoutContentSection = $(html).find('.checkout-content-section').html();
                    $('.checkout-content-section').html(checkoutContentSection);

                    if (segment_1 == 'checkout') {
                      getAddressDetails();
                    }
                    
                    const accountDetailForm = $(html).find('.account-detail-form').html();
                    $('.account-detail-form').html(accountDetailForm);
                    
                    const accountInfoBox = $(html).find('.account-info-box').html();
                    $('.account-info-box').html(accountInfoBox);
                    
                    const accountAddressDetailSection = $(html).find('.account-address-detail-section').html();
                    $('.account-address-detail-section').html(accountAddressDetailSection);
                    
                    const accountOrderListSection = $(html).find('.account-order-list-section').html();
                    $('.account-order-list-section').html(accountOrderListSection);

                },
                error: function () {
                    alert('Failed to refresh content');
                }
            });
        }
        /*End AddToCart JS*/

        /*Start Wishlist JS*/
        $(document).on('click', '.add-to-wishlist-btn', function (e) {
            e.preventDefault();
            let productId = $(this).data('product-id');

            $.ajax({
                url: '/add-to-wishlist',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload();
                },
                error: function (xhr) {
                  const msg = xhr.responseJSON?.message || 'Something went wrong!';
                  alert(msg);
                  if ( xhr.responseJSON?.err_type == 'not-login') {
                    setTimeout(() => {
                      localStorage.setItem('redirectUrl', $('meta[name="current-full-url"]').attr('content'));
                      window.location.href = "/sign-in";
                    }, 100);
                  }
                }
            });
        });

        $(document).on('click', '.remove-from-wishlist-btn', function (e) {
            e.preventDefault();
            let productId = $(this).data('product-id');

            $.ajax({
                url: '/wishlist/remove-item',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload();
                },
                error: function (xhr) {
                  const msg = xhr.responseJSON?.message || 'Something went wrong!';
                  alert(msg);
                }
            });
        });

        $(document).on('click', '.remove-all-wishlist-btn', function (e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to remove all items from your wishlist?')) return;

            $.ajax({
                url: '/wishlist/clear',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload(); // your existing refresh logic
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });

        $(document).on('click', '.wishlist-add-to-cart-btn', function (e) {
            e.preventDefault();
            let productId = $(this).data('product-id');

            $.ajax({
                url: '/wishlist/add-to-cart',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload();
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });

        $(document).on('click', '.add-all-to-cart-btn', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/wishlist/add-all-to-cart',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload();
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });
        /*End Wishlist JS*/

        /*Start compare JS*/
        // Add to Compare
        $(document).on('click', '.add-to-compare-btn', function (e) {
            e.preventDefault();

            const productId = $(this).data('product-id');

            $.ajax({
                url: '/compare/add',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload(); // Optional refresh
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });

        // Remove from Compare
        $(document).on('click', '.remove-from-compare-btn', function (e) {
            e.preventDefault();

            const productId = $(this).data('product-id');

            $.ajax({
                url: '/compare/remove',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload(); // Optional refresh
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });

        // Clear Compare List
        $(document).on('click', '#clear-compare-btn', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/compare/clear',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    refreshDataWithoutReload(); // Optional refresh
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong!';
                    alert(msg);
                }
            });
        });

        // Compare → Cart (Single)
        $(document).on('click', '.compare-add-to-cart-btn', function (e) {
            e.preventDefault();
            const productId = $(this).data('product-id');

            $.ajax({
                url: '/compare/add-to-cart',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    alert(res.message);
                    refreshDataWithoutReload(); // optional
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Something went wrong!');
                }
            });
        });

        // Compare → Cart (All)
        $(document).on('click', '.compare-add-all-to-cart-btn', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/compare/add-all-to-cart',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    alert(res.message);
                    refreshDataWithoutReload(); // optional
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Something went wrong!');
                }
            });
        });
        /*End compare JS*/

        /*Start Checkout JS*/
        function getAddressDetails() {
          $.ajax({
              url: '/customer/get-address',
              type: 'GET',
              success: function (res) {
                populateAddressForm('billing', res.billing);
                populateAddressForm('shipping', res.shipping);
              },
              error: function () {
                  console.error('Failed to load address data.');
              }
          });
        }

        function populateAddressForm(type, data) {
          if (!data) return;

          $(`#${type}_first_name`).val(data.first_name);
          $(`#${type}_last_name`).val(data.last_name);
          $(`#${type}_email`).val(data.email);
          $(`#${type}_phone`).val(data.phone_number);
          $(`#${type}_address`).val(data.address);
          $(`#${type}_city`).val(data.city);
          $(`#${type}_state`).val(data.state);
          $(`#${type}_zip`).val(data.pin_code);
          $(`#${type}_country`).val(data.country);
        }

        if (segment_1 == 'checkout') {
          const savedData = localStorage.getItem('checkout_form_data');
          if (savedData) {
              // const formData = JSON.parse(savedData);
              // Object.entries(formData).forEach(([key, value]) => {
              //     $(`[name="${key}"]`).val(value);
              // });

              // // If shipping checkbox was enabled, trigger the field show
              // if (formData['shipping_enabled']) {
              //     $('#ship_to_different_address').prop('checked', true).trigger('change');
              // }

              // // If agree_terms checkbox was enabled
              // if (formData['agree_terms']) {
              //     $('#agree_terms').prop('checked', true);
              // }

              // Optionally clear it once restored
              localStorage.removeItem('checkout_form_data');
          }

          getAddressDetails();
        }

        // Show/hide shipping fields
        $(document).on('change', '#ship_to_different_address', function (e) {
          if ($(this).is(':checked')) {
            $('#shipping-fields').slideDown();
          } else {
            $('#shipping-fields').slideUp();
          }
        });

        // Handle Checkout form submission
        $(document).on('submit', '#checkout-form', function (e) {
          e.preventDefault();

          let total_cart_item = $('.total_cart_item').val();
          if (total_cart_item <= 0) {
            alert('Cart is empty.');
            window.location.href = '/cart';
            return;
          }

          // Check if user is logged in
          let isLoggedIn = $('meta[name="customer_logged_in"]').attr('content');

          if (!isLoggedIn) {
              // Store form data
              const formData = {};
              $('#checkout-form').serializeArray().forEach(field => {
                  formData[field.name] = field.value;
              });
              
              localStorage.setItem('checkout_form_data', JSON.stringify(formData));
              localStorage.setItem('redirectUrl', $('meta[name="current-full-url"]').attr('content'));

              // Redirect to login
              if (confirm("You must be logged in to place an order. Click OK to log in.")) {
                window.location.href = "/sign-in";
              }
              return;
          }

          let form = $('#checkout-form');
          let errors = [];

          // Clear old errors
          $('.error-msg').text('');

          // Validation rules (only for fields you want extra validation on)
          let validationRules = {
            billing_first_name: { required: true },
            billing_last_name: { required: true },
            billing_email: { required: true, email: true },
            billing_phone: { required: true, number: true, minLength: 10, maxLength: 10 },
            billing_address: { required: true },
            billing_city: { required: true },
            billing_state: { required: true },
            billing_zip: { required: true, number: true, minLength: 4, maxLength: 6 },
            billing_country: { required: true },
            order_notes: { required: true },

            shipping_first_name: { required: true },
            shipping_last_name: { required: true },
            shipping_email: { required: true, email: true },
            shipping_phone: { required: true, number: true, minLength: 10, maxLength: 10 },
            shipping_address: { required: true },
            shipping_city: { required: true },
            shipping_state: { required: true },
            shipping_zip: { required: true, number: true, minLength: 4, maxLength: 6 },
            shipping_country: { required: true },
          };

          // Required fields
          let requiredFields = [
            'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
            'billing_address', 'billing_city', 'billing_state', 'billing_zip',
            'billing_country', 'order_notes'
          ];

          // Add shipping fields if shipping checkbox is checked
          if ($('#ship_to_different_address').is(':checked')) {
            requiredFields = requiredFields.concat([
              'shipping_first_name', 'shipping_last_name', 'shipping_email', 'shipping_phone',
              'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'
            ]);
          }

          // Loop and validate each field
          requiredFields.forEach(function (field) {
            let value = $(`[name="${field}"]`).val()?.trim() || '';
            let $error = $(`.error-msg[data-error-for="${field}"]`);
            let rules = validationRules[field] || {};

            // Format label
            let label = field.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());

            // Required check
            if ((rules.required || true) && value === '') {
              errors.push(field);
              $error.text(`${label} is required.`);
              return;
            }

            // Min length check
            if (rules.minLength && value.length < rules.minLength) {
              errors.push(field);
              $error.text(`${label} must be at least ${rules.minLength} characters.`);
              return;
            }

            // Max length check
            if (rules.maxLength && value.length > rules.maxLength) {
              errors.push(field);
              $error.text(`${label} must not exceed ${rules.maxLength} characters.`);
              return;
            }

            // Email format check
            if (rules.email) {
              let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(value)) {
                errors.push(field);
                $error.text(`Please enter a valid email address.`);
                return;
              }
            }

            // Number only check
            if (rules.number && !/^\d+$/.test(value)) {
              errors.push(field);
              $error.text(`${label} must be a valid number.`);
              return;
            }
          });

          // Terms and Conditions checkbox
          if (!$('#agree_terms').is(':checked')) {
            errors.push('agree_terms');
            $(`.error-msg[data-error-for="agree_terms"]`).text('You must agree to the terms.');
          }

          // Payment method validation
          let paymentMethod = $('input[name="payment_method"]:checked').val();
          if (!paymentMethod) {
            errors.push('payment_method');
            $(`.error-msg[data-error-for="payment_method"]`).text('Please select a payment method.');
          }

          if (errors.length > 0) {
            alert('Please fill all required fields.');
            $('html, body').animate({ scrollTop: $('.error-msg:visible:first').offset().top - 250 }, 400);
            return;
          }

          // Prepare form data
          let formData = form.serialize();

          // AJAX
          $.ajax({
            url: '/place-order', // You should define this route in web.php/api.php
            method: 'POST',
            data: formData + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
            success: function (response) {

              if (response?.stripe) {
                  const stripe = Stripe(response.stripe_public_key);
                  stripe.redirectToCheckout({ sessionId: response.session_id });
              } else if (response?.razorpay) {
                  startRazorpayPayment(response); // You'll define this function separately
              } else {
                  alert(response.message || 'Order placed!');
                  window.location.href = response?.redirect_url || '/thank-you';
              }

              // alert(response.message || 'Order placed successfully!');
              // refreshDataWithoutReload();
              // window.location.href = '/thank-you'; // Optional redirect
            },
            error: function (xhr) {
              const msg = xhr.responseJSON?.message || 'Something went wrong!';
              alert(msg);
            }
          });
        });

        function startRazorpayPayment(orderData, $redirect_url = '/thank-you') {
            const options = {
                key: orderData.key, // Razorpay key from your server/global settings
                amount: orderData.amount * 100, // Convert to paisa
                currency: orderData.currency,
                name: 'Order Payment',
                description: 'Payment for order #' + orderData.order_id,
                image: '/assets/images/logo.png', // Optional logo
                order_id: '', // If using Razorpay Orders API (optional)
                handler: function (response) {
                    // Payment success
                    $.ajax({
                        url: '/razorpay/payment-success',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            razorpay_payment_id: response.razorpay_payment_id,
                            order_id: orderData.order_id,
                        },
                        success: function (res) {
                            alert(res.message || 'Payment successful!');
                            window.location.href = $redirect_url;
                        },
                        error: function () {
                            alert('Payment verification failed.');
                        }
                    });
                },
                prefill: {
                    name: orderData.name,
                    email: orderData.email,
                    contact: orderData.contact,
                },
                theme: {
                    color: '#3399cc'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        }

        /*End Checkout JS*/

        /*Start Account Order Pay Now JS*/
        $(document).on('click', '.order-payment-link', function (e) {
            e.preventDefault();

            const paymentMethod = $(this).data('select-payment');
            const orderId = $(this).data('order-id');

            $.ajax({
                url: '/account-order/pay-now',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    payment_method: paymentMethod,
                    order_id: orderId,
                },
                success: function (response) {
                    refreshDataWithoutReload();
                    if (response.stripe) {
                        const stripe = Stripe(response.stripe_public_key);
                        stripe.redirectToCheckout({ sessionId: response.session_id });
                    } else if (response.razorpay) {
                        startRazorpayPayment(response, '/account-order');
                    } else {
                        alert(response.message || 'Payment initiated');
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Something went wrong!');
                }
            });
        });
        /*End Account Order Pay Now JS*/

        /*Start Account Update JS*/
        const hasPlaceholderImage = $('#profilePreview')?.attr('src')?.includes('user-placeholder.png');
        const hasImage = $('#profilePreview')?.attr('src') !== '' && hasPlaceholderImage;

        if (hasImage) {
          $('.profile-preview-container').show();
          $('#removeProfileImageBtn').show();
        } else {
          // $('.profile-preview-container').hide();
          // $('#removeProfileImageBtn').hide();
        }

        $(document).on('click', '#removeProfileImageBtn', function (e) {
          // Set placeholder (optional, if keeping visible)
          $('#profilePreview').attr('src', '/assets/images/user-placeholder.png');

          // Flag backend for removal
          $('#removeProfileImage').val('1');

          // Hide preview container
          $('.profile-preview-container').hide();
        });

        $(document).on('change', 'input[name="profile"]', function () {
          const file = this.files[0];

          if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
              $('#profilePreview').attr('src', e.target.result);
              $('.profile-preview-container').show();
              $('#removeProfileImageBtn').show();
              $('#removeProfileImage').val('0'); // reset removal flag
            };
            reader.readAsDataURL(file);
          } else {
            // If no file is selected
            $('#profilePreview').attr('src', '/assets/images/user-placeholder.png');
            $('#removeProfileImageBtn').hide();
            $('#removeProfileImage').val('1');
          }
        });

        $(document).on('click', '#profilePreview', function (e) {
          var imgSrc = $(this).attr('src');
          $('#modalProfileImage').attr('src', imgSrc);
          $('#profileImageModal').modal('show');
        });

        $(document).on('click', '.toggle-password', function () {
          let eyeThis = $(this);
          const targetInput = $(eyeThis.data('target'));
          const inputType = targetInput.attr('type') === 'password' ? 'text' : 'password';
          targetInput.attr('type', inputType);

          let passwordIcon = eyeThis.data('password-icon');
          let textIcon = eyeThis.data('text-icon');

          if (inputType === 'text') {
            eyeThis.css({
              background: `url(${textIcon}) no-repeat center center`,
              'background-size': 'contain'
            });
          } else {
            eyeThis.css({
              background: `url(${passwordIcon}) no-repeat center center`,
              'background-size': 'contain'
            });
          }
        })

        $(document).on('submit', '#accountDetailForm', function (e) {
          e.preventDefault();

          const form = $(this);
          const formData = new FormData(form[0]); // fixed
          const csrfToken = $('meta[name="csrf-token"]').attr('content');
          const responseBox = $('#accountResponse');

          responseBox.hide().removeClass('success error');

          $.ajax({
            url: '/customer/update-account',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
              responseBox
                .css({ color: '#186f65' }) // green
                .text(res.message)
                .fadeIn();

              refreshDataWithoutReload();
            },
            error: function (xhr) {
              const errors = xhr.responseJSON?.errors || {};

              // Clear previous error messages
              $('.form-group .error-message').remove();

              if (Object.keys(errors).length) {
                // Display individual field errors
                Object.entries(errors).forEach(([field, messages]) => {
                  const input = $(`[name="${field}"]`);
                  const errorEl = $('<div>')
                    .addClass('error-message text-danger mt-1')
                    .text(messages[0]);
                  input.closest('.form-group').append(errorEl);
                });

                // General response box (optional)
                responseBox
                  .css({ color: '#a94442' }) // red
                  .text('Please fill the correct details.')
                  .fadeIn();
              } else {
                responseBox
                  .css({ color: '#a94442' })
                  .text('Something went wrong.')
                  .fadeIn();
              }
            }
          });

          setTimeout(() => {
            responseBox.hide();
          }, 3000);
        });

        /*End Account Update JS*/

        /*Start Account Addresses JS*/
        $(document).on('submit', '#addressForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const data = form.serialize();
            const csrf = $('meta[name="csrf-token"]').attr('content');
            const responseBox = $('#addressResponse');

            // Clear previous errors
            responseBox.text('').hide().removeClass('error');
            form.find('.text-danger').remove(); // Remove existing error texts

            $.ajax({
                url: '/customer/save-address',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (res) {
                    responseBox.text(res.message).css('color', 'green').fadeIn();
                    // refreshDataWithoutReload();
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    const errors = response?.errors || {};
                    let generalError = response?.message || 'Something went wrong.';

                    // Field-wise error display
                    if (Object.keys(errors).length > 0) {
                        $.each(errors, function (field, messages) {
                            const input = form.find(`[name="${field}"]`);
                            input.after(`<div class="text-danger">${messages[0]}</div>`);
                        });
                    } else {
                        // Show general error
                        responseBox.text(generalError).css('color', 'red').fadeIn();
                    }
                }
            });

          setTimeout(() => {
            responseBox.hide();
          }, 3000);
        });
        /*End Account Addresses JS*/

        /*Start Download Invoice JS*/
        $(document).on('click', '#downloadInvoice', function () {
            const orderId = $(this).data('order-id');

            $.ajax({
                url: `/customer/invoice-link/${orderId}`,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // important for binary
                },
                success: function (data, status, xhr) {
                    const blob = new Blob([data], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;

                    // Optional: Extract filename from headers
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    let filename = 'invoice.pdf';
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        filename = disposition.split('filename=')[1].replace(/"/g, '');
                    }

                    a.download = filename;
                    a.click();
                    window.URL.revokeObjectURL(url);
                },
                error: function () {
                    alert('Failed to download invoice.');
                }
            });
        });

        /*End Download Invoice JS*/

      });


    },

    


  };
  eCommerce.init();

})(jQuery);
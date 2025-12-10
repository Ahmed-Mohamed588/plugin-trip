/**
 * Frontend JavaScript
 * File: assets/js/frontend.js
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initTripFilters();
        initHotelFilters();
        initImageGallery();
        initSmoothScroll();
    });
    
    /**
     * Initialize Trip Filters
     */
    function initTripFilters() {
        // AJAX filter for trips
        $(document).on('change', '.tbp-trip-filter select', function() {
            var $container = $(this).closest('.tbp-trip-filter-container');
            var $resultsContainer = $container.find('.tbp-trips-results');
            var cityId = $container.find('.tbp-filter-city').val();
            var seasonId = $container.find('.tbp-filter-season').val();
            
            // Show loading
            $resultsContainer.addClass('tbp-loading');
            
            $.ajax({
                url: tbpData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'tbp_filter_trips',
                    nonce: tbpData.nonce,
                    city_id: cityId,
                    season_id: seasonId
                },
                success: function(response) {
                    if (response.success) {
                        renderTrips(response.data, $resultsContainer);
                    }
                },
                error: function() {
                    $resultsContainer.html('<p>حدث خطأ أثناء تحميل الرحلات</p>');
                },
                complete: function() {
                    $resultsContainer.removeClass('tbp-loading');
                }
            });
        });
    }
    
    /**
     * Render trips HTML
     */
    function renderTrips(trips, $container) {
        if (trips.length === 0) {
            $container.html('<p>لا توجد رحلات متاحة</p>');
            return;
        }
        
        var html = '<div class="tbp-trips-grid tbp-columns-3">';
        
        trips.forEach(function(trip) {
            html += '<div class="tbp-trip-card">';
            
            if (trip.image) {
                html += '<div class="tbp-trip-image">';
                html += '<a href="' + trip.url + '">';
                html += '<img src="' + trip.image + '" alt="' + trip.title + '">';
                html += '</a>';
                html += '</div>';
            }
            
            html += '<div class="tbp-trip-card-content">';
            html += '<h3 class="tbp-trip-title"><a href="' + trip.url + '">' + trip.title + '</a></h3>';
            
            html += '<div class="tbp-trip-meta">';
            if (trip.city) {
                html += '<div class="tbp-trip-city"><i class="eicon-map-pin"></i> ' + trip.city + '</div>';
            }
            if (trip.duration) {
                html += '<div class="tbp-trip-duration"><i class="eicon-clock"></i> ' + trip.duration + ' يوم</div>';
            }
            html += '</div>';
            
            if (trip.price) {
                html += '<div class="tbp-trip-price">' + Number(trip.price).toLocaleString() + ' جنيه</div>';
            }
            
            html += '<a href="' + trip.url + '" class="tbp-trip-button">حجز الرحلة</a>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        
        $container.html(html);
    }
    
    /**
     * Initialize Hotel Filters
     */
    function initHotelFilters() {
        $(document).on('change', '.tbp-hotel-star-filter', function() {
            var stars = $(this).val();
            var $hotels = $('.tbp-hotel-card');
            
            if (stars === '') {
                $hotels.show();
            } else {
                $hotels.hide();
                $hotels.filter('[data-stars="' + stars + '"]').show();
            }
        });
    }
    
    /**
     * Initialize Image Gallery (Lightbox)
     */
    function initImageGallery() {
        // Simple lightbox for gallery images
        $(document).on('click', '.tbp-gallery-image img', function(e) {
            e.preventDefault();
            
            var $img = $(this);
            var src = $img.attr('src').replace('-150x150', '');
            
            // Create lightbox
            var lightbox = '<div class="tbp-lightbox">';
            lightbox += '<div class="tbp-lightbox-overlay"></div>';
            lightbox += '<div class="tbp-lightbox-content">';
            lightbox += '<span class="tbp-lightbox-close">&times;</span>';
            lightbox += '<img src="' + src + '" alt="">';
            lightbox += '</div>';
            lightbox += '</div>';
            
            $('body').append(lightbox);
            $('.tbp-lightbox').fadeIn(300);
        });
        
        // Close lightbox
        $(document).on('click', '.tbp-lightbox-close, .tbp-lightbox-overlay', function() {
            $('.tbp-lightbox').fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Close on ESC key
        $(document).on('keyup', function(e) {
            if (e.keyCode === 27) {
                $('.tbp-lightbox-close').click();
            }
        });
    }
    
    /**
     * Initialize Smooth Scroll
     */
    function initSmoothScroll() {
        $(document).on('click', 'a[href^="#"]', function(e) {
            var target = $(this).attr('href');
            
            if (target !== '#' && $(target).length) {
                e.preventDefault();
                
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 500);
            }
        });
    }
    
    /**
     * Lazy Load Images
     */
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            var lazyImages = document.querySelectorAll('img[data-src]');
            
            var imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }
    
    /**
     * Card Hover Animations
     */
    function initCardAnimations() {
        $('.tbp-trip-card, .tbp-city-card, .tbp-hotel-card').hover(
            function() {
                $(this).addClass('hovered');
            },
            function() {
                $(this).removeClass('hovered');
            }
        );
    }
    
    /**
     * Price Formatter
     */
    function formatPrice(price) {
        return Number(price).toLocaleString('ar-EG');
    }
    
    /**
     * Show More Description
     */
    $(document).on('click', '.tbp-show-more', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var $content = $this.prev('.tbp-description-content');
        
        if ($content.hasClass('expanded')) {
            $content.removeClass('expanded');
            $this.text('عرض المزيد');
        } else {
            $content.addClass('expanded');
            $this.text('عرض أقل');
        }
    });
    
    /**
     * Stars Rating Display
     */
    function displayStars(count, container) {
        var stars = '';
        for (var i = 0; i < count; i++) {
            stars += '★';
        }
        $(container).html(stars);
    }
    
    /**
     * Back to Top Button
     */
    function initBackToTop() {
        var $backToTop = $('<button class="tbp-back-to-top" title="العودة للأعلى">↑</button>');
        $('body').append($backToTop);
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $backToTop.fadeIn();
            } else {
                $backToTop.fadeOut();
            }
        });
        
        $backToTop.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    }
    
    // Initialize back to top (optional)
    // initBackToTop();
    
})(jQuery);
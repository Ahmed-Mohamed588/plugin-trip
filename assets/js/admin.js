/**
 * Admin JavaScript
 * File: assets/js/admin.js
 */

(function($) {
    'use strict';
    
    var hotelIndex = 0;
    
    $(document).ready(function() {
        initHotels();
        initGallery();
    });
    
    /**
     * Initialize Hotels Management
     */
    function initHotels() {
        // Set initial hotel index
        hotelIndex = $('#tbp-hotels-wrapper .tbp-hotel-item').length;
        
        // Add hotel
        $(document).on('click', '.tbp-add-hotel', function(e) {
            e.preventDefault();
            
            var template = $('#tbp-hotel-template').html();
            template = template.replace(/\{\{INDEX\}\}/g, hotelIndex);
            
            $('#tbp-hotels-wrapper').append(template);
            updateHotelNumbers();
            hotelIndex++;
        });
        
        // Remove hotel
        $(document).on('click', '.tbp-remove-hotel', function(e) {
            e.preventDefault();
            
            if (confirm('هل أنت متأكد من حذف هذا الفندق؟')) {
                $(this).closest('.tbp-hotel-item').remove();
                updateHotelNumbers();
            }
        });
        
        // Make hotels sortable
        if ($.fn.sortable) {
            $('#tbp-hotels-wrapper').sortable({
                handle: '.tbp-hotel-header',
                placeholder: 'tbp-hotel-placeholder',
                update: function() {
                    updateHotelNumbers();
                }
            });
        }
    }
    
    /**
     * Update hotel numbers
     */
    function updateHotelNumbers() {
        $('#tbp-hotels-wrapper .tbp-hotel-item').each(function(index) {
            $(this).find('.hotel-number').text(index + 1);
        });
    }
    
    /**
     * Initialize Gallery Management
     */
    function initGallery() {
        var galleryFrame;
        var currentGalleryInput;
        var currentGalleryContainer;
        var isHotelGallery = false;
        
        // Add gallery images
        $(document).on('click', '.tbp-add-gallery-images', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            currentGalleryContainer = $button.closest('.tbp-gallery-container');
            currentGalleryInput = currentGalleryContainer.find('input[type="hidden"]');
            isHotelGallery = false;
            
            openGalleryFrame();
        });
        
        // Add hotel gallery images
        $(document).on('click', '.tbp-add-hotel-images', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            currentGalleryContainer = $button.closest('.tbp-gallery-container');
            currentGalleryInput = currentGalleryContainer.find('.tbp-hotel-gallery');
            isHotelGallery = true;
            
            openGalleryFrame();
        });
        
        function openGalleryFrame() {
            // If the frame already exists, reopen it
            if (galleryFrame) {
                galleryFrame.open();
                return;
            }
            
            // Create the frame
            galleryFrame = wp.media({
                title: 'اختر الصور',
                button: {
                    text: 'إضافة الصور'
                },
                multiple: true
            });
            
            // When images are selected
            galleryFrame.on('select', function() {
                var selection = galleryFrame.state().get('selection');
                var ids = [];
                var imagesHtml = '';
                
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    
                    imagesHtml += '<div class="tbp-gallery-image">';
                    imagesHtml += '<img src="' + attachment.sizes.thumbnail.url + '" />';
                    imagesHtml += '<a href="#" class="tbp-remove-image" data-id="' + attachment.id + '">×</a>';
                    imagesHtml += '</div>';
                });
                
                // Get existing IDs
                var existingIds = currentGalleryInput.val();
                if (existingIds) {
                    ids = existingIds.split(',').concat(ids);
                }
                
                // Update input and display
                currentGalleryInput.val(ids.join(','));
                currentGalleryContainer.find('.tbp-gallery-images').append(imagesHtml);
            });
            
            // Open the frame
            galleryFrame.open();
        }
        
        // Remove gallery image
        $(document).on('click', '.tbp-remove-image', function(e) {
            e.preventDefault();
            
            var $image = $(this).closest('.tbp-gallery-image');
            var imageId = $(this).data('id');
            var $container = $image.closest('.tbp-gallery-container');
            var $input = $container.find('input[type="hidden"]');
            
            // Remove image
            $image.remove();
            
            // Update IDs
            var ids = $input.val().split(',');
            ids = ids.filter(function(id) {
                return id != imageId;
            });
            $input.val(ids.join(','));
        });
    }
    
})(jQuery);
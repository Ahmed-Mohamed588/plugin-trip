<?php
/**
 * Custom Metaboxes (بدون ACF)
 * File: includes/class-metaboxes.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Metaboxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        add_action('save_post', array($this, 'save_city_meta'), 10, 2);
        add_action('save_post', array($this, 'save_trip_meta'), 10, 2);
    }
    
    /**
     * Add Metaboxes
     */
    public function add_metaboxes() {
        // City Metaboxes
        add_meta_box(
            'tbp_city_details',
            __('تفاصيل المدينة', 'travel-booking'),
            array($this, 'render_city_details'),
            'tbp_city',
            'normal',
            'high'
        );
        
        add_meta_box(
            'tbp_city_hotels',
            __('الفنادق', 'travel-booking'),
            array($this, 'render_city_hotels'),
            'tbp_city',
            'normal',
            'high'
        );
        
        // Trip Metaboxes
        add_meta_box(
            'tbp_trip_details',
            __('تفاصيل الرحلة', 'travel-booking'),
            array($this, 'render_trip_details'),
            'tbp_trip',
            'normal',
            'high'
        );
    }
    
    /**
     * Render City Details Metabox
     */
    public function render_city_details($post) {
        wp_nonce_field('tbp_city_meta', 'tbp_city_meta_nonce');
        
        $description = get_post_meta($post->ID, '_tbp_city_description', true);
        $latitude = get_post_meta($post->ID, '_tbp_city_latitude', true);
        $longitude = get_post_meta($post->ID, '_tbp_city_longitude', true);
        $gallery = get_post_meta($post->ID, '_tbp_city_gallery', true);
        $gallery_ids = !empty($gallery) ? explode(',', $gallery) : array();
        ?>
        
        <div class="tbp-metabox">
            <div class="tbp-field">
                <label><?php _e('وصف المدينة', 'travel-booking'); ?></label>
                <?php
                wp_editor($description, 'tbp_city_description', array(
                    'textarea_name' => 'tbp_city_description',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                ));
                ?>
            </div>
            
            <div class="tbp-field-row">
                <div class="tbp-field tbp-field-half">
                    <label><?php _e('خط العرض', 'travel-booking'); ?></label>
                    <input type="text" name="tbp_city_latitude" value="<?php echo esc_attr($latitude); ?>" placeholder="30.0444" />
                </div>
                
                <div class="tbp-field tbp-field-half">
                    <label><?php _e('خط الطول', 'travel-booking'); ?></label>
                    <input type="text" name="tbp_city_longitude" value="<?php echo esc_attr($longitude); ?>" placeholder="31.2357" />
                </div>
            </div>
            
            <div class="tbp-field">
                <label><?php _e('معرض صور المدينة', 'travel-booking'); ?></label>
                <div class="tbp-gallery-container">
                    <div class="tbp-gallery-images">
                        <?php if (!empty($gallery_ids)) : ?>
                            <?php foreach ($gallery_ids as $img_id) : ?>
                                <div class="tbp-gallery-image">
                                    <?php echo wp_get_attachment_image($img_id, 'thumbnail'); ?>
                                    <a href="#" class="tbp-remove-image" data-id="<?php echo esc_attr($img_id); ?>">×</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button tbp-add-gallery-images"><?php _e('إضافة صور', 'travel-booking'); ?></button>
                    <input type="hidden" name="tbp_city_gallery" value="<?php echo esc_attr($gallery); ?>" />
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render City Hotels Metabox
     */
    public function render_city_hotels($post) {
        $hotels = get_post_meta($post->ID, '_tbp_city_hotels', true);
        $hotels = !empty($hotels) ? $hotels : array();
        ?>
        
        <div class="tbp-metabox">
            <div id="tbp-hotels-wrapper">
                <?php foreach ($hotels as $index => $hotel) : ?>
                    <?php $this->render_hotel_item($index, $hotel); ?>
                <?php endforeach; ?>
            </div>
            
            <button type="button" class="button button-primary tbp-add-hotel">
                <?php _e('إضافة فندق', 'travel-booking'); ?>
            </button>
        </div>
        
        <script type="text/html" id="tbp-hotel-template">
            <?php $this->render_hotel_item('{{INDEX}}', array()); ?>
        </script>
        <?php
    }
    
    /**
     * Render Single Hotel Item
     */
    private function render_hotel_item($index, $hotel = array()) {
        $hotel = wp_parse_args($hotel, array(
            'name' => '',
            'stars' => '4',
            'description' => '',
            'gallery' => '',
            'price' => '',
            'amenities' => array(),
            'phone' => '',
            'address' => '',
        ));
        
        $gallery_ids = !empty($hotel['gallery']) ? explode(',', $hotel['gallery']) : array();
        ?>
        <div class="tbp-hotel-item" data-index="<?php echo esc_attr($index); ?>">
            <div class="tbp-hotel-header">
                <h3><?php _e('فندق', 'travel-booking'); ?> #<span class="hotel-number"><?php echo esc_html($index + 1); ?></span></h3>
                <button type="button" class="button button-small tbp-remove-hotel"><?php _e('حذف', 'travel-booking'); ?></button>
            </div>
            
            <div class="tbp-hotel-content">
                <div class="tbp-field-row">
                    <div class="tbp-field tbp-field-two-thirds">
                        <label><?php _e('اسم الفندق', 'travel-booking'); ?> <span class="required">*</span></label>
                        <input type="text" name="tbp_hotels[<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($hotel['name']); ?>" required />
                    </div>
                    
                    <div class="tbp-field tbp-field-third">
                        <label><?php _e('عدد النجوم', 'travel-booking'); ?></label>
                        <select name="tbp_hotels[<?php echo esc_attr($index); ?>][stars]">
                            <option value="3" <?php selected($hotel['stars'], '3'); ?>>3 <?php _e('نجوم', 'travel-booking'); ?></option>
                            <option value="4" <?php selected($hotel['stars'], '4'); ?>>4 <?php _e('نجوم', 'travel-booking'); ?></option>
                            <option value="5" <?php selected($hotel['stars'], '5'); ?>>5 <?php _e('نجوم', 'travel-booking'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="tbp-field">
                    <label><?php _e('وصف الفندق', 'travel-booking'); ?></label>
                    <textarea name="tbp_hotels[<?php echo esc_attr($index); ?>][description]" rows="3"><?php echo esc_textarea($hotel['description']); ?></textarea>
                </div>
                
                <div class="tbp-field">
                    <label><?php _e('صور الفندق', 'travel-booking'); ?></label>
                    <div class="tbp-gallery-container">
                        <div class="tbp-gallery-images">
                            <?php if (!empty($gallery_ids)) : ?>
                                <?php foreach ($gallery_ids as $img_id) : ?>
                                    <div class="tbp-gallery-image">
                                        <?php echo wp_get_attachment_image($img_id, 'thumbnail'); ?>
                                        <a href="#" class="tbp-remove-image" data-id="<?php echo esc_attr($img_id); ?>">×</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button tbp-add-hotel-images" data-index="<?php echo esc_attr($index); ?>">
                            <?php _e('إضافة صور', 'travel-booking'); ?>
                        </button>
                        <input type="hidden" name="tbp_hotels[<?php echo esc_attr($index); ?>][gallery]" value="<?php echo esc_attr($hotel['gallery']); ?>" class="tbp-hotel-gallery" />
                    </div>
                </div>
                
                <div class="tbp-field-row">
                    <div class="tbp-field tbp-field-half">
                        <label><?php _e('السعر لليلة (جنيه)', 'travel-booking'); ?></label>
                        <input type="number" name="tbp_hotels[<?php echo esc_attr($index); ?>][price]" value="<?php echo esc_attr($hotel['price']); ?>" min="0" step="1" />
                    </div>
                    
                    <div class="tbp-field tbp-field-half">
                        <label><?php _e('رقم الهاتف', 'travel-booking'); ?></label>
                        <input type="text" name="tbp_hotels[<?php echo esc_attr($index); ?>][phone]" value="<?php echo esc_attr($hotel['phone']); ?>" />
                    </div>
                </div>
                
                <div class="tbp-field">
                    <label><?php _e('العنوان', 'travel-booking'); ?></label>
                    <input type="text" name="tbp_hotels[<?php echo esc_attr($index); ?>][address]" value="<?php echo esc_attr($hotel['address']); ?>" />
                </div>
                
                <div class="tbp-field">
                    <label><?php _e('المرافق', 'travel-booking'); ?></label>
                    <div class="tbp-amenities">
                        <?php
                        $amenities_options = array(
                            'wifi' => __('واي فاي مجاني', 'travel-booking'),
                            'pool' => __('مسبح', 'travel-booking'),
                            'gym' => __('صالة رياضية', 'travel-booking'),
                            'spa' => __('سبا', 'travel-booking'),
                            'restaurant' => __('مطعم', 'travel-booking'),
                            'parking' => __('موقف سيارات', 'travel-booking'),
                            'ac' => __('تكييف', 'travel-booking'),
                            'tv' => __('تلفزيون', 'travel-booking'),
                        );
                        
                        foreach ($amenities_options as $key => $label) :
                            $checked = in_array($key, (array)$hotel['amenities']);
                        ?>
                            <label class="tbp-checkbox">
                                <input type="checkbox" name="tbp_hotels[<?php echo esc_attr($index); ?>][amenities][]" value="<?php echo esc_attr($key); ?>" <?php checked($checked); ?> />
                                <?php echo esc_html($label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render Trip Details Metabox
     */
    public function render_trip_details($post) {
        wp_nonce_field('tbp_trip_meta', 'tbp_trip_meta_nonce');
        
        $city = get_post_meta($post->ID, '_tbp_trip_city', true);
        $price = get_post_meta($post->ID, '_tbp_trip_price', true);
        $duration = get_post_meta($post->ID, '_tbp_trip_duration', true);
        $program = get_post_meta($post->ID, '_tbp_trip_program', true);
        $includes = get_post_meta($post->ID, '_tbp_trip_includes', true);
        $excludes = get_post_meta($post->ID, '_tbp_trip_excludes', true);
        $max_people = get_post_meta($post->ID, '_tbp_trip_max_people', true);
        $gallery = get_post_meta($post->ID, '_tbp_trip_gallery', true);
        $gallery_ids = !empty($gallery) ? explode(',', $gallery) : array();
        
        // Get all cities
        $cities = get_posts(array(
            'post_type' => 'tbp_city',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        ?>
        
        <div class="tbp-metabox">
            <div class="tbp-field-row">
                <div class="tbp-field tbp-field-half">
                    <label><?php _e('المدينة', 'travel-booking'); ?> <span class="required">*</span></label>
                    <select name="tbp_trip_city" required>
                        <option value=""><?php _e('اختر المدينة', 'travel-booking'); ?></option>
                        <?php foreach ($cities as $city_post) : ?>
                            <option value="<?php echo esc_attr($city_post->ID); ?>" <?php selected($city, $city_post->ID); ?>>
                                <?php echo esc_html($city_post->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="tbp-field tbp-field-quarter">
                    <label><?php _e('السعر (جنيه)', 'travel-booking'); ?> <span class="required">*</span></label>
                    <input type="number" name="tbp_trip_price" value="<?php echo esc_attr($price); ?>" min="0" step="1" required />
                </div>
                
                <div class="tbp-field tbp-field-quarter">
                    <label><?php _e('المدة (أيام)', 'travel-booking'); ?> <span class="required">*</span></label>
                    <input type="number" name="tbp_trip_duration" value="<?php echo esc_attr($duration); ?>" min="1" step="1" required />
                </div>
            </div>
            
            <div class="tbp-field">
                <label><?php _e('البرنامج التفصيلي', 'travel-booking'); ?></label>
                <?php
                wp_editor($program, 'tbp_trip_program', array(
                    'textarea_name' => 'tbp_trip_program',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                ));
                ?>
            </div>
            
            <div class="tbp-field-row">
                <div class="tbp-field tbp-field-half">
                    <label><?php _e('يشمل', 'travel-booking'); ?></label>
                    <textarea name="tbp_trip_includes" rows="5"><?php echo esc_textarea($includes); ?></textarea>
                    <p class="description"><?php _e('مثال: الإقامة، وجبات الطعام، المواصلات', 'travel-booking'); ?></p>
                </div>
                
                <div class="tbp-field tbp-field-half">
                    <label><?php _e('لا يشمل', 'travel-booking'); ?></label>
                    <textarea name="tbp_trip_excludes" rows="5"><?php echo esc_textarea($excludes); ?></textarea>
                    <p class="description"><?php _e('مثال: التأشيرات، التأمين الشخصي', 'travel-booking'); ?></p>
                </div>
            </div>
            
            <div class="tbp-field">
                <label><?php _e('الحد الأقصى للأشخاص', 'travel-booking'); ?></label>
                <input type="number" name="tbp_trip_max_people" value="<?php echo esc_attr($max_people); ?>" min="1" step="1" />
            </div>
            
            <div class="tbp-field">
                <label><?php _e('معرض صور الرحلة', 'travel-booking'); ?></label>
                <div class="tbp-gallery-container">
                    <div class="tbp-gallery-images">
                        <?php if (!empty($gallery_ids)) : ?>
                            <?php foreach ($gallery_ids as $img_id) : ?>
                                <div class="tbp-gallery-image">
                                    <?php echo wp_get_attachment_image($img_id, 'thumbnail'); ?>
                                    <a href="#" class="tbp-remove-image" data-id="<?php echo esc_attr($img_id); ?>">×</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button tbp-add-gallery-images"><?php _e('إضافة صور', 'travel-booking'); ?></button>
                    <input type="hidden" name="tbp_trip_gallery" value="<?php echo esc_attr($gallery); ?>" />
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save City Meta
     */
    public function save_city_meta($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['tbp_city_meta_nonce']) || !wp_verify_nonce($_POST['tbp_city_meta_nonce'], 'tbp_city_meta')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save city details
        if (isset($_POST['tbp_city_description'])) {
            update_post_meta($post_id, '_tbp_city_description', wp_kses_post($_POST['tbp_city_description']));
        }
        
        if (isset($_POST['tbp_city_latitude'])) {
            update_post_meta($post_id, '_tbp_city_latitude', sanitize_text_field($_POST['tbp_city_latitude']));
        }
        
        if (isset($_POST['tbp_city_longitude'])) {
            update_post_meta($post_id, '_tbp_city_longitude', sanitize_text_field($_POST['tbp_city_longitude']));
        }
        
        if (isset($_POST['tbp_city_gallery'])) {
            update_post_meta($post_id, '_tbp_city_gallery', sanitize_text_field($_POST['tbp_city_gallery']));
        }
        
        // Save hotels
        if (isset($_POST['tbp_hotels'])) {
            $hotels = array();
            foreach ($_POST['tbp_hotels'] as $hotel) {
                $hotels[] = array(
                    'name' => sanitize_text_field($hotel['name']),
                    'stars' => sanitize_text_field($hotel['stars']),
                    'description' => sanitize_textarea_field($hotel['description']),
                    'gallery' => sanitize_text_field($hotel['gallery']),
                    'price' => absint($hotel['price']),
                    'amenities' => isset($hotel['amenities']) ? array_map('sanitize_text_field', $hotel['amenities']) : array(),
                    'phone' => sanitize_text_field($hotel['phone']),
                    'address' => sanitize_text_field($hotel['address']),
                );
            }
            update_post_meta($post_id, '_tbp_city_hotels', $hotels);
        } else {
            delete_post_meta($post_id, '_tbp_city_hotels');
        }
    }
    
    /**
     * Save Trip Meta
     */
    public function save_trip_meta($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['tbp_trip_meta_nonce']) || !wp_verify_nonce($_POST['tbp_trip_meta_nonce'], 'tbp_trip_meta')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save trip details
        if (isset($_POST['tbp_trip_city'])) {
            update_post_meta($post_id, '_tbp_trip_city', absint($_POST['tbp_trip_city']));
        }
        
        if (isset($_POST['tbp_trip_price'])) {
            update_post_meta($post_id, '_tbp_trip_price', absint($_POST['tbp_trip_price']));
        }
        
        if (isset($_POST['tbp_trip_duration'])) {
            update_post_meta($post_id, '_tbp_trip_duration', absint($_POST['tbp_trip_duration']));
        }
        
        if (isset($_POST['tbp_trip_program'])) {
            update_post_meta($post_id, '_tbp_trip_program', wp_kses_post($_POST['tbp_trip_program']));
        }
        
        if (isset($_POST['tbp_trip_includes'])) {
            update_post_meta($post_id, '_tbp_trip_includes', sanitize_textarea_field($_POST['tbp_trip_includes']));
        }
        
        if (isset($_POST['tbp_trip_excludes'])) {
            update_post_meta($post_id, '_tbp_trip_excludes', sanitize_textarea_field($_POST['tbp_trip_excludes']));
        }
        
        if (isset($_POST['tbp_trip_max_people'])) {
            update_post_meta($post_id, '_tbp_trip_max_people', absint($_POST['tbp_trip_max_people']));
        }
        
        if (isset($_POST['tbp_trip_gallery'])) {
            update_post_meta($post_id, '_tbp_trip_gallery', sanitize_text_field($_POST['tbp_trip_gallery']));
        }
    }
}
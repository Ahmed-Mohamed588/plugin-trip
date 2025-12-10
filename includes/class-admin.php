<?php
/**
 * Admin Interface
 * File: includes/class-admin.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_filter('manage_tbp_city_posts_columns', array($this, 'city_columns'));
        add_action('manage_tbp_city_posts_custom_column', array($this, 'city_column_content'), 10, 2);
        add_filter('manage_tbp_trip_posts_columns', array($this, 'trip_columns'));
        add_action('manage_tbp_trip_posts_custom_column', array($this, 'trip_column_content'), 10, 2);
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('الرحلات', 'travel-booking'),
            __('الرحلات', 'travel-booking'),
            'manage_options',
            'travel-booking',
            array($this, 'dashboard_page'),
            'dashicons-palmtree',
            25
        );
        
        add_submenu_page(
            'travel-booking',
            __('لوحة التحكم', 'travel-booking'),
            __('لوحة التحكم', 'travel-booking'),
            'manage_options',
            'travel-booking',
            array($this, 'dashboard_page')
        );
    }
    
    public function dashboard_page() {
        $cities_count = wp_count_posts('tbp_city')->publish;
        $trips_count = wp_count_posts('tbp_trip')->publish;
        
        // Count total hotels
        $cities = get_posts(array(
            'post_type' => 'tbp_city',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));
        
        $hotels_count = 0;
        foreach ($cities as $city_id) {
            $hotels = get_field('city_hotels', $city_id);
            if ($hotels) {
                $hotels_count += count($hotels);
            }
        }
        ?>
        <div class="wrap">
            <h1><?php _e('لوحة تحكم الرحلات', 'travel-booking'); ?></h1>
            
            <div class="tbp-dashboard">
                <div class="tbp-stats">
                    <div class="tbp-stat-box">
                        <div class="tbp-stat-icon">
                            <span class="dashicons dashicons-location-alt"></span>
                        </div>
                        <div class="tbp-stat-content">
                            <h3><?php echo esc_html($cities_count); ?></h3>
                            <p><?php _e('مدينة', 'travel-booking'); ?></p>
                        </div>
                    </div>
                    
                    <div class="tbp-stat-box">
                        <div class="tbp-stat-icon">
                            <span class="dashicons dashicons-airplane"></span>
                        </div>
                        <div class="tbp-stat-content">
                            <h3><?php echo esc_html($trips_count); ?></h3>
                            <p><?php _e('رحلة', 'travel-booking'); ?></p>
                        </div>
                    </div>
                    
                    <div class="tbp-stat-box">
                        <div class="tbp-stat-icon">
                            <span class="dashicons dashicons-building"></span>
                        </div>
                        <div class="tbp-stat-content">
                            <h3><?php echo esc_html($hotels_count); ?></h3>
                            <p><?php _e('فندق', 'travel-booking'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="tbp-quick-links">
                    <h2><?php _e('روابط سريعة', 'travel-booking'); ?></h2>
                    <div class="tbp-links-grid">
                        <a href="<?php echo admin_url('post-new.php?post_type=tbp_city'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('إضافة مدينة جديدة', 'travel-booking'); ?>
                        </a>
                        <a href="<?php echo admin_url('post-new.php?post_type=tbp_trip'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('إضافة رحلة جديدة', 'travel-booking'); ?>
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=tbp_city'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-list-view"></span>
                            <?php _e('عرض كل المدن', 'travel-booking'); ?>
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=tbp_trip'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-list-view"></span>
                            <?php _e('عرض كل الرحلات', 'travel-booking'); ?>
                        </a>
                        <a href="<?php echo admin_url('edit-tags.php?taxonomy=tbp_season&post_type=tbp_trip'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-tag"></span>
                            <?php _e('إدارة المواسم', 'travel-booking'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=travel-booking-settings'); ?>" class="tbp-quick-link">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php _e('الإعدادات', 'travel-booking'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="tbp-help-box">
                    <h2><?php _e('هل تحتاج مساعدة؟', 'travel-booking'); ?></h2>
                    <p><?php _e('راجع التوثيق الكامل للبلاجن للحصول على تعليمات مفصلة حول الاستخدام والتخصيص.', 'travel-booking'); ?></p>
                    <a href="#" class="button button-primary"><?php _e('عرض التوثيق', 'travel-booking'); ?></a>
                </div>
            </div>
        </div>
        
        <style>
            .tbp-dashboard {
                max-width: 1200px;
            }
            .tbp-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin: 30px 0;
            }
            .tbp-stat-box {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 30px;
                display: flex;
                align-items: center;
                gap: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .tbp-stat-icon {
                width: 60px;
                height: 60px;
                background: #007bff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .tbp-stat-icon .dashicons {
                font-size: 30px;
                color: #fff;
                width: 30px;
                height: 30px;
            }
            .tbp-stat-content h3 {
                margin: 0;
                font-size: 36px;
                font-weight: 700;
                color: #333;
            }
            .tbp-stat-content p {
                margin: 5px 0 0;
                color: #666;
                font-size: 14px;
            }
            .tbp-quick-links {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 30px;
                margin: 20px 0;
            }
            .tbp-quick-links h2 {
                margin: 0 0 20px;
            }
            .tbp-links-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
            .tbp-quick-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 15px 20px;
                background: #f8f9fa;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                text-decoration: none;
                color: #333;
                transition: all 0.3s ease;
            }
            .tbp-quick-link:hover {
                background: #007bff;
                color: #fff;
                border-color: #007bff;
                transform: translateY(-2px);
            }
            .tbp-quick-link .dashicons {
                font-size: 20px;
            }
            .tbp-help-box {
                background: #e7f3ff;
                border: 1px solid #007bff;
                border-radius: 8px;
                padding: 30px;
                margin: 20px 0;
            }
            .tbp-help-box h2 {
                margin: 0 0 10px;
                color: #007bff;
            }
            @media (max-width: 782px) {
                .tbp-stats,
                .tbp-links-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <?php
    }
    
    public function city_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['hotels_count'] = __('عدد الفنادق', 'travel-booking');
        $new_columns['trips_count'] = __('عدد الرحلات', 'travel-booking');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    public function city_column_content($column, $post_id) {
        switch ($column) {
            case 'hotels_count':
                $hotels = get_field('city_hotels', $post_id);
                echo $hotels ? count($hotels) : '0';
                break;
                
            case 'trips_count':
                $trips = get_posts(array(
                    'post_type' => 'tbp_trip',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'trip_city',
                            'value' => $post_id,
                            'compare' => '=',
                        ),
                    ),
                    'fields' => 'ids',
                ));
                echo count($trips);
                break;
        }
    }
    
    public function trip_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['city'] = __('المدينة', 'travel-booking');
        $new_columns['season'] = __('الموسم', 'travel-booking');
        $new_columns['price'] = __('السعر', 'travel-booking');
        $new_columns['duration'] = __('المدة', 'travel-booking');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    public function trip_column_content($column, $post_id) {
        switch ($column) {
            case 'city':
                $city_id = get_field('trip_city', $post_id);
                if ($city_id) {
                    echo '<a href="' . get_edit_post_link($city_id) . '">' . get_the_title($city_id) . '</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'season':
                $terms = get_the_terms($post_id, 'tbp_season');
                if ($terms && !is_wp_error($terms)) {
                    $seasons = array();
                    foreach ($terms as $term) {
                        $seasons[] = $term->name;
                    }
                    echo implode(', ', $seasons);
                } else {
                    echo '—';
                }
                break;
                
            case 'price':
                $price = get_field('trip_price', $post_id);
                echo $price ? number_format($price) . ' ' . __('جنيه', 'travel-booking') : '—';
                break;
                
            case 'duration':
                $duration = get_field('trip_duration', $post_id);
                echo $duration ? $duration . ' ' . __('يوم', 'travel-booking') : '—';
                break;
        }
    }
    
    public function admin_notices() {
        // Check if ACF is installed
        if (!class_exists('ACF')) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php _e('تحذير:', 'travel-booking'); ?></strong>
                    <?php _e('بلاجن Travel Booking Pro يتطلب Advanced Custom Fields Pro للعمل بشكل صحيح.', 'travel-booking'); ?>
                    <a href="https://www.advancedcustomfields.com/pro/" target="_blank"><?php _e('احصل على ACF Pro', 'travel-booking'); ?></a>
                </p>
            </div>
            <?php
        }
    }
}
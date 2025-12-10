<?php
/**
 * Taxonomies Registration
 * File: includes/class-taxonomies.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Taxonomies {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register'));
    }
    
    public static function register() {
        self::register_trip_season();
    }
    
    private static function register_trip_season() {
        $labels = array(
            'name'              => __('المواسم', 'travel-booking'),
            'singular_name'     => __('موسم', 'travel-booking'),
            'search_items'      => __('بحث في المواسم', 'travel-booking'),
            'all_items'         => __('كل المواسم', 'travel-booking'),
            'parent_item'       => __('موسم رئيسي', 'travel-booking'),
            'parent_item_colon' => __('موسم رئيسي:', 'travel-booking'),
            'edit_item'         => __('تعديل الموسم', 'travel-booking'),
            'update_item'       => __('تحديث الموسم', 'travel-booking'),
            'add_new_item'      => __('إضافة موسم جديد', 'travel-booking'),
            'new_item_name'     => __('اسم الموسم الجديد', 'travel-booking'),
            'menu_name'         => __('المواسم', 'travel-booking'),
        );
        
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'season'),
            'show_in_rest'      => true,
        );
        
        register_taxonomy('tbp_season', array('tbp_trip'), $args);
    }
}
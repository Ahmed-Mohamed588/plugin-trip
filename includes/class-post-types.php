<?php
/**
 * Post Types Registration
 * File: includes/class-post-types.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Post_Types {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register'));
        add_filter('enter_title_here', array($this, 'change_title_placeholder'), 10, 2);
    }
    
    public static function register() {
        self::register_cities();
        self::register_trips();
    }
    
    private static function register_cities() {
        $labels = array(
            'name'               => __('المدن', 'travel-booking'),
            'singular_name'      => __('مدينة', 'travel-booking'),
            'menu_name'          => __('المدن', 'travel-booking'),
            'add_new'            => __('إضافة مدينة', 'travel-booking'),
            'add_new_item'       => __('إضافة مدينة جديدة', 'travel-booking'),
            'edit_item'          => __('تعديل المدينة', 'travel-booking'),
            'new_item'           => __('مدينة جديدة', 'travel-booking'),
            'view_item'          => __('عرض المدينة', 'travel-booking'),
            'search_items'       => __('بحث في المدن', 'travel-booking'),
            'not_found'          => __('لم يتم العثور على مدن', 'travel-booking'),
            'not_found_in_trash' => __('لا توجد مدن في سلة المهملات', 'travel-booking'),
            'all_items'          => __('كل المدن', 'travel-booking'),
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-location-alt',
            'menu_position'       => 25,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'city'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
        );
        
        register_post_type('tbp_city', $args);
    }
    
    private static function register_trips() {
        $labels = array(
            'name'               => __('الرحلات', 'travel-booking'),
            'singular_name'      => __('رحلة', 'travel-booking'),
            'menu_name'          => __('الرحلات', 'travel-booking'),
            'add_new'            => __('إضافة رحلة', 'travel-booking'),
            'add_new_item'       => __('إضافة رحلة جديدة', 'travel-booking'),
            'edit_item'          => __('تعديل الرحلة', 'travel-booking'),
            'new_item'           => __('رحلة جديدة', 'travel-booking'),
            'view_item'          => __('عرض الرحلة', 'travel-booking'),
            'search_items'       => __('بحث في الرحلات', 'travel-booking'),
            'not_found'          => __('لم يتم العثور على رحلات', 'travel-booking'),
            'not_found_in_trash' => __('لا توجد رحلات في سلة المهملات', 'travel-booking'),
            'all_items'          => __('كل الرحلات', 'travel-booking'),
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => 'dashicons-airplane',
            'menu_position'       => 26,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'trip'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
        );
        
        register_post_type('tbp_trip', $args);
    }
    
    public function change_title_placeholder($title, $post) {
        if ($post->post_type === 'tbp_city') {
            $title = __('اكتب اسم المدينة', 'travel-booking');
        } elseif ($post->post_type === 'tbp_trip') {
            $title = __('اكتب اسم الرحلة', 'travel-booking');
        }
        return $title;
    }
}
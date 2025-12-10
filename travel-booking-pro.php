<?php
/**
 * Plugin Name: Travel Booking Pro
 * Description: نظام متكامل لإدارة الرحلات والمدن والفنادق مع تكامل كامل مع Elementor Pro
 * Version: 1.0.0
 * Author: Ahmed Mohamed
 * Text Domain: travel-booking
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TBP_VERSION', '1.0.0');
define('TBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TBP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TBP_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Travel_Booking_Pro {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        // Core
        require_once TBP_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once TBP_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once TBP_PLUGIN_DIR . 'includes/class-metaboxes.php';
        require_once TBP_PLUGIN_DIR . 'includes/class-templates.php';
        require_once TBP_PLUGIN_DIR . 'includes/class-admin.php';
        require_once TBP_PLUGIN_DIR . 'includes/class-frontend.php';
        
        // Elementor Integration
        require_once TBP_PLUGIN_DIR . 'includes/elementor/class-elementor-integration.php';
    }
    
    private function init_hooks() {
        // Activation & Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Init
        add_action('plugins_loaded', array($this, 'init'));
        add_action('init', array($this, 'load_textdomain'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    public function activate() {
        // Register post types and taxonomies
        TBP_Post_Types::register();
        TBP_Taxonomies::register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create default options
        add_option('tbp_version', TBP_VERSION);
        
        // Add default seasons
        $this->create_default_seasons();
    }
    
    private function create_default_seasons() {
        if (!term_exists('شتوي', 'tbp_season')) {
            wp_insert_term('شتوي', 'tbp_season', array(
                'description' => 'رحلات الموسم الشتوي',
                'slug' => 'winter'
            ));
        }
        
        if (!term_exists('صيفي', 'tbp_season')) {
            wp_insert_term('صيفي', 'tbp_season', array(
                'description' => 'رحلات الموسم الصيفي',
                'slug' => 'summer'
            ));
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function init() {
        // Initialize classes
        TBP_Post_Types::get_instance();
        TBP_Taxonomies::get_instance();
        TBP_Metaboxes::get_instance();
        TBP_Templates::get_instance();
        TBP_Admin::get_instance();
        TBP_Frontend::get_instance();
        
        // Elementor Integration
        if (did_action('elementor/loaded')) {
            TBP_Elementor_Integration::get_instance();
        }
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('travel-booking', false, dirname(TBP_PLUGIN_BASENAME) . '/languages');
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style('tbp-frontend', TBP_PLUGIN_URL . 'assets/css/frontend.css', array(), TBP_VERSION);
        wp_enqueue_script('tbp-frontend', TBP_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), TBP_VERSION, true);
        
        wp_localize_script('tbp-frontend', 'tbpData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tbp_nonce'),
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        $post_types = array('tbp_city', 'tbp_trip');
        
        if (in_array($screen->post_type, $post_types) || $screen->id === 'toplevel_page_travel-booking') {
            wp_enqueue_style('tbp-admin', TBP_PLUGIN_URL . 'assets/css/admin.css', array(), TBP_VERSION);
            wp_enqueue_script('tbp-admin', TBP_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), TBP_VERSION, true);
            
            wp_localize_script('tbp-admin', 'tbpAdmin', array(
                'nonce' => wp_create_nonce('tbp_admin_nonce'),
                'addHotel' => __('إضافة فندق', 'travel-booking'),
                'removeHotel' => __('حذف', 'travel-booking'),
            ));
        }
    }
}

// Initialize the plugin
function tbp_init() {
    return Travel_Booking_Pro::get_instance();
}

// Start the plugin
tbp_init();
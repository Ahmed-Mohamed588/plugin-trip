<?php
/**
 * Elementor Integration
 * File: includes/elementor/class-elementor-integration.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Elementor_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Check if Elementor is loaded
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_category'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_widget_styles'));
        add_action('elementor/frontend/after_register_scripts', array($this, 'enqueue_widget_scripts'));
    }
    
    /**
     * Add Custom Category
     */
    public function add_category($elements_manager) {
        $elements_manager->add_category(
            'travel-booking',
            array(
                'title' => __('الرحلات والمدن', 'travel-booking'),
                'icon' => 'fa fa-plane',
            )
        );
    }
    
    /**
     * Register Widgets
     */
    public function register_widgets($widgets_manager) {
        // Load widget files
        require_once TBP_PLUGIN_DIR . 'includes/elementor/widgets/class-cities-grid.php';
        require_once TBP_PLUGIN_DIR . 'includes/elementor/widgets/class-trips-grid.php';
        require_once TBP_PLUGIN_DIR . 'includes/elementor/widgets/class-hotels-list.php';
        
        // Register widgets
        $widgets_manager->register(new \TBP_Cities_Grid_Widget());
        $widgets_manager->register(new \TBP_Trips_Grid_Widget());
        $widgets_manager->register(new \TBP_Hotels_List_Widget());
    }
    
    /**
     * Enqueue Widget Styles
     */
    public function enqueue_widget_styles() {
        wp_enqueue_style(
            'tbp-elementor-widgets',
            TBP_PLUGIN_URL . 'assets/css/elementor-widgets.css',
            array(),
            TBP_VERSION
        );
    }
    
    /**
     * Enqueue Widget Scripts
     */
    public function enqueue_widget_scripts() {
        wp_enqueue_script(
            'tbp-elementor-widgets',
            TBP_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            TBP_VERSION,
            true
        );
    }
}
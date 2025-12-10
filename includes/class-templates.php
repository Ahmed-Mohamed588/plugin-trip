<?php
/**
 * Templates Handler
 * File: includes/class-templates.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Templates {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('template_include', array($this, 'template_loader'));
        add_filter('single_template', array($this, 'single_template'));
        add_filter('archive_template', array($this, 'archive_template'));
    }
    
    public function template_loader($template) {
        $post_type = get_post_type();
        
        if ($post_type === 'tbp_city') {
            if (is_single()) {
                return $this->get_template('single-city.php', $template);
            } elseif (is_archive()) {
                return $this->get_template('archive-city.php', $template);
            }
        } elseif ($post_type === 'tbp_trip') {
            if (is_single()) {
                return $this->get_template('single-trip.php', $template);
            } elseif (is_archive()) {
                return $this->get_template('archive-trip.php', $template);
            }
        }
        
        return $template;
    }
    
    public function single_template($template) {
        $post_type = get_post_type();
        
        if ($post_type === 'tbp_city') {
            return $this->get_template('single-city.php', $template);
        } elseif ($post_type === 'tbp_trip') {
            return $this->get_template('single-trip.php', $template);
        }
        
        return $template;
    }
    
    public function archive_template($template) {
        $post_type = get_post_type();
        
        if ($post_type === 'tbp_city') {
            return $this->get_template('archive-city.php', $template);
        } elseif ($post_type === 'tbp_trip') {
            return $this->get_template('archive-trip.php', $template);
        }
        
        return $template;
    }
    
    private function get_template($template_name, $default) {
        // Check if template exists in theme
        $theme_template = locate_template(array(
            'travel-booking/' . $template_name,
            $template_name
        ));
        
        if ($theme_template) {
            return $theme_template;
        }
        
        // Check if template exists in plugin
        $plugin_template = TBP_PLUGIN_DIR . 'templates/' . $template_name;
        
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        return $default;
    }
    
    public static function get_template_part($slug, $name = null, $args = array()) {
        $templates = array();
        $name = (string) $name;
        
        if ('' !== $name) {
            $templates[] = "travel-booking/{$slug}-{$name}.php";
        }
        
        $templates[] = "travel-booking/{$slug}.php";
        
        $template = locate_template($templates);
        
        if (!$template) {
            if ('' !== $name) {
                $fallback = TBP_PLUGIN_DIR . "templates/{$slug}-{$name}.php";
                if (file_exists($fallback)) {
                    $template = $fallback;
                }
            }
            
            if (!$template) {
                $fallback = TBP_PLUGIN_DIR . "templates/{$slug}.php";
                if (file_exists($fallback)) {
                    $template = $fallback;
                }
            }
        }
        
        if ($template) {
            extract($args);
            include $template;
        }
    }
}
<?php
/**
 * Frontend Functionality
 * File: includes/class-frontend.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_tbp_filter_trips', array($this, 'ajax_filter_trips'));
        add_action('wp_ajax_nopriv_tbp_filter_trips', array($this, 'ajax_filter_trips'));
        add_shortcode('tbp_cities', array($this, 'cities_shortcode'));
        add_shortcode('tbp_trips', array($this, 'trips_shortcode'));
        add_shortcode('tbp_hotels', array($this, 'hotels_shortcode'));
    }
    
    /**
     * AJAX Filter Trips
     */
    public function ajax_filter_trips() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        $city_id = isset($_POST['city_id']) ? intval($_POST['city_id']) : 0;
        $season_id = isset($_POST['season_id']) ? intval($_POST['season_id']) : 0;
        
        $args = array(
            'post_type' => 'tbp_trip',
            'posts_per_page' => -1,
        );
        
        // Filter by city
        if ($city_id > 0) {
            $args['meta_query'] = array(
                array(
                    'key' => 'trip_city',
                    'value' => $city_id,
                    'compare' => '=',
                ),
            );
        }
        
        // Filter by season
        if ($season_id > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tbp_season',
                    'field' => 'term_id',
                    'terms' => $season_id,
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        $trips = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $trip_id = get_the_ID();
                
                $trips[] = array(
                    'id' => $trip_id,
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'image' => get_the_post_thumbnail_url($trip_id, 'large'),
                    'price' => get_field('trip_price', $trip_id),
                    'duration' => get_field('trip_duration', $trip_id),
                    'city' => get_the_title(get_field('trip_city', $trip_id)),
                );
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success($trips);
    }
    
    /**
     * Cities Shortcode
     */
    public function cities_shortcode($atts) {
        $atts = shortcode_atts(array(
            'number' => 6,
            'columns' => 3,
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        $args = array(
            'post_type' => 'tbp_city',
            'posts_per_page' => intval($atts['number']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="tbp-cities-grid tbp-columns-' . esc_attr($atts['columns']) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="tbp-city-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="tbp-city-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('large'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="tbp-city-card-content">
                        <h3 class="tbp-city-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="tbp-city-description">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="tbp-city-button">
                            <?php _e('عرض التفاصيل', 'travel-booking'); ?>
                        </a>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __('لا توجد مدن متاحة', 'travel-booking') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Trips Shortcode
     */
    public function trips_shortcode($atts) {
        $atts = shortcode_atts(array(
            'number' => 6,
            'columns' => 3,
            'city' => '',
            'season' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        $args = array(
            'post_type' => 'tbp_trip',
            'posts_per_page' => intval($atts['number']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );
        
        // Filter by city
        if (!empty($atts['city'])) {
            $args['meta_query'] = array(
                array(
                    'key' => 'trip_city',
                    'value' => intval($atts['city']),
                    'compare' => '=',
                ),
            );
        }
        
        // Filter by season
        if (!empty($atts['season'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tbp_season',
                    'field' => 'term_id',
                    'terms' => intval($atts['season']),
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="tbp-trips-grid tbp-columns-' . esc_attr($atts['columns']) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $trip_id = get_the_ID();
                $price = get_field('trip_price', $trip_id);
                $duration = get_field('trip_duration', $trip_id);
                $city_id = get_field('trip_city', $trip_id);
                ?>
                <div class="tbp-trip-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="tbp-trip-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('large'); ?>
                            </a>
                            
                            <?php 
                            $seasons = get_the_terms($trip_id, 'tbp_season');
                            if ($seasons && !is_wp_error($seasons)) : 
                            ?>
                                <div class="tbp-trip-season">
                                    <?php echo esc_html($seasons[0]->name); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="tbp-trip-card-content">
                        <h3 class="tbp-trip-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="tbp-trip-meta">
                            <?php if ($city_id) : ?>
                                <div class="tbp-trip-city">
                                    <i class="dashicons dashicons-location"></i>
                                    <?php echo get_the_title($city_id); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($duration) : ?>
                                <div class="tbp-trip-duration">
                                    <i class="dashicons dashicons-clock"></i>
                                    <?php echo esc_html($duration); ?> <?php _e('يوم', 'travel-booking'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($price) : ?>
                            <div class="tbp-trip-price">
                                <?php echo number_format($price); ?> <?php _e('جنيه', 'travel-booking'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php the_permalink(); ?>" class="tbp-trip-button">
                            <?php _e('حجز الرحلة', 'travel-booking'); ?>
                        </a>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __('لا توجد رحلات متاحة', 'travel-booking') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Hotels Shortcode
     */
    public function hotels_shortcode($atts) {
        $atts = shortcode_atts(array(
            'city' => '',
            'stars' => '',
            'layout' => 'grid',
            'columns' => 3,
        ), $atts);
        
        if (empty($atts['city'])) {
            return '<p>' . __('يرجى تحديد المدينة', 'travel-booking') . '</p>';
        }
        
        $city_id = intval($atts['city']);
        $hotels = get_field('city_hotels', $city_id);
        
        if (empty($hotels)) {
            return '<p>' . __('لا توجد فنادق في هذه المدينة', 'travel-booking') . '</p>';
        }
        
        // Filter by stars if needed
        if (!empty($atts['stars'])) {
            $hotels = array_filter($hotels, function($hotel) use ($atts) {
                return $hotel['hotel_stars'] === $atts['stars'];
            });
        }
        
        if (empty($hotels)) {
            return '<p>' . __('لا توجد فنادق تطابق التصفية', 'travel-booking') . '</p>';
        }
        
        $layout_class = $atts['layout'] === 'grid' ? 'tbp-hotels-grid tbp-columns-' . $atts['columns'] : 'tbp-hotels-list';
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($layout_class); ?>">
            <?php foreach ($hotels as $hotel) : ?>
                <div class="tbp-hotel-card">
                    <?php if (!empty($hotel['hotel_gallery'])) : ?>
                        <div class="tbp-hotel-image">
                            <img src="<?php echo esc_url($hotel['hotel_gallery'][0]['url']); ?>" alt="<?php echo esc_attr($hotel['hotel_name']); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="tbp-hotel-card-content">
                        <h3 class="tbp-hotel-name"><?php echo esc_html($hotel['hotel_name']); ?></h3>
                        
                        <div class="tbp-hotel-stars">
                            <?php for ($i = 0; $i < (int)$hotel['hotel_stars']; $i++) : ?>
                                ★
                            <?php endfor; ?>
                        </div>
                        
                        <?php if (!empty($hotel['hotel_description'])) : ?>
                            <div class="tbp-hotel-description">
                                <?php echo wp_kses_post($hotel['hotel_description']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($hotel['hotel_price'])) : ?>
                            <div class="tbp-hotel-price">
                                <strong><?php echo number_format($hotel['hotel_price']); ?></strong> 
                                <?php _e('جنيه / ليلة', 'travel-booking'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
}
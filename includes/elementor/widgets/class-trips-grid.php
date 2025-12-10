<?php
/**
 * Trips Grid Widget (مع Metaboxes)
 * File: includes/elementor/widgets/class-trips-grid.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Trips_Grid_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'tbp_trips_grid';
    }
    
    public function get_title() {
        return __('شبكة الرحلات', 'travel-booking');
    }
    
    public function get_icon() {
        return 'eicon-posts-grid';
    }
    
    public function get_categories() {
        return ['travel-booking'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('المحتوى', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'posts_per_page',
            [
                'label' => __('عدد الرحلات', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 50,
            ]
        );
        
        $this->add_responsive_control(
            'columns',
            [
                'label' => __('عدد الأعمدة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
            ]
        );
        
        // Get all cities for filter
        $cities = get_posts(array(
            'post_type' => 'tbp_city',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));
        
        $city_options = array('' => __('كل المدن', 'travel-booking'));
        foreach ($cities as $city_id) {
            $city_options[$city_id] = get_the_title($city_id);
        }
        
        $this->add_control(
            'filter_city',
            [
                'label' => __('تصفية حسب المدينة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $city_options,
            ]
        );
        
        // Get all seasons for filter
        $seasons = get_terms(array(
            'taxonomy' => 'tbp_season',
            'hide_empty' => false,
        ));
        
        $season_options = array('' => __('كل المواسم', 'travel-booking'));
        if (!empty($seasons) && !is_wp_error($seasons)) {
            foreach ($seasons as $season) {
                $season_options[$season->term_id] = $season->name;
            }
        }
        
        $this->add_control(
            'filter_season',
            [
                'label' => __('تصفية حسب الموسم', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $season_options,
            ]
        );
        
        $this->add_control(
            'orderby',
            [
                'label' => __('الترتيب حسب', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('التاريخ', 'travel-booking'),
                    'title' => __('الاسم', 'travel-booking'),
                    'meta_value_num' => __('السعر', 'travel-booking'),
                    'rand' => __('عشوائي', 'travel-booking'),
                ],
            ]
        );
        
        $this->add_control(
            'show_price',
            [
                'label' => __('عرض السعر', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_duration',
            [
                'label' => __('عرض المدة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_city',
            [
                'label' => __('عرض المدينة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_excerpt',
            [
                'label' => __('عرض المقتطف', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Card
        $this->start_controls_section(
            'card_style',
            [
                'label' => __('بطاقة الرحلة', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => __('لون الخلفية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .tbp-trip-card',
            ]
        );
        
        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __('انحناء الحواف', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .tbp-trip-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .tbp-trip-card',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Title
        $this->start_controls_section(
            'title_style',
            [
                'label' => __('العنوان', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => __('اللون', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-title a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'title_hover_color',
            [
                'label' => __('اللون عند التمرير', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-title a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .tbp-trip-title',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Price
        $this->start_controls_section(
            'price_style',
            [
                'label' => __('السعر', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'price_color',
            [
                'label' => __('اللون', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#28a745',
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-price' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .tbp-trip-price',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Button
        $this->start_controls_section(
            'button_style',
            [
                'label' => __('الزر', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('button_tabs');
        
        $this->start_controls_tab(
            'button_normal',
            [
                'label' => __('عادي', 'travel-booking'),
            ]
        );
        
        $this->add_control(
            'button_text_color',
            [
                'label' => __('لون النص', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'button_bg_color',
            [
                'label' => __('لون الخلفية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'button_hover',
            [
                'label' => __('التمرير', 'travel-booking'),
            ]
        );
        
        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('لون النص', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => __('لون الخلفية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .tbp-trip-button',
                'separator' => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('المسافة الداخلية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('انحناء الحواف', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tbp-trip-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $args = array(
            'post_type' => 'tbp_trip',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => 'DESC',
        );
        
        // Filter by city
        if (!empty($settings['filter_city'])) {
            $args['meta_query'] = array(
                array(
                    'key' => '_tbp_trip_city',
                    'value' => $settings['filter_city'],
                    'compare' => '=',
                ),
            );
        }
        
        // Filter by season
        if (!empty($settings['filter_season'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tbp_season',
                    'field' => 'term_id',
                    'terms' => $settings['filter_season'],
                ),
            );
        }
        
        // For price ordering
        if ($settings['orderby'] === 'meta_value_num') {
            $args['meta_key'] = '_tbp_trip_price';
        }
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) :
            ?>
            <div class="tbp-trips-grid tbp-columns-<?php echo esc_attr($settings['columns']); ?>">
                <?php while ($query->have_posts()) : $query->the_post(); 
                    $trip_id = get_the_ID();
                    $price = get_post_meta($trip_id, '_tbp_trip_price', true);
                    $duration = get_post_meta($trip_id, '_tbp_trip_duration', true);
                    $city_id = get_post_meta($trip_id, '_tbp_trip_city', true);
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
                            
                            <?php if ('yes' === $settings['show_excerpt']) : ?>
                                <div class="tbp-trip-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="tbp-trip-meta">
                                <?php if ('yes' === $settings['show_city'] && $city_id) : ?>
                                    <div class="tbp-trip-city">
                                        <i class="eicon-map-pin"></i>
                                        <?php echo get_the_title($city_id); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ('yes' === $settings['show_duration'] && $duration) : ?>
                                    <div class="tbp-trip-duration">
                                        <i class="eicon-clock"></i>
                                        <?php echo esc_html($duration); ?> <?php _e('يوم', 'travel-booking'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ('yes' === $settings['show_price'] && $price) : ?>
                                <div class="tbp-trip-price">
                                    <?php echo number_format($price); ?> <?php _e('جنيه', 'travel-booking'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="tbp-trip-button">
                                <?php _e('حجز الرحلة', 'travel-booking'); ?>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<p>' . __('لا توجد رحلات متاحة', 'travel-booking') . '</p>';
        endif;
    }
}
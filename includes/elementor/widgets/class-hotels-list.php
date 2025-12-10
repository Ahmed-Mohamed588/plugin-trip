<?php
/**
 * Hotels List Widget (مع Metaboxes)
 * File: includes/elementor/widgets/class-hotels-list.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class TBP_Hotels_List_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'tbp_hotels_list';
    }
    
    public function get_title() {
        return __('قائمة الفنادق', 'travel-booking');
    }
    
    public function get_icon() {
        return 'eicon-products';
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
        
        // Get all cities
        $cities = get_posts(array(
            'post_type' => 'tbp_city',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));
        
        $city_options = array();
        foreach ($cities as $city_id) {
            $city_options[$city_id] = get_the_title($city_id);
        }
        
        $this->add_control(
            'city_id',
            [
                'label' => __('المدينة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $city_options,
                'description' => __('اختر المدينة لعرض فنادقها', 'travel-booking'),
            ]
        );
        
        $this->add_control(
            'filter_stars',
            [
                'label' => __('تصفية حسب النجوم', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __('كل الفنادق', 'travel-booking'),
                    '3' => __('3 نجوم', 'travel-booking'),
                    '4' => __('4 نجوم', 'travel-booking'),
                    '5' => __('5 نجوم', 'travel-booking'),
                ],
            ]
        );
        
        $this->add_control(
            'layout',
            [
                'label' => __('نمط العرض', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('شبكة', 'travel-booking'),
                    'list' => __('قائمة', 'travel-booking'),
                ],
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => __('عدد الأعمدة', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'condition' => [
                    'layout' => 'grid',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Card
        $this->start_controls_section(
            'card_style',
            [
                'label' => __('بطاقة الفندق', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => __('لون الخلفية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .tbp-hotel-card',
            ]
        );
        
        $this->add_control(
            'card_border_radius',
            [
                'label' => __('انحناء الحواف', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .tbp-hotel-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .tbp-hotel-card',
            ]
        );
        
        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('المسافة الداخلية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Title
        $this->start_controls_section(
            'title_style',
            [
                'label' => __('اسم الفندق', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => __('اللون', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-name' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .tbp-hotel-name',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Stars
        $this->start_controls_section(
            'stars_style',
            [
                'label' => __('النجوم', 'travel-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'stars_color',
            [
                'label' => __('اللون', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-stars' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'stars_size',
            [
                'label' => __('الحجم', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-stars' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
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
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-price' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'price_bg_color',
            [
                'label' => __('لون الخلفية', 'travel-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tbp-hotel-price' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .tbp-hotel-price strong',
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['city_id'])) {
            echo '<p>' . __('يرجى اختيار مدينة', 'travel-booking') . '</p>';
            return;
        }
        
        $city_id = $settings['city_id'];
        $hotels = get_post_meta($city_id, '_tbp_city_hotels', true);
        
        if (empty($hotels)) {
            echo '<p>' . __('لا توجد فنادق في هذه المدينة', 'travel-booking') . '</p>';
            return;
        }
        
        // Filter by stars if needed
        if (!empty($settings['filter_stars'])) {
            $hotels = array_filter($hotels, function($hotel) use ($settings) {
                return $hotel['stars'] === $settings['filter_stars'];
            });
        }
        
        if (empty($hotels)) {
            echo '<p>' . __('لا توجد فنادق تطابق التصفية', 'travel-booking') . '</p>';
            return;
        }
        
        $layout_class = $settings['layout'] === 'grid' ? 'tbp-hotels-grid tbp-columns-' . $settings['columns'] : 'tbp-hotels-list';
        ?>
        
        <div class="<?php echo esc_attr($layout_class); ?>">
            <?php foreach ($hotels as $hotel) : 
                $gallery_ids = !empty($hotel['gallery']) ? explode(',', $hotel['gallery']) : array();
            ?>
                <div class="tbp-hotel-card">
                    <?php if (!empty($gallery_ids)) : ?>
                        <div class="tbp-hotel-image">
                            <?php echo wp_get_attachment_image($gallery_ids[0], 'large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="tbp-hotel-card-content">
                        <h3 class="tbp-hotel-name"><?php echo esc_html($hotel['name']); ?></h3>
                        
                        <div class="tbp-hotel-stars">
                            <?php for ($i = 0; $i < (int)$hotel['stars']; $i++) : ?>
                                ★
                            <?php endfor; ?>
                        </div>
                        
                        <?php if (!empty($hotel['description'])) : ?>
                            <div class="tbp-hotel-description">
                                <?php echo wp_kses_post(wpautop($hotel['description'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($hotel['amenities'])) : ?>
                            <div class="tbp-hotel-amenities">
                                <strong><?php _e('المرافق:', 'travel-booking'); ?></strong>
                                <ul>
                                    <?php foreach ($hotel['amenities'] as $amenity) : ?>
                                        <li><?php echo esc_html($this->get_amenity_label($amenity)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($hotel['price'])) : ?>
                            <div class="tbp-hotel-price">
                                <strong><?php echo number_format($hotel['price']); ?></strong> 
                                <?php _e('جنيه / ليلة', 'travel-booking'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="tbp-hotel-info">
                            <?php if (!empty($hotel['phone'])) : ?>
                                <div class="tbp-hotel-phone">
                                    <i class="eicon-phone"></i>
                                    <?php echo esc_html($hotel['phone']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($hotel['address'])) : ?>
                                <div class="tbp-hotel-address">
                                    <i class="eicon-map-pin"></i>
                                    <?php echo esc_html($hotel['address']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    private function get_amenity_label($amenity) {
        $labels = array(
            'wifi' => __('واي فاي مجاني', 'travel-booking'),
            'pool' => __('مسبح', 'travel-booking'),
            'gym' => __('صالة رياضية', 'travel-booking'),
            'spa' => __('سبا', 'travel-booking'),
            'restaurant' => __('مطعم', 'travel-booking'),
            'parking' => __('موقف سيارات', 'travel-booking'),
            'ac' => __('تكييف', 'travel-booking'),
            'tv' => __('تلفزيون', 'travel-booking'),
        );
        
        return isset($labels[$amenity]) ? $labels[$amenity] : $amenity;
    }
}
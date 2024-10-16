<?php

/*

Plugin Name: Flexi Real Estate Plugin
Description: A plugin to manage real estate properties.
Version: 1.0
Author: Oksana Baranik

*/

// Enqueue scripts
function real_estate_filter_scripts() {
    wp_enqueue_script('real-estate-filter-ajax', plugin_dir_url(__FILE__) . 'real-estate-filter-ajax.js', array('jquery'), null, true);
    wp_localize_script('real-estate-filter-ajax', 'realEstateAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('real_estate_filter_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'real_estate_filter_scripts');

// Registration of post type 'real_estate'
function real_estate_post_type() {
    $labels = array(
        'name'               => 'Об\'єкти нерухомості',
        'singular_name'      => 'Об\'єкт нерухомості',
        'menu_name'          => 'Об\'єкти нерухомості',
        'name_admin_bar'     => 'Об\'єкт нерухомості',
        'add_new'            => 'Додати новий',
        'add_new_item'       => 'Додати новий об\'єкт нерухомості',
        'new_item'           => 'Новий об\'єкт нерухомості',
        'edit_item'          => 'Редагувати об\'єкт нерухомості',
        'view_item'          => 'Переглянути об\'єкт нерухомості',
        'all_items'          => 'Всі об\'єкти нерухомості',
        'search_items'       => 'Шукати об\'єкти нерухомості',
        'not_found'          => 'Не знайдено об\'єктів нерухомості',
        'not_found_in_trash' => 'Не знайдено об\'єктів нерухомості у кошику'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'real-estate'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('real_estate', $args);
}
add_action('init', 'real_estate_post_type');

// Registration of taxonomy 'district'
function real_estate_taxonomy() {
    $labels = array(
        'name'              => 'Райони',
        'singular_name'     => 'Район',
        'search_items'      => 'Шукати райони',
        'all_items'         => 'Всі райони',
        'parent_item'       => 'Батьківський район',
        'parent_item_colon' => 'Батьківський район:',
        'edit_item'         => 'Редагувати район',
        'update_item'       => 'Оновити район',
        'add_new_item'      => 'Додати новий район',
        'new_item_name'     => 'Назва нового району',
        'menu_name'         => 'Райони',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'district'),
    );

    register_taxonomy('district', array('real_estate'), $args);
}
add_action('init', 'real_estate_taxonomy');

// Add shortcode for filter
function real_estate_filter_shortcode( $atts ) {

    ob_start(); 
    ?>
    <form id="real-estate-filter" method="POST" class="mb-4">
        <div class="form-group">
            <label for="location">Район:</label>
            <select name="location" id="location" class="form-control">
                <option value="">Виберіть район</option>
                <?php
                $districts = get_terms('district');
                foreach ($districts as $district) {
                    echo '<option value="' . esc_attr($district->term_id) . '">' . esc_html($district->name) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="floors">Кількість поверхів:</label>
            <select name="floors" id="floors" class="form-control">
                <option value="">Виберіть кількість поверхів</option>
                <?php for ($i = 1; $i <= 20; $i++) : ?>
                    <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="build_type">Тип будівлі:</label>
            <select name="build_type" id="build_type" class="form-control">
                <option value="">Виберіть тип будівлі</option>
                <option value="Панель">Панель</option>
                <option value="Цегла">Цегла</option>
                <option value="Піноблок">Піноблок</option>
            </select>
        </div>

        <div class="form-group">
            <label for="environmental_friendliness">Екологічність:</label>
            <select name="environmental_friendliness" id="environmental_friendliness" class="form-control">
                <option value="">Виберіть екологічність (1-5)</option>
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount_of_rooms">Кількість кімнат:</label>
            <select name="amount_of_rooms" id="amount_of_rooms" class="form-control">
                <option value="">Виберіть кількість кімнат</option>
                <?php for ($i = 1; $i <= 10; $i++) : ?>
                    <option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="balcony">Балкон:</label>
            <select name="balcony" id="balcony" class="form-control">
                <option value="">Виберіть наявність балкона</option>
                <option value="Так">Так</option>
                <option value="Ні">Ні</option>
            </select>
        </div>

        <div class="form-group">
            <label for="bathroom">Санвузол:</label>
            <select name="bathroom" id="bathroom" class="form-control">
                <option value="">Виберіть наявність санвузла</option>
                <option value="Так">Так</option>
                <option value="Ні">Ні</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Фільтрувати</button>
    </form>

    <?php
    return ob_get_clean(); 
}
add_shortcode('real_estate_filter', 'real_estate_filter_shortcode');

// Create class for a widget
class Real_Estate_Filter_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'real_estate_filter_widget',
            'Фільтр об\'єктів нерухомості',
            array('description' => 'Віджет для фільтрації об\'єктів нерухомості')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
    
        $title = get_theme_mod('real_estate_filter_widget_title', __('Фільтр', 'text_domain'));
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
    
        $number_of_items = get_theme_mod('real_estate_filter_widget_number_of_items', 5);
        
        echo do_shortcode('[real_estate_filter number_of_items="' . esc_attr($number_of_items) . '"]'); // Викликаємо шорткод
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        
        $title = !empty($instance['title']) ? $instance['title'] : __('Фільтр', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Заголовок:', 'text_domain'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
        
        $number_of_items = !empty($instance['number_of_items']) ? $instance['number_of_items'] : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number_of_items')); ?>">
                <?php esc_html_e('Кількість показаних об\'єктів:', 'text_domain'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number_of_items')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_items')); ?>" type="number" value="<?php echo esc_attr($number_of_items); ?>" step="1" min="1" />
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number_of_items'] = (!empty($new_instance['number_of_items'])) ? intval($new_instance['number_of_items']) : 5;
        return $instance;
    }
}

// Register widget
function register_real_estate_filter_widget() {
    register_widget('Real_Estate_Filter_Widget');
}
add_action('widgets_init', 'register_real_estate_filter_widget');

// Processing AJAX request for filtering real estate objects
function real_estate_filter() {

    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'real_estate_filter_nonce')) {
        wp_send_json_error('Invalid nonce');
        wp_die();
    }

    $number_of_items = get_theme_mod('real_estate_filter_widget_number_of_items', 5);
    $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
    parse_str($_POST['filter'], $filter);

    $args = array(
        'post_type' => 'real_estate',
        'posts_per_page' => $number_of_items,
        'paged' => $paged,
    );

    $meta_query = [];
    
    if (!empty($filter['floors'])) {
        $meta_query[] = [
            'key' => 'number_of_floors',
            'value' => $filter['floors'],
            'compare' => '='
        ];
    }
    
    if (!empty($filter['build_type'])) {
        $meta_query[] = [
            'key' => 'build_type',
            'value' => $filter['build_type'],
            'compare' => '='
        ];
    }
    
    if (!empty($filter['environmental_friendliness'])) {
        $meta_query[] = [
            'key' => 'environmental_friendliness',
            'value' => $filter['environmental_friendliness'],
            'compare' => '='
        ];
    }
 
    if (!empty($filter['location'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'district',
                'field' => 'id',
                'terms' => $filter['location']
            ]
        ];
    }

    if (!empty($filter['amount_of_rooms']) || !empty($filter['balcony']) || !empty($filter['bathroom'])) {
        $rooms_meta_query = [
            'relation' => 'AND'
        ];

        if (!empty($filter['amount_of_rooms'])) {
            $rooms_meta_query[] = [
                'key' => 'amount_of_rooms',
                'value' => $filter['amount_of_rooms'],
                'compare' => '='
            ];
        }

        if (!empty($filter['balcony'])) {
            $rooms_meta_query[] = [
                'key' => 'balcony',
                'value' => $filter['balcony'] === 'Так' ? '1' : '0',
                'compare' => '='
            ];
        }

        if (!empty($filter['bathroom'])) {
            $rooms_meta_query[] = [
                'key' => 'bathroom',
                'value' => $filter['bathroom'] === 'Так' ? '1' : '0',
                'compare' => '='
            ];
        }

        $meta_query[] = [
            'key' => 'premises',
            'value' => '',
            'compare' => 'EXISTS',
            'meta_query' => $rooms_meta_query
        ];
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        echo '<div id="real-estate-results" class="row">';
            while ($query->have_posts()) : 
            $query->the_post(); ?>
            
            <div class="col-md-4">
                <h5>Будинок:</h5>
                <div class="card real-estate-item">
                    <div class="card-img">
                    <?php 
                        $building_image = get_field('image');
                        if (!empty($building_image)): ?>
                            <img class="img-fluid" src="<?php echo esc_url($building_image['url']); ?>" alt="<?php echo esc_attr($building_image['alt']); ?>" />
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div class="card-body__title">
                            <h5 class="card-title"><?php the_field('build_name') ?></h5>
                        </div>
                        <div class="card-body__info">
                            <p class="card-floors">Кількість поверхів: <?php the_field('number_of_floors') ?></p>
                            <p class="card-type">Тип будинку: <?php the_field('build_type') ?></p>
                            <p class="card-ecological">Екологічність: <?php the_field('environmental_friendliness') ?></p>
                            <p class="card-text"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Детальніше</a> 
                        </div> 
                    </div>
                </div>

                <?php 
                $rooms = get_field('premises');
                if ($rooms) : ?>
                    <h6>Приміщення:</h6>
                    <div class="rooms-list">
                        <?php foreach ($rooms as $room) : ?>
                            <div class="room-item card card-body">
                                <div class="card-img">
                                    <?php if (!empty($room['rooms_image'])): ?>
                                        <img class="img-fluid" src="<?php echo esc_url($room['rooms_image']['url']); ?>" alt="<?php echo esc_attr($room['rooms_image']['alt']); ?>" />
                                    <?php endif; ?>
                                </div>
                                <p>Площа: <?php echo esc_html($room['square']); ?> м²</p>
                                <p>Кількість кімнат: <?php echo esc_html($room['amount_of_rooms']); ?></p>
                                <p>Балкон: <?php echo esc_html($room['balcony'] ? 'Так' : 'Ні'); ?></p>
                                <p>Санвузол: <?php echo esc_html($room['bathroom'] ? 'Так' : 'Ні'); ?></p>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Детальніше</a>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                </div>
            <?php endwhile;
        echo '</div>';

        // Pagination
        $total_pages = $query->max_num_pages;
        if ($total_pages > 1) :
            $current_page = max(1, $paged);
            echo '<div class="pagination">';
            echo paginate_links(array(
                'base' => '%_%',
                'format' => '?paged=%#%',
                'current' => $current_page,
                'total' => $total_pages,
                'prev_text' => __('Попередня', 'text-domain'), 
                'next_text' => __('Наступна', 'text-domain'), 
            ));
            echo '</div>';
        endif;

    else :
        echo '<p>Об\'єкти нерухомості не знайдені.</p>';
    endif;

    wp_die(); 
}

add_action('wp_ajax_real_estate_filter', 'real_estate_filter');
add_action('wp_ajax_nopriv_real_estate_filter', 'real_estate_filter');

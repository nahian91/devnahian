<?php
class Categories_List_Widget extends WP_Widget {

    // assigned all default values
    protected $default_config = [
        'widget_title' => 'Categories List Widget',
        'selected_categories' => array(),
    ];

    function __construct() {
        $widget_ops = array(
            'classname' => 'categories-list-sidebar', // widget class appears to the widget parent DIV
        );
        parent::__construct( 'categories-list-widget', 'Categories List Widget', $widget_ops );
    }

    // front-end display of the widget
    function widget( $args, $instance ) {

        // merge the $default_config and widget's $instance array
        $instance = array_merge($this->default_config, $instance);

        // get all the input field value from the widget $instance
        $widget_title = $instance['widget_title']; // get the widget title
        $selected_categories = $instance['selected_categories']; // get selected categories

        // starting the parent DIV declared to the sidebar
        echo $args['before_widget'];

        if ( $widget_title ) {
            ?>
            <div class="section-title">
                <h5><?php echo $widget_title; ?></h5>
            </div>
            <?php
        }

        // Display categories
        if (!empty($selected_categories)) {
            ?>
            <ul class="widget-categories">
                <?php
                $categories = get_categories(array(
                    'orderby' => 'name',
                    'order'   => 'ASC',
                    'include' => $selected_categories, // Limit to selected categories
                ));

                foreach ($categories as $category) {
                    ?>
                    <li>
                        <a href="<?php echo get_category_link($category->term_id); ?>" class="categorie"><?php echo $category->name; ?></a>
                        <span class="ml-auto"><?php echo $category->count; ?> Posts</span>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }

        // ending the parent DIV declared to the sidebar
        echo $args['after_widget'];
    }

    // widget default update function to update all the form fields
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );
        $instance['selected_categories'] = isset( $new_instance['selected_categories'] ) ? array_map( 'intval', $new_instance['selected_categories'] ) : array();
        return $instance;
    }

    // widget default form function to handle all the form data
    function form( $instance ) { 
        $instance = array_merge($this->default_config, $instance);
        $selected_categories = $instance['selected_categories']; // Get selected categories from instance

        // Get all categories
        $categories = get_categories(array(
            'orderby' => 'name',
            'order'   => 'ASC'
        ));
        ?>

        <!-- Widget Title -->
        <div>
            <h4><?php esc_html_e( 'Widget Title:', 'ewa-essentialwa-theme' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
            </label>
        </div>

        <!-- Selected Categories -->
        <div>
            <h4><?php esc_html_e( 'Selected Categories:', 'ewa-essentialwa-theme' ); ?></h4>
            <select id="<?= $this->get_field_id( 'selected_categories' ); ?>" name="<?= $this->get_field_name( 'selected_categories' ); ?>[]" multiple="multiple" style="height: 100px;">
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category->term_id; ?>" <?php selected( in_array($category->term_id, $selected_categories) ); ?>><?php echo $category->name; ?></option>
                <?php } ?>
            </select>
        </div>

        <?php 
    }
}

// initialize the widget to the admin side
function Categories_List_Widget_init() {
    register_widget( 'Categories_List_Widget' );
}
add_action( 'widgets_init', 'Categories_List_Widget_init' );
?>

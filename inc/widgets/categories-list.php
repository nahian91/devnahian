<?php
class Categories_List_Widget extends WP_Widget {

    // Assigned all default values
    protected $default_config = [
        'widget_title' => 'Categories List',
    ];

    function __construct() {
        $widget_ops = array(
            'classname' => 'categories-list-sidebar', // Widget class appears to the widget parent DIV
        );
        parent::__construct( 'categories-list-widget', 'Categories List Widget', $widget_ops );
    }

    // Front-end display of the widget
    function widget( $args, $instance ) {

        // Merge the $default_config and widget's $instance array
        $instance = array_merge($this->default_config, $instance);

        // Get all the input field value from the widget $instance
        $widget_title = $instance['widget_title']; // Get the widget title

        // Starting the parent DIV declared to the sidebar
        echo $args['before_widget'];

        if ( $widget_title ) {
            ?>
            <div class="section-title">
                <h5><?php echo $widget_title; ?></h5>
            </div>
            <?php
        }

        // Display all categories
        $categories = get_categories(array(
            'orderby' => 'name',
            'order'   => 'ASC',
        ));
        ?>
        <ul class="widget-categories two-columns">
            <?php foreach ($categories as $category) { ?>
                <li>
                    <a href="<?php echo get_category_link($category->term_id); ?>" class="categorie"><?php echo $category->name; ?></a>
                </li>
            <?php } ?>
        </ul>
        <?php

        // Ending the parent DIV declared to the sidebar
        echo $args['after_widget'];
    }

    // Widget default update function to update all the form fields
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );
        return $instance;
    }

    // Widget default form function to handle all the form data
    function form( $instance ) { 
        $instance = array_merge($this->default_config, $instance);
        ?>

        <!-- Widget Title -->
        <div>
            <h4><?php esc_html_e( 'Widget Title:', 'ewa-essentialwa-theme' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
            </label>
        </div>

        <?php 
    }
}

// Initialize the widget to the admin side
function Categories_List_Widget_init() {
    register_widget( 'Categories_List_Widget' );
}
add_action( 'widgets_init', 'Categories_List_Widget_init' );
?>

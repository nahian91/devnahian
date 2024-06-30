<?php
class Popular_Posts_List_Widget extends WP_Widget {
    // Assigned all default values
    protected $default_config = [
        'widget_title' => 'Popular Posts',
        'template' => 'list-thumbnail',
        'amount_of_posts' => 3,
    ];

    function __construct() {
        $widget_ops = array(
            'classname' => 'popular-posts-sidebar', // widget class appears to the widget parent DIV
        );
        parent::__construct( 'popular-posts-list-widget', 'Popular Posts List Widget', $widget_ops );
    }

    // Front-end display of the widget
    function widget( $args, $instance ) {
        // Merge the $default_config and widget's $instance array
        $instance = array_merge($this->default_config, $instance);

        // Get all the input field value from the widget $instance
        $widget_title = $instance['widget_title']; // Get the widget title
        $posts_amount = $instance['amount_of_posts']; // Get the total amount of posts to be displayed

        // Starting the parent DIV declared to the sidebar
        echo $args['before_widget']; ?>

        <?php if ( $widget_title ) { ?>
            <div class="section-title">
                <h5><?php echo $widget_title; ?></h5>
            </div>
        <?php } ?>
        <ul class="widget-latest-posts">
        <?php
        $query_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_amount,
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        );

        $query = new WP_Query($query_args);

        if ($query->have_posts()) :
            $i = 0;
            while ($query->have_posts()) : $query->the_post(); 
            $i++; 
        ?>

            <li class="last-post">
                <div class="image">
                    <?php the_post_thumbnail(); ?>
                </div>
                <div class="nb"><?php echo $i; ?></div>
                <div class="content">
                    <p>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </p>
                    <small>
                        <span class="icon_clock_alt"></span> <?php echo get_the_date(); ?></small>
                </div>
            </li>
        <?php endwhile;
        wp_reset_query();
        endif;
        ?>
        </ul>
        <?php 
        echo $args['after_widget'];
    }

    // Widget default update function to update all the form fields
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    // Widget default form function to handle all the form data
    function form( $instance ) { 
        $instance = array_merge($this->default_config, $instance);
    ?>

        <!-- Shows all the input fields to the widget admin side -->
        <div>
            <h4><?php esc_html_e( 'Widget Title:', 'ewa-essentialwa-theme' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
            </label>

            <h4><?php esc_html_e( 'Amount Of Posts:', 'ewa-essentialwa-theme' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'amount_of_posts' ); ?>" name="<?= $this->get_field_name( 'amount_of_posts' ); ?>" type="text" value="<?= $instance['amount_of_posts']; ?>" />
            </label>
        </div>

    <?php 
    }
}

// Initialize the widget to the admin side
function Popular_Posts_List_Widget_init() {
    register_widget( 'Popular_Posts_List_Widget' );
}
add_action( 'widgets_init', 'Popular_Posts_List_Widget_init' );
?>

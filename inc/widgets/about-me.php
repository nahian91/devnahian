<?php
class About_Me_Widget extends WP_Widget {

    // assigned all default values
    protected $default_config = [
        'widget_title' => 'About Me Widget',
        'image_src' => '',
        'heading' => '',
        'paragraph_text' => '',
        'link_url' => '',
    ];

    function __construct() {
        $widget_ops = array(
            'classname' => 'about-me-sidebar', // widget class appears to the widget parent DIV
        );
        parent::__construct( 'about-me-widget', 'About Me Widget', $widget_ops );
    }

    // front-end display of the widget
    function widget( $args, $instance ) {

        // merge the $default_config and widget's $instance array
        $instance = array_merge($this->default_config, $instance);

        // get all the input field value from the widget $instance
        $widget_title = $instance['widget_title']; // get the widget title
        $image_src = $instance['image_src'];
        $heading = $instance['heading'];
        $paragraph_text = $instance['paragraph_text'];
        $link_url = $instance['link_url'];

        // starting the parent DIV declared to the sidebar
        echo $args['before_widget']; ?>

        <?php if ( $widget_title ) { ?>
            <div class="section-title">
                <h5><?php echo $widget_title; ?></h5>
            </div>
        <?php } ?>

        <div class="about-info">
            <?php if ($image_src) { ?>
                <img src="<?php echo esc_url($image_src); ?>" alt="">
            <?php } ?>
            <?php if ($heading) { ?>
                <h4><?php echo esc_html($heading); ?></h4>
            <?php } ?>
            <?php if ($paragraph_text) { ?>
                <p><?php echo esc_html($paragraph_text); ?></p>
            <?php } ?>
            <?php if ($link_url) { ?>
                <a href="<?php echo esc_url($link_url); ?>">Know More</a>
            <?php } ?>
        </div>

        <?php
        // ending the parent DIV declared to the sidebar
        echo $args['after_widget'];
    }

    // widget default update function to update all the form fields
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    // widget default form function to handle all the form data
    function form( $instance ) { 

        $instance = array_merge($this->default_config, $instance);

        ?>

        <!-- shows all the input fields to the widget admin side -->
        <div>
            <h4><?php esc_html_e( 'Widget Title:', 'devnahian' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
            </label>
        </div>

        <div>
            <h4><?php esc_html_e( 'Image Source:', 'devnahian' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'image_src' ); ?>" name="<?= $this->get_field_name( 'image_src' ); ?>" type="text" value="<?= $instance['image_src']; ?>" />
            </label>
        </div>

        <div>
            <h4><?php esc_html_e( 'Heading:', 'devnahian' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'heading' ); ?>" name="<?= $this->get_field_name( 'heading' ); ?>" type="text" value="<?= $instance['heading']; ?>" />
            </label>
        </div>

        <div>
            <h4><?php esc_html_e( 'Paragraph Text:', 'devnahian' ); ?></h4>
            <label>
                <textarea id="<?= $this->get_field_id( 'paragraph_text' ); ?>" name="<?= $this->get_field_name( 'paragraph_text' ); ?>" rows="4"><?= $instance['paragraph_text']; ?></textarea>
            </label>
        </div>

        <div>
            <h4><?php esc_html_e( 'Link URL:', 'devnahian' ); ?></h4>
            <label>
                <input id="<?= $this->get_field_id( 'link_url' ); ?>" name="<?= $this->get_field_name( 'link_url' ); ?>" type="text" value="<?= $instance['link_url']; ?>" />
            </label>
        </div>

        <?php 
    }
}

// initialize the widget to the admin side
function About_Me_Widget_init() {
    register_widget( 'About_Me_Widget' );
}
add_action( 'widgets_init', 'About_Me_Widget_init' );
?>

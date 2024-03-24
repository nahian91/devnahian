<?php class Search_Box_Widget extends WP_Widget {


// assigned all default values
protected $default_config = [
  'widget_title' => 'Search Box Widget',
];

function __construct() {
  $widget_ops = array(
    'classname' => 'seach-box-sidebar', // widget class appears to the widget parent DIV
  );
  parent::__construct( 'seach-box-widget', 'Search Box Widget', $widget_ops );
}


// front-end display of the widget
function widget( $args, $instance ) {

//merge the $default_config and widget's $instance array
$instance = array_merge($this->default_config, $instance);


//get all the input field value from the widget $instance
$widget_title = $instance['widget_title']; // get the widget title

// starting the parent DIV declared to the sidebar
echo $args['before_widget']; ?>


  <?php if ( $widget_title ) { ?>
    <div class="section-title">
        <h5><?php echo $widget_title; ?></h5>
    </div>
  <?php } ?>

  <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
      <label>
         <span class="screen-reader-text"><?php echo _x('Search for:', 'label') ?></span>
         <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search …', 'placeholder') ?>" value="<?php echo get_search_query() ?>" name="s"
         title="<?php echo esc_attr_x('Search for:', 'label') ?>"/></label>
      <input type="submit" class="search-submit" value="<?php echo esc_attr_x('Search', 'submit button') ?>"/>
   </form>
     


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

      <h4><?php esc_html_e( 'Widget Title:', 'ewa-essentialwa-theme' ); ?></h4>
      <label>
        <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
      </label>


    </div>

   <?php 
}

}


// initialize the widget to the admin side
function Search_Box_Widget_init() {
register_widget( 'Search_Box_Widget' );
}

add_action( 'widgets_init', 'Search_Box_Widget_init' );
?>

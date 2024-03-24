<?php class Recent_Posts_List_Widget extends WP_Widget {


// assigned all default values
protected $default_config = [
  'widget_title' => 'Recents Posts',
  'template' => 'list-thumbnail',
  'amount_of_posts' => 3,
  'in_ex_categories' => '',
  'show_link_more' => 0,
  'link_more_link' => '',
  'link_more_text' => '',
];

function __construct() {
  $widget_ops = array(
    'classname' => 'recent-posts-sidebar', // widget class appears to the widget parent DIV
  );
  parent::__construct( 'recent-posts-list-widget', 'Recent Posts List Widget', $widget_ops );
}


// front-end display of the widget
function widget( $args, $instance ) {

//merge the $default_config and widget's $instance array
$instance = array_merge($this->default_config, $instance);


//get all the input field value from the widget $instance
$widget_title = $instance['widget_title']; // get the widget title
$posts_amount = $instance['amount_of_posts']; // get the total amount of posts to be displayed

// starting the parent DIV declared to the sidebar
echo $args['before_widget']; ?>


  <?php if ( $widget_title ) { ?>
  <h2 class="widget-title"><?php echo $widget_title; ?></h2>
  <?php } ?>


      <?php

      $query_args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_amount,
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
          <?php 

        endwhile;

        wp_reset_query();

      endif; 

      ?>


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


      <h4><?php esc_html_e( 'Amount Of Posts:', 'ewa-essentialwa-theme' ); ?></h4>
      <label>
        <input id="<?= $this->get_field_id( 'amount_of_posts' ); ?>" name="<?= $this->get_field_name( 'amount_of_posts' ); ?>" type="text" value="<?= $instance['amount_of_posts']; ?>" />
      </label>


    </div>

   <?php 
}

}


// initialize the widget to the admin side
function Recent_Posts_List_Widget_init() {
register_widget( 'Recent_Posts_List_Widget' );
}

add_action( 'widgets_init', 'Recent_Posts_List_Widget_init' );
?>

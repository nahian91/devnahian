<?php class Categories_List_Widget extends WP_Widget {


// assigned all default values
protected $default_config = [
  'widget_title' => 'Categories List Widget',
  'template' => 'list-thumbnail',
  'amount_of_posts' => 3,
  'in_ex_categories' => '',
  'show_link_more' => 0,
  'link_more_link' => '',
  'link_more_text' => '',
];

function __construct() {
  $widget_ops = array(
    'classname' => 'categories-list-sidebar', // widget class appears to the widget parent DIV
  );
  parent::__construct( 'categories-list-widget', 'Categories List Widget', $widget_ops );
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

  <div class="widget">
      <div class="section-title">
         <h5>Categories</h5>
      </div>
      <ul class="widget-categories">
         <?php
         $categories = get_categories(array(
            'orderby' => 'name',
            'order'   => 'ASC'
         ));

         foreach ($categories as $category) {
               ?>
               <li>
                  <a href="<?php echo get_category_link($category->term_id); ?>"
                     class="categorie"><?php echo $category->name; ?></a>
                  <span class="ml-auto"><?php echo $category->count; ?> Posts</span>
               </li>
         <?php
         } ?>
      </ul>
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

      <h4><?php esc_html_e( 'Widget Title:', 'ewa-essentialwa-theme' ); ?></h4>
      <label>
        <input id="<?= $this->get_field_id( 'widget_title' ); ?>" name="<?= $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?= $instance['widget_title']; ?>" />
      </label>


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

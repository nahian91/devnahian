<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package anahian
 */
?>

<aside id="secondary" class="widget-area">
   

   <div class="widget">
      <div class="section-title">
         <h5>Latest Posts</h5>
      </div>
      <ul class="widget-latest-posts">
         <?php
         $args = array(
               'post_type'      => 'post',
               'posts_per_page' => 5,
               'order'          => 'DESC'
         );
         $query = new WP_Query($args);
         if ($query->have_posts()) :
               $i = 0;
               while ($query->have_posts()) :
                  $query->the_post();
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
         endif;
         ?>
      </ul>
   </div>

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
</aside>

<?php dynamic_sidebar( 'sidebar-1' ); ?>
<!-- Secondary -->

<?php
   /**
    * Cart Page
    *
    * @see     https://docs.woocommerce.com/document/template-structure/
    * @package WooCommerce\Templates
    * @version 7.9.0
    */
   
   defined( 'ABSPATH' ) || exit;
   
    ?>
<section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/breadcumb.jpg')">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="post-single-content">
               <h4><?php the_title();?></h4>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="car-area pt-80 pb-80">
   <div class="container">
      <div class="row">
         <div class="col-md-8">
			<?php do_action( 'woocommerce_before_cart' ); ?>
            <form class="woocommerce-cart-form form-container" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
               <?php do_action( 'woocommerce_before_cart_table' ); ?>
               <?php
                  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                  	$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                  	$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                  
                  	if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                  		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                  		?>
               <!-- .cart-contents-wrapper -->
               <div class="cart-contents-wrapper mb-3">
                  <!-- .cart-contents -->
                  <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                  <div class="cart-contents">
                     <!-- .cart-product-image -->
                     <div class="cart-product-image">
                        <?php
                           $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'cart-image-thumb' ), $cart_item, $cart_item_key );
                           
                           if ( ! $product_permalink ) {
                           echo $thumbnail; // PHPCS: XSS ok.
                           } else {
                           printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                           }
                           ?>
                     </div>
                     <!-- .cart-product-title -->
                     <div class="cart-product-title">
                        <?php
                           if ( ! $product_permalink ) {
                           	echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                           } else {
                           	echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                           }
                           
                           do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                           
                           // Meta data.
                           echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.
                           
                           // Backorder notification.
                           if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                           	echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'tutorstarter' ) . '</p>', $product_id ) );
                           }
                           ?>
                     </div>
                     <!-- .cart-product-actions -->
                     <div class="cart-product-actions">
                        <!-- .cart-product-price -->
                        <div class="cart-product-price">
                           <?php
                              echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                              ?>
                        </div>
                     </div>
                        <!-- .cart-product-remove -->
                        <div class="cart-product-remove">
                           <?php
                              echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                              	'woocommerce_cart_item_remove_link',
                              	sprintf(
                              		'<a href="%s" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
                              		esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                              		esc_html__( 'Remove this item', 'tutorstarter' ),
                              		esc_attr( $product_id ),
                              		esc_attr( $_product->get_sku() ),
                              		esc_html__( 'X', 'tutorstarter' )
                              	),
                              	$cart_item_key
                              );
                              ?>
                        </div>
                  </div>
                  <!-- .cart-contents -->
                  <?php do_action( 'woocommerce_cart_contents' ); ?>
                  <?php do_action( 'woocommerce_after_cart_contents' ); ?>
               </div>
               <!-- .cart-contents-wrapper -->
               <?php
                  }
                  }
                  ?>
               <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>
            <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
         </div>
         <div class="col-md-4">
            <!-- .cart-collaterals -->
            <div class="cart-collaterals">
               <!-- .cart-summary -->
               <div class="cart-summary">
                  <?php
                     /**
                      * Cart collaterals hook.
                      *
                      * @hooked woocommerce_cross_sell_display
                      * @hooked woocommerce_cart_totals - 10
                      */
                     do_action( 'woocommerce_cart_collaterals' );
                     ?>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
</section>
<?php do_action( 'woocommerce_after_cart' ); ?>

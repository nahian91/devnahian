<?php
/**
 * Checkout Form
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$order_button_text = __( 'Complete Payment', 'tutorstarter' );
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

<section class="checkout-area">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<?php do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'tutorstarter' ) ) );
	return;
}

?>
			</div>
		</div>
	</div>
</section>

<section class="checkout-area">
	<div class="container">
		
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		<?php if ( $checkout->get_checkout_fields() ) : ?>

<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
<div class="row">
			<div class="col-md-8">
			
	
	<div class="tutorstarter-checkout-container tutorstarter-cart-container">
		<div class="checkout-contents" id="customer_details">
			<div class="checkout-payment">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
		</div><!-- .checkout-payment -->
	</div>
	
	
	
</div><!-- .tutorstarter-checkout-container -->

			</div>
			<div class="col-md-4">
			<div class="checkout-order-summary">
		<h2 class="checkout-heading cart-page-heading"><?php esc_html_e( 'Summary', 'tutorstarter' ); ?></h2>
		<div class="cart-collaterals">
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	
			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
			
			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
	
			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	
			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
	
			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		</div>
	</div>
			</div>
			</div>
			</form><!-- .checkout -->
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
	<?php endif; ?>
	</div>
</section>


<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
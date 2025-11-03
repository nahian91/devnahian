<?php
/**
 * Internal Collection Block Template (SEO Optimized, No Inline CSS).
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'internal-collection-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $id = sanitize_title( $block['anchor'] );
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'internal-collection';
if ( ! empty( $block['className'] ) ) {
    $className .= ' ' . sanitize_html_class( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $className .= ' align' . sanitize_html_class( $block['align'] );
}

// Load values and assign defaults.
$internal_collections = get_field( 'internal_collections' );

?>

<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">

    <div class="internal-collection-link">
        <h5>Handpicked Posts You Can’t Miss</h5>
        <div class="row">
            <?php foreach($internal_collections as $collection) {
                ?>
                    <div class="col-md-6">
                        <span><a href="<?php the_permalink();?>"><?php echo $collection->post_title;?></a></span>
                    </div>
                <?php 
            }  ?>
            
        </div>
    </div>

</div>
<!-- Theme Collection Section End -->

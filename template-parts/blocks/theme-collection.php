<?php
/**
 * Theme Collection Block Template.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'theme-collection-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $id = sanitize_title( $block['anchor'] );
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'theme-collection';
if ( ! empty( $block['className'] ) ) {
    $className .= ' ' . sanitize_html_class( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $className .= ' align' . sanitize_html_class( $block['align'] );
}

// Load values and assign defaults.
$image                = get_field( 'image' );
$title                = get_field( 'title' );
$description          = get_field( 'description' );
$features             = get_field( 'features' );
$demo_link_label      = get_field( 'demo_link_label' );
$demo_link            = get_field( 'demo_link' );
$download_link_label  = get_field( 'download_link_label' );
$download_link        = get_field( 'download_link' );
?>

<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">

    <?php if ( $image ) : ?>
        <div class="theme-collection-img" style="background-image:url('<?php echo esc_url( $image ); ?>')"></div>
    <?php endif; ?>

    <?php if ( $title ) : ?>
        <h4><?php echo esc_html( $title ); ?></h4>
    <?php endif; ?>

    <?php if ( $description ) : ?>
        <p><?php echo esc_html( $description ); ?></p>
    <?php endif; ?>

    <?php if ( $features ) : ?>
        <div class="theme-collection-features">
            <h5><?php esc_html_e( 'Features', 'devnahian' ); ?></h5>
            <?php foreach ( $features as $feature ) : ?>
                <?php if ( ! empty( $feature['feature_title'] ) ) : ?>
                    <span><?php echo esc_html( $feature['feature_title'] ); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="theme-collection-link">
        <?php if ( $demo_link ) : ?>
            <a href="<?php echo esc_url( $demo_link ); ?>" target="_blank" rel="noopener">
                <?php 
                // Use custom label if set, otherwise default 'Demo'
                echo esc_html( $demo_link_label ? $demo_link_label : __( 'Demo', 'devnahian' ) ); 
                ?>
            </a>
        <?php endif; ?>

        <?php if ( $download_link ) : ?>
            <a href="<?php echo esc_url( $download_link ); ?>" target="_blank" rel="noopener">
                <?php 
                // Use custom label if set, otherwise default 'Download Now'
                echo esc_html( $download_link_label ? $download_link_label : __( 'Download Now', 'devnahian' ) ); 
                ?>
            </a>
        <?php endif; ?>
    </div>

</div>

<!-- Theme Collection Section End -->

<?php
/**
 * Theme Collection Block Template (SEO Optimized, No Inline CSS, with Click Tracking)
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

// SEO-friendly image attributes
$alt_text = '';
$title_attr = '';

if ( $image ) {
    $attachment_id = attachment_url_to_postid( $image );
    $alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

    if ( empty( $alt_text ) && $title ) {
        $alt_text = $title . ' - Learn with Abdullah Nahian';
    }

    $title_attr = $title ? $title . ' - Explore Features & Demo' : '';
}

// Get click counts
$demo_clicks     = (int) get_post_meta(get_the_ID(), 'theme_click_demo', true);
$download_clicks = (int) get_post_meta(get_the_ID(), 'theme_click_download', true);
?>

<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">

    <?php if ( $image ) : ?>
        <img
            class="theme-collection-img"
            src="<?php echo esc_url( $image ); ?>"
            alt="<?php echo esc_attr( $alt_text ); ?>"
            <?php if ( $title_attr ) : ?>title="<?php echo esc_attr( $title_attr ); ?>"<?php endif; ?>
            loading="lazy"
        >
    <?php endif; ?>

    <?php if ( $title ) : 
        $title_id = sanitize_title( $title );
    ?>
        <h2 id="<?php echo esc_attr( $title_id ); ?>">
            <?php echo esc_html( $title ); ?>
        </h2>
    <?php endif; ?>

    <?php if ( $description ) : ?>
        <p><?php echo esc_html( $description ); ?></p>
    <?php endif; ?>

    <?php if ( $features ) : ?>
        <div class="theme-collection-features">
            <h3><?php esc_html_e( 'Features', 'devnahian' ); ?></h3>
            <?php foreach ( $features as $feature ) : ?>
                <?php if ( ! empty( $feature['feature_title'] ) ) : ?>
                    <span><?php echo esc_html( $feature['feature_title'] ); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="theme-collection-link">
        <?php if ( $demo_link ) : ?>
            <a href="<?php echo esc_url( $demo_link ); ?>" 
               class="theme-collection-btn track-click" 
               data-title="<?php echo esc_attr( $title ); ?>" 
               data-type="demo" 
               target="_blank" rel="noopener">
                <?php echo esc_html( $demo_link_label ? $demo_link_label : __( 'Demo', 'devnahian' ) ); ?>
            </a>
            <small>Clicks: <?php echo $demo_clicks; ?></small>
        <?php endif; ?>

        <?php if ( $download_link ) : ?>
            <a href="<?php echo esc_url( $download_link ); ?>" 
               class="theme-collection-btn track-click" 
               data-title="<?php echo esc_attr( $title ); ?>" 
               data-type="download" 
               target="_blank" rel="noopener">
                <?php echo esc_html( $download_link_label ? $download_link_label : __( 'Download Now', 'devnahian' ) ); ?>
            </a>
            <small>Clicks: <?php echo $download_clicks; ?></small>
        <?php endif; ?>
    </div>

</div>

<?php
// JSON-LD Structured Data for SEO
if ( $title ) {
    $structured_data = [
        "@context" => "https://schema.org",
        "@type" => "SoftwareApplication",
        "name" => $title,
        "image" => $image ? esc_url( $image ) : '',
        "description" => $description ? wp_strip_all_tags( $description ) : '',
        "applicationCategory" => "WordPress Theme",
        "url" => $demo_link ? esc_url( $demo_link ) : '',
    ];

    if ( $features ) {
        $feature_list = [];
        foreach ( $features as $feature ) {
            if ( ! empty( $feature['feature_title'] ) ) {
                $feature_list[] = $feature['feature_title'];
            }
        }
        if ( $feature_list ) {
            $structured_data["featureList"] = $feature_list;
        }
    }
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode( $structured_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?>
    </script>
<?php } ?>

<!-- Theme Collection Section End -->

<!-- Click Tracking JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.track-click').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const data = {
                action: 'track_theme_click',
                title: this.dataset.title,
                type: this.dataset.type,
            };
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });
        });
    });
});
</script>

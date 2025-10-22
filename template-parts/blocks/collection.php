<?php
/**
 * Collection Block Template.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'collection-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
    $id = sanitize_title( $block['anchor'] );
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'collection';
if ( ! empty( $block['className'] ) ) {
    $className .= ' ' . sanitize_html_class( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $className .= ' align' . sanitize_html_class( $block['align'] );
}

// Load ACF fields.
$thumb       = get_field( 'code_thumb' );
$title       = get_field( 'code_title' );
$author      = get_field( 'code_author' );
$tech_used   = get_field( 'code_tech_used' );
$description = get_field( 'code_description' );
$demo_link   = get_field( 'code_link' );
?>

<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">

    <?php if ( $thumb ) : ?>
    <img src="<?php echo $thumb;?>" alt="">
<?php endif; ?>
        <h4><?php echo esc_html( $title ); ?></h4>

    <table>

        <?php if ( $author ) : ?>
        <tr>
            <th><?php esc_html_e( 'Author', 'textdomain' ); ?></th>
            <td itemprop="author"><?php echo esc_html( $author ); ?></td>
        </tr>
        <?php endif; ?>

        <?php if ( $tech_used ) : ?>
        <tr>
            <th><?php esc_html_e( 'Tech used', 'textdomain' ); ?></th>
            <td itemprop="programmingLanguage"><?php echo esc_html( $tech_used ); ?></td>
        </tr>
        <?php endif; ?>

        <?php if ( $description ) : ?>
        <tr>
            <th><?php esc_html_e( 'Description', 'textdomain' ); ?></th>
            <td itemprop="description"><?php echo esc_html( $description ); ?></td>
        </tr>
        <?php endif; ?>

        <?php if ( $demo_link ) : ?>
        <tr>
            <th><?php esc_html_e( 'Live Preview', 'textdomain' ); ?></th>
            <td>
                <a href="<?php echo esc_url( $demo_link ); ?>" target="_blank" rel="noopener" itemprop="codeRepository">
                    <?php esc_html_e( 'Demo & Code', 'textdomain' ); ?>
                </a>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>

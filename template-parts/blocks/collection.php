<?php
// এখানে শুধু output থাকবে, কোনো function define নয়

// create id attribute for specific styling
$id = 'collection-' . $block['id'];

// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';

// ACF field variables
$item_image = get_field('item_image');
$item_author_name = get_field('item_author_name');
$item_link = get_field('item_link');
$item_made_with = get_field('item_made_with');
$item_title = get_field('item_title');
$item_description = get_field('item_description');
$item_compatible_browsers = get_field('item_compatible_browsers');
$item_responsive = get_field('item_responsive');
$item_dependencies = get_field('item_dependencies');
?>

<div id="<?php echo esc_attr($id); ?>" class="item <?php echo esc_attr($align_class); ?>">
    <div class="item-file">
        <?php if( !empty($item_image) ): ?>
            <img src="<?php echo esc_url($item_image['url']); ?>" alt="">
        <?php endif; ?>
    </div>

    <div class="item-meta">
        <div class="single-item-meta">
            <span>Author</span>
            <p><?php echo esc_html($item_author_name); ?></p>
        </div>
        <div class="single-item-meta">
            <span>Links</span>
            <p><a href="<?php echo esc_url($item_link); ?>">Preview & Download</a></p>
        </div>
        <div class="single-item-meta">
            <span>Made With</span>
            <p><?php echo esc_html($item_made_with); ?></p>
        </div>
    </div>

    <div class="item-content">
        <span>About Code</span>
        <h4><?php echo esc_html($item_title); ?></h4>
        <p><?php echo esc_html($item_description); ?></p>
    </div>

    <div class="item-bottom">
        <table>
            <tr>
                <td>Compatible browsers:</td>
                <td><?php echo esc_html(implode(', ', (array) $item_compatible_browsers)); ?></td>
            </tr>
            <tr>
                <td>Responsive:</td>
                <td><?php echo esc_html($item_responsive); ?></td>
            </tr>
            <tr>
                <td>Dependencies:</td>
                <td><?php echo esc_html($item_dependencies); ?></td>
            </tr>
        </table>
    </div>
</div>

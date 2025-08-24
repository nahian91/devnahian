<?php 
// শুধু output থাকবে, function define নয়

// create id attribute for specific styling
$id = 'vscode-code-' . $block['id'];

// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';

// ACF fields
$code     = get_field('code_text') ?: '';
$language = get_field('code_language') ?: 'php'; // default language
?>

<div id="<?php echo esc_attr($id); ?>" class="vscode-code-widget <?php echo esc_attr($align_class); ?>">
    <pre><code class="language-<?php echo esc_attr($language); ?>">
<?php echo esc_html($code); ?>
    </code></pre>
    <button class="copy-btn">Copy</button>
</div>

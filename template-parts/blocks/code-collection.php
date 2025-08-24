<?php 
// শুধুমাত্র output, function define নয়

// create id attribute for styling
$id = 'vscode-code-' . $block['id'];

// create align class from block setting
$align_class = $block['align'] ? 'align' . $block['align'] : '';

// ACF fields
$code     = get_field('code_text') ?: '';
$language = get_field('code_language') ?: 'php'; // default language
?>

<div id="<?php echo esc_attr($id); ?>" class="vscode-code-widget <?php echo esc_attr($align_class); ?>">
    <pre><code class="language-<?php echo esc_attr($language); ?>" id="<?php echo esc_attr($id); ?>-code"><?php echo esc_html($code); ?></code></pre>
    <button class="copy-btn" data-target="<?php echo esc_attr($id); ?>-code">Copy</button>
</div>

<style>
.vscode-code-widget {
    position: relative;
    background: #1e1e1e;
    border-radius: 6px;
    overflow-x: auto;
    padding: 15px;
    margin: 1em 0;
}

.vscode-code-widget code {
    font-family: 'Fira Code', monospace;
    font-size: 14px;
    color: #f8f8f2;
    white-space: pre;
}

.vscode-code-widget .copy-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #0073aa;
    color: #fff;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.vscode-code-widget .copy-btn:hover {
    background: #005177;
}
</style>
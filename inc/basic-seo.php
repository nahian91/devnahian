<?php
/**
 * DevNahian Full SEO Screen & Meta
 * All-in-one SEO for posts/pages/products/courses/tutor lessons
 * Single-file PHP solution (~270 lines)
 */
if (!defined('ABSPATH')) exit;

// -------------------------
// 1. Add Full-width SEO Meta Box
// -------------------------
function dn_full_seo_screen_meta_box() {
    $post_types = ['post','page','product','courses','tutor_lesson'];
    foreach ($post_types as $pt) {
        add_meta_box(
            'dn_full_seo_screen',
            'SEO Overview',
            'dn_full_seo_screen_html',
            $pt,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes','dn_full_seo_screen_meta_box');

// -------------------------
// 2. Display Meta Box HTML
// -------------------------
function dn_full_seo_screen_html($post){
    // Load saved meta
    $title = get_post_meta($post->ID,'_dn_seo_title',true);
    $description = get_post_meta($post->ID,'_dn_seo_description',true);
    $noindex = get_post_meta($post->ID,'_dn_seo_noindex',true);
    $focus_keywords = get_post_meta($post->ID,'_dn_seo_focus_keywords',true);

    $content = strip_tags($post->post_content);
    $words_total = str_word_count($content);

    // Process keywords
    $keywords = array_map('trim', explode(',', $focus_keywords));
    $scores = [];
    $densities = [];
    $total_score = 0;
    foreach($keywords as $kw){
        if(!$kw) continue;
        $score = 0;
        if(stripos($title,$kw)!==false) $score+=30;
        if(stripos($description,$kw)!==false) $score+=20;
        if(stripos(substr($content,0,200),$kw)!==false) $score+=30;
        preg_match_all('/<h[12]>.*?<\/h[12]>/i',$post->post_content,$matches);
        foreach($matches[0] as $heading){
            if(stripos($heading,$kw)!==false){ $score+=20; break; }
        }
        if($score>100) $score=100;
        $scores[$kw] = $score;
        $total_score += $score;

        // Keyword density
        $count = 0;
        if($words_total>0){
            $count = preg_match_all('/\b'.preg_quote($kw,'/').'\b/i',$content);
            $densities[$kw] = round(($count/$words_total)*100,1); // %
        }else{
            $densities[$kw] = 0;
        }
    }

    // Overall keyword score
    $overall_score = count($scores) ? round($total_score/count($scores)) : 0;
    $keyword_color = ($overall_score>=70?'green':($overall_score>=40?'orange':($overall_score>0?'red':'gray')));

    // Readability
    $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
    $avg_words_per_sentence = $sentences ? $words_total / count($sentences) : 0;
    $paragraphs = preg_split('/\n+/', $content, -1, PREG_SPLIT_NO_EMPTY);
    $avg_sentences_per_paragraph = $paragraphs ? count($sentences)/count($paragraphs) : 0;
    preg_match_all('/\b(is|are|was|were|be|been|being)\s+\w+ed\b/i',$content,$passive);
    $passive_count = count($passive[0]);
    $transitions = ['however','therefore','but','moreover','consequently','thus','although','meanwhile','furthermore'];
    $transition_count = 0;
    foreach($transitions as $word) $transition_count += substr_count(strtolower($content),$word);
    $read_score = 100;
    if($avg_words_per_sentence>20) $read_score -= 20;
    if($avg_sentences_per_paragraph>5) $read_score -= 20;
    if($passive_count>5) $read_score -= 20;
    if($transition_count<3) $read_score -= 20;
    if($read_score<0) $read_score=0;
    $read_color = ($read_score>=70?'green':($read_score>=40?'orange':($read_score>0?'red':'gray')));

    // Combined SEO score
    $combined_score = round(($overall_score*0.6)+($read_score*0.4));

    // Featured image
    $featured_image = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID,'medium') : '';

    // Google snippet preview
    $seo_title_preview = $title ? $title : get_the_title($post->ID);
    $seo_desc_preview = $description ? $description : (has_excerpt($post->ID)?get_the_excerpt($post->ID):wp_trim_words($content,30));
    ?>

    <div class="dn-full-seo" style="border:1px solid #ddd;padding:15px;background:#fafafa;">
        <h2 style="margin-top:0;">SEO Overview</h2>

        <!-- SEO Title & Description -->
        <p><strong>SEO Title:</strong><br>
        <input type="text" name="dn_seo_title" value="<?php echo esc_attr($title); ?>" style="width:100%;"></p>

        <p><strong>SEO Description:</strong><br>
        <textarea name="dn_seo_description" rows="4" style="width:100%;"><?php echo esc_textarea($description); ?></textarea></p>

        <!-- Focus Keywords -->
        <p><strong>Focus Keywords (comma separated):</strong><br>
        <input type="text" name="dn_seo_focus_keywords" value="<?php echo esc_attr($focus_keywords); ?>" style="width:100%;"></p>

        <!-- Keyword Scores Table -->
        <?php if(count($scores)): ?>
        <h3>Keyword Scores & Density</h3>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th style="border:1px solid #ccc;padding:5px;">Keyword</th><th style="border:1px solid #ccc;padding:5px;">Score</th><th style="border:1px solid #ccc;padding:5px;">Density (%)</th></tr>
            <?php foreach($scores as $kw=>$score):
                $kw_color = ($score>=70?'green':($score>=40?'orange':($score>0?'red':'gray')));
            ?>
            <tr>
                <td style="border:1px solid #ccc;padding:5px;"><?php echo esc_html($kw); ?></td>
                <td style="border:1px solid #ccc;padding:5px;color:<?php echo $kw_color;?>;"><?php echo $score;?>/100</td>
                <td style="border:1px solid #ccc;padding:5px;"><?php echo $densities[$kw]; ?>%</td>
            </tr>
            <?php endforeach;?>
        </table>
        <?php endif; ?>

        <!-- Scores Summary -->
        <p><strong>Overall Keyword Score:</strong> <span style="color:<?php echo $keyword_color;?>;"><?php echo $overall_score;?>/100</span></p>
        <p><strong>Readability Score:</strong> <span style="color:<?php echo $read_color;?>;"><?php echo $read_score;?>/100</span></p>
        <p><strong>Combined SEO Score:</strong> <span style="color:<?php echo ($combined_score>=70?'green':($combined_score>=40?'orange':'red'));?>;"><?php echo $combined_score;?>/100</span></p>

        <!-- Noindex -->
        <p><label><input type="checkbox" name="dn_seo_noindex" value="1" <?php checked($noindex,1);?>> Noindex this item?</label></p>

        <!-- Featured Image -->
        <?php if($featured_image): ?>
        <p><strong>Featured Image Preview:</strong><br><img src="<?php echo esc_url($featured_image); ?>" style="max-width:200px;"></p>
        <?php endif; ?>

        <!-- Google Snippet Preview -->
        <h3>Google Search Preview</h3>
        <div style="border:1px solid #ccc;padding:10px;background:#fff;">
            <p style="color:#1a0dab;font-size:18px;margin:0;"><?php echo esc_html($seo_title_preview); ?></p>
            <p style="color:#006621;font-size:14px;margin:0;"><?php echo esc_url(get_permalink($post->ID)); ?></p>
            <p style="color:#545454;font-size:13px;margin-top:5px;"><?php echo esc_html($seo_desc_preview); ?></p>
        </div>
    </div>

    <?php
}

// -------------------------
// 3. Save Meta Data
// -------------------------
function dn_save_full_seo_meta($post_id){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if(isset($_POST['dn_seo_title'])) update_post_meta($post_id,'_dn_seo_title',sanitize_text_field($_POST['dn_seo_title']));
    if(isset($_POST['dn_seo_description'])) update_post_meta($post_id,'_dn_seo_description',sanitize_textarea_field($_POST['dn_seo_description']));
    if(isset($_POST['dn_seo_focus_keywords'])) update_post_meta($post_id,'_dn_seo_focus_keywords',sanitize_text_field($_POST['dn_seo_focus_keywords']));
    $noindex = isset($_POST['dn_seo_noindex']) ? 1 : 0;
    update_post_meta($post_id,'_dn_seo_noindex',$noindex);
}
add_action('save_post','dn_save_full_seo_meta');

// -------------------------
// 4. Output Open Graph & Twitter Meta in Head
// -------------------------
function dn_output_seo_meta(){
    if(is_singular()){
        global $post;
        $title = get_post_meta($post->ID,'_dn_seo_title',true) ?: get_the_title($post);
        $description = get_post_meta($post->ID,'_dn_seo_description',true) ?: (has_excerpt($post->ID)?get_the_excerpt($post->ID):wp_trim_words(strip_tags($post->post_content),30));
        $social_image = has_post_thumbnail($post->ID)?get_the_post_thumbnail_url($post,'full'):'';

        echo '<meta property="og:title" content="'.esc_attr($title).'">'."\n";
        echo '<meta property="og:description" content="'.esc_attr($description).'">'."\n";
        if($social_image) echo '<meta property="og:image" content="'.esc_url($social_image).'">'."\n";

        echo '<meta name="twitter:title" content="'.esc_attr($title).'">'."\n";
        echo '<meta name="twitter:description" content="'.esc_attr($description).'">'."\n";
        if($social_image) echo '<meta name="twitter:image" content="'.esc_url($social_image).'">'."\n";

        // Noindex
        $noindex = get_post_meta($post->ID,'_dn_seo_noindex',true);
        if($noindex) echo '<meta name="robots" content="noindex,follow">'."\n";
    }
}
add_action('wp_head','dn_output_seo_meta',1);

// -------------------------
// 5. Output JSON-LD Schema
// -------------------------
function dn_output_seo_schema(){
    if(is_singular()){
        global $post;
        $title = get_post_meta($post->ID,'_dn_seo_title',true) ?: get_the_title($post);
        $description = get_post_meta($post->ID,'_dn_seo_description',true) ?: (has_excerpt($post->ID)?get_the_excerpt($post->ID):wp_trim_words(strip_tags($post->post_content),30));
        $social_image = has_post_thumbnail($post->ID)?get_the_post_thumbnail_url($post,'full'):'';

        $schema = [
            "@context"=>"https://schema.org",
            "@type"=>"Article",
            "mainEntityOfPage"=>["@type"=>"WebPage","@id"=>get_permalink()],
            "headline"=>$title,
            "description"=>$description,
            "author"=>["@type"=>"Person","name"=>get_the_author()],
        ];
        if($social_image) $schema["image"]=esc_url($social_image);

        echo '<script type="application/ld+json">'.wp_json_encode($schema,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>'."\n";
    }
}
add_action('wp_head','dn_output_seo_schema',20);


// -------------------------
// 6. Add SEO Score Column in Admin Post List
// -------------------------

// Add a new column
function dn_add_seo_score_column($columns){
    $columns['dn_seo_score'] = 'SEO Score';
    return $columns;
}
add_filter('manage_post_posts_columns', 'dn_add_seo_score_column');
add_filter('manage_page_posts_columns', 'dn_add_seo_score_column');

// Make it sortable
function dn_seo_score_column_sortable($columns){
    $columns['dn_seo_score'] = 'dn_seo_score';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns','dn_seo_score_column_sortable');
add_filter('manage_edit-page_sortable_columns','dn_seo_score_column_sortable');

// Display the SEO score
function dn_show_seo_score_column($column, $post_id){
    if($column == 'dn_seo_score'){
        $focus_keywords = get_post_meta($post_id,'_dn_seo_focus_keywords',true);
        $title = get_post_meta($post_id,'_dn_seo_title',true);
        $description = get_post_meta($post_id,'_dn_seo_description',true);
        $content = strip_tags(get_post_field('post_content',$post_id));
        $words_total = str_word_count($content);

        $keywords = array_map('trim', explode(',', $focus_keywords));
        $scores = [];
        $total_score = 0;
        foreach($keywords as $kw){
            if(!$kw) continue;
            $score = 0;
            if(stripos($title,$kw)!==false) $score+=30;
            if(stripos($description,$kw)!==false) $score+=20;
            if(stripos(substr($content,0,200),$kw)!==false) $score+=30;
            preg_match_all('/<h[12]>.*?<\/h[12]>/i',get_post_field('post_content',$post_id),$matches);
            foreach($matches[0] as $heading){
                if(stripos($heading,$kw)!==false){ $score+=20; break; }
            }
            if($score>100) $score=100;
            $scores[$kw] = $score;
            $total_score += $score;
        }

        $overall_score = count($scores) ? round($total_score/count($scores)) : 0;

        // Readability score
        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $avg_words_per_sentence = $sentences ? $words_total / count($sentences) : 0;
        $paragraphs = preg_split('/\n+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $avg_sentences_per_paragraph = $paragraphs ? count($sentences)/count($paragraphs) : 0;
        preg_match_all('/\b(is|are|was|were|be|been|being)\s+\w+ed\b/i',$content,$passive);
        $passive_count = count($passive[0]);
        $transitions = ['however','therefore','but','moreover','consequently','thus','although','meanwhile','furthermore'];
        $transition_count = 0;
        foreach($transitions as $word) $transition_count += substr_count(strtolower($content),$word);
        $read_score = 100;
        if($avg_words_per_sentence>20) $read_score -= 20;
        if($avg_sentences_per_paragraph>5) $read_score -= 20;
        if($passive_count>5) $read_score -= 20;
        if($transition_count<3) $read_score -= 20;
        if($read_score<0) $read_score=0;

        // Combined SEO score
        $combined_score = round(($overall_score*0.6)+($read_score*0.4));

        // Color code
        $color = $combined_score>=70?'green':($combined_score>=40?'orange':'red');

        echo '<span style="color:'.$color.';font-weight:bold;">'.$combined_score.'/100</span>';
    }
}
add_action('manage_post_posts_custom_column','dn_show_seo_score_column',10,2);
add_action('manage_page_posts_custom_column','dn_show_seo_score_column',10,2);


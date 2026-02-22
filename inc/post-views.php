<?php
/**
 * Infinity Unique Daily Views Tracker + Realistic Watch Time + 7-Day/30-Day Analytics
 * Author: Abdullah Nahian (improved)
 * Website: https://devnahian.com
 */

// ----------------------------
// Helpers
// ----------------------------
if (!function_exists('infinity_get_user_ip')) {
    function infinity_get_user_ip() {
        // Try common headers (may include comma-separated values, take first)
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($parts[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field($ip ?: '0.0.0.0');
    }
}

if (!function_exists('format_watch_time')) {
    function format_watch_time($seconds) {
        $seconds = (int) round($seconds);
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return $h > 0 ? sprintf("%02d:%02d:%02d", $h, $m, $s) : sprintf("%02d:%02d", $m, $s);
    }
}

// ----------------------------
// Track Unique Daily Views
// ----------------------------
// NOTE: use template_redirect so is_single() works
function infinity_track_unique_post_views() {
    if (!is_single()) return;
    global $post;
    if (empty($post->ID)) return;

    $post_id = (int) $post->ID;
    $today = date('Y-m-d', current_time('timestamp'));
    $meta_key = '_infinity_unique_views_' . $today;
    $user_ip = infinity_get_user_ip();
    $cookie_name = 'infinity_post_' . $post_id;

    $viewers = get_post_meta($post_id, $meta_key, true);
    if (!is_array($viewers)) $viewers = [];

    // build ip list for quick lookup
    $ips = array_column($viewers, 'ip');
    if (!in_array($user_ip, $ips) && !isset($_COOKIE[$cookie_name])) {
        $viewers[] = ['ip' => $user_ip, 'time' => date('Y-m-d H:i:s', current_time('timestamp'))];
        update_post_meta($post_id, $meta_key, $viewers);

        // expire at end of WP day (server-aware)
        $end_of_day = strtotime('tomorrow', current_time('timestamp')) - 1;
        setcookie($cookie_name, 'true', $end_of_day, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('template_redirect', 'infinity_track_unique_post_views', 20);

// ----------------------------
// Track Total Views (simple counter)
// ----------------------------
function infinity_track_total_post_views() {
    if (!is_single()) return;
    global $post;
    if (empty($post->ID)) return;

    $post_id = (int) $post->ID;
    $cookie_name = 'viewed_post_' . $post_id;

    if (!isset($_COOKIE[$cookie_name])) {
        $count_key = 'post_views_count';
        $count = (int) get_post_meta($post_id, $count_key, true);
        $count = $count + 1;
        update_post_meta($post_id, $count_key, $count);

        // short-lived cookie to avoid multiple increments per hour
        setcookie($cookie_name, 'true', time() + 3600, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('template_redirect', 'infinity_track_total_post_views', 20);

// ----------------------------
// Enqueue (inline) JS for Watch Time
// (keeps your sendBeacon approach — works well for unload events)
// ----------------------------
function infinity_enqueue_watchtime_script() {
    if (!is_single()) return;
    global $post;
    if (empty($post->ID)) return;
    $post_id = (int) $post->ID;

    // Optionally add a nonce here. If you want nonce protection, generate one and
    // include it in the sendBeacon params. Many sendBeacon environments can send it.
    // $nonce = wp_create_nonce('infinity_watchtime_' . $post_id);
    ?>
    <script>
    (function(){
        var startTime = Date.now();
        window.addEventListener('beforeunload', function () {
            try {
                var watchTime = Math.floor((Date.now() - startTime) / 1000);
                // use sendBeacon for reliability
                var data = new URLSearchParams();
                data.append('action', 'infinity_save_watchtime');
                data.append('post_id', '<?php echo esc_js($post_id); ?>');
                data.append('watch_time', watchTime);
                // If you enabled nonce server-side, add it:
                // data.append('security', '<?php // echo esc_js($nonce); ?>');

                navigator.sendBeacon('<?php echo esc_js(admin_url("admin-ajax.php")); ?>', data);
            } catch (e) {
                // fail silently
            }
        });
    })();
    </script>
    <?php
}
add_action('wp_footer','infinity_enqueue_watchtime_script');

// ----------------------------
// AJAX Save Watch Time (Capped)
// ----------------------------
function infinity_save_watchtime_ajax() {
    $post_id = intval($_POST['post_id'] ?? 0);
    $watch_time = intval($_POST['watch_time'] ?? 0);

    if (!$post_id || $watch_time <= 0) {
        wp_send_json_error('invalid');
        wp_die();
    }

    // Optional: if you add nonce verification, uncomment below:
    // if (!wp_verify_nonce($_POST['security'] ?? '', 'infinity_watchtime_' . $post_id)) {
    //     wp_send_json_error('bad_nonce');
    //     wp_die();
    // }

    $max_daily_watch = 3600; // 1 hour per user per post
    $today = date('Y-m-d', current_time('timestamp'));
    $watch_key = '_infinity_watch_time_' . $today;

    $watchers = get_post_meta($post_id, $watch_key, true);
    if (!is_array($watchers)) $watchers = [];

    $user_ip = infinity_get_user_ip();
    $current = isset($watchers[$user_ip]) ? intval($watchers[$user_ip]) : 0;
    $watchers[$user_ip] = min($current + $watch_time, $max_daily_watch);

    update_post_meta($post_id, $watch_key, $watchers);

    wp_send_json_success();
    wp_die();
}
add_action('wp_ajax_infinity_save_watchtime','infinity_save_watchtime_ajax');
add_action('wp_ajax_nopriv_infinity_save_watchtime','infinity_save_watchtime_ajax');

// ----------------------------
// Helper Functions (public)
// ----------------------------
function get_post_views($post_id){
    return (int) get_post_meta($post_id,'post_views_count',true) ?: 0;
}

function get_post_avg_watch_time($post_id,$date=''){
    $date = $date ?: date('Y-m-d',current_time('timestamp'));
    $watchers = get_post_meta($post_id,'_infinity_watch_time_'.$date,true);
    if(!is_array($watchers) || empty($watchers)) return 0;
    $total = array_sum($watchers);
    $unique = count($watchers);
    return $unique ? round($total/$unique) : 0;
}

// ----------------------------
// Admin Columns (post list) - scoped to 'post' post type
// ----------------------------
function infinity_add_post_views_column($columns){
    $columns['infinity_total_views'] = 'Total Views';
    $columns['infinity_today_unique'] = "Today's Unique Views";
    $columns['infinity_avg_watch'] = "Avg Watch Time";
    return $columns;
}
add_filter('manage_post_posts_columns','infinity_add_post_views_column');

function infinity_show_post_views_column($column,$post_id){
    if($column=='infinity_total_views') {
        echo esc_html( get_post_meta($post_id,'post_views_count',true) ?: 0 );
    }
    if($column=='infinity_today_unique'){
        $today = date('Y-m-d',current_time('timestamp'));
        $views = get_post_meta($post_id,'_infinity_unique_views_'.$today,true);
        echo esc_html( is_array($views) ? count($views) : 0 );
    }
    if($column=='infinity_avg_watch'){
        echo esc_html( format_watch_time( get_post_avg_watch_time($post_id) ) );
    }
}
add_action('manage_post_posts_custom_column','infinity_show_post_views_column',10,2);

// ----------------------------
// Admin Menu & Reports Page
// ----------------------------
function infinity_add_views_report_submenu(){
    add_submenu_page(
        'edit.php',
        'Infinity Analytics',
        'Analytics',
        'manage_options',
        'today-views-report',
        'infinity_today_views_report_page'
    );
}
add_action('admin_menu','infinity_add_views_report_submenu');

// ----------------------------
// Reports Page Implementation
// ----------------------------
function infinity_today_views_report_page(){
    $today = date('Y-m-d', current_time('timestamp'));
    $all_posts = get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>-1]);
    $active_tab = sanitize_text_field($_GET['tab'] ?? 'today');

    ?>
    <div class="wrap">
        <h1>📈 Infinity Analytics</h1>
        <p><strong>Date:</strong> <?php echo esc_html($today); ?></p>
        <h2 class="nav-tab-wrapper">
            <a href="?page=today-views-report&tab=today" class="nav-tab <?php echo $active_tab=='today'?'nav-tab-active':''; ?>">Today</a>
            <a href="?page=today-views-report&tab=7days" class="nav-tab <?php echo $active_tab=='7days'?'nav-tab-active':''; ?>">Last 7 Days</a>
            <a href="?page=today-views-report&tab=30days" class="nav-tab <?php echo $active_tab=='30days'?'nav-tab-active':''; ?>">Last 30 Days</a>
            <a href="?page=today-views-report&tab=reports" class="nav-tab <?php echo $active_tab=='reports'?'nav-tab-active':''; ?>">Reports</a>
            <a href="?page=today-views-report&tab=categories" class="nav-tab <?php echo $active_tab=='categories'?'nav-tab-active':''; ?>">Categories</a>
            <a href="?page=today-views-report&tab=plugins" class="nav-tab <?php echo $active_tab=='plugins'?'nav-tab-active':''; ?>">Plugins</a>
            <a href="?page=today-views-report&tab=all_content" 
   class="nav-tab <?php echo $active_tab=='all_content'?'nav-tab-active':''; ?>">
   All Content
</a>
        </h2>
        <div class="tab-content" style="margin-top:20px;">
    <?php

    // --------- Today tab ----------
    if($active_tab=='today'){
        $latest_views_arr = [];
        $top_posts_today_arr = [];
        $total_views = $total_watch = 0;
        $yesterday = date('Y-m-d', strtotime($today.' -1 day'));
        $y_total_views = $y_total_watch = 0;

        foreach($all_posts as $post){
            $views = get_post_meta($post->ID,'_infinity_unique_views_'.$today,true);
            $views = is_array($views)?$views:[];
            $total_views += count($views);

            $watch = get_post_meta($post->ID,'_infinity_watch_time_'.$today,true);
            $watch = is_array($watch)?$watch:[];
            foreach($watch as $sec) $total_watch += min(intval($sec),3600);

            foreach($views as $v) {
                if (!empty($v['time'])) {
                    $latest_views_arr[] = ['post'=>$post,'time'=>strtotime($v['time'])];
                }
            }

            if(count($views)>0){
                $avg_watch = count($views) ? round(array_sum($watch)/count($views),1) : 0;
                $top_posts_today_arr[] = ['post'=>$post,'views'=>count($views),'avg_watch'=>$avg_watch];
            }

            // Yesterday
            $y_views = get_post_meta($post->ID,'_infinity_unique_views_'.$yesterday,true); $y_views = is_array($y_views)?$y_views:[];
            $y_total_views += count($y_views);
            $y_watch = get_post_meta($post->ID,'_infinity_watch_time_'.$yesterday,true); $y_watch = is_array($y_watch)?array_sum($y_watch):0;
            $y_total_watch += $y_watch;
        }

        $avg_watch = $total_views?($total_watch/$total_views):0;
        $percent_change = $y_total_views ? round((($total_views-$y_total_views)/$y_total_views)*100,1):100;
        $trend_icon = $percent_change>=0?'▲':'▼'; $trend_color = $percent_change>=0?'#27ae60':'#e74c3c';

        // 4 Cards
        echo '<div style="display:flex;flex-wrap:wrap;gap:15px;margin:20px 0;">';
        $cards = [
            ['📅 Today Total Views',$total_views,'#0073aa'],
            ['⏱ Average Watch Time',format_watch_time($avg_watch),'#16a085'],
            ['📆 Yesterday Views',$y_total_views,'#f39c12'],
            ['📊 Today vs Yesterday',"<span style='color:$trend_color'>$trend_icon ".abs($percent_change)."%</span>",'#333']
        ];
        foreach($cards as $c) echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;"><h3 style="margin:0 0 10px;font-size:16px;">'.$c[0].'</h3><p style="font-size:22px;font-weight:bold;color:'.$c[2].';">'.$c[1].'</p></div>';
        echo '</div>';

        // Latest 5 Viewed Posts
        echo '<h2>🕒 Latest 5 Viewed Posts Today</h2>';
        usort($latest_views_arr,function($a,$b){ return $b['time']-$a['time']; });
        $latest_views_arr=array_slice($latest_views_arr,0,5);
        echo '<div style="display:flex;flex-wrap:wrap;gap:15px;">';
        foreach($latest_views_arr as $item){
            $p=$item['post']; $pid=$p->ID;
            $thumb = get_the_post_thumbnail_url($pid,'medium') ?: 'https://via.placeholder.com/150';
            $avg = format_watch_time(get_post_avg_watch_time($pid));
            $last = date('H:i:s',$item['time']);
            echo '<div style="width:200px;background:#fff;padding:12px;border-radius:10px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">';
            echo "<img src='".esc_url($thumb)."' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
            echo "<p>Last Viewed: $last</p><p>Avg Watch Time: $avg</p></div>";
        }
        echo '</div>';

        // Top 20 Posts Today
        echo '<h2>🔥 Top 20 Posts Today</h2>';
        usort($top_posts_today_arr,function($a,$b){ return $b['views']-$a['views']; });
        $top_posts_today_arr=array_slice($top_posts_today_arr,0,20);
        echo '<div style="display:flex;flex-wrap:wrap;gap:15px;">';
        foreach($top_posts_today_arr as $item){
            $pid=$item['post']->ID; $thumb = get_the_post_thumbnail_url($pid,'medium') ?: 'https://via.placeholder.com/150';
            echo '<div style="width:200px;background:#fff;padding:12px;border-radius:10px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">';
            echo "<img src='".esc_url($thumb)."' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
            echo '<a href="'.esc_url(get_permalink($pid)).'" target="_blank" style="display:block;margin:5px 0;font-weight:bold;">'.esc_html(get_the_title($pid)).'</a>';
            echo '<p>Views: '.intval($item['views']).'</p><p>Avg Watch: '.esc_html(format_watch_time($item['avg_watch'])).'</p></div>';
        }
        echo '</div>';
    }

    // ----- 7 days / 30 days tabs -----
    if($active_tab=='7days'||$active_tab=='30days'){
        $days = $active_tab=='7days'?7:30;
        echo "<h2>📅 Last $days Days Top 20 Posts</h2><div style='display:flex;flex-wrap:wrap;gap:15px;'>";
        $top_posts = [];
        $today_ts = current_time('timestamp');

        foreach($all_posts as $p){
            $pid = $p->ID;
            $total_views = 0;
            $total_watch = 0;
            $prev_total_views = 0;

            foreach(range(0,$days-1) as $d){
                $date = date('Y-m-d', strtotime($today." -$d days", $today_ts));
                $v = get_post_meta($pid,'_infinity_unique_views_'.$date,true); $v = is_array($v)?count($v):0;
                $w = get_post_meta($pid,'_infinity_watch_time_'.$date,true); $w = is_array($w)?array_sum($w):0;

                $total_views += $v;
                $total_watch += $w;
            }

            foreach(range($days,2*$days-1) as $d){
                $date = date('Y-m-d', strtotime($today." -$d days", $today_ts));
                $v = get_post_meta($pid,'_infinity_unique_views_'.$date,true); $v = is_array($v)?count($v):0;
                $prev_total_views += $v;
            }

            if($total_views>0){
                $avg_watch = round($total_watch/$total_views,1);
                $avg_current = $total_views/$days;
                $avg_prev = $prev_total_views/$days;
                if($avg_prev>0){
                    $change = round((($avg_current - $avg_prev)/$avg_prev)*100,1);
                    $trend_icon = $change>=0?'▲':'▼';
                    $trend_color = $change>=0?'#27ae60':'#e74c3c';
                    $trend = "<span style='color:$trend_color;'>$trend_icon ".abs($change)."%</span>";
                } else { $trend = '-'; }
                $top_posts[] = ['post'=>$p,'views'=>$total_views,'avg_watch'=>$avg_watch,'trend'=>$trend];
            }
        }

        usort($top_posts,function($a,$b){ return $b['views']-$a['views']; });
        $top_posts = array_slice($top_posts,0,20);

        foreach($top_posts as $item){
            $pid = $item['post']->ID;
            $thumb = get_the_post_thumbnail_url($pid,'medium') ?: 'https://via.placeholder.com/150';
            echo '<div style="width:200px;background:#fff;padding:12px;border-radius:10px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">';
            echo "<img src='".esc_url($thumb)."' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
            echo '<a href="'.esc_url(get_permalink($pid)).'" target="_blank" style="display:block;margin:8px 0;font-weight:bold;font-size:15px;color:#333;">'.esc_html(get_the_title($pid)).'</a>';
            echo '<p>Views: <strong>'.intval($item['views']).'</strong> '.$item['trend'].'</p>';
            echo '<p>Avg Watch: <strong>'.esc_html(format_watch_time($item['avg_watch'])).'</strong></p>';
            echo '</div>';
        }

        echo '</div>';
    }

if ($active_tab == 'reports') {

    /* --------------------------------
     * Helper Functions
     * -------------------------------- */

    if (!function_exists('get_total_views_by_meta')) {
        function get_total_views_by_meta($post_id) {
            $keys = get_post_custom_keys($post_id);
            if (!$keys) return 0;

            $total = 0;
            foreach ($keys as $key) {
                if (strpos($key, '_infinity_unique_views_') === 0) {
                    $views = get_post_meta($post_id, $key, true);
                    if (is_array($views)) {
                        $total += count($views);
                    }
                }
            }
            return $total;
        }
    }

    // Total views up to a specific date (YYYY-MM-DD)
    if (!function_exists('get_total_views_by_date')) {
        function get_total_views_by_date($post_id, $date) {
            $keys = get_post_custom_keys($post_id);
            if (!$keys) return 0;

            $total = 0;
            foreach ($keys as $key) {
                if (strpos($key, '_infinity_unique_views_') === 0) {
                    $key_date = str_replace('_infinity_unique_views_', '', $key);
                    if ($key_date <= $date) {
                        $views = get_post_meta($post_id, $key, true);
                        if (is_array($views)) {
                            $total += count($views);
                        }
                    }
                }
            }
            return $total;
        }
    }

    if (!function_exists('get_todays_views')) {
        function get_todays_views($post_id) {
            $today = wp_date('Y-m-d');
            $views = get_post_meta($post_id, '_infinity_unique_views_' . $today, true);
            return is_array($views) ? count($views) : 0;
        }
    }

    if (!function_exists('get_yesterdays_views')) {
        function get_yesterdays_views($post_id) {
            $yesterday = wp_date('Y-m-d', strtotime('-1 day', current_time('timestamp')));
            $views = get_post_meta($post_id, '_infinity_unique_views_' . $yesterday, true);
            return is_array($views) ? count($views) : 0;
        }
    }

    if (!function_exists('get_avg_watch_time')) {
        function get_avg_watch_time($post_id) {
            $today = wp_date('Y-m-d');
            $watchers = get_post_meta($post_id, '_infinity_watch_time_' . $today, true);
            if (!is_array($watchers) || empty($watchers)) return 0;
            return round(array_sum($watchers) / count($watchers));
        }
    }

    if (!function_exists('format_watch_time')) {
        function format_watch_time($seconds) {
            return floor($seconds / 60) . 'm ' . ($seconds % 60) . 's';
        }
    }

    if (!function_exists('get_theme_collection_block_count')) {
        function get_theme_collection_block_count($post_id) {
            $post = get_post($post_id);
            if (empty($post->post_content)) return 0;

            $blocks = parse_blocks($post->post_content);
            $target = 'acf/theme-collections';
            $count  = 0;

            $walker = function ($blocks) use (&$count, $target, &$walker) {
                foreach ($blocks as $block) {
                    if (!empty($block['blockName']) && $block['blockName'] === $target) {
                        $count++;
                    }
                    if (!empty($block['innerBlocks'])) {
                        $walker($block['innerBlocks']);
                    }
                }
            };

            $walker($blocks);
            return $count;
        }
    }

    /* --------------------------------
     * Get All Posts
     * -------------------------------- */

    $all_posts = get_posts([
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    /* --------------------------------
     * Summary Cards
     * -------------------------------- */

    $thresholds = [10,20,30,40,50,60,70,80,90,100,200,300,400,500,600,700,800,900,1000];

    $cards = [
        ['label' => 'Total Posts', 'count' => count($all_posts)]
    ];

    foreach ($thresholds as $t) {
        $cards[] = [
            'label'       => "Total {$t}+ Post Views",
            'count'       => 0,
            'today_added' => 0,
            'value'       => $t
        ];
    }

    $cards[] = [
        'label' => 'Posts with < 5 Collections',
        'count' => 0,
        'is_collection' => true
    ];

    $today     = wp_date('Y-m-d');
    $yesterday = wp_date('Y-m-d', strtotime('-1 day', current_time('timestamp')));

    foreach ($all_posts as $p) {

        $total_today     = get_total_views_by_date($p->ID, $today);
        $total_yesterday = get_total_views_by_date($p->ID, $yesterday);

        foreach ($thresholds as $i => $t) {

            if ($total_today >= $t) {
                $cards[$i + 1]['count']++;
            }

            // Newly crossed today
            if ($total_yesterday < $t && $total_today >= $t) {
                $cards[$i + 1]['today_added']++;
            }
        }

        if (get_theme_collection_block_count($p->ID) < 5) {
            $cards[count($cards) - 1]['count']++;
        }
    }

    /* --------------------------------
     * Render Summary Cards (CSS SAME)
     * -------------------------------- */

    echo '<div style="display:flex;flex-wrap:wrap;gap:15px;margin:20px 0;">';

    $colors = [
        '#0073aa','#1abc9c','#3498db','#9b59b6','#f39c12',
        '#e67e22','#e74c3c','#2ecc71','#d35400','#c0392b',
        '#8e44ad','#16a085','#2980b9','#c0392b','#8e44ad'
    ];

    foreach ($cards as $i => $c) {

        $value = intval($c['count']);

        if (!empty($c['today_added'])) {
            $value .= ' <span style="font-size:14px;opacity:.7;">(+' . intval($c['today_added']) . ')</span>';
        }

        echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;
            box-shadow:0 4px 12px rgba(0,0,0,.08);text-align:center;">';

        echo '<h3 style="margin-bottom:10px;">' . esc_html($c['label']) . '</h3>';
        echo '<p style="font-size:22px;font-weight:bold;color:' . ($colors[$i] ?? '#333') . ';">' . $value . '</p>';
        echo '</div>';
    }

    echo '</div>';

    /* --------------------------------
     * Sort Posts by Total Views
     * -------------------------------- */

    usort($all_posts, function ($a, $b) {
        return get_total_views_by_meta($b->ID) - get_total_views_by_meta($a->ID);
    });

    // ---------- Build Ranking Maps ----------

// Today ranking (by total views)
$today_rank_posts = $all_posts;
usort($today_rank_posts, function ($a, $b) {
    return get_total_views_by_meta($b->ID) - get_total_views_by_meta($a->ID);
});

$today_rank_map = [];
$rank = 1;
foreach ($today_rank_posts as $p) {
    $today_rank_map[$p->ID] = $rank++;
}

// Yesterday ranking
$yesterday_date = wp_date('Y-m-d', strtotime('-1 day', current_time('timestamp')));

$yesterday_rank_posts = $all_posts;
usort($yesterday_rank_posts, function ($a, $b) use ($yesterday_date) {
    return get_total_views_by_date($b->ID, $yesterday_date) - get_total_views_by_date($a->ID, $yesterday_date);
});

$yesterday_rank_map = [];
$rank = 1;
foreach ($yesterday_rank_posts as $p) {
    $yesterday_rank_map[$p->ID] = $rank++;
}


// ---------- Posts Table ----------
echo '<h2>📊 All Posts by Views</h2>';
echo '<table class="wp-list-table widefat fixed striped"><thead>
<tr>
    <th>Post Title</th>
    <th>Total Views</th>
    <th>Today\'s Views</th>
    <th>Yesterday\'s Views</th>
    <th>Last 7 Days Views</th>
    <th>Ranking</th>
</tr></thead><tbody>';

foreach ($all_posts as $post) {

    $total        = get_total_views_by_meta($post->ID);
    $today_v      = get_todays_views($post->ID);
    $yesterday_v  = get_yesterdays_views($post->ID);

    // Last 7 days total
    $week_total = 0;
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
        $v = get_post_meta($post->ID, '_infinity_unique_views_' . $date, true);
        if (is_array($v)) $week_total += count($v);
    }

    // Ranking logic
    $today_rank     = $today_rank_map[$post->ID] ?? null;
    $yesterday_rank = $yesterday_rank_map[$post->ID] ?? null;

    $rank_change_html = '<span style="color:#999;">—</span>';
    $top10_badge = '';

    if ($today_rank && $yesterday_rank) {
        $diff = $yesterday_rank - $today_rank;

        if ($diff > 0) {
            $rank_change_html = '<span style="color:#27ae60;font-weight:bold;">▲ +' . $diff . '</span>';
        } elseif ($diff < 0) {
            $rank_change_html = '<span style="color:#e74c3c;font-weight:bold;">▼ ' . abs($diff) . '</span>';
        }
    }

    // 🎉 Entered Top 10 for first time
    if ($today_rank <= 10 && ($yesterday_rank === null || $yesterday_rank > 10)) {
        $top10_badge = ' <span style="background:#f1c40f;color:#000;padding:2px 6px;border-radius:10px;font-size:11px;font-weight:bold;">TOP 10</span>';
    }

    echo '<tr>';

    echo '<td>
        <a target="_blank" href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html($post->post_title) . '</a>
    </td>';

    echo '<td>' . intval($total) . '</td>';

    // Today Views badge
    if ($today_v >= 20) {
        echo '<td><span style="background:#ff6347;color:#fff;padding:3px 8px;border-radius:12px;font-size:12px;">' . intval($today_v) . '</span></td>';
    } else {
        echo '<td>' . intval($today_v) . '</td>';
    }

    echo '<td>' . intval($yesterday_v) . '</td>';

    // Last 7 Days badge
    if ($week_total >= 50) {
        echo '<td><span style="background:#1abc9c;color:#fff;padding:3px 8px;border-radius:12px;font-size:12px;">' . intval($week_total) . '</span></td>';
    } else {
        echo '<td>' . intval($week_total) . '</td>';
    }

    echo '<td>
        <strong>#' . intval($today_rank) . '</strong>
        ' . $rank_change_html . $top10_badge . '
    </td>';

    echo '</tr>';
}

echo '</tbody></table>';
}







// ----- Categories Tab -----
// ----- Categories Tab with Percentage Change -----
if ($active_tab == 'categories') {
    echo '<h2>📊 Category Analytics & Comparison</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
        <tr>
            <th>Category Name</th>
            <th>Today Views</th>
            <th>Last 7 Days</th>
            <th>Last 30 Days</th>
            <th>Best Post</th>
        </tr>
    </thead>';
    echo '<tbody>';

    $categories = get_categories(['hide_empty' => false]);
    $categories_data = [];

    foreach ($categories as $cat) {
        $cat_posts = get_posts([
            'category' => $cat->term_id,
            'numberposts' => -1,
        ]);

        $today_total = $yesterday_total = 0;
        $week_total = $prev_week_total = 0;
        $month_total = $prev_month_total = 0;
        $best_post = ['title' => '', 'views' => 0];

        $today = date('Y-m-d', current_time('timestamp'));
        $yesterday = date('Y-m-d', strtotime('-1 day', current_time('timestamp')));

        foreach ($cat_posts as $p) {
            // Today / Yesterday
            $today_views_arr = get_post_meta($p->ID, '_infinity_unique_views_'.$today, true);
            $today_views_arr = is_array($today_views_arr) ? $today_views_arr : [];
            $today_count = count($today_views_arr);
            $today_total += $today_count;

            $yesterday_views_arr = get_post_meta($p->ID, '_infinity_unique_views_'.$yesterday, true);
            $yesterday_views_arr = is_array($yesterday_views_arr) ? $yesterday_views_arr : [];
            $yesterday_count = count($yesterday_views_arr);
            $yesterday_total += $yesterday_count;

            // Last 7 days / previous 7 days
            $week_count = $prev_week_count = 0;
            for ($i=0; $i<7; $i++){
                $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
                $v = get_post_meta($p->ID, '_infinity_unique_views_'.$date, true);
                if(is_array($v)) $week_count += count($v);
            }
            for ($i=7; $i<14; $i++){
                $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
                $v = get_post_meta($p->ID, '_infinity_unique_views_'.$date, true);
                if(is_array($v)) $prev_week_count += count($v);
            }
            $week_total += $week_count;
            $prev_week_total += $prev_week_count;

            // Last 30 days / previous 30 days
            $month_count = $prev_month_count = 0;
            for ($i=0; $i<30; $i++){
                $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
                $v = get_post_meta($p->ID, '_infinity_unique_views_'.$date, true);
                if(is_array($v)) $month_count += count($v);
            }
            for ($i=30; $i<60; $i++){
                $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
                $v = get_post_meta($p->ID, '_infinity_unique_views_'.$date, true);
                if(is_array($v)) $prev_month_count += count($v);
            }
            $month_total += $month_count;
            $prev_month_total += $prev_month_count;

            // Best post by last 30 days
            if($month_count > $best_post['views']){
                $best_post['views'] = $month_count;
                $best_post['title'] = get_the_title($p->ID);
            }
        }

        // Percentage changes
        $today_percent = $yesterday_total ? round((($today_total-$yesterday_total)/$yesterday_total)*100,1) : ($today_total ? 100 : 0);
        $week_percent  = $prev_week_total ? round((($week_total-$prev_week_total)/$prev_week_total)*100,1) : ($week_total ? 100 : 0);
        $month_percent = $prev_month_total ? round((($month_total-$prev_month_total)/$prev_month_total)*100,1) : ($month_total ? 100 : 0);

        // Trend arrows & colors
        $today_arrow = $today_percent >= 0 ? '▲' : '▼';
        $today_color = $today_percent >= 0 ? 'green' : 'red';

        $week_arrow = $week_percent >= 0 ? '▲' : '▼';
        $week_color = $week_percent >= 0 ? 'green' : 'red';

        $month_arrow = $month_percent >= 0 ? '▲' : '▼';
        $month_color = $month_percent >= 0 ? 'green' : 'red';

        // Save for sorting
        $categories_data[] = [
            'name' => $cat->name,
            'today_total' => $today_total,
            'today_percent' => $today_percent,
            'today_arrow' => $today_arrow,
            'today_color' => $today_color,
            'week_total' => $week_total,
            'week_percent' => $week_percent,
            'week_arrow' => $week_arrow,
            'week_color' => $week_color,
            'month_total' => $month_total,
            'month_percent' => $month_percent,
            'month_arrow' => $month_arrow,
            'month_color' => $month_color,
            'best_post' => $best_post['title'],
        ];
    }

    // Sort by Today Views descending
    usort($categories_data, function($a,$b){
        return $b['today_total'] - $a['today_total'];
    });

    foreach ($categories_data as $c) {
        echo '<tr>';
        echo '<td>'.esc_html($c['name']).'</td>';
        echo '<td style="color:'.$c['today_color'].';">'.intval($c['today_total']).' '.$c['today_arrow'].' '.abs($c['today_percent']).'%</td>';
        echo '<td style="color:'.$c['week_color'].';">'.intval($c['week_total']).' '.$c['week_arrow'].' '.abs($c['week_percent']).'%</td>';
        echo '<td style="color:'.$c['month_color'].';">'.intval($c['month_total']).' '.$c['month_arrow'].' '.abs($c['month_percent']).'%</td>';
        echo '<td>'.esc_html($c['best_post']).'</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

if ($active_tab == 'plugins') {
    $username  = 'nahian91';
    $cache_key = 'wp_org_stats_full_' . $username;
    
    // 1. Try to get cached data to keep the page fast
    $plugin_data_list = get_transient($cache_key);

    if (false === $plugin_data_list) {
        // 2. Fetch all plugins from the author
        $list_url = "https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]=$username&request[per_page]=100";
        $list_response = wp_remote_get($list_url);

        if (is_wp_error($list_response)) {
            echo '<div class="notice notice-error"><p>Error connecting to WordPress.org API.</p></div>';
            return;
        }

        $list_data = json_decode(wp_remote_retrieve_body($list_response));
        $plugin_data_list = [];

        if (!empty($list_data->plugins)) {
            foreach ($list_data->plugins as $plugin) {
                // 3. Fetch detailed daily stats for this specific plugin
                // limit=3 ensures we get Today and Yesterday regardless of timezone resets
                $stats_url = "https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug={$plugin->slug}&limit=3";
                $stats_response = wp_remote_get($stats_url);
                
                $today_count = 0;
                $yesterday_count = 0;

                if (!is_wp_error($stats_response)) {
                    $stats_raw = json_decode(wp_remote_retrieve_body($stats_response), true);
                    
                    if (!empty($stats_raw) && is_array($stats_raw)) {
                        krsort($stats_raw); // Sort keys (dates) newest to oldest
                        $counts = array_values($stats_raw);

                        $today_count     = isset($counts[0]) ? (int)$counts[0] : 0;
                        $yesterday_count = isset($counts[1]) ? (int)$counts[1] : 0;
                    }
                }

                $plugin_data_list[] = [
                    'name'      => $plugin->name,
                    'slug'      => $plugin->slug,
                    'today'     => $today_count,
                    'yesterday' => $yesterday_count,
                    'all_time'  => $plugin->downloaded,
                    'active'    => $plugin->active_installs
                ];
            }

            // 4. Order the list by TODAY'S downloads (highest first)
            usort($plugin_data_list, function($a, $b) {
                return $b['today'] <=> $a['today'];
            });

            // Cache the final processed array for 12 hours
            set_transient($cache_key, $plugin_data_list, 12 * HOUR_IN_SECONDS);
        }
    }

    // --- RENDER TABLE ---
    if (!empty($plugin_data_list)) {
        echo '<h2>Plugin Performance: ' . esc_html($username) . '</h2>';
        echo '<table class="wp-list-table widefat fixed striped" style="margin-top:20px; border-radius: 5px; overflow: hidden;">';
        echo '<thead>
                <tr>
                    <th style="font-weight:bold;">Plugin Name</th>
                    <th style="width:100px;">Today</th>
                    <th style="width:100px;">Yesterday</th>
                    <th style="width:130px;">Trend (vs Yesterday)</th>
                    <th style="width:120px;">Active Installs</th>
                    <th style="width:120px;">All Time</th>
                </tr>
              </thead>';
        echo '<tbody>';

        foreach ($plugin_data_list as $plugin) {
            $today = $plugin['today'];
            $yesterday = $plugin['yesterday'];
            $diff = $today - $yesterday;
            
            // Calculate Growth/Decline Indicator
            $trend_html = '<span style="color:#999;">—</span>';
            if ($diff > 0) {
                $pct = ($yesterday > 0) ? round(($diff / $yesterday) * 100) : 100;
                $trend_html = "<span style='color:#46b450; font-weight:bold;'>▲ {$pct}%</span> <small>(+{$diff})</small>";
            } elseif ($diff < 0) {
                $pct = ($yesterday > 0) ? abs(round(($diff / $yesterday) * 100)) : 0;
                $trend_html = "<span style='color:#dc3232; font-weight:bold;'>▼ {$pct}%</span> <small>({$diff})</small>";
            }

            echo "<tr>
                    <td><strong><a href='https://wordpress.org/plugins/{$plugin['slug']}/' target='_blank' style='text-decoration:none;'>{$plugin['name']}</a></strong></td>
                    <td style='font-size:1.1em; font-weight:bold; color:#2271b1;'>" . number_format($today) . "</td>
                    <td>" . number_format($yesterday) . "</td>
                    <td>{$trend_html}</td>
                    <td>" . (is_numeric($plugin['active']) ? number_format($plugin['active']) : $plugin['active']) . "</td>
                    <td>" . number_format($plugin['all_time']) . "</td>
                  </tr>";
        }

        echo '</tbody></table>';
        echo '<p style="color:#777; font-size:11px; margin-top:10px;">Data is synced with WordPress.org and cached for 12 hours.</p>';
    } else {
        echo '<p>No plugins found for user <strong>' . esc_html($username) . '</strong>.</p>';
    }
}  

// --------------------------------
// Global View Helpers
// --------------------------------
if (!function_exists('get_total_views_by_meta')) {
    function get_total_views_by_meta($post_id) {
        $keys = get_post_custom_keys($post_id);
        if (!$keys) return 0;

        $total = 0;
        foreach ($keys as $key) {
            if (strpos($key, '_infinity_unique_views_') === 0) {
                $views = get_post_meta($post_id, $key, true);
                if (is_array($views)) {
                    $total += count($views);
                }
            }
        }
        return $total;
    }
}
// ----------------------------
// Pages Tab
// ----------------------------
// ----------------------------
// All Content (Without Posts)
// ----------------------------
if ($active_tab == 'all_content') {

    echo '<h2>📊 All Content Analytics (Excluding Posts)</h2>';

    // Get all public post types
    $post_types = get_post_types(
        ['public' => true],
        'names'
    );

    // Remove default "post"
    $post_types = array_diff($post_types, ['post']);

    // Get all items from all selected post types
    $items = get_posts([
        'post_type'   => $post_types,
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    if (empty($items)) {
        echo '<p>No data available.</p>';
        return;
    }

    // Sort by total views
    usort($items, function ($a, $b) {
        return get_total_views_by_meta($b->ID) - get_total_views_by_meta($a->ID);
    });

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Total Views</th>
                <th>Today</th>
                <th>Yesterday</th>
                <th>Last 7 Days</th>
            </tr>
          </thead>';
    echo '<tbody>';

    foreach ($items as $item) {

        $post_type_obj = get_post_type_object($item->post_type);

        $total = get_total_views_by_meta($item->ID);

        $today = date('Y-m-d', current_time('timestamp'));
        $yesterday = date('Y-m-d', strtotime('-1 day', current_time('timestamp')));

        $today_v = get_post_meta($item->ID, '_infinity_unique_views_'.$today, true);
        $today_v = is_array($today_v) ? count($today_v) : 0;

        $yesterday_v = get_post_meta($item->ID, '_infinity_unique_views_'.$yesterday, true);
        $yesterday_v = is_array($yesterday_v) ? count($yesterday_v) : 0;

        $week_total = 0;
        for ($i=0; $i<7; $i++) {
            $date = date('Y-m-d', strtotime("-$i days", current_time('timestamp')));
            $v = get_post_meta($item->ID, '_infinity_unique_views_'.$date, true);
            if (is_array($v)) $week_total += count($v);
        }

        echo '<tr>';
        echo '<td>
                <a target="_blank" href="' . esc_url(get_permalink($item->ID)) . '">' 
                    . esc_html($item->post_title) . 
                '</a>
              </td>';
        echo '<td>' . esc_html($post_type_obj->labels->singular_name) . '</td>';
        echo '<td>' . intval($total) . '</td>';
        echo '<td>' . intval($today_v) . '</td>';
        echo '<td>' . intval($yesterday_v) . '</td>';
        echo '<td>' . intval($week_total) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

    echo '</div></div>';
}
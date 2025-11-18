<?php
/**
 * ==========================================================
 * 🚀 Infinity Unique Daily Views Tracker + Realistic Watch Time + 7-Day/30-Day Analytics
 * ==========================================================
 * Tracks unique post views, capped watch time, and shows:
 * - Today's Views Table
 * - Last 7/30 Days Analytics
 * - Latest 5 posts with last view time
 * - Average Watch Time per post
 * - Day-wise Reports
 * 
 * Author: Abdullah Nahian
 * Website: https://devnahian.com
 * ==========================================================
 */

// ============================
// Format Watch Time in HH:MM:SS or MM:SS
// ============================
function format_watch_time($seconds) {
    $seconds = round($seconds);
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    if ($h > 0) return sprintf("%02d:%02d:%02d", $h, $m, $s);
    return sprintf("%02d:%02d", $m, $s);
}

// ============================
// Track Unique Daily Views
// ============================
function infinity_track_unique_post_views() {
    if (!is_single()) return;
    global $post;
    if (empty($post->ID)) return;

    $post_id = $post->ID;
    $bd_timestamp = current_time('timestamp');
    $today = date('Y-m-d', $bd_timestamp);
    $meta_key = '_infinity_unique_views_' . $today;

    $user_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $cookie_name = 'infinity_post_' . $post_id;

    $viewers = get_post_meta($post_id, $meta_key, true);
    if (!is_array($viewers)) $viewers = [];

    if (!in_array($user_ip, array_column($viewers,'ip')) && !isset($_COOKIE[$cookie_name])) {
        $viewers[] = ['ip'=>$user_ip,'time'=>date('Y-m-d H:i:s',$bd_timestamp)];
        update_post_meta($post_id, $meta_key, $viewers);

        $end_of_day = strtotime('tomorrow', $bd_timestamp) - 1;
        setcookie($cookie_name, 'true', $end_of_day, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('init','infinity_track_unique_post_views');

// ============================
// Track Total Views
// ============================
function track_post_views() {
    if (!is_single()) return;
    global $post;
    $post_id = $post->ID;
    $cookie_name = 'viewed_post_' . $post_id;

    if (!isset($_COOKIE[$cookie_name])) {
        $count_key = 'post_views_count';
        $count = get_post_meta($post_id, $count_key, true);
        $count = $count ? $count + 1 : 1;
        update_post_meta($post_id, $count_key, $count);

        setcookie($cookie_name, 'true', time() + 3600, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('init','track_post_views');

// ============================
// Track Watch Time via JS
// ============================
function infinity_enqueue_watchtime_script() {
    if (!is_single()) return;
    global $post;
    $post_id = $post->ID;
    ?>
    <script>
    let startTime = Date.now();
    window.addEventListener("beforeunload", function() {
        let watchTime = Math.floor((Date.now() - startTime)/1000);
        navigator.sendBeacon('<?php echo admin_url("admin-ajax.php"); ?>', new URLSearchParams({
            action: 'infinity_save_watchtime',
            post_id: '<?php echo $post_id; ?>',
            watch_time: watchTime
        }));
    });
    </script>
    <?php
}
add_action('wp_footer','infinity_enqueue_watchtime_script');

// ============================
// AJAX Save Watch Time (with cap)
// ============================
function infinity_save_watchtime_ajax() {
    $post_id = intval($_POST['post_id'] ?? 0);
    $watch_time = intval($_POST['watch_time'] ?? 0);
    if (!$post_id || $watch_time <= 0) wp_send_json_error();

    $max_daily_watch = 3600; // 1 hour max per user per post per day
    $bd_timestamp = current_time('timestamp');
    $today = date('Y-m-d', $bd_timestamp);
    $watch_key = '_infinity_watch_time_' . $today;

    $watchers = get_post_meta($post_id, $watch_key, true);
    if (!is_array($watchers)) $watchers = [];

    $user_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $current = $watchers[$user_ip] ?? 0;
    $new_total = min($current + $watch_time, $max_daily_watch); // cap daily watch time
    $watchers[$user_ip] = $new_total;

    update_post_meta($post_id, $watch_key, $watchers);
    wp_send_json_success();
}
add_action('wp_ajax_infinity_save_watchtime','infinity_save_watchtime_ajax');
add_action('wp_ajax_nopriv_infinity_save_watchtime','infinity_save_watchtime_ajax');

// ============================
// Helper Functions
// ============================
function get_post_views($post_id){
    return get_post_meta($post_id,'post_views_count',true) ?: 0;
}

function get_post_avg_watch_time($post_id,$date=''){
    $date = $date ?: date('Y-m-d',current_time('timestamp'));
    $watch_key = '_infinity_watch_time_'.$date;
    $watchers = get_post_meta($post_id,$watch_key,true);
    if(!is_array($watchers) || empty($watchers)) return 0;

    $total_seconds = array_sum($watchers);
    $unique_users = count($watchers);
    $avg_seconds = $unique_users ? $total_seconds/$unique_users : 0;

    return round($avg_seconds); // in seconds
}

// ============================
// Admin Columns
// ============================
function infinity_add_post_views_column($columns){
    $columns['infinity_total_views'] = 'Total Views';
    $columns['infinity_today_unique'] = "Today's Unique Views";
    $columns['infinity_avg_watch'] = "Avg Watch Time";
    return $columns;
}
add_filter('manage_posts_columns','infinity_add_post_views_column');

function infinity_show_post_views_column($column,$post_id){
    if($column=='infinity_total_views'){
        echo get_post_meta($post_id,'post_views_count',true) ?: 0;
    }
    if($column=='infinity_today_unique'){
        $today = date('Y-m-d',current_time('timestamp'));
        $views = get_post_meta($post_id,'_infinity_unique_views_'.$today,true);
        echo is_array($views) ? count($views) : 0;
    }
    if($column=='infinity_avg_watch'){
        $avg = get_post_avg_watch_time($post_id);
        echo format_watch_time($avg);
    }
}
add_action('manage_posts_custom_column','infinity_show_post_views_column',10,2);

// ============================
// Admin Menu & Reports
// ============================
function infinity_add_views_report_submenu(){
    add_submenu_page(
        'edit.php',
        'Analytics',
        'Analytics',
        'manage_options',
        'today-views-report',
        'infinity_today_views_report_page'
    );
}
add_action('admin_menu','infinity_add_views_report_submenu');

// ============================
// Admin Page: Today, 7 Days, 30 Days, Reports
// ============================
function infinity_today_views_report_page(){
    $bd_timestamp = current_time('timestamp');
    $today = date('Y-m-d',$bd_timestamp);
    $all_posts = get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>-1]);
    $active_tab = $_GET['tab'] ?? 'today';
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">📈 Infinity Analytics</h1>
        <p><strong>Date:</strong> <?php echo esc_html($today); ?></p>

        <h2 class="nav-tab-wrapper">
            <a href="?page=today-views-report&tab=today" class="nav-tab <?php echo $active_tab=='today'?'nav-tab-active':''; ?>">Today</a>
            <a href="?page=today-views-report&tab=7days" class="nav-tab <?php echo $active_tab=='7days'?'nav-tab-active':''; ?>">Last 7 Days</a>
            <a href="?page=today-views-report&tab=30days" class="nav-tab <?php echo $active_tab=='30days'?'nav-tab-active':''; ?>">Last 30 Days</a>
            <a href="?page=today-views-report&tab=reports" class="nav-tab <?php echo $active_tab=='reports'?'nav-tab-active':''; ?>">Reports</a>
        </h2>

        <div class="tab-content" style="margin-top:20px;">
        <?php
if($active_tab=='today'){
    $total_views = 0;
    $total_watch = 0;

    $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
    $y_total_views = 0;
    $y_total_watch = 0;

    $latest_views = []; // for latest 5 viewed posts
    $top_posts_today = []; // for top 20 posts

    foreach($all_posts as $post){
        // Today's data
        $views = get_post_meta($post->ID,'_infinity_unique_views_'.$today,true);
        if(!is_array($views)) $views=[];
        $total_views += count($views);

        $watch = get_post_meta($post->ID,'_infinity_watch_time_'.$today,true);
        if(!is_array($watch)) $watch=[];
        foreach($watch as $ip => $seconds){
            if($seconds > 3600) $seconds = 0; // cap max watch time
            $total_watch += $seconds;
        }

        // Latest 5 viewed posts (use last view timestamp)
        foreach($views as $v){
            $latest_views[] = ['post'=>$post,'time'=>strtotime($v['time'])];
        }

        // Top 20 posts
        if(count($views) > 0){
            $avg_watch = count($views) ? round(array_sum($watch)/count($views),1) : 0;
            $top_posts_today[] = ['post'=>$post,'views'=>count($views),'avg_watch'=>$avg_watch];
        }

        // Yesterday's data
        $y_views = get_post_meta($post->ID,'_infinity_unique_views_'.$yesterday,true);
        if(!is_array($y_views)) $y_views=[];
        $y_total_views += count($y_views);

        $y_watch = get_post_meta($post->ID,'_infinity_watch_time_'.$yesterday,true);
        if(!is_array($y_watch)) $y_watch=[];
        $y_total_watch += array_sum($y_watch);
    }

    // Average watch time today
    $avg_watch = $total_views ? $total_watch / $total_views : 0;

    // Today vs Yesterday %
    $percent_change = $y_total_views ? round((($total_views - $y_total_views)/$y_total_views)*100,1) : 100;
    $trend_icon = $percent_change >=0 ? '▲' : '▼';
    $trend_color = $percent_change >=0 ? '#27ae60' : '#e74c3c';

    // ----------------- 4 Cards -----------------
    echo '<div style="display:flex;flex-wrap:wrap;gap:15px;margin:20px 0;">';

    // Card 1: Today Total Views
    echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;">';
    echo '<h3 style="margin:0 0 10px;font-size:16px;">📅 Today Total Views</h3>';
    echo '<p style="font-size:22px;font-weight:bold;color:#0073aa;">'.esc_html($total_views).'</p></div>';

    // Card 2: Average Watch Time
    echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;">';
    echo '<h3 style="margin:0 0 10px;font-size:16px;">⏱ Average Watch Time</h3>';
    echo '<p style="font-size:22px;font-weight:bold;color:#16a085;">'.format_watch_time($avg_watch).'</p></div>';

    // Card 3: Yesterday Views
    echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;">';
    echo '<h3 style="margin:0 0 10px;font-size:16px;">📆 Yesterday Views</h3>';
    echo '<p style="font-size:22px;font-weight:bold;color:#f39c12;">'.esc_html($y_total_views).'</p></div>';

    // Card 4: Today vs Yesterday %
    echo '<div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;">';
    echo '<h3 style="margin:0 0 10px;font-size:16px;">📊 Today vs Yesterday</h3>';
    echo '<p style="font-size:22px;font-weight:bold;color:#333;"><span style="color:'.$trend_color.';">'.$trend_icon.' '.abs($percent_change).'%</span></p></div>';

    echo '</div>';

    // ----------------- Latest 5 Viewed Posts -----------------
    echo '<h2>🕒 Latest 5 Viewed Posts Today</h2>';
    usort($latest_views,function($a,$b){ return $b['time'] - $a['time']; });
    $latest_views = array_slice($latest_views,0,5);
    echo '<div style="display:flex;flex-wrap:wrap;gap:15px;">';
    foreach($latest_views as $item){
        $thumb = get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150';
        echo '<div style="width:220px;background:#fff;padding:12px;border-radius:10px;text-align:center;">';
        echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;'>";
        echo '<a href="'.get_permalink($item['post']->ID).'" target="_blank" style="display:block;margin:5px 0;font-weight:bold;">'.get_the_title($item['post']).'</a>';
        echo '<p>Last Viewed: '.date('H:i:s, d M',$item['time']).'</p>';
        echo '</div>';
    }
    echo '</div>';

    // ----------------- Top 20 Posts Today -----------------
    echo '<h2>🔥 Top 20 Posts Today</h2>';
    usort($top_posts_today,function($a,$b){ return $b['views'] - $a['views']; });
    $top_posts_today = array_slice($top_posts_today,0,20);
    echo '<div style="display:flex;flex-wrap:wrap;gap:15px;">';
    foreach($top_posts_today as $item){
        $thumb = get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150';
        echo '<div style="width:220px;background:#fff;padding:12px;border-radius:10px;text-align:center;">';
        echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;'>";
        echo '<a href="'.get_permalink($item['post']->ID).'" target="_blank" style="display:block;margin:5px 0;font-weight:bold;">'.get_the_title($item['post']).'</a>';
        echo '<p>Views: '.$item['views'].'</p>';
        echo '<p>Avg Watch: '.format_watch_time($item['avg_watch']).'</p>';
        echo '</div>';
    }
    echo '</div>';
}


        // -------------------- 7 & 30 DAYS TAB --------------------
        if($active_tab=='7days' || $active_tab=='30days'){
            $days = $active_tab=='7days'?7:30;
            echo "<h2>📅 Last $days Days Top Posts</h2>";
            echo '<div style="display:flex;flex-wrap:wrap;gap:15px;">';
            $top_posts=[];
            for($d=0;$d<$days;$d++){
                $date = date('Y-m-d',strtotime($today." -$d days",$bd_timestamp));
                foreach($all_posts as $post){
                    $views = get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views=[];
                    $watch = get_post_meta($post->ID,'_infinity_watch_time_'.$date,true);
                    if(!is_array($watch)) $watch=[];
                    $avg_watch = count($views)?round(array_sum($watch)/count($views),1):0;
                    if(count($views)>0) $top_posts[]= ['post'=>$post,'views'=>count($views),'avg_watch'=>$avg_watch];
                }
            }
            usort($top_posts,function($a,$b){return $b['views']-$a['views'];});
            $top_posts=array_slice($top_posts,0,20);
            foreach($top_posts as $item){
                $thumb = get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150';
                echo '<div style="width:220px;background:#fff;padding:12px;border-radius:10px;text-align:center;">';
                echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;'>";
                echo '<a href="'.get_permalink($item['post']->ID).'" target="_blank" style="display:block;margin:5px 0;font-weight:bold;">'.get_the_title($item['post']).'</a>';
                echo '<p>Views: '.$item['views'].'</p>';
                echo '<p>Avg Watch: '.format_watch_time($item['avg_watch']).'</p>';
                echo '</div>';
            }
            echo '</div>';
        }

        // -------------------- REPORTS TAB --------------------
        if($active_tab=='reports'){
            echo '<h2>📊 Day-Wise Reports (Last 30 Days)</h2>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Date</th><th>Total Views</th><th>Unique Views</th><th>Avg Watch</th><th>Top Post</th></tr></thead><tbody>';
            for($d=0;$d<30;$d++){
                $date = date('Y-m-d',strtotime($today." -$d days",$bd_timestamp));
                $total=0; $unique=0; $total_watch=0; $top_post_title='-'; $top_post_link='#'; $top_post_count=0;
                $day_posts=[];
                foreach($all_posts as $post){
                    $views = get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views=[];
                    $watch = get_post_meta($post->ID,'_infinity_watch_time_'.$date,true);
                    if(!is_array($watch)) $watch=[];
                    $total += count($views);
                    $unique += count(array_unique(array_column($views,'ip')));
                    $total_watch += array_sum($watch);
                    if(count($views)>0) $day_posts[]= ['post'=>$post,'views'=>count($views)];
                }
                if(!empty($day_posts)){
                    usort($day_posts,function($a,$b){return $b['views']-$a['views'];});
                    $top_post_title=get_the_title($day_posts[0]['post']);
                    $top_post_link=get_permalink($day_posts[0]['post']->ID);
                    $top_post_count=$day_posts[0]['views'];
                }
                $avg_watch = $unique ? $total_watch/$unique : 0;
                echo "<tr><td>$date</td><td>$total</td><td>$unique</td><td>".format_watch_time($avg_watch)."</td><td><a href='$top_post_link' target='_blank'>$top_post_title ($top_post_count)</a></td></tr>";
            }
            echo '</tbody></table>';
        }
        ?>
        </div>
    </div>
<?php
}


?>

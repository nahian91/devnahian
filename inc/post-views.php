<?php
/**
 * ==========================================================
 * 🚀 Infinity Unique Daily Views Tracker + Realistic Watch Time + 7-Day/30-Day Analytics
 * ==========================================================
 * Author: Abdullah Nahian
 * Website: https://devnahian.com
 * ==========================================================
 */

// ============================
// Format Watch Time
// ============================
function format_watch_time($seconds) {
    $seconds = round($seconds);
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return $h > 0 ? sprintf("%02d:%02d:%02d", $h, $m, $s) : sprintf("%02d:%02d", $m, $s);
}

// ============================
// Track Unique Daily Views
// ============================
function infinity_track_unique_post_views() {
    if (!is_single()) return;
    global $post;
    if (empty($post->ID)) return;

    $post_id = $post->ID;
    $today = date('Y-m-d', current_time('timestamp'));
    $meta_key = '_infinity_unique_views_' . $today;
    $user_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $cookie_name = 'infinity_post_' . $post_id;

    $viewers = get_post_meta($post_id, $meta_key, true);
    if (!is_array($viewers)) $viewers = [];

    if (!in_array($user_ip, array_column($viewers,'ip')) && !isset($_COOKIE[$cookie_name])) {
        $viewers[] = ['ip'=>$user_ip,'time'=>date('Y-m-d H:i:s', current_time('timestamp'))];
        update_post_meta($post_id, $meta_key, $viewers);

        $end_of_day = strtotime('tomorrow') - 1;
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
// Enqueue JS for Watch Time
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
// AJAX Save Watch Time (Capped)
// ============================
function infinity_save_watchtime_ajax() {
    $post_id = intval($_POST['post_id'] ?? 0);
    $watch_time = intval($_POST['watch_time'] ?? 0);
    if (!$post_id || $watch_time <= 0) wp_send_json_error();

    $max_daily_watch = 3600; // 1 hour per user per post
    $today = date('Y-m-d', current_time('timestamp'));
    $watch_key = '_infinity_watch_time_' . $today;

    $watchers = get_post_meta($post_id, $watch_key, true);
    if (!is_array($watchers)) $watchers = [];

    $user_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $current = $watchers[$user_ip] ?? 0;
    $watchers[$user_ip] = min($current + $watch_time, $max_daily_watch);

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
    $watchers = get_post_meta($post_id,'_infinity_watch_time_'.$date,true);
    if(!is_array($watchers) || empty($watchers)) return 0;
    $total = array_sum($watchers);
    $unique = count($watchers);
    return $unique ? round($total/$unique) : 0;
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
    if($column=='infinity_total_views') echo get_post_meta($post_id,'post_views_count',true) ?: 0;
    if($column=='infinity_today_unique'){
        $today = date('Y-m-d',current_time('timestamp'));
        $views = get_post_meta($post_id,'_infinity_unique_views_'.$today,true);
        echo is_array($views) ? count($views) : 0;
    }
    if($column=='infinity_avg_watch'){
        echo format_watch_time(get_post_avg_watch_time($post_id));
    }
}
add_action('manage_posts_custom_column','infinity_show_post_views_column',10,2);

// ============================
// Admin Menu & Report Page
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
// Reports Page (FULL)
// ============================
function infinity_today_views_report_page(){
    $today = date('Y-m-d', current_time('timestamp'));
    $all_posts = get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>-1]);
    $active_tab = $_GET['tab'] ?? 'today';
    ?>
    <div class="wrap">
        <h1>📈 Infinity Analytics</h1>
        <p><strong>Date:</strong> <?php echo esc_html($today); ?></p>
        <h2 class="nav-tab-wrapper">
            <a href="?page=today-views-report&tab=today" class="nav-tab <?php echo $active_tab=='today'?'nav-tab-active':''; ?>">Today</a>
            <a href="?page=today-views-report&tab=7days" class="nav-tab <?php echo $active_tab=='7days'?'nav-tab-active':''; ?>">Last 7 Days</a>
            <a href="?page=today-views-report&tab=30days" class="nav-tab <?php echo $active_tab=='30days'?'nav-tab-active':''; ?>">Last 30 Days</a>
            <a href="?page=today-views-report&tab=reports" class="nav-tab <?php echo $active_tab=='reports'?'nav-tab-active':''; ?>">Reports</a>
        </h2>
        <div class="tab-content" style="margin-top:20px;">
    <?php

    // ----------------- TODAY -----------------
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
            foreach($watch as $sec) $total_watch += min($sec,3600);

            foreach($views as $v) $latest_views_arr[] = ['post'=>$post,'time'=>strtotime($v['time'])];

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
            echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
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
            echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
            echo '<a href="'.get_permalink($pid).'" target="_blank" style="display:block;margin:5px 0;font-weight:bold;">'.get_the_title($pid).'</a>';
            echo '<p>Views: '.$item['views'].'</p><p>Avg Watch: '.format_watch_time($item['avg_watch']).'</p></div>';
        }
        echo '</div>';
    }

    // ================= 7 DAYS / 30 DAYS =================
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
                $date = date('Y-m-d', strtotime($today." -$d days",$today_ts));
                $v = get_post_meta($pid,'_infinity_unique_views_'.$date,true); $v = is_array($v)?count($v):0;
                $w = get_post_meta($pid,'_infinity_watch_time_'.$date,true); $w = is_array($w)?array_sum($w):0;

                $total_views += $v;
                $total_watch += $w;
            }

            foreach(range($days,2*$days-1) as $d){
                $date = date('Y-m-d', strtotime($today." -$d days",$today_ts));
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
            echo "<img src='$thumb' style='width:100%;height:120px;object-fit:cover;border-radius:8px;'>";
            echo '<a href="'.get_permalink($pid).'" target="_blank" style="display:block;margin:8px 0;font-weight:bold;font-size:15px;color:#333;">'.get_the_title($pid).'</a>';
            echo '<p>Views: <strong>'.$item['views'].'</strong> '.$item['trend'].'</p>';
            echo '<p>Avg Watch: <strong>'.format_watch_time($item['avg_watch']).'</strong></p>';
            echo '</div>';
        }

        echo '</div>';
    }

    // ================= REPORTS =================
    if($active_tab=='reports'){

        // Helper: get total views for a post from all _infinity_unique_views_* meta
        function get_total_views_by_meta($post_id){
            $keys = get_post_custom_keys($post_id);
            if(!$keys) return 0;

            $total = 0;
            foreach($keys as $key){
                if(strpos($key,'_infinity_unique_views_')===0){
                    $views = get_post_meta($post_id, $key, true);
                    if(is_array($views)) $total += count($views);
                }
            }
            return $total;
        }

        // Helper: get today's unique views
        function get_todays_views($post_id){
            $today = date('Y-m-d', current_time('timestamp'));
            $views = get_post_meta($post_id,'_infinity_unique_views_'.$today,true);
            return is_array($views) ? count($views) : 0;
        }

        // Helper: get average watch time for today
        function get_avg_watch_time($post_id){
            $today = date('Y-m-d', current_time('timestamp'));
            $watchers = get_post_meta($post_id,'_infinity_watch_time_'.$today,true);
            if(!is_array($watchers) || empty($watchers)) return 0;
            $total = array_sum($watchers);
            $unique = count($watchers);
            return $unique ? round($total/$unique) : 0;
        }

        // Fetch all posts
        $all_posts = get_posts([
            'post_type'   => 'post',
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        // Sort by total views descending
        usort($all_posts, function($a, $b){
            return get_total_views_by_meta($b->ID) - get_total_views_by_meta($a->ID);
        });

        // Display table
        echo '<h2>📊 All Posts by Views</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Post Title</th><th>Total Views</th><th>Today\'s Views</th><th>Avg Watch Time</th></tr></thead>';
        echo '<tbody>';
        foreach($all_posts as $post){
            $total_views = get_total_views_by_meta($post->ID);
            $today_views = get_todays_views($post->ID);
            $avg_watch = format_watch_time(get_avg_watch_time($post->ID));

            echo '<tr>';
            echo '<td><a href="'.get_permalink($post->ID).'" target="_blank">'.esc_html($post->post_title).'</a></td>';
            echo '<td>'.$total_views.'</td>';
            echo '<td>'.$today_views.'</td>';
            echo '<td>'.$avg_watch.'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    echo '</div></div>';
}
?>

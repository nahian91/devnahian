<?php
/**
 * ==========================================================
 * 🚀 Infinity Unique Daily Views Tracker + 7-Day/30-Day/Reports Analytics (Bangladesh Time)
 * ==========================================================
 * Tracks unique post views and shows:
 * - Today's Views Table
 * - Last 7/30 Days Analytics
 * - Latest 5 posts with last view time
 * - Reports Overview
 * 
 * Author: Abdullah Nahian
 * Website: https://devnahian.com
 * ==========================================================
 */

// ============================
// Track Unique Daily Views (Bangladesh Time)
// ============================
function infinity_track_unique_post_views() {
    if ( !is_single() ) return;

    global $post;
    if ( empty($post->ID) ) return;

    $post_id = $post->ID;

    // Bangladesh timezone timestamp
    $bd_timestamp = current_time('timestamp'); // WordPress timezone
    $today = date('Y-m-d', $bd_timestamp);
    $meta_key = '_infinity_unique_views_' . $today;

    $user_ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
    $cookie_name = 'infinity_post_' . $post_id;

    $viewers = get_post_meta($post_id, $meta_key, true);
    if ( !is_array($viewers) ) $viewers = [];

    if ( !in_array($user_ip, array_column($viewers,'ip')) && !isset($_COOKIE[$cookie_name]) ) {
        $viewers[] = ['ip'=>$user_ip, 'time'=>date('Y-m-d H:i:s', $bd_timestamp)];
        update_post_meta($post_id, $meta_key, $viewers);

        // Cookie expires at 11:59:59 PM BD time
        $end_of_day = strtotime('tomorrow', $bd_timestamp) - 1;
        setcookie($cookie_name, 'true', $end_of_day, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('init', 'infinity_track_unique_post_views');

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
        $count = $count ? $count+1 : 1;
        update_post_meta($post_id, $count_key, $count);

        setcookie($cookie_name, 'true', time()+3600, '/');
        $_COOKIE[$cookie_name] = 'true';
    }
}
add_action('init', 'track_post_views');

// ============================
// Helper to get total post views
// ============================
function get_post_views($post_id) {
    $count = get_post_meta($post_id, 'post_views_count', true);
    return $count ? $count : 0;
}

// ============================
// Admin Submenu
// ============================
function infinity_add_views_report_submenu() {
    add_submenu_page(
        'edit.php',
        'Analytics',
        'Analytics',
        'manage_options',
        'today-views-report',
        'infinity_today_views_report_page'
    );
}
add_action('admin_menu', 'infinity_add_views_report_submenu');

// ============================
// Infinity Analytics Admin Page
// ============================
function infinity_today_views_report_page() {
    $bd_timestamp = current_time('timestamp');
    $today = date('Y-m-d', $bd_timestamp); 
    $yesterday = date('Y-m-d', strtotime($today.' -1 day', $bd_timestamp));

    $all_posts = get_posts([
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    // ---------- Calculate Totals ----------
    $total_today_views  = 0;
    $total_today_unique = 0;
    $total_yesterday_views  = 0;
    $total_yesterday_unique = 0;

    foreach($all_posts as $post){
        // Today
        $today_views = get_post_meta($post->ID,'_infinity_unique_views_'.$today,true);
        if(!is_array($today_views)) $today_views = [];
        $total_today_views += count($today_views);
        $total_today_unique += count(array_unique(array_column($today_views,'ip')));

        // Yesterday
        $yesterday_views = get_post_meta($post->ID,'_infinity_unique_views_'.$yesterday,true);
        if(!is_array($yesterday_views)) $yesterday_views = [];
        $total_yesterday_views += count($yesterday_views);
        $total_yesterday_unique += count(array_unique(array_column($yesterday_views,'ip')));
    }

    // ---------- Comparison ----------
    $compare_percent = $total_yesterday_views>0 ? (($total_today_views-$total_yesterday_views)/$total_yesterday_views)*100 : 100;
    if($compare_percent>0) $compare_trend = '<span style="color:#2ecc71;">▲ '.round($compare_percent,1).'%</span>';
    elseif($compare_percent<0) $compare_trend = '<span style="color:#e74c3c;">▼ '.abs(round($compare_percent,1)).'%</span>';
    else $compare_trend = '<span style="color:#888;">▬ 0%</span>';

    $active_tab = isset($_GET['tab'])?$_GET['tab']:'today';
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">📈 Infinity Analytics</h1>
        <p><strong>Date:</strong> <?php echo esc_html($today); ?></p>

        <!-- ---------- Summary Cards ---------- -->
        <div style="display:flex;flex-wrap:wrap;gap:15px;margin:20px 0;">
        <?php
        $cards = [
            ['title'=>'📅 Today Total Views','value'=>$total_today_views,'color'=>'#0073aa','is_html'=>false],
            ['title'=>'👥 Unique Post Views','value'=>$total_today_unique,'color'=>'#16a085','is_html'=>false],
            ['title'=>'📆 Yesterday Views','value'=>$total_yesterday_views,'color'=>'#f39c12','is_html'=>false],
            ['title'=>'📊 Today vs Yesterday','value'=>$compare_trend,'color'=>'#333','is_html'=>true],
        ];
        foreach($cards as $card){
            ?>
            <div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;">
                <h3 style="margin:0 0 10px;font-size:16px;"><?php echo esc_html($card['title']); ?></h3>
                <p style="font-size:22px;font-weight:bold;color:<?php echo $card['color']; ?>;">
                    <?php echo $card['is_html'] ? $card['value'] : esc_html($card['value']); ?>
                </p>
            </div>
            <?php
        }
        ?>
        </div>

        <!-- ---------- Tabs ---------- -->
        <h2 class="nav-tab-wrapper">
            <a href="?page=today-views-report&tab=today" class="nav-tab <?php echo $active_tab=='today'?'nav-tab-active':''; ?>">Today</a>
            <a href="?page=today-views-report&tab=7days" class="nav-tab <?php echo $active_tab=='7days'?'nav-tab-active':''; ?>">Last 7 Days</a>
            <a href="?page=today-views-report&tab=30days" class="nav-tab <?php echo $active_tab=='30days'?'nav-tab-active':''; ?>">Last 30 Days</a>
            <a href="?page=today-views-report&tab=reports" class="nav-tab <?php echo $active_tab=='reports'?'nav-tab-active':''; ?>">Reports</a>
        </h2>

        <div class="tab-content" style="margin-top:20px;">
        <?php
        // =================== TODAY TAB ===================
if($active_tab=='today'){

    echo '<h2>🌞 Latest 5 Posts Viewed Today</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';

    $latest_views = [];
    foreach ($all_posts as $post) {
        $views = get_post_meta($post->ID, '_infinity_unique_views_' . $today, true);
        if (!is_array($views)) $views = [];
        foreach ($views as $view) {
            $latest_views[] = [
                'post' => $post,
                'time' => $view['time'] ?? '',
            ];
        }
    }

    usort($latest_views, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
    $latest_views = array_slice($latest_views, 0, 5);

    foreach ($latest_views as $item) {
        $thumb = get_the_post_thumbnail_url($item['post']->ID, 'medium') ?: 'https://via.placeholder.com/150?text=No+Image';
        $time_only = $item['time'] ? date('h:i:s A', strtotime($item['time'])) : '-';
        ?>
        <div style="width:220px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.2s;">
            <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:120px;object-fit:cover;">
            <div style="padding:12px;text-align:center;">
                <a href="<?php echo esc_url(get_permalink($item['post']->ID)); ?>" target="_blank" style="font-weight:bold;font-size:14px;display:block;margin-bottom:4px;"><?php echo esc_html(get_the_title($item['post'])); ?></a>
                <p style="margin:0;color:#888;font-size:12px;">Viewed at: <?php echo esc_html($time_only); ?></p>
            </div>
        </div>
        <?php
    }
    echo '</div>';


    // =================== TOP 20 MOST VIEWED TODAY ===================
    echo '<h2 style="margin-top:40px;">🔥 Top 20 Most Viewed Posts Today</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';

    $today_ranking = [];

    foreach ($all_posts as $post) {
        $views = get_post_meta($post->ID, '_infinity_unique_views_' . $today, true);
        if (!is_array($views)) $views = [];

        $today_ranking[] = [
            'post' => $post,
            'count' => count($views)
        ];
    }

    usort($today_ranking, fn($a, $b) => $b['count'] - $a['count']);
    $today_ranking = array_slice($today_ranking, 0, 20);

    foreach ($today_ranking as $item) {
        $thumb = get_the_post_thumbnail_url($item['post']->ID, 'medium') ?: 'https://via.placeholder.com/150?text=No+Image';
        ?>
        <div style="width:220px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.2s;">
            <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:120px;object-fit:cover;">
            <div style="padding:12px;text-align:center;">
                <a href="<?php echo esc_url(get_permalink($item['post']->ID)); ?>" target="_blank" style="font-weight:bold;font-size:14px;display:block;margin-bottom:4px;"><?php echo esc_html(get_the_title($item['post'])); ?></a>
                <p style="margin:0;color:#888;font-size:12px;">Views Today: <?php echo esc_html($item['count']); ?></p>
            </div>
        </div>
        <?php
    }

    echo '</div>';
}


        // =================== 7 DAYS TAB ===================
        if($active_tab=='7days'){
            echo '<h2>🏆 Top Posts Last 7 Days</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';
            $top_7_posts = [];
            for($d=0; $d<7; $d++){
                $date_range[] = date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
            }
            foreach($all_posts as $post){
                $current=0;$previous=0;
                for($d=0;$d<7;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = [];
                    $current += count($views);
                }
                for($d=7;$d<14;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = [];
                    $previous += count($views);
                }
                if($current>0){
                    $change=$previous>0?(($current-$previous)/$previous)*100:100;
                    $top_7_posts[]=['post'=>$post,'current'=>$current,'percentage'=>round($change,1)];
                }
            }
            usort($top_7_posts,fn($a,$b)=>$b['current']-$a['current']);
            $top_7_posts=array_slice($top_7_posts,0,20);
            foreach($top_7_posts as $item){
                $thumb=get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150?text=No+Image';
                ?>
                <div style="width:220px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.2s;">
                    <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:120px;object-fit:cover;">
                    <div style="padding:12px;text-align:center;">
                        <a href="<?php echo esc_url(get_permalink($item['post']->ID)); ?>" target="_blank" style="font-weight:bold;font-size:14px;display:block;margin-bottom:4px;"><?php echo esc_html(get_the_title($item['post'])); ?></a>
                        <p style="margin:0;color:#555;font-size:13px;">Views: <strong><?php echo esc_html($item['current']); ?></strong></p>
                        <p style="margin:0;color:<?php echo $item['percentage']>=0?'#2ecc71':'#e74c3c'; ?>;font-size:13px;">Change: <?php echo esc_html($item['percentage']); ?>%</p>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        }

        // =================== 30 DAYS TAB ===================
        if($active_tab=='30days'){
            echo '<h2>🗓️ Top Posts Last 30 Days</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';
            $top_30_posts=[];
            foreach($all_posts as $post){
                $current=0;$previous=0;
                for($d=0;$d<30;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = [];
                    $current += count($views);
                }
                for($d=30;$d<60;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = [];
                    $previous += count($views);
                }
                if($current>0){
                    $change=$previous>0?(($current-$previous)/$previous)*100:100;
                    $top_30_posts[]=['post'=>$post,'current'=>$current,'percentage'=>round($change,1)];
                }
            }
            usort($top_30_posts,fn($a,$b)=>$b['current']-$a['current']);
            $top_30_posts=array_slice($top_30_posts,0,20);
            foreach($top_30_posts as $item){
                $thumb=get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150?text=No+Image';
                ?>
                <div style="width:220px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.2s;">
                    <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:120px;object-fit:cover;">
                    <div style="padding:12px;text-align:center;">
                        <a href="<?php echo esc_url(get_permalink($item['post']->ID)); ?>" target="_blank" style="font-weight:bold;font-size:14px;display:block;margin-bottom:4px;"><?php echo esc_html(get_the_title($item['post'])); ?></a>
                        <p style="margin:0;color:#555;font-size:13px;">Views: <strong><?php echo esc_html($item['current']); ?></strong></p>
                        <p style="margin:0;color:<?php echo $item['percentage']>=0?'#2ecc71':'#e74c3c'; ?>;font-size:13px;">Change: <?php echo esc_html($item['percentage']); ?>%</p>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        }

        // =================== REPORTS TAB ===================
        if($active_tab=='reports'){
            echo '<h2>📑 All Time Reports (Ordered by Today\'s Views)</h2><table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Post</th><th>Total Views</th><th>Unique Views Today</th><th>Unique Views Last 7 Days</th><th>Unique Views Last 30 Days</th></tr></thead><tbody>';

            $report_posts = [];
            foreach($all_posts as $post){
                $unique_today = count(get_post_meta($post->ID,'_infinity_unique_views_'.$today,true) ?: []);
                $unique_7 = 0; $unique_30=0;

                for($d=0;$d<7;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $unique_7 += count(get_post_meta($post->ID,'_infinity_unique_views_'.$date,true) ?: []);
                }
                for($d=0;$d<30;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days", $bd_timestamp));
                    $unique_30 += count(get_post_meta($post->ID,'_infinity_unique_views_'.$date,true) ?: []);
                }

                $report_posts[] = [
                    'post' => $post,
                    'total_views' => get_post_views($post->ID),
                    'unique_today' => $unique_today,
                    'unique_7' => $unique_7,
                    'unique_30' => $unique_30
                ];
            }

            usort($report_posts, fn($a,$b)=>$b['unique_today'] - $a['unique_today']);

            foreach($report_posts as $item){
                echo '<tr>';
                echo '<td><a href="'.get_permalink($item['post']->ID).'" target="_blank">'.get_the_title($item['post']->ID).'</a></td>';
                echo '<td>'.$item['total_views'].'</td>';
                echo '<td>'.$item['unique_today'].'</td>';
                echo '<td>'.$item['unique_7'].'</td>';
                echo '<td>'.$item['unique_30'].'</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        }

        ?>
        </div>
    </div>
    <?php
}
?>

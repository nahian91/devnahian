<?php
/**
 * ==========================================================
 * 🚀 Infinity Unique Daily Views Tracker + 7-Day Trend Cards
 * ==========================================================
 * Tracks unique post views and shows:
 * - Today's Views Table
 * - Last 7/30 Days Analytics
 * - Latest 5 posts with last view time
 * 
 * Author: Abdullah Nahian
 * Website: https://devnahian.com
 * ==========================================================
 */

// ==========================================================
// 🧠 Track Unique Daily Post Views
// ==========================================================
function infinity_track_unique_post_views() {
    if ( is_single() ) {
        global $post;
        if ( empty( $post->ID ) ) return;

        $post_id   = $post->ID;
        $today     = gmdate( 'Y-m-d' );
        $meta_key  = '_infinity_unique_views_' . $today;
        $user_ip   = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );

        $viewers = get_post_meta( $post_id, $meta_key, true );
        if ( ! is_array( $viewers ) ) $viewers = [];

        if ( ! in_array( $user_ip, array_column($viewers,'ip'), true ) ) {
            $viewers[] = ['ip'=>$user_ip, 'time'=>current_time('mysql')];
            update_post_meta( $post_id, $meta_key, $viewers );
        }
    }
}
add_action( 'wp_head', 'infinity_track_unique_post_views' );

// ==========================================================
// ⚙️ Add Admin Submenu under "Posts"
// ==========================================================
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
add_action( 'admin_menu', 'infinity_add_views_report_submenu' );

// ==========================================================
// 📊 Infinity Analytics Admin Page
// ==========================================================
function infinity_today_views_report_page() {
    $today     = current_time('Y-m-d'); 
    $yesterday = date('Y-m-d', strtotime($today.' -1 day'));

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
        if(!is_array($today_views)) $today_views = maybe_unserialize($today_views);
        if(!is_array($today_views)) $today_views = [];
        $total_today_views += count($today_views);
        $total_today_unique += count(array_unique(array_column($today_views,'ip')));

        // Yesterday
        $yesterday_views = get_post_meta($post->ID,'_infinity_unique_views_'.$yesterday,true);
        if(!is_array($yesterday_views)) $yesterday_views = maybe_unserialize($yesterday_views);
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
            // ---------- Latest 5 Posts Viewed Today ----------
echo '<h2>🌞 Latest 5 Posts Viewed Today</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';

$latest_views = [];

foreach ($all_posts as $post) {
    $views = get_post_meta($post->ID, '_infinity_unique_views_' . $today, true);
    if (!is_array($views)) $views = maybe_unserialize($views);
    if (!is_array($views)) $views = [];

    foreach ($views as $view) {
        $latest_views[] = [
            'post' => $post,
            'time' => $view['time'] ?? '',
        ];
    }
}

// Sort by latest time descending
usort($latest_views, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));

// Take the latest 5 views overall
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


            // ---------- Top Posts Today ----------
            echo '<h2>🌞 Top Posts Today</h2><div style="display:flex;flex-wrap:wrap;gap:15px;">';
            $top_today = [];
            foreach($all_posts as $post){
                $views=get_post_meta($post->ID,'_infinity_unique_views_'.$today,true);
                if(!is_array($views)) $views = maybe_unserialize($views);
                if(!is_array($views)) $views = [];
                if(!empty($views)){
                    $top_today[] = ['post'=>$post,'count'=>count($views)];
                }
            }
            usort($top_today,fn($a,$b)=>$b['count']-$a['count']);
            $top_today=array_slice($top_today,0,20);
            foreach($top_today as $item){
                $thumb = get_the_post_thumbnail_url($item['post']->ID,'medium') ?: 'https://via.placeholder.com/150?text=No+Image';
                ?>
                <div style="width:220px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.2s;">
                    <img src="<?php echo esc_url($thumb); ?>" style="width:100%;height:120px;object-fit:cover;">
                    <div style="padding:12px;text-align:center;">
                        <a href="<?php echo esc_url(get_permalink($item['post']->ID)); ?>" target="_blank" style="font-weight:bold;font-size:14px;display:block;margin-bottom:6px;"><?php echo esc_html(get_the_title($item['post'])); ?></a>
                        <p style="margin:0;color:#555;font-size:13px;">Views: <strong><?php echo esc_html($item['count']); ?></strong></p>
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
            for($i=0;$i<count($all_posts);$i++){
                $post = $all_posts[$i];
                $current=0;$previous=0;
                for($d=0;$d<7;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days"));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = maybe_unserialize($views);
                    if(!is_array($views)) $views = [];
                    $current += count($views);
                }
                for($d=7;$d<14;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days"));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = maybe_unserialize($views);
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
                    $date=date('Y-m-d', strtotime($today."-$d days"));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = maybe_unserialize($views);
                    if(!is_array($views)) $views = [];
                    $current += count($views);
                }
                for($d=30;$d<60;$d++){
                    $date=date('Y-m-d', strtotime($today."-$d days"));
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = maybe_unserialize($views);
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
            echo '<h2>📊 Last 30 Days Reports</h2>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Date</th><th>Total Views</th><th>Best Post</th><th>Views</th></tr></thead><tbody>';
            for($i=0;$i<30;$i++){
                $date=date('Y-m-d', strtotime($today."-$i days"));
                $total_views=0;$best_post_title='-';$best_post_views=0;
                foreach($all_posts as $post){
                    $views=get_post_meta($post->ID,'_infinity_unique_views_'.$date,true);
                    if(!is_array($views)) $views = maybe_unserialize($views);
                    if(!is_array($views)) $views = [];
                    $count = count($views);
                    $total_views+=$count;
                    if($count>$best_post_views){ $best_post_views=$count; $best_post_title=get_the_title($post);}
                }
                echo '<tr>';
                echo '<td>'.esc_html($date).'</td>';
                echo '<td>'.esc_html($total_views).'</td>';
                echo '<td>'.esc_html($best_post_title).'</td>';
                echo '<td>'.esc_html($best_post_views).'</td>';
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

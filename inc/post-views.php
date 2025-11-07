<?php
/**
 * ==========================================================
 * 🚀 Infinity Unique Daily Views Tracker + 7-Day Trend Cards
 * ==========================================================
 * Tracks unique post views and shows:
 * - Today's Views Table
 * - Top 5 Posts (Last 7 Days) with Comparison to Previous Week
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

		if ( ! in_array( $user_ip, $viewers, true ) ) {
			$viewers[] = $user_ip;
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
// 📊 Display Analytics Page (Today + Yesterday + 7 Days + 30 Days)
// ==========================================================
function infinity_today_views_report_page() {

	// ---------- 🕒 Bangladesh time ----------
	$today = current_time('Y-m-d'); // site time in WP
	$yesterday = date('Y-m-d', strtotime($today . ' -1 day'));

	$all_posts = get_posts([
		'post_type'   => 'post',
		'post_status' => 'publish',
		'numberposts' => -1,
	]);

	// ---------- 📦 Calculate Totals ----------
	$total_today_views = 0;
	$total_today_unique = 0;
	$total_yesterday_views = 0;
	$total_yesterday_unique = 0;

	foreach ($all_posts as $post) {
		// --- Today views ---
		$today_key = '_infinity_unique_views_' . $today;
		$today_views = get_post_meta($post->ID, $today_key, true);

		if (!empty($today_views)) {
			if (!is_array($today_views)) $today_views = maybe_unserialize($today_views);
			if (is_array($today_views)) {
				$total_today_views += count($today_views);
				$total_today_unique += count(array_unique($today_views));
			}
		}

		// --- Yesterday views ---
		$yesterday_key = '_infinity_unique_views_' . $yesterday;
		$yesterday_views = get_post_meta($post->ID, $yesterday_key, true);

		if (!empty($yesterday_views)) {
			if (!is_array($yesterday_views)) $yesterday_views = maybe_unserialize($yesterday_views);
			if (is_array($yesterday_views)) {
				$total_yesterday_views += count($yesterday_views);
				$total_yesterday_unique += count(array_unique($yesterday_views));
			}
		}
	}

	// ---------- 📈 Compare Today vs Yesterday ----------
	$compare_percent = $total_yesterday_views > 0
		? (($total_today_views - $total_yesterday_views) / $total_yesterday_views) * 100
		: 100;

	$compare_trend = '';
	if ($compare_percent > 0) {
		$compare_trend = '<span style="color:#2ecc71;">▲ ' . round($compare_percent, 1) . '%</span>';
	} elseif ($compare_percent < 0) {
		$compare_trend = '<span style="color:#e74c3c;">▼ ' . abs(round($compare_percent, 1)) . '%</span>';
	} else {
		$compare_trend = '<span style="color:#888;">▬ 0%</span>';
	}

	// ---------- 🏆 Top 5 Posts (Last 7 Days) ----------
	$top_7_posts = [];
	foreach ($all_posts as $post) {
		$current_7 = 0;
		$previous_7 = 0;

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', strtotime($today . "-$i days"));
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta($post->ID, $key, true);
			if (!empty($views)) {
				if (!is_array($views)) $views = maybe_unserialize($views);
				if (is_array($views)) $current_7 += count($views);
			}
		}

		for ($i = 7; $i < 14; $i++) {
			$date = date('Y-m-d', strtotime($today . "-$i days"));
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta($post->ID, $key, true);
			if (!empty($views)) {
				if (!is_array($views)) $views = maybe_unserialize($views);
				if (is_array($views)) $previous_7 += count($views);
			}
		}

		if ($current_7 > 0) {
			$change = $previous_7 > 0 ? (($current_7 - $previous_7) / $previous_7) * 100 : 100;
			$top_7_posts[] = [
				'post'        => $post,
				'current'     => $current_7,
				'previous'    => $previous_7,
				'percentage'  => round($change, 1),
			];
		}
	}
	usort($top_7_posts, fn($a, $b) => $b['current'] - $a['current']);
	$top_7_posts = array_slice($top_7_posts, 0, 20);

	// ---------- 🗓️ Top 5 Posts (Last 30 Days) ----------
	$top_30_posts = [];
	foreach ($all_posts as $post) {
		$current_30 = 0;
		$previous_30 = 0;

		for ($i = 0; $i < 30; $i++) {
			$date = date('Y-m-d', strtotime($today . "-$i days"));
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta($post->ID, $key, true);
			if (!empty($views)) {
				if (!is_array($views)) $views = maybe_unserialize($views);
				if (is_array($views)) $current_30 += count($views);
			}
		}

		for ($i = 30; $i < 60; $i++) {
			$date = date('Y-m-d', strtotime($today . "-$i days"));
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta($post->ID, $key, true);
			if (!empty($views)) {
				if (!is_array($views)) $views = maybe_unserialize($views);
				if (is_array($views)) $previous_30 += count($views);
			}
		}

		if ($current_30 > 0) {
			$change = $previous_30 > 0 ? (($current_30 - $previous_30) / $previous_30) * 100 : 100;
			$top_30_posts[] = [
				'post'       => $post,
				'current'    => $current_30,
				'previous'   => $previous_30,
				'percentage' => round($change, 1),
			];
		}
	}
	usort($top_30_posts, fn($a, $b) => $b['current'] - $a['current']);
	$top_30_posts = array_slice($top_30_posts, 0, 20);

	// ---------- 🌞 Top 5 Posts Today ----------
	$top_today = [];
	foreach ($all_posts as $post) {
		$views = get_post_meta($post->ID, '_infinity_unique_views_' . $today, true);
		if (!empty($views)) {
			if (!is_array($views)) $views = maybe_unserialize($views);
			if (is_array($views) && count($views) > 0) {
				$top_today[] = [
					'post'  => $post,
					'today' => count($views),
				];
			}
		}
	}
	usort($top_today, fn($a, $b) => $b['today'] - $a['today']);
	$top_today = array_slice($top_today, 0, 20);

	// ---------- PAGE OUTPUT ----------
	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">📈 Infinity Analytics</h1>';
	echo '<p><strong>Date:</strong> ' . esc_html($today) . '</p>';
	echo '<hr class="wp-header-end" style="margin-bottom:20px;">';

	// ---------- Summary Cards ----------
	echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:30px;">';

	// Today Total Views
	echo '<div style="flex:1;min-width:200px;background:#fff;padding:20px;border:1px solid #ddd;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
	echo '<h3 style="margin:0;font-size:16px;">📅 Today Total Views</h3>';
	echo '<p style="font-size:22px;margin:8px 0 0;font-weight:bold;color:#0073aa;">' . esc_html($total_today_views) . '</p>';
	echo '</div>';

	// Today Unique Views
	echo '<div style="flex:1;min-width:200px;background:#fff;padding:20px;border:1px solid #ddd;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
	echo '<h3 style="margin:0;font-size:16px;">👥 Today Unique Views</h3>';
	echo '<p style="font-size:22px;margin:8px 0 0;font-weight:bold;color:#16a085;">' . esc_html($total_today_unique) . '</p>';
	echo '</div>';

	// Yesterday Views
	echo '<div style="flex:1;min-width:200px;background:#fff;padding:20px;border:1px solid #ddd;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
	echo '<h3 style="margin:0;font-size:16px;">📆 Yesterday Views</h3>';
	echo '<p style="font-size:22px;margin:8px 0 0;font-weight:bold;color:#f39c12;">' . esc_html($total_yesterday_views) . '</p>';
	echo '</div>';

	// Comparison Card
	echo '<div style="flex:1;min-width:200px;background:#fff;padding:20px;border:1px solid #ddd;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">';
	echo '<h3 style="margin:0;font-size:16px;">📊 Today vs Yesterday</h3>';
	echo '<p style="font-size:22px;margin:8px 0 0;font-weight:bold;">' . $compare_trend . '</p>';
	echo '</div>';
	echo '</div>';

	// ---------- 🌞 Top 5 Today ----------
	if (!empty($top_today)) {
		echo '<h2>🌞 Top 20 Posts Today</h2>';
		echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:15px;margin-bottom:30px;">';
		foreach ($top_today as $item) {
			$post = $item['post'];
			$today_views = $item['today'];
			$thumb = get_the_post_thumbnail_url($post->ID, 'medium') ?: 'https://via.placeholder.com/300x180?text=No+Image';
			echo '<div style="background:#fff;border:1px solid #ddd;border-radius:10px;width:250px;box-shadow:0 2px 6px rgba(0,0,0,0.05);overflow:hidden;">';
			echo '<img src="' . esc_url($thumb) . '" style="width:100%;height:140px;object-fit:cover;">';
			echo '<div style="padding:12px;">';
			echo '<h3 style="margin:0 0 8px;font-size:14px;line-height:1.4;"><a href="' . esc_url(get_permalink($post->ID)) . '" target="_blank">' . esc_html(get_the_title($post)) . '</a></h3>';
			echo '<p style="margin:0;font-size:13px;color:#333;">Today\'s Views: <strong>' . esc_html($today_views) . '</strong></p>';
			echo '</div></div>';
		}
		echo '</div>';
	}

	// ---------- 🏆 Top 5 Posts Last 7 Days ----------
	if (!empty($top_7_posts)) {
		echo '<h2>🏆 Top 20 Posts (Last 7 Days)</h2>';
		echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:15px;">';
		foreach ($top_7_posts as $item) {
			$post = $item['post'];
			$current = $item['current'];
			$percentage = $item['percentage'];
			$thumb = get_the_post_thumbnail_url($post->ID, 'medium') ?: 'https://via.placeholder.com/300x180?text=No+Image';
			$trend = $percentage > 0 ? '<span style="color:#2ecc71;">▲ ' . esc_html($percentage) . '%</span>' : ($percentage < 0 ? '<span style="color:#e74c3c;">▼ ' . esc_html(abs($percentage)) . '%</span>' : '<span style="color:#888;">▬ 0%</span>');
			echo '<div style="background:#fff;border:1px solid #ddd;border-radius:10px;width:250px;box-shadow:0 2px 6px rgba(0,0,0,0.05);overflow:hidden;">';
			echo '<img src="' . esc_url($thumb) . '" style="width:100%;height:140px;object-fit:cover;">';
			echo '<div style="padding:12px;">';
			echo '<h3 style="margin:0 0 8px;font-size:14px;line-height:1.4;"><a href="' . esc_url(get_permalink($post->ID)) . '" target="_blank">' . esc_html(get_the_title($post)) . '</a></h3>';
			echo '<p style="margin:4px 0;font-size:13px;color:#333;">7-Day Views: <strong>' . esc_html($current) . '</strong></p>';
			echo '<p style="margin:0;font-size:13px;">Change vs Prev Week: ' . $trend . '</p>';
			echo '</div></div>';
		}
		echo '</div>';
	}

	// ---------- 🗓️ Top 5 Posts Last 30 Days ----------
	if (!empty($top_30_posts)) {
		echo '<h2>🗓️ Top 20 Posts (Last 30 Days)</h2>';
		echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:15px;">';
		foreach ($top_30_posts as $item) {
			$post = $item['post'];
			$current = $item['current'];
			$percentage = $item['percentage'];
			$thumb = get_the_post_thumbnail_url($post->ID, 'medium') ?: 'https://via.placeholder.com/300x180?text=No+Image';
			$trend = $percentage > 0 ? '<span style="color:#2ecc71;">▲ ' . esc_html($percentage) . '%</span>' : ($percentage < 0 ? '<span style="color:#e74c3c;">▼ ' . esc_html(abs($percentage)) . '%</span>' : '<span style="color:#888;">▬ 0%</span>');
			echo '<div style="background:#fff;border:1px solid #ddd;border-radius:10px;width:250px;box-shadow:0 2px 6px rgba(0,0,0,0.05);overflow:hidden;">';
			echo '<img src="' . esc_url($thumb) . '" style="width:100%;height:140px;object-fit:cover;">';
			echo '<div style="padding:12px;">';
			echo '<h3 style="margin:0 0 8px;font-size:14px;line-height:1.4;"><a href="' . esc_url(get_permalink($post->ID)) . '" target="_blank">' . esc_html(get_the_title($post)) . '</a></h3>';
			echo '<p style="margin:4px 0;font-size:13px;color:#333;">30-Day Views: <strong>' . esc_html($current) . '</strong></p>';
			echo '<p style="margin:0;font-size:13px;">Change vs Prev 30 Days: ' . $trend . '</p>';
			echo '</div></div>';
		}
		echo '</div>';
	}

	echo '</div>'; // wrap end
}

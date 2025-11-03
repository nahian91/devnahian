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
		'Today\'s Unique Views',
		'Today\'s Views',
		'manage_options',
		'today-views-report',
		'infinity_today_views_report_page'
	);
}
add_action( 'admin_menu', 'infinity_add_views_report_submenu' );


// ==========================================================
// 📊 Display Admin Report Page with Comparison Cards
// ==========================================================
function infinity_today_views_report_page() {
	$today = gmdate( 'Y-m-d' );
	$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
	$per_page = 20;

	$all_posts = get_posts([
		'post_type'   => 'post',
		'post_status' => 'publish',
		'numberposts' => -1,
	]);

	// ---------- 🧩 Today's Views ----------
	$results = [];
	foreach ( $all_posts as $post ) {
		$today_key = '_infinity_unique_views_' . $today;
		$viewers = get_post_meta( $post->ID, $today_key, true );
		$today_views = is_array( $viewers ) ? count( $viewers ) : 0;

		if ( $today_views > 0 ) {
			$meta = get_post_meta( $post->ID );
			$total = 0;
			foreach ( $meta as $key => $val ) {
				if ( strpos( $key, '_infinity_unique_views_' ) === 0 && is_array( maybe_unserialize( $val[0] ) ) ) {
					$total += count( maybe_unserialize( $val[0] ) );
				}
			}
			$results[] = [
				'post'  => $post,
				'today' => $today_views,
				'total' => $total,
			];
		}
	}

	usort( $results, fn( $a, $b ) => $b['today'] - $a['today'] );


	// ---------- 🏆 7-Day Comparison Cards ----------
	$top_posts = [];

	foreach ( $all_posts as $post ) {
		$current_7 = 0;
		$previous_7 = 0;

		// last 7 days
		for ( $i = 0; $i < 7; $i++ ) {
			$date = gmdate( 'Y-m-d', strtotime( "-$i days", strtotime( $today ) ) );
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta( $post->ID, $key, true );
			if ( is_array( $views ) ) $current_7 += count( $views );
		}

		// previous 7 days (8–14 days ago)
		for ( $i = 7; $i < 14; $i++ ) {
			$date = gmdate( 'Y-m-d', strtotime( "-$i days", strtotime( $today ) ) );
			$key = '_infinity_unique_views_' . $date;
			$views = get_post_meta( $post->ID, $key, true );
			if ( is_array( $views ) ) $previous_7 += count( $views );
		}

		if ( $current_7 > 0 ) {
			$change = $previous_7 > 0 ? (($current_7 - $previous_7) / $previous_7) * 100 : 100;
			$top_posts[] = [
				'post'        => $post,
				'current'     => $current_7,
				'previous'    => $previous_7,
				'percentage'  => round( $change, 1 ),
			];
		}
	}

	usort( $top_posts, fn( $a, $b ) => $b['current'] - $a['current'] );
	$top_posts = array_slice( $top_posts, 0, 5 ); // Top 5 posts


	// ---------- PAGE OUTPUT ----------
	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">🔥 Today\'s Unique Post Views</h1>';
	echo '<p><strong>Date:</strong> ' . esc_html( $today ) . '</p>';
	echo '<hr class="wp-header-end" style="margin-bottom:20px;">';

	// ---------- 🏆 Top Cards Section ----------
	if ( ! empty( $top_posts ) ) {
		echo '<h2>🏆 Top 5 Posts (Last 7 Days)</h2>';
		echo '<div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:15px;margin-bottom:30px;">';

		foreach ( $top_posts as $item ) {
			$post  = $item['post'];
			$current = $item['current'];
			$previous = $item['previous'];
			$percentage = $item['percentage'];

			$thumb = get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: 'https://via.placeholder.com/300x180?text=No+Image';

			// Determine trend direction
			if ( $percentage > 0 ) {
				$trend = '<span style="color:#2ecc71;">▲ ' . esc_html( $percentage ) . '%</span>';
			} elseif ( $percentage < 0 ) {
				$trend = '<span style="color:#e74c3c;">▼ ' . esc_html( abs( $percentage ) ) . '%</span>';
			} else {
				$trend = '<span style="color:#888;">▬ 0%</span>';
			}

			echo '<div style="background:#fff;border:1px solid #ddd;border-radius:10px;width:250px;box-shadow:0 2px 6px rgba(0,0,0,0.05);overflow:hidden;">';
			echo '<img src="' . esc_url( $thumb ) . '" style="width:100%;height:140px;object-fit:cover;">';
			echo '<div style="padding:12px;">';
			echo '<h3 style="margin:0 0 8px;font-size:14px;line-height:1.4;"><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">' . esc_html( get_the_title( $post ) ) . '</a></h3>';
			echo '<p style="margin:4px 0;font-size:13px;color:#333;">7-Day Views: <strong>' . esc_html( $current ) . '</strong></p>';
			echo '<p style="margin:0;font-size:13px;">Change vs Prev Week: ' . $trend . '</p>';
			echo '</div></div>';
		}

		echo '</div>';
	}

	// ---------- Today's Table ----------
	if ( ! empty( $results ) ) {
		echo '<table class="wp-list-table widefat fixed striped posts">';
		echo '<thead>
				<tr>
					<th>Post Title</th>
					<th>Unique Views Today</th>
					<th>Total Views (All Time)</th>
					<th>Date</th>
				</tr>
			  </thead><tbody>';

		foreach ( $results as $row ) {
			$post  = $row['post'];
			$today_views = $row['today'];
			$total_views = $row['total'];

			echo '<tr>';
			echo '<td class="column-primary">
					<strong><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">' . esc_html( get_the_title( $post ) ) . '</a></strong>
					<div class="row-actions">
						<span class="view"><a href="' . esc_url( get_permalink( $post->ID ) ) . '" target="_blank">View</a></span>
					</div>
				  </td>';

			echo '<td><span style="background:#2271b1;color:#fff;padding:4px 8px;border-radius:4px;">' . esc_html( $today_views ) . '</span></td>';
			echo '<td><span style="background:#2ecc71;color:#fff;padding:4px 8px;border-radius:4px;">' . esc_html( $total_views ) . '</span></td>';
			echo '<td>' . esc_html( $today ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
	} else {
		echo '<p>😴 No posts have received unique views today.</p>';
	}

	echo '</div>';
}

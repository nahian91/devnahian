<?php
/**
 * Template for displaying courses
 *
 * @package Tutor\Templates
 * @subpackage CourseArchive
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.5.8
 */

use TUTOR\Input;

tutor_utils()->tutor_custom_header();

?>
<section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/breadcumb.jpg')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">                    
				<div class="post-single-content">
					<h4>Courses</h4>
				</div> 
			</div>
		</div>
	</div>
</section>
<?php 

$get = isset( $_GET['course_filter'] ) ? Input::sanitize_array( $_GET ) : array();//phpcs:ignore
if ( isset( $get['course_filter'] ) ) {
	$filter = ( new \Tutor\Course_Filter( false ) )->load_listing( $get, true );
	query_posts( $filter );
}

// Load the template.
tutor_load_template(
	'archive-course-init',
	array_merge(
		$get,
		array(
			'course_filter'     => (bool) tutor_utils()->get_option( 'course_archive_filter', false ),
			'supported_filters' => tutor_utils()->get_option( 'supported_course_filters', array() ),
			'loop_content_only' => false,
		)
	)
);

tutor_utils()->tutor_custom_footer();

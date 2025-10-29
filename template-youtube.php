<?php
/**
 * Template Name: YouTube Videos
 */

get_header();

?>

<section class="breadcumb-area" style="background-image:url('<?php echo get_template_directory_uri();?>/assets/img/bg-footer.jpg')">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
			<div class="post-single-content">
						<h4><?php the_title();?></h4>
					</div> 
			</div>
		</div>
	</div>
</section>

<?php 

// Channel ID of Code with Abdullah Nahian
$channel_id = 'UCOwyfgQcUnq9uAx9e8rUWqg';
$rss_url = "https://www.youtube.com/feeds/videos.xml?channel_id={$channel_id}";
$rss = @simplexml_load_file($rss_url);

// Convert to array
$videos = [];
if (!empty($rss->entry)) {
    foreach ($rss->entry as $video) {
        $video_id = (string) $video->children('yt', true)->videoId;
        $title = (string) $video->title;
        $thumbnail = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";
        $video_link = "https://www.youtube.com/watch?v={$video_id}";
        $videos[] = [
            'id' => $video_id,
            'title' => $title,
            'thumbnail' => $thumbnail,
            'link' => $video_link,
        ];
    }
}

// Randomize and slice initial load
shuffle($videos);
$initial_videos = array_slice($videos, 0, 9);
$remaining_videos = array_slice($videos, 9);
?>

<main id="primary" class="site-main py-5">
    <section class="youtube-top-videos-section">
        <div class="container">
            <h2 class="section-title">Latest YouTube Videos</h2>

            <div id="youtube-video-grid" class="row">
                <?php foreach ($initial_videos as $video) : ?>
                    <div class="col-md-4 mb-4 video-card">
                        <div class="youtube-video-card">
                            <a href="<?php echo esc_url($video['link']); ?>" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url($video['thumbnail']); ?>" alt="<?php echo esc_attr($video['title']); ?>" class="img-fluid">
                                <h5><?php echo esc_html($video['title']); ?></h5>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($remaining_videos)) : ?>
                <div class="text-center mt-4">
                    <button id="load-more-videos" class="btn btn-primary">Load More</button>
                </div>
            <?php endif; ?>

            <script>
                const allVideos = <?php echo wp_json_encode($remaining_videos); ?>;
                let index = 0;

                document.addEventListener("DOMContentLoaded", function () {
                    const grid = document.getElementById("youtube-video-grid");
                    const button = document.getElementById("load-more-videos");

                    if (button) {
                        button.addEventListener("click", function () {
                            const nextVideos = allVideos.splice(0, 9);
                            nextVideos.forEach(video => {
                                const col = document.createElement("div");
                                col.className = "col-md-4 mb-4 video-card";
                                col.innerHTML = `
                                    <div class="youtube-video-card">
                                        <a href="${video.link}" target="_blank" rel="noopener">
                                            <img src="${video.thumbnail}" alt="${video.title}" class="img-fluid">
                                            <h5>${video.title}</h5>
                                        </a>
                                    </div>`;
                                grid.appendChild(col);
                            });

                            if (allVideos.length === 0) {
                                button.style.display = "none";
                            }
                        });
                    }
                });
            </script>

            <style>
                .youtube-video-card {
                    border: 1px solid #ddd;
                    padding: 10px;
                    border-radius: 8px;
                    text-align: center;
                    transition: all 0.3s ease;
                    background: #fff;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                }
                .youtube-video-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
                .youtube-video-card img {
                    border-radius: 6px;
                    margin-bottom: 10px;
                }
                .youtube-video-card h5 {
                    font-size: 16px;
                    color: #333;
                    margin: 0;
                }
                #load-more-videos {
                    background-color: #000d16;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    color: #fff;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.3s;
                }
                #load-more-videos:hover {
                    background-color: #000d16;
                }
            </style>

        </div>
    </section>
</main>

<?php
get_footer();

<?php
get_header();

/**
 * Single Resource Template
 * Designed for conversion: Stacked Gallery, No Tabs, Sticky Sidebar
 */

while (have_posts()) : the_post();
    // 1. Fetch ACF Fields (As per your JSON structure)
    $image          = get_field('resource_image');
    $badge          = get_field('resource_badge');
    $short_desc     = get_field('resource_short_description');
    $price          = get_field('resource_price') ?: '0';
    $full_desc      = get_field('resource_description'); // WYSIWYG
    $video_url      = get_field('resource_video');
    $gallery        = get_field('resource_gallery'); // Array
    $preview_url    = get_field('resource_preview');
    $features       = get_field('resource_features'); // Repeater
    $techs          = get_field('resource_technology'); // Repeater
    
    // Admin Setting
    $bkash_number   = "01686195607"; 
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

<main class="resource-single-layout py-5 bg-light min-vh-100">
    <div class="container">

        <div class="row g-5">
            <div class="col-lg-8">
                <div class="resource-single-content">
                    <img src="<?php echo $image['url'];?>" alt="">
                </div>
                <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
                    <h3 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Description</h3>
                    <div class="resource-entry-content fs-5">
                        <?php echo $full_desc ? $full_desc : get_the_content(); ?>
                    </div>
                </article>

                <?php if($features): ?>
                <section class="mb-5">
                    <h4 class="fw-bold mb-4">Features</h4>
                    <div class="row g-3">
                        <?php foreach($features as $f): ?>
                            <div class="col-md-6">
                                <div class="resource-features d-flex align-items-center p-3 bg-white border rounded-4 shadow-sm h-100">
                                    <span class="dashicons dashicons-yes text-success me-3 fs-3"></span>
                                    <span class="fw-medium"><?php echo esc_html($f['resource_feature_title']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if($video_url): ?>
                <section class="mb-5">
                    <h4 class="fw-bold mb-4">Video Overview</h4>
                    <div class="ratio ratio-16x9 rounded-4 shadow-lg overflow-hidden border bg-black">
                        <iframe src="<?php echo esc_url($video_url); ?>" allowfullscreen></iframe>
                    </div>
                </section>
                <?php endif; ?>

                <?php if($gallery): ?>
                <section class="mb-5">
                    <h4 class="fw-bold mb-4">Gallery</h4>
                    <div class="resource-gallery gallery-vertical-stack d-flex flex-column gap-5">
                        <?php foreach($gallery as $img): ?>
                            <div class="gallery-card shadow-sm rounded-4 overflow-hidden border bg-white">
                                <img src="<?php echo esc_url($img['url']); ?>" class="img-fluid w-100" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 10;">                    
                    <div class="card feature-single-box border-0 shadow rounded-4 p-4 mb-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="fw-bold mb-0"><?php echo esc_html($price); ?></h2>
                        </div>

                        <div class="d-grid gap-3">
                            <button id="buyNowBtn" class="btn btn-primary btn-lg rounded-3 py-3 fw-bold shadow-sm">
                                <span class="dashicons dashicons-cart me-2"></span>Buy Now
                            </button>
                            
                            <?php if($preview_url): ?>
                                <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="btn btn-outline-dark py-2 rounded-3 fw-medium">
                                    Live Preview ↗
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if($techs): ?>
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="features-tech small fw-bold text-uppercase text-muted mb-3">Language Used</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach($techs as $t): ?>
                                    <span class="badge bg-light text-dark border fw-normal py-2 px-3"><?php echo esc_html($t['resource_technology_title']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-3 border rounded-4 bg-white shadow-sm d-flex align-items-center">
                        <div class="text-success me-3">
                            <span class="dashicons dashicons-shield" style="font-size: 32px; width:32px; height:32px;"></span>
                        </div>
                        <div>
                            <div class="fw-bold small">Verified Checkout</div>
                            <div class="text-muted small">Access link sent to WhatsApp.</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<div id="orderModal" class="dnr-modal-overlay" style="display:none;">
    <div class="dnr-modal-container shadow-lg">
        <button class="close-modal">&times;</button>
        
        <div id="formSection">
            <div class="text-center mb-4">
                <h5 class="fw-bold">Payment to bKash (Personal)</h5>
                <div class="bg-primary text-white p-3 rounded-4 mt-3 shadow-sm">
                    <small class="d-block opacity-75">Merchant Number</small>
                    <strong class="fs-4"><?php echo $bkash_number; ?></strong>
                </div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $bkash_number; ?>" class="img-fluid mt-3 border p-2 bg-white rounded-3 shadow-sm">
            </div>

            <form id="resourceOrderForm" class="row g-2">
                <div class="col-12"><input type="text" name="client_name" class="form-control shadow-none" placeholder="Your Full Name" required></div>
                <div class="col-12"><input type="email" name="client_email" class="form-control shadow-none" placeholder="Email Address" required></div>
                <div class="col-12"><input type="text" name="client_phone" class="form-control shadow-none" placeholder="WhatsApp Number" required></div>
                <div class="col-6"><input type="text" name="bkash_number" class="form-control shadow-none" placeholder="Sent From No." required></div>
                <div class="col-6"><input type="text" name="trx_id" class="form-control shadow-none" placeholder="Transaction ID" required></div>
                
                <input type="hidden" name="action" value="submit_resource_order">
                <input type="hidden" name="product_id" value="<?php the_ID(); ?>">
                
                <div class="col-12 mt-3">
                    <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow">Submit Order</button>
                </div>
            </form>
        </div>

        <div id="successSection" style="display:none;" class="text-center py-5">
            <div class="spinner-border text-warning mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h3 class="fw-bold">Verifying TrxID</h3>
            <p class="text-muted px-4">Thank you! We are manually verifying your payment. Your download link will be sent to WhatsApp within 1-6 hours.</p>
            <button class="btn btn-dark btn-sm px-4 rounded-pill" onclick="location.reload()">Done</button>
        </div>
    </div>
</div>

<style>
/* MODAL & UI STYLES */
.dnr-modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; display:flex; align-items:center; justify-content:center; backdrop-filter: blur(10px); }
.dnr-modal-container { background:#fff; padding:40px; border-radius:30px; width:95%; max-width:460px; position:relative; animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.close-modal { position:absolute; right:20px; top:15px; background:none; border:none; font-size:36px; color:#ddd; cursor:pointer; transition: 0.2s; }
.close-modal:hover { color: #f00; }
.form-control { border-radius:12px; padding: 12px; border: 1px solid #eee; background: #f9f9f9; }
.form-control:focus { border-color: #0077b7; background: #fff; }

/* CONTENT STYLES */
.resource-entry-content { line-height: 1.8; color: #333; }
.resource-entry-content h2, .resource-entry-content h3 { font-weight: 800; margin-top: 2rem; color: #000; }
.gallery-card img { transition: 0.5s ease; cursor: zoom-in; }
.gallery-card:hover img { transform: scale(1.03); }
.dashicons { vertical-align: middle; }

.btn.btn-outline-dark.py-2.rounded-3.fw-medium {
  display: block;
  background-color: #0077b7;
  color: #fff;
  border: 0;
  margin-top: 20px;
}
#buyNowBtn {
  display: block;
  width: 100%;
  background-color: #0077b7;
  padding: 5px 0 !important;
  font-size: 20px !important;
  display: flex;
  gap: 5px;
  justify-content: center;
  align-items: center;
}

.dashicons.dashicons-yes.text-success.me-3.fs-3 {
}
.resource-features .dashicons.dashicons-yes {
  color: #0077b7 !important;
  margin-right: 10px;
}
.resource-features {
  margin-bottom: 2px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Modal Open/Close
    $('#buyNowBtn').on('click', function() {
        $('#orderModal').fadeIn(200).css('display', 'flex');
    });
    $('.close-modal').on('click', function() {
        $('#orderModal').fadeOut(200);
    });

    // Handle AJAX Submission
    $('#resourceOrderForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#formSection').fadeOut(300, function() {
                        $('#successSection').fadeIn();
                    });
                } else {
                    alert('Submission failed: ' + response.data);
                    $btn.prop('disabled', false).text('Submit Order');
                }
            },
            error: function() {
                alert('Connection error. Please try again.');
                $btn.prop('disabled', false).text('Submit Order');
            }
        });
    });
});
</script>

<?php endwhile; get_footer(); ?>
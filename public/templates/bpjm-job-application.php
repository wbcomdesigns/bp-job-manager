<?php

/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if (!defined('ABSPATH')) {
	exit;
}
$job_id = filter_input(INPUT_GET, 'args') ? filter_input(INPUT_GET, 'args') : '';

get_header();
global $post;
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="post-<?php echo esc_attr($post->ID); ?>" class="post-<?php echo esc_attr($post->ID); ?> page type-page status-publish hentry">
			<header class="entry-header">
				<h1 class="entry-title">
					<?php
					// Escaping and echoing the post title
					echo esc_html($post->post_title);

					if (!empty($job_id)) {
						$job = get_post($job_id);

						// Concatenating with a colon, escaping, and then echoing the job title
						echo esc_html(': ' . $job->post_title);
					}
					?>
				</h1>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php
				if (!empty($job_id)) {
					echo do_shortcode('[job_apply id="' . $job_id . '"]');
				} else {
				?>
					<div>
						<p>
							<?php esc_html_e('No content available.', 'bp-job-manager'); ?>
						</p>
					</div>
				<?php
				}
				?>
			</div><!-- .entry-content -->
		</div><!-- #post-## -->
	</main><!-- #main -->
</div>
<?php
get_footer();

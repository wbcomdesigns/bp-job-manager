<?php

/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if (!defined('ABSPATH')) {
	exit;
}

$job_id = filter_input(INPUT_GET, 'args', FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_GET, 'args', FILTER_SANITIZE_NUMBER_INT) : '';

get_header();
if (!isset($post)) {
    $post = get_post();
}
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="post-<?php echo esc_attr($post->ID); ?>" class="post-<?php echo esc_attr($post->ID); ?> page type-page status-publish hentry">
			<header class="entry-header">
				<h1 class="entry-title">
					<?php echo esc_html($post->post_title); ?>
					<?php if (!empty($job_id)) : 
						$job = get_post($job_id);
						if ($job) {
							echo esc_html(': ' . $job->post_title);
						}
					endif; ?>
				</h1>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php if (!empty($job_id)) : ?>
					<?php echo do_shortcode('[job_apply id="' . $job_id . '"]'); ?>
				<?php else : ?>
					<div>
						<p><?php esc_html_e('No content available.', 'bp-job-manager'); ?></p>
					</div>
				<?php endif; ?>
			</div><!-- .entry-content -->
		</div><!-- #post-## -->
	</main><!-- #main -->
</div>
<?php
get_footer();
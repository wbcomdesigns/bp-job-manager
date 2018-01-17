<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly
get_header();
global $post;
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="post-<?php echo $post->ID; ?>" class="post-<?php echo $post->ID; ?> page type-page status-publish hentry">
			<header class="entry-header">
				<h1 class="entry-title">
					<?php
					echo $post->post_title;
					if ( isset( $_GET['args'] ) ) {
						$job_id = sanitize_text_field( $_GET['args'] );
						$job    = get_post( $job_id );
						echo ': ' . $job->post_title;
					}
					?>
				</h1>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php
				if ( isset( $_GET['args'] ) ) {
					$job_id = sanitize_text_field( $_GET['args'] );
					echo do_shortcode( '[job_apply id="' . $job_id . '"]' );
				} else {
					?>
					<div>
						<p>
							<?php _e( 'No content available.', 'bp-job-manager' ); ?>
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

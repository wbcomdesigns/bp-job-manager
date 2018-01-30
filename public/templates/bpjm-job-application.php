<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
global $post;
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="post-<?php echo esc_attr( $post->ID ); ?> page type-page status-publish hentry">
			<header class="entry-header">
				<h1 class="entry-title">
					<?php
					echo esc_html( $post->post_title, 'bp-job-manager' );
					if ( isset( $_GET['args'] ) ) {
						$job_id = sanitize_text_field( wp_unslash( $_GET['args'] ) );
						$job    = get_post( $job_id );
						echo esc_html( ': ' . $job->post_title, 'bp-job-manager' );
					}
					?>
				</h1>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php
				if ( isset( $_GET['args'] ) ) {
					$job_id = sanitize_text_field( wp_unslash( $_GET['args'] ) );
					echo do_shortcode( '[job_apply id="' . $job_id . '"]' );
				} else {
					?>
					<div>
						<p>
							<?php esc_html_e( 'No content available.', 'bp-job-manager' ); ?>
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

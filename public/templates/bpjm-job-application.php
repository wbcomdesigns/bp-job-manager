<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bpjm_job_id = (int) filter_input( INPUT_GET, 'args', FILTER_SANITIZE_NUMBER_INT );

get_header();
if ( ! isset( $post ) ) {
	$post = get_post(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required for template rendering.
}
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="post-<?php echo esc_attr( $post->ID ); ?> page type-page status-publish hentry">
			<header class="entry-header">
				<h1 class="entry-title">
					<?php echo esc_html( $post->post_title ); ?>
					<?php
					if ( ! empty( $bpjm_job_id ) ) :
						$bpjm_job = get_post( $bpjm_job_id );
						if ( $bpjm_job ) {
							echo esc_html( ': ' . $bpjm_job->post_title );
						}
					endif;
					?>
				</h1>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<?php if ( ! empty( $bpjm_job_id ) ) : ?>
					<?php
					/*
					 * `job_content_start` / `job_content_end` wrap job content
					 * everywhere WP Job Manager renders it: core fires the pair in
					 * WP_Job_Manager_Post_Types::job_content(), and theme overrides
					 * such as Reign's content-single-job_listing.php repeat them.
					 * This page renders job content too - the [job_apply] form for
					 * one listing - but fired neither, so anything printing through
					 * them was silently dropped here.
					 *
					 * WP Job Manager Applications registers its validation errors on
					 * `job_content_start`, so a rejected application had no way to
					 * report itself on this page; the member saw the form come back
					 * with no message and reasonably concluded an empty application
					 * had been accepted.
					 *
					 * Third-party hook names, intentionally unprefixed - they belong
					 * to WP Job Manager's contract, not ours.
					 */
					do_action( 'job_content_start' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from WP Job Manager.
					?>
					<?php echo do_shortcode( '[job_apply id="' . $bpjm_job_id . '"]' ); ?>
					<?php do_action( 'job_content_end' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from WP Job Manager. ?>
				<?php else : ?>
					<div>
						<p><?php esc_html_e( 'No content available.', 'bp-job-manager' ); ?></p>
					</div>
				<?php endif; ?>
			</div><!-- .entry-content -->
		</div><!-- #post-## -->
	</main><!-- #main -->
</div>
<?php
get_footer();
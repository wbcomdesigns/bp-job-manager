<?php
/**
 * Help & Support partial: rendered inside shell.php (non-settings tab).
 *
 * Ports the legacy welcome page (documentation, support, feedback) into
 * the card-panel vocabulary.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Getting Started', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-card__body">
		<p class="bpjm-card__body-lead">
			<?php esc_html_e( 'BuddyPress Job Manager connects WP Job Manager to BuddyPress, adding dedicated Jobs and Resumes tabs to member profiles so employers and candidates can work entirely from their profile.', 'bp-job-manager' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Use the General tab to choose which member roles can post jobs and apply, and to control activity-feed updates and application notifications.', 'bp-job-manager' ); ?>
		</p>
	</div>
</div>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Help & Support Resources', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'Find the resources you need to get help from us. The documentation is a great place to start; for anything else, our support center is ready.', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-card__body">
		<div class="bpjm-resource-grid">
			<div class="bpjm-resource">
				<span class="bpjm-resource__icon" aria-hidden="true"><span class="dashicons dashicons-book"></span></span>
				<h3 class="bpjm-resource__title"><?php esc_html_e( 'Documentation', 'bp-job-manager' ); ?></h3>
				<p class="bpjm-resource__desc"><?php esc_html_e( 'Our comprehensive guide covers every aspect of the plugin. You will find most of your answers here.', 'bp-job-manager' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/" class="bpjm-btn bpjm-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?></a>
			</div>
			<div class="bpjm-resource">
				<span class="bpjm-resource__icon" aria-hidden="true"><span class="dashicons dashicons-sos"></span></span>
				<h3 class="bpjm-resource__title"><?php esc_html_e( 'Support Center', 'bp-job-manager' ); ?></h3>
				<p class="bpjm-resource__desc"><?php esc_html_e( 'Our support center provides top-notch assistance. Reach out any time you need a hand.', 'bp-job-manager' ); ?></p>
				<a href="https://wbcomdesigns.com/support/" class="bpjm-btn bpjm-btn-secondary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get Support', 'bp-job-manager' ); ?></a>
			</div>
		</div>
	</div>
</div>

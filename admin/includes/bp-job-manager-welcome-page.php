<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    bp-job-manager
 * @subpackage bp-job-manager/admin/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
?>

<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
			<p class="wbcom-welcome-description"><?php esc_html_e( 'Incorporates BuddyPress with the WP Job Manager plugin by creating specific tabs in employer’s and candidate’s profiles.', 'bp-job-manager' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'bp-job-manager' ); ?></h3>
				<p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'bp-job-manager' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Job Manager to learn all aspects of the plugin. You will find most of your answers here.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'bp-job-manager' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'bp-job-manager' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->

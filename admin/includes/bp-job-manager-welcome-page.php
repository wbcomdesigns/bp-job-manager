<?php
/**
 * This file is used for rendering and saving the plugin welcome settings.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    bp-job-manager
 * @subpackage bp-job-manager/admin/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
			<p class="wbcom-welcome-description"><?php esc_html_e( 'Integrates BuddyPress with the WP Job Manager plugin by adding dedicated tabs for employers and candidates in their profiles.', 'bp-job-manager' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help & Support Resources', 'bp-job-manager' ); ?></h3>
				<p><?php esc_html_e( 'Find the resources you need to get help from us. The documentation is a great place to start. For additional assistance, our customer care team is available through the support center.', 'bp-job-manager' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'Our comprehensive guide to BuddyPress Job Manager covers all aspects of the plugin. You’ll find most of your answers here.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'Our support center is here to provide you with top-notch assistance. Once your plugin is activated, feel free to reach out for help at any time.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'bp-job-manager' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Share Your Feedback', 'bp-job-manager' ); ?></h3>
						<p><?php esc_html_e( 'We’d love to hear about your experience with the plugin and welcome any suggestions for future updates.', 'bp-job-manager' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'bp-job-manager' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->

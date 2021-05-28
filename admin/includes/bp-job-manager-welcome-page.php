<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 */
if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
?>

<div class="wbcom-tab-content">
    <div class="wbcom-welcome-main-wrapper">
        <div class="wbcom-welcome-head">
            <h2 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Job Manager', 'bp-job-manager' ); ?></h2>
            <p class="wbcom-welcome-description"><?php esc_html_e( 'Allow the site admin to select global friends for all his members with BuddyPress Auto friends plugin. If you are starting an online community with BuddyPress and want some common friends to be added to the friend list of your members, BuddyPress Auto friends is the perfect option for you.', 'bp-job-manager' ) ?></p>
        </div><!-- .wbcom-welcome-head -->

        <div class="wbcom-welcome-content">
            
            <div class="wbcom-video-link-wrapper">
                
            </div>

            <div class="wbcom-welcome-support-info">
                <h3><?php esc_html_e( 'Help &amp; Support Resources', 'bp-job-manager' ); ?></h3>
                <p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'bp-job-manager' ); ?></p>
                <hr>

                <div class="three-col">

                    <div class="col">
                        <h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'bp-job-manager' ); ?></h3>
                        <p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Auto Friends to learn all aspects of the plugin. You will find most of your answers here.', 'bp-job-manager' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-paid-addons/bp-job-manager/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'bp-job-manager' ); ?></h3>
                        <p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'bp-job-manager' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'bp-job-manager' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'bp-job-manager' ); ?></h3>
                        <p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'bp-job-manager' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'bp-job-manager' ); ?></a>
                    </div>

                </div>

            </div>
        </div>

    </div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
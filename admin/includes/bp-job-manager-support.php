<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="bpjm-adming-setting">
	<div class="bpjm-tab-header"><h3><?php esc_attr_e( 'Have some questions?', 'bp-job-manager' ); ?></h3></div>
		<div class="bpjm-admin-settings-block">
		<div id="bpjm-settings-tbl">
			<div class="bpjm-admin-row">
				<div>
					<button class="bpjm-accordion"><?php esc_attr_e( 'What plugin does this plugin require?', 'bp-job-manager' ); ?></button>
					<div class="panel">
						<p>
							<?php esc_attr_e( 'Since this plugin deals with jobs, in buddypress user profiles, so to have the plugin fully functional, you must have the following plugins:', 'bp-job-manager' ); ?>
							<ol type="1" class="bpjm-required-plugins-links">
								<li><a href="https://buddypress.org/download/" target="_blank">BuddyPress</a> - <i><?php esc_attr_e( 'Free Available', 'bp-job-manager' ); ?></i></li>
								<li><a href="https://wpjobmanager.com/" target="_blank">WordPress Job Manager</a> - <i><?php esc_attr_e( 'Free Available', 'bp-job-manager' ); ?></i></li>
								<li><a href="https://wpjobmanager.com/add-ons/applications/" target="_blank">WordPress Job Manager - Applications</a> - <i><?php esc_attr_e( 'Paid Addon', 'bp-job-manager' ); ?></i></li>
								<li><a href="https://wpjobmanager.com/add-ons/resume-manager/" target="_blank">WordPress Job Manager - Resume Manager</a> - <i><?php esc_attr_e( 'Paid Addon', 'bp-job-manager' ); ?></i></li>
								<li><a href="https://wpjobmanager.com/add-ons/bookmarks/" target="_blank">WordPress Job Manager - Bookmarks</a> - <i><?php esc_attr_e( 'Paid Addon - Optional for this plugin', 'bp-job-manager' ); ?></i></li>
								<li><a href="https://wpjobmanager.com/add-ons/job-alerts/" target="_blank">WordPress Job Manager - Job Alerts</a> - <i><?php esc_attr_e( 'Paid Addon - Optional for this plugin', 'bp-job-manager' ); ?></i></li>
							</ol>
						</p>
					</div>
				</div>
			</div>

			<div class="bpjm-admin-row">
				<div>
					<button class="bpjm-accordion"><?php esc_attr_e( 'How does this plugin work?', 'bp-job-manager' ); ?></button>
					<div class="panel">
						<p><?php esc_attr_e( 'This plugin integrates the WordPress Job Manager with the big name, BuddyPress. The site members can post the jobs from their profiles and other members can apply to the same jobs.', 'bp-job-manager' ); ?></p>
					</div>
				</div>
			</div>

			<div class="bpjm-admin-row">
				<div>
					<button class="bpjm-accordion"><?php esc_attr_e( 'How to go for any custom development?', 'bp-job-manager' ); ?></button>
					<div class="panel">
						<p><?php esc_attr_e( 'If you need additional help you can contact us for <a href="https://wbcomdesigns.com/contact/" target="_blank" title="Custom Development by Wbcom Designs">Custom Development</a>.', 'bp-job-manager' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

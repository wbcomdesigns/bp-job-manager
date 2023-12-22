<div class="wrap">
	<div class="wbcom-bb-plugins-offer-wrapper">
		<div id="wb_admin_logo">
			<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/?utm_source=pluginoffernotice&utm_medium=community_banner" target="_blank">
				<img src="<?php echo esc_url( BPJM_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
			</a>
		</div>
	</div>
	<div class="wbcom-wrap wbcom-plugin-wrapper">
		<div class="wbcom_admin_header-wrapper">
			<div id="wb_admin_plugin_name">
				<?php esc_html_e( 'BuddyPress Job Manager', 'bp-job-manager' ); ?>
				<span>
				<?php
				/* translators: %s: */
				printf( esc_html__( 'Version %s', 'bp-job-manager' ), esc_attr( BPJM_PLUGIN_VERSION ) );
				?>
				</span>
			</div>
			<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
		</div>
		<div class="wbcom-all-addons-plugins-wrap">
		<h4 class="wbcom-support-section"><?php esc_html_e( 'Plugin License', 'bp-job-manager' ); ?></h4>
		<div class="wb-plugins-license-tables-wrap">
			<div class="wbcom-license-support-wrapp">
			<table class="form-table wb-license-form-table desktop-license-headings">
				<thead>
					<tr>
						<th class="wb-product-th"><?php esc_html_e( 'Product', 'bp-job-manager' ); ?></th>
						<th class="wb-version-th"><?php esc_html_e( 'Version', 'bp-job-manager' ); ?></th>
						<th class="wb-key-th"><?php esc_html_e( 'Key', 'bp-job-manager' ); ?></th>
						<th class="wb-status-th"><?php esc_html_e( 'Status', 'bp-job-manager' ); ?></th>
						<th class="wb-action-th"><?php esc_html_e( 'Action', 'bp-job-manager' ); ?></th>
					</tr>
				</thead>
			</table>
			<?php do_action( 'wbcom_add_plugin_license_code' ); ?>
			<table class="form-table wb-license-form-table">
				<tfoot>
					<tr>
						<th class="wb-product-th"><?php esc_html_e( 'Product', 'bp-job-manager' ); ?></th>
						<th class="wb-version-th"><?php esc_html_e( 'Version', 'bp-job-manager' ); ?></th>
						<th class="wb-key-th"><?php esc_html_e( 'Key', 'bp-job-manager' ); ?></th>
						<th class="wb-status-th"><?php esc_html_e( 'Status', 'bp-job-manager' ); ?></th>
						<th class="wb-action-th"><?php esc_html_e( 'Action', 'bp-job-manager' ); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	</div>
	</div><!-- .wbcom-wrap -->
</div><!-- .wrap -->

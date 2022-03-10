<div class="wrap">
	<hr class="wp-header-end">
	<div class="wbcom-wrap">
		<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
		<?php
		$wbcom_setting_obj = new Wbcom_Admin_Settings();
		$free_plugins      = $wbcom_setting_obj->wbcom_all_free_plugins();
		$paid_plugins      = $wbcom_setting_obj->wbcom_all_paid_plugins();
		?>
		<h4 class="wbcom-plugin-heading"><?php esc_html_e( 'Paid Addons', 'bp-job-manager' ); ?></h4>
		<div class="reign-demos-wrapper reign-importer-section">
			<div class="reign-demos-inner-wrapper wbcom-plugins-inner-wrapper">
				<?php
				foreach ( $paid_plugins as $key => $plugin_details ) {
					?>
					<div class="wbcom-req-plugin-card">
						<div class="wbcom_single_left">
							<div class="wbcom_single_icon_wrapper">
								<i class="<?php echo esc_attr( $plugin_details['icon'] ); ?>" aria-hidden="true"></i>
							</div>
						</div>
						<div class="wbcom_single_right">
							<h3><?php echo esc_html( $plugin_details['name'] ); ?></h3>
							<div class="wbcom_single_right-wrap">
								<p class="plugin-description"><?php echo esc_html( $plugin_details['description'] ); ?></p>
								<div class="activation_button_wrap">
									<a href="<?php echo esc_url( $plugin_details['download_url'] ); ?>" class="wb_btn wb_btn_default" target="_blank" >
										<i class="fa fa-eye"></i>
										<?php esc_html_e( 'View', 'bp-job-manager' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<h4 class="wbcom-plugin-heading"><?php esc_html_e( 'Free Addons', 'bp-job-manager' ); ?></h4>
		<div class="reign-demos-wrapper reign-importer-section">
			<div class="reign-demos-inner-wrapper wbcom-plugins-inner-wrapper">
				<?php
				foreach ( $free_plugins as $key => $plugin_details ) {
					?>
					<div class="wbcom-req-plugin-card">
						<div class="wbcom_single_left">
							<div class="wbcom_single_icon_wrapper">
								<i class="<?php echo esc_attr( $plugin_details['icon'] ); ?>" aria-hidden="true"></i>
							</div>
						</div>
						<div class="wbcom_single_right">
							<h3><a href="<?php echo esc_url( $plugin_details['wp_url'] ); ?>"><?php echo esc_html( $plugin_details['name'] ); ?></a></h3>
							<div class="wbcom_single_right-wrap">
								<p class="plugin-description"><?php echo esc_html( $plugin_details['description'] ); ?></p>
								<div class="activation_button_wrap">
									<a href="<?php echo esc_url( $plugin_details['wp_url'] ); ?>" class="wb_btn wb_btn_default" target="blank">
										<i class="fa fa-eye"></i>
										<?php esc_html_e( 'View', 'bp-job-manager' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div><!-- .wbcom-wrap -->
</div><!-- .wrap -->

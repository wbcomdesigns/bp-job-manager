<?php
/**
 * Settings tabs template for BuddyPress Job Manager.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bpjm_current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpjm-welcome';
echo '<div class="wbcom-tabs-section">
		<div class="nav-tab-wrapper">
			<div class="wb-responsive-menu">
				<span>' . esc_html( 'Menu' ) . '</span>
				<input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn">
				<span class="wb-icon-bars"></span>
				</label>
			</div>
			<ul>';
foreach ( $this->plugin_settings_tabs as $bpjm_tab_key => $bpjm_tab_caption ) {
	$bpjm_active = $bpjm_current_tab === $bpjm_tab_key ? 'nav-tab-active' : '';
	echo '<li class="' . esc_attr( $bpjm_tab_key ) . '">
			<a class="nav-tab ' . esc_attr( $bpjm_active ) . '" id="' . esc_attr( $bpjm_tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $bpjm_tab_key ) . '">' . esc_html( $bpjm_tab_caption ) . '</a>
		</li>';
}
echo '</div>
	</ul>
</div>';

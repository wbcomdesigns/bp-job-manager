<?php

$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpjm-welcome';
echo '<div class="wbcom-tabs-section">
		<div class="nav-tab-wrapper">
			<div class="wb-responsive-menu">
				<span>' . esc_html( 'Menu' ) . '</span>
				<input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn">
				<span class="wb-icon-bars"></span>
				</label>
			</div>
			<ul>';
foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
	$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
	echo '<li class="' . esc_attr( $tab_key ) . '">
			<a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption, 'bp-job-manager' ) . '</a>
		</li>';
}
echo '</div>
	</ul>
</div>';
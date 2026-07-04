<?php
/**
 * Admin page shell: page header, sidebar nav, body slot.
 *
 * Receives from Bp_Job_Manager_Admin_Panel::render_page():
 *
 * @var array  $bpjm_tabs           Tab registry keyed by slug.
 * @var string $active              Active tab slug.
 * @var string $page_url            Base URL (admin.php?page=bp-job-manager).
 * @var array  $settings            bpjm_general_settings option.
 * @var string $view                View slug (e.g. 'overview', 'settings-general').
 * @var string $view_path           Absolute path to the partial.
 * @var bool   $in_settings_group   True when the active tab is a Settings tab.
 * @var string $settings_form_group register_setting() group for settings_fields().
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;

$bpjm_version = defined( 'BPJM_PLUGIN_VERSION' ) ? BPJM_PLUGIN_VERSION : '';
?>
<div class="wrap bpjm-admin">

	<header class="bpjm-page-header">
		<div class="bpjm-page-header__title">
			<span class="dashicons dashicons-businessman" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'BuddyPress Job Manager', 'bp-job-manager' ); ?></h1>
				<p class="bpjm-page-header__subtitle"><?php esc_html_e( 'Bring WP Job Manager into BuddyPress profiles — members post jobs, manage resumes, and apply, all from their profile.', 'bp-job-manager' ); ?></p>
			</div>
		</div>
		<div class="bpjm-page-header__actions">
			<?php if ( $bpjm_version ) : ?>
				<span class="bpjm-version-pill">v<?php echo esc_html( $bpjm_version ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<?php
	/*
	 * Without this marker, core's common.js re-parents every .notice to
	 * sit right after the first <h1> it finds, which slots the "Settings
	 * saved" banner between our title and its subtitle instead of below
	 * the whole header. See playbook Part 10.2.
	 */
	?>
	<hr class="wp-header-end">

	<div class="bpjm-settings-layout">

		<aside class="bpjm-settings-sidebar">
			<div class="bpjm-settings-sidebar-brand">
				<span class="bpjm-settings-brand-icon" aria-hidden="true">
					<span class="dashicons dashicons-businessman"></span>
				</span>
				<div class="bpjm-settings-brand-text">
					<p class="bpjm-settings-brand-name"><?php esc_html_e( 'Job Manager', 'bp-job-manager' ); ?></p>
					<p class="bpjm-settings-brand-sub"><?php esc_html_e( 'Plugin', 'bp-job-manager' ); ?></p>
				</div>
			</div>
			<nav class="bpjm-settings-sidebar-nav" aria-label="<?php esc_attr_e( 'Job Manager navigation', 'bp-job-manager' ); ?>">
				<?php
				$printed_groups = array();
				$group_labels   = array(
					'settings' => esc_html__( 'Settings', 'bp-job-manager' ),
					'account'  => esc_html__( 'Resources', 'bp-job-manager' ),
					'discover' => esc_html__( 'More Tools', 'bp-job-manager' ),
				);
				foreach ( $bpjm_tabs as $bpjm_slug => $bpjm_tab ) {
					$group = isset( $bpjm_tab['group'] ) ? $bpjm_tab['group'] : 'main';
					if ( 'main' !== $group && ! in_array( $group, $printed_groups, true ) ) {
						echo '<div class="bpjm-snav-divider" role="separator"></div>';
						if ( isset( $group_labels[ $group ] ) ) {
							echo '<p class="bpjm-snav-section-label">' . esc_html( $group_labels[ $group ] ) . '</p>';
						}
						$printed_groups[] = $group;
					}
					$classes  = 'bpjm-snav-link';
					$classes .= $active === $bpjm_slug ? ' bpjm-snav-link--active' : '';
					echo '<a href="' . esc_url( $page_url . '&tab=' . $bpjm_slug ) . '" class="' . esc_attr( $classes ) . '">';
					echo '<span class="dashicons ' . esc_attr( $bpjm_tab['icon'] ) . '" aria-hidden="true"></span>';
					echo esc_html( $bpjm_tab['label'] );
					echo '</a>';
				}
				?>

				<div class="bpjm-snav-divider" role="separator"></div>
				<p class="bpjm-snav-section-label"><?php esc_html_e( 'Help', 'bp-job-manager' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/" class="bpjm-snav-link" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-book" aria-hidden="true"></span>
					<?php esc_html_e( 'Documentation', 'bp-job-manager' ); ?>
					<span class="dashicons dashicons-external bpjm-snav-link__ext" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'bp-job-manager' ); ?></span>
				</a>
			</nav>
		</aside>

		<div class="bpjm-settings-main">
			<?php
			/*
			 * Render settings notices inside the content column so the
			 * banner aligns with the panel chrome instead of spanning the
			 * full admin width. See playbook Part 10.2.
			 */
			settings_errors();
			?>
			<?php if ( $in_settings_group ) : ?>
				<form method="post" action="options.php" id="bpjm-settings-form">
					<?php settings_fields( $settings_form_group ); ?>
					<?php
					if ( file_exists( $view_path ) ) {
						include $view_path;
					}
					?>
					<div class="bpjm-save-bar">
						<?php submit_button( __( 'Save Settings', 'bp-job-manager' ), 'primary bpjm-btn bpjm-btn-primary', 'submit', false ); ?>
					</div>
				</form>
			<?php else : ?>
				<?php
				if ( file_exists( $view_path ) ) {
					include $view_path;
				}
				?>
			<?php endif; ?>
		</div>

	</div>
</div>

<?php
/**
 * WB Plugins hub — landing dashboard at ?page=wbcomplugins.
 *
 * Lists every Wbcom plugin that has registered a submenu under the
 * shared wbcomplugins parent. Peer plugins appear automatically once
 * their admin page is in the WordPress menu table.
 *
 * Legacy wbcom-wrapper plugins register four boilerplate helper pages
 * (Our Plugins, Our Themes, Support, License) under this hub. Those are
 * not real plugins and must not appear as cards — we filter them out
 * via wbcom_hub_wrapper_helper_slugs. See playbook Part 15.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;

$submenu_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] )
	? $GLOBALS['submenu']['wbcomplugins']
	: array();

// Slugs of the legacy wbcom-wrapper boilerplate helper pages. Every
// un-migrated Wbcom plugin still registers these shared pages under the
// hub. They are not plugins, so they must not appear as cards.
// Filterable so Pro modules or future wrapper slugs can be added.
$wrapper_helper_slugs = apply_filters(
	'wbcom_hub_wrapper_helper_slugs',
	array(
		'wbcom-plugins-page',
		'wbcom-themes-page',
		'wbcom-support-page',
		'wbcom-license-page',
	)
);

$bpjm_plugins = array();
foreach ( $submenu_entries as $entry ) {
	$slug = isset( $entry[2] ) ? (string) $entry[2] : '';
	if ( '' === $slug || 'wbcomplugins' === $slug ) {
		continue;
	}
	if ( in_array( $slug, $wrapper_helper_slugs, true ) ) {
		continue;
	}
	$bpjm_plugins[] = array(
		'slug'       => $slug,
		'menu_title' => isset( $entry[0] ) ? wp_strip_all_tags( (string) $entry[0] ) : $slug,
		'page_title' => isset( $entry[3] ) ? wp_strip_all_tags( (string) $entry[3] ) : '',
		'url'        => admin_url( 'admin.php?page=' . rawurlencode( $slug ) ),
	);
}

$plugin_count = count( $bpjm_plugins );
?>
<div class="wrap bpjm-admin">
	<header class="bpjm-page-header">
		<div class="bpjm-page-header__title">
			<span class="dashicons dashicons-lightbulb" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'WB Plugins', 'bp-job-manager' ); ?></h1>
				<p class="bpjm-page-header__subtitle">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: active Wbcom plugin count */
							_n(
								'%d Wbcom plugin active on this site.',
								'%d Wbcom plugins active on this site.',
								$plugin_count,
								'bp-job-manager'
							),
							$plugin_count
						)
					);
					?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 0 === $plugin_count ) : ?>
		<div class="bpjm-empty-state">
			<span class="bpjm-empty-state__icon" aria-hidden="true">
				<span class="dashicons dashicons-lightbulb"></span>
			</span>
			<p class="bpjm-empty-state__title"><?php esc_html_e( 'No Wbcom plugins attached to this hub yet', 'bp-job-manager' ); ?></p>
			<p class="bpjm-empty-state__desc">
				<?php esc_html_e( 'Activate one or more Wbcom plugins and they will appear here automatically.', 'bp-job-manager' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="bpjm-hub-grid">
			<?php foreach ( $bpjm_plugins as $bpjm_plugin ) : ?>
				<a href="<?php echo esc_url( $bpjm_plugin['url'] ); ?>" class="bpjm-hub-card">
					<span class="bpjm-hub-card__icon" aria-hidden="true">
						<span class="dashicons dashicons-admin-plugins"></span>
					</span>
					<span class="bpjm-hub-card__title"><?php echo esc_html( $bpjm_plugin['menu_title'] ); ?></span>
					<?php if ( ! empty( $bpjm_plugin['page_title'] ) && $bpjm_plugin['page_title'] !== $bpjm_plugin['menu_title'] ) : ?>
						<span class="bpjm-hub-card__subtitle"><?php echo esc_html( $bpjm_plugin['page_title'] ); ?></span>
					<?php endif; ?>
					<span class="bpjm-hub-card__cta">
						<?php esc_html_e( 'Open settings', 'bp-job-manager' ); ?>
						<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="bpjm-card bpjm-card--spaced">
		<div class="bpjm-card__head">
			<p class="bpjm-card__title"><?php esc_html_e( 'About WB Plugins', 'bp-job-manager' ); ?></p>
		</div>
		<div class="bpjm-card__body">
			<p class="bpjm-card__body-lead">
				<?php esc_html_e( 'This hub is the single entry point for every Wbcom Designs plugin installed on your site. Each plugin lives on its own page under this menu and keeps its own settings and data.', 'bp-job-manager' ); ?>
			</p>
			<p>
				<a href="https://wbcomdesigns.com/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Visit wbcomdesigns.com for more plugins and themes →', 'bp-job-manager' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>

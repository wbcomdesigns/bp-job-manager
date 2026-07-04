<?php
/**
 * Discover partial: ecosystem cross-promotion (read-only display view).
 *
 * Rendered inside shell.php as a non-settings tab. This is a pure
 * presentation view — product cards that link out to other free Wbcom
 * Designs tools. No forms, no settings, no options, no AJAX.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;

$bpjm_ecosystem_img = BPJM_PLUGIN_URL . 'assets/images/ecosystem/';

/*
 * Each product: brand mark (shipped in assets/images/ecosystem/), name,
 * a short admin-specific blurb (worded differently from readme.txt), and
 * the wbcomdesigns.com download URL.
 */
$bpjm_ecosystem = array(
	array(
		'name' => __( 'BuddyNext', 'bp-job-manager' ),
		'logo' => 'buddynext.svg',
		'desc' => __( 'The social network layer for WordPress, with feeds, member spaces, and messaging.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
	),
	array(
		'name' => __( 'Jetonomy', 'bp-job-manager' ),
		'logo' => 'jetonomy.svg',
		'desc' => __( 'Self-moderating forums and Q&A boards that scale to huge topic counts.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
	),
	array(
		'name' => __( 'Mediaverse', 'bp-job-manager' ),
		'logo' => 'mediaverse.svg',
		'desc' => __( 'A photo and video community with albums, reactions, following, and DMs.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
	),
	array(
		'name' => __( 'Listora', 'bp-job-manager' ),
		'logo' => 'listora.svg',
		'desc' => __( 'Build directories your members can search, review, map, and submit to.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/listora/',
	),
	array(
		'name' => __( 'Learnomy', 'bp-job-manager' ),
		'logo' => 'learnomy.svg',
		'desc' => __( 'Sell online courses with quizzes, payments, and completion certificates.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
	),
);
?>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'More Free Tools from Wbcom Designs', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'Job Manager brings hiring into member profiles. These free Wbcom Designs plugins build out the rest of the community around those careers — the network itself, forums, media, directories, and courses.', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-card__body">
		<div class="bpjm-discover-grid">
			<?php foreach ( $bpjm_ecosystem as $bpjm_product ) : ?>
				<div class="bpjm-discover-card">
					<span class="bpjm-discover-card__logo" aria-hidden="true">
						<img src="<?php echo esc_url( $bpjm_ecosystem_img . $bpjm_product['logo'] ); ?>" alt="<?php echo esc_attr( $bpjm_product['name'] ); ?>" width="52" height="52" loading="lazy" />
					</span>
					<h3 class="bpjm-discover-card__title"><?php echo esc_html( $bpjm_product['name'] ); ?></h3>
					<p class="bpjm-discover-card__desc"><?php echo esc_html( $bpjm_product['desc'] ); ?></p>
					<a class="bpjm-btn bpjm-btn-secondary bpjm-discover-card__cta" href="<?php echo esc_url( $bpjm_product['url'] ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Get it free', 'bp-job-manager' ); ?>
						<span class="dashicons dashicons-external" aria-hidden="true"></span>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: product name. */
							echo esc_html( sprintf( __( '%s (opens in a new tab)', 'bp-job-manager' ), $bpjm_product['name'] ) );
							?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

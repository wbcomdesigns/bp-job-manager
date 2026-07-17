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
		'name' => 'BuddyX',
		'logo' => 'buddyx.png',
		'icon' => 'admin-appearance',
		'desc' => __( 'A free, fast community theme for BuddyPress, BuddyBoss and PeepSo with a modern layout and dark mode.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddyx-theme/',
	),
	array(
		'name' => 'BuddyNext',
		'logo' => 'buddynext.svg',
		'desc' => __( 'The social network layer for WordPress, with feeds, member spaces, and messaging.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
	),
	array(
		'name' => 'Jetonomy',
		'logo' => 'jetonomy.svg',
		'desc' => __( 'Self-moderating forums and Q&A boards that scale to huge topic counts.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
	),
	array(
		'name' => 'Mediaverse',
		'logo' => 'mediaverse.svg',
		'desc' => __( 'A photo and video community with albums, reactions, following, and DMs.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
	),
	array(
		'name' => 'Eventonomy',
		'logo' => 'eventonomy.svg',
		'icon' => 'calendar-alt',
		'desc' => __( 'Run community events with RSVPs, calendars, and front-end submissions.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/eventonomy/',
	),
	array(
		'name' => 'WB Gamification',
		'logo' => 'wb-gamification.svg',
		'icon' => 'awards',
		'desc' => __( 'Reward members with points, badges, and leaderboards to keep engagement high.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/',
	),
	array(
		'name' => 'Listora',
		'logo' => 'listora.svg',
		'desc' => __( 'Build directories your members can search, review, map, and submit to.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/listora/',
	),
	array(
		'name' => 'WP Career Board',
		'logo' => 'wp-career-board.svg',
		'icon' => 'businessman',
		'desc' => __( 'Add a job board with front-end listings, applications, and employer profiles.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wp-career-board/',
	),
	array(
		'name' => 'Learnomy',
		'logo' => 'learnomy.svg',
		'desc' => __( 'Sell online courses with quizzes, payments, and completion certificates.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
	),
	array(
		'name' => 'WP Sell Services',
		'logo' => 'wp-sell-services.svg',
		'icon' => 'cart',
		'desc' => __( 'A free service marketplace with vendor dashboards, an 11-status order flow, and Stripe or PayPal built in.', 'bp-job-manager' ),
		'url'  => 'https://wbcomdesigns.com/downloads/wp-sell-services/',
	),
);
?>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'More Free Tools from Wbcom Designs', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'Job Manager brings hiring into member profiles. These free Wbcom Designs plugins build out the rest of the community around those careers: the theme and network itself, forums, media, events, gamification, directories, jobs, courses, and services.', 'bp-job-manager' ); ?></p>
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

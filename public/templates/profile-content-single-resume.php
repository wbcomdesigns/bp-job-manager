<?php
/**
 * Template for displaying a single resume on a BuddyPress profile.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * What a member's profile shows when they have expressed no preference.
 *
 * Everything, which is what "Show the latest resume on the member's profile"
 * has always claimed to do. Filterable so a site can narrow it globally without
 * every member having to touch their own settings.
 *
 * @since 3.0.0
 *
 * @return array<string,string>
 */
function bpjm_resume_profile_default_fields() {
	return apply_filters(
		'bpjm_resume_profile_default_fields',
		array(
			'display_resume' => 'yes',
			'email'          => 'yes',
			'prof_title'     => 'yes',
			'location'       => 'yes',
			'video'          => 'yes',
			'description'    => 'yes',
			'url'            => 'yes',
			'education'      => 'yes',
			'experience'     => 'yes',
		)
	);
}

/**
 * Display single resume.
 *
 * @param  int $post_id Post ID.
 * @return void
 */
function bpjm_show_resume_at_profile( $post_id ) {
	global $post;

	$post = get_post( $post_id, OBJECT ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required for setup_postdata() in template.

	if ( empty( $post ) ) {
		return;
	}

	/*
	 * Resume Manager owns whether a resume is visible - that is what publishing
	 * and hiding a resume mean. Anything not published never reaches a profile.
	 */
	if ( 'publish' !== $post->post_status ) {
		return;
	}

	setup_postdata( $post );

	/*
	 * Absence of a preference means SHOW.
	 *
	 * This function used to return early unless the member had a
	 * `bpjm_display_fields` meta with `display_resume => yes`. That meta was
	 * written in exactly one place: an activation hook that looped every user on
	 * the site and stamped it on each. So the feature worked for members who
	 * existed when the plugin was activated and silently did nothing for
	 * everyone who registered afterwards - on a growing community, most members,
	 * while the admin Overview reported the feature as on.
	 *
	 * The activation loop is gone - the activator class was removed - so there is
	 * no seeded meta to depend on. A member who has explicitly chosen to hide
	 * fields still has that meta and it is still honoured; everyone else - which
	 * is now everyone new - gets the resume shown, which is what the setting
	 * always promised.
	 */
	$fields_display = get_user_meta( bp_displayed_user_id(), 'bpjm_display_fields', true );

	if ( ! is_array( $fields_display ) ) {
		$fields_display = bpjm_resume_profile_default_fields();
	}

	if ( isset( $fields_display['display_resume'] ) && 'yes' !== $fields_display['display_resume'] ) {
		return;
	}

	if ( isset( $fields_display['email'] ) || isset( $fields_display['prof_title'] ) || isset( $fields_display['location'] ) || isset( $fields_display['video'] ) || isset( $fields_display['description'] ) ) {
		?>
		<div class="bp-widget">
		<?php if ( ! empty( $fields_display['email'] ) || ! empty( $fields_display['prof_title'] ) || ! empty( $fields_display['location'] ) || ! empty( $fields_display['video'] ) || ! empty( $fields_display['description'] ) ) { ?>
			<h2>
			<?php
			// Translate first, then filter the translated string, and escape once
			// at output. The hook name's "peronal" misspelling is a long-standing
			// public contract - do not correct it.
			echo esc_html( apply_filters( 'bpjm_profile_peronal_inf_txt', __( 'Personal Information', 'bp-job-manager' ) ) );
			?>
			</h2>
			<?php } ?>
			<table class="profile-fields">
				<?php

				if ( ! empty( $fields_display['email'] ) ) {

					$email = get_post_meta( $post_id, '_candidate_email', true );
					?>
				<tr>
					<td class='label'> <?php esc_html_e( 'E-mail', 'bp-job-manager' ); ?></td>
					<td class='data'> <?php echo esc_html( sanitize_email( $email ) ); ?> </td>
				</tr>
				<?php } if ( ! empty( $fields_display['prof_title'] ) ) { ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Professional Title', 'bp-job-manager' ); ?></td>
					<td class="data"><?php echo esc_html( the_candidate_title() ); ?></td>
				</tr>
				<?php } if ( ! empty( $fields_display['location'] ) ) { ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Location', 'bp-job-manager' ); ?></td>
					<td class="data"><?php echo esc_html( the_candidate_location() ); ?></td>
				</tr>
				<?php } if ( ! empty( $fields_display['video'] ) ) { ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Video', 'bp-job-manager' ); ?></td>
					<td class="data"><?php echo esc_html( the_candidate_video() ); ?></td>
				</tr>
				<?php } if ( ! empty( $fields_display['description'] ) ) { ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Description', 'bp-job-manager' ); ?></td>
					<td class="data"><?php echo wp_kses_post( apply_filters( 'the_resume_description', get_the_content() ) ); ?></td>
				</tr>
					<?php
				}

				?>
			</table>
		</div>
				<?php
	}
	if ( ! empty( $fields_display['url'] ) ) {
		?>
		<div class="bp-widget">
			<h2>
			<?php
			// Translate first, then filter the translated string, escape once at output.
			echo esc_html( apply_filters( 'bpjm_profile_urls_txt', __( 'URL(s)', 'bp-job-manager' ) ) );
			?>
			</h2>
			<table class="profile-fields">
				<tr>
					<td class="label"><?php esc_html_e( 'URL(s)', 'bp-job-manager' ); ?></td>
					<td class="data"><?php the_resume_links(); ?></td>
				</tr>
			</table>
		</div>
			<?php
	}
	if ( ! empty( $fields_display['education'] ) ) {
		$items = get_post_meta( $post_id, '_candidate_education', true );
		if ( $items ) :
			?>
			<div class="bp-widget">
				<h2>
				<?php
				// Translate first, then filter the translated string, escape once at output.
				echo esc_html( apply_filters( 'bpjm_profile_education_txt', __( 'Education', 'bp-job-manager' ) ) );
				?>
				</h2>
				<table class="profile-fields">
				<?php
				$c = 0;
				foreach ( $items as $item ) :
					if ( 0 === $c % 2 ) {
						$setclass = 'bpjm-set-even';
					} else {
						$setclass = 'bpjm-set-odd';
					}
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'School Name', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['location'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Qualification', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['qualification'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Date', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['date'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Notes', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( wptexturize( $item['notes'] ) ) . '</td>
					 </tr>';
					++$c;
					endforeach;
				?>
				</table>
			</div>
				<?php
			endif;
	}
	if ( ! empty( $fields_display['experience'] ) ) {
		$items = get_post_meta( $post_id, '_candidate_experience', true );
		if ( $items ) :
			?>
			<div class="bp-widget">
				<h2>
				<?php
				// Translate the string first (unescaped - esc_html() below is the
				// single escape point; escaping here too would double-encode any
				// entity a translation introduces, e.g. an apostrophe).
				$translated_experience_text = __( 'Experience', 'bp-job-manager' );

				// Apply the filter to the translated string.
				$filtered_experience_text = apply_filters( 'bpjm_profile_experience_txt', $translated_experience_text );

				// Escape and echo the filtered text.
				echo esc_html( $filtered_experience_text );
				?>
				</h2>
				<table class="profile-fields">
				<?php
				$c = 0;
				foreach ( $items as $item ) :
					if ( 0 === $c % 2 ) {
						$setclass = 'bpjm-set-even';
					} else {
						$setclass = 'bpjm-set-odd';
					}
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Employer', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['employer'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Job Title', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['job_title'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Date', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['date'] ) . '</td>
					 </tr>';
					echo '<tr class="' . esc_html( $setclass ) . '">
						<td class="label">' . esc_html__( 'Notes', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( wptexturize( $item['notes'] ) ) . '</td>
					 </tr>';
					++$c;
					endforeach;
				?>
				</table>
			</div>
				<?php
			endif;
	}
	?>
	<?php
	wp_reset_postdata();
}

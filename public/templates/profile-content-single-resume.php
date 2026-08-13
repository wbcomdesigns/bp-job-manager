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

	/*
	 * Rows are built first, then rendered only if any survived.
	 *
	 * Every row used to print unconditionally whenever its field was enabled, so
	 * a resume with no video showed a "Video" label with nothing beside it, and a
	 * resume with no links still got a whole "URL(s)" section containing one
	 * empty row. A label with no value is not information - it is a gap the
	 * member cannot fill from this screen and cannot remove.
	 *
	 * Note the getters: the_candidate_title() and friends ECHO and return null,
	 * so the old `echo esc_html( the_candidate_title() )` escaped nothing and
	 * printed via the function's own output. get_the_*() returns the value, which
	 * is what lets us test it for emptiness and escape it once here.
	 */
	$bpjm_rows = array();

	if ( ! empty( $fields_display['email'] ) ) {
		$bpjm_email = sanitize_email( (string) get_post_meta( $post_id, '_candidate_email', true ) );
		if ( '' !== $bpjm_email ) {
			$bpjm_rows[] = array( __( 'E-mail', 'bp-job-manager' ), esc_html( $bpjm_email ) );
		}
	}

	if ( ! empty( $fields_display['prof_title'] ) && function_exists( 'get_the_candidate_title' ) ) {
		$bpjm_title = trim( (string) get_the_candidate_title() );
		if ( '' !== $bpjm_title ) {
			$bpjm_rows[] = array( __( 'Professional Title', 'bp-job-manager' ), esc_html( $bpjm_title ) );
		}
	}

	if ( ! empty( $fields_display['location'] ) && function_exists( 'get_the_candidate_location' ) ) {
		$bpjm_location = trim( wp_strip_all_tags( (string) get_the_candidate_location() ) );
		if ( '' !== $bpjm_location ) {
			$bpjm_rows[] = array( __( 'Location', 'bp-job-manager' ), esc_html( $bpjm_location ) );
		}
	}

	if ( ! empty( $fields_display['video'] ) && function_exists( 'get_the_candidate_video' ) ) {
		$bpjm_video = trim( (string) get_the_candidate_video() );
		if ( '' !== $bpjm_video ) {
			$bpjm_rows[] = array( __( 'Video', 'bp-job-manager' ), esc_html( $bpjm_video ) );
		}
	}

	if ( ! empty( $fields_display['description'] ) ) {
		$bpjm_description = trim( (string) apply_filters( 'the_resume_description', get_the_content() ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from Resume Manager.
		if ( '' !== wp_strip_all_tags( $bpjm_description ) ) {
			$bpjm_rows[] = array( __( 'Description', 'bp-job-manager' ), wp_kses_post( $bpjm_description ) );
		}
	}

	if ( ! empty( $bpjm_rows ) ) {
		?>
		<div class="bp-widget bpjm-resume-widget">
			<h3 class="screen-heading profile-group-title">
			<?php
			/*
			 * "Resume", not "Personal Information" - this is the member's resume
			 * shown on their profile, and calling it something else made it read
			 * as a second set of profile fields. The hook name's "peronal"
			 * misspelling is a long-standing public contract - do not correct it.
			 */
			echo esc_html( apply_filters( 'bpjm_profile_peronal_inf_txt', __( 'Resume', 'bp-job-manager' ) ) );
			?>
			</h3>
			<table class="profile-fields">
				<?php foreach ( $bpjm_rows as $bpjm_row ) : ?>
					<tr>
						<td class="label"><?php echo esc_html( $bpjm_row[0] ); ?></td>
						<td class="data"><?php echo wp_kses_post( $bpjm_row[1] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php
	}

	if ( ! empty( $fields_display['url'] ) && function_exists( 'the_resume_links' ) ) {
		/*
		 * Resume Manager only offers an echoing the_resume_links(), so buffer it
		 * to find out whether there are any links before committing to a heading.
		 * This section used to render unconditionally: a "URL(s)" heading over a
		 * "URL(s)" row with nothing in it, on every resume without links.
		 */
		ob_start();
		the_resume_links();
		$bpjm_links = trim( (string) ob_get_clean() );

		if ( '' !== wp_strip_all_tags( $bpjm_links ) ) {
			?>
			<div class="bp-widget bpjm-resume-widget">
				<h3 class="screen-heading profile-group-title">
				<?php
				// Translate first, then filter the translated string, escape once at output.
				echo esc_html( apply_filters( 'bpjm_profile_urls_txt', __( 'Links', 'bp-job-manager' ) ) );
				?>
				</h3>
				<table class="profile-fields">
					<tr>
						<td class="data" colspan="2"><?php echo wp_kses_post( $bpjm_links ); ?></td>
					</tr>
				</table>
			</div>
			<?php
		}
	}
	if ( ! empty( $fields_display['education'] ) ) {
		$items = get_post_meta( $post_id, '_candidate_education', true );
		if ( $items ) :
			?>
			<div class="bp-widget">
				<h3 class="screen-heading profile-group-title">
				<?php
				// Translate first, then filter the translated string, escape once at output.
				echo esc_html( apply_filters( 'bpjm_profile_education_txt', __( 'Education', 'bp-job-manager' ) ) );
				?>
				</h3>
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
				<h3 class="screen-heading profile-group-title">
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
				</h3>
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

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bpjm_show_resume_at_profile( $post_id ) {

	global $post;
	$fields_display = get_option( 'bpjm_display_fields' );

	$display_resume = ( isset( $fields_display['display_resume'] ) ) ? $fields_display['display_resume'] : 'no';

	if ( $display_resume != 'yes' ) {
		return;
	}

	$post = get_post( $post_id, OBJECT );
	setup_postdata( $post );
	if ( isset( $fields_display['email'] ) || isset( $fields_display['prof_title'] ) || isset( $fields_display['location'] ) || isset( $fields_display['video'] ) || isset( $fields_display['description'] ) ) {
		?>
		<div class="bp-widget">
			<h2><?php echo apply_filters( 'bpjm_profile_peronal_inf_txt', 'Personal Information' ); ?></h2>
			<table class="profile-fields">
				<?php
				if ( isset( $fields_display['email'] ) ) {
					$email = get_post_meta( $post_id, '_candidate_email', true );
					echo "<tr>
								<td class='label'>" . __( 'E-mail', 'bp-job-manager' ) . "</td>
								<td class='data'>" . $email . '</td>
							  </tr>';
				}
				if ( isset( $fields_display['prof_title'] ) ) {
					?>
						<tr>
							<td class="label"><?php _e( 'Professional Title', 'bp-job-manager' ); ?></td>
							<td class="data"><?php the_candidate_title(); ?></td>
						</tr>
						<?php
				}
				if ( isset( $fields_display['location'] ) ) {
					?>
						<tr>
							<td class="label"><?php _e( 'Location', 'bp-job-manager' ); ?></td>
							<td class="data"><?php the_candidate_location(); ?></td>
						</tr>
						<?php
				}
				if ( isset( $fields_display['video'] ) ) {
					?>
						<tr>
							<td class="label"><?php _e( 'Video', 'bp-job-manager' ); ?></td>
							<td class="data"><?php the_candidate_video(); ?></td>
						</tr>
						<?php
				}
				if ( isset( $fields_display['description'] ) ) {
					?>
						<tr>
							<td class="label"><?php _e( 'Description', 'bp-job-manager' ); ?></td>
							<td class="data"><?php echo apply_filters( 'the_resume_description', get_the_content() ); ?></td>
						</tr>
						<?php
				}
				?>
			</table>
		</div>
		<?php
	}
	if ( isset( $fields_display['url'] ) ) {
		?>
		<div class="bp-widget">
			<h2><?php echo apply_filters( 'bpjm_profile_urls_txt', 'URL(s)' ); ?></h2>
			<table class="profile-fields">
				<tr>
					<td class="label"><?php _e( 'URL(s)', 'bp-job-manager' ); ?></td>
					<td class="data"><?php the_resume_links(); ?></td>
				</tr>
			</table>
		</div>
		<?php
	}
	if ( isset( $fields_display['education'] ) ) {
		if ( $items = get_post_meta( $post_id, '_candidate_education', true ) ) :
			?>
			<div class="bp-widget">
				<h2><?php echo apply_filters( 'bpjm_profile_education_txt', 'Education' ); ?></h2>
				<table class="profile-fields">
			<?php
			$c = 0;
			foreach ( $items as $item ) :
				if ( $c % 2 == 0 ) {
					$setclass = 'bpjm-set-even';
				} else {
					$setclass = 'bpjm-set-odd';
				}
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'School Name', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['location'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Qualification', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['qualification'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Date', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['date'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Notes', 'bp-job-manager' ) . '</td>
						<td class="data">' . wpautop( wptexturize( $item['notes'] ) ) . '</td>
					 </tr>';
				$c++;
			endforeach;
			?>
				</table>
			</div>
			<?php
		endif;
	}
	if ( isset( $fields_display['experience'] ) ) {
		if ( $items = get_post_meta( $post_id, '_candidate_experience', true ) ) :
			?>
			<div class="bp-widget">
				<h2><?php echo apply_filters( 'bpjm_profile_experience_txt', 'Experience' ); ?></h2>
				<table class="profile-fields">
			<?php
			$c = 0;
			foreach ( $items as $item ) :
				if ( $c % 2 == 0 ) {
					$setclass = 'bpjm-set-even';
				} else {
					$setclass = 'bpjm-set-odd';
				}
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Employer', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['employer'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Job Title', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['job_title'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Date', 'bp-job-manager' ) . '</td>
						<td class="data">' . esc_html( $item['date'] ) . '</td>
					 </tr>';
				echo '<tr class="' . $setclass . '">
						<td class="label">' . __( 'Notes', 'bp-job-manager' ) . '</td>
						<td class="data">' . wpautop( wptexturize( $item['notes'] ) ) . '</td>
					 </tr>';
				$c++;
			endforeach;
			?>
				</table>
			</div>
			<?php
		endif;
	}
	?>
	<?php
	wp_reset_postdata(); }

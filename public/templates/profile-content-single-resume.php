<?php 
function bpjm_show_resume_at_profile($post_id) {
	global $post;
	$fields_display = get_option( 'bpjm_display_fields' );
	$post = get_post( $post_id, OBJECT );
	setup_postdata( $post );
	if(isset($fields_display['email']) || isset($fields_display['prof_title']) || isset($fields_display['location']) || isset($fields_display['video']) || isset($fields_display['description'])){
		?>
		<div class="bp-widget">	
			<h2><?php echo apply_filters( 'bpjm_profile_peronal_inf_txt', 'Personal Information' ); ?></h2>
			<table class="profile-fields">
				<?php 
					if( isset( $fields_display['email'] ) ){
						$email   = get_post_meta( $post_id, '_candidate_email', true );
						echo "<tr>
								<td class='label'>". __('E-mail', 'buddypress') ."</td>
								<td class='data'>".$email."</td>
							  </tr>";
					}
					if( isset( $fields_display['prof_title'] ) ){
						?>
						<tr>
							<td class="label"><?php _e('Professional Title', 'buddypress') ?></td>
							<td class="data"><?php the_candidate_title(); ?></td>
						</tr>
						<?php
					}
					if( isset( $fields_display['location'] ) ){
						?>
						<tr>
							<td class="label"><?php _e('Location', 'buddypress') ?></td>
							<td class="data"><?php the_candidate_location(); ?></td>
						</tr>
						<?php
					}
					if( isset( $fields_display['video'] ) ){
						?>
						<tr>
							<td class="label"><?php _e('Video', 'buddypress') ?></td>
							<td class="data"><?php the_candidate_video(); ?></td>
						</tr>
						<?php
					}
					if( isset( $fields_display['description'] ) ){
						?>
						<tr>
							<td class="label"><?php _e('Description', 'buddypress') ?></td>
							<td class="data"><?php echo apply_filters( 'the_resume_description', get_the_content() ); ?></td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
		<?php
	}
	if(isset($fields_display['url'])){
		?>
		<div class="bp-widget">	
			<h2><?php echo apply_filters( 'bpjm_profile_urls_txt', 'URL(s)' ); ?></h2>
			<table class="profile-fields">
				<tr>
					<td class="label"><?php _e('URL(s)', 'buddypress') ?></td>
					<td class="data"><?php the_resume_links(); ?></td>
				</tr>
			</table>
		</div>
		<?php
	}
	if(isset($fields_display['education'])){
		?>
		<div class="bp-widget">	
			<h2><?php echo apply_filters( 'bpjm_profile_education_txt', 'Education' ); ?></h2>
			<table class="profile-fields">
				<tr>
					<td class="label"><?php _e('URL(s)', 'buddypress') ?></td>
					<td class="data"><?php the_resume_links(); ?></td>
				</tr>
			</table>
		</div>
		<?php
	}
	?>
		
</br>
</br>
</br>
</br>
</br>
</br>
	<div class="resume_preview single-resume">
		<h1><?php the_title(); ?></h1>
		<?php if ( resume_manager_user_can_view_resume( $post_id ) ) : ?>
			<div class="single-resume-content">
				<?php do_action( 'single_resume_start' ); ?>

				<div class="resume-aside">
					<?php the_candidate_photo(); ?>
					<?php the_resume_links(); ?>
					<p class="job-title"><?php the_candidate_title(); ?></p>
					<p class="location"><?php the_candidate_location(); ?></p>

					<?php the_candidate_video(); ?>
				</div>

				<div class="resume_description">
					<?php echo apply_filters( 'the_resume_description', get_the_content() ); ?>
				</div>

				<?php if ( ( $skills = wp_get_object_terms( $post_id, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
					<h2><?php _e( 'Skills', 'wp-job-manager-resumes' ); ?></h2>
					<ul class="resume-manager-skills">
						<?php echo '<li>' . implode( '</li><li>', $skills ) . '</li>'; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $items = get_post_meta( $post_id, '_candidate_education', true ) ) : ?>
					<h2><?php _e( 'Education', 'wp-job-manager-resumes' ); ?></h2>
					<dl class="resume-manager-education">
						<?php
						foreach( $items as $item ) : ?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<h3><?php printf( __( '%s at %s', 'wp-job-manager-resumes' ), '<strong class="qualification">' . esc_html( $item['qualification'] ) . '</strong>', '<strong class="location">' . esc_html( $item['location'] ) . '</strong>' ); ?></h3>
						</dt>
						<dd>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
					?>
				</dl>
			<?php endif; ?>

			<?php if ( $items = get_post_meta( $post_id, '_candidate_experience', true ) ) : ?>
				<h2><?php _e( 'Experience', 'wp-job-manager-resumes' ); ?></h2>
				<dl class="resume-manager-experience">
					<?php
					foreach( $items as $item ) : ?>

					<dt>
						<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
						<h3><?php printf( __( '%s at %s', 'wp-job-manager-resumes' ), '<strong class="job_title">' . esc_html( $item['job_title'] ) . '</strong>', '<strong class="employer">' . esc_html( $item['employer'] ) . '</strong>' ); ?></h3>
					</dt>
					<dd>
						<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
					</dd>

				<?php endforeach;
				?>
			</dl>
		<?php endif; ?>

		<ul class="meta">
			<?php do_action( 'single_resume_meta_start' ); ?>

			<?php if ( get_the_resume_category() ) : ?>
				<li class="resume-category"><?php the_resume_category(); ?></li>
			<?php endif; ?>

			<li class="date-posted" itemprop="datePosted"><date><?php printf( __( 'Updated %s ago', 'wp-job-manager-resumes' ), human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) ) ); ?></date></li>

			<?php do_action( 'single_resume_meta_end' ); ?>
		</ul>

		<?php get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

		<?php do_action( 'single_resume_end' ); ?>
	</div>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>
</div>
<?php wp_reset_postdata(); }
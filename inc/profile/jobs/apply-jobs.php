<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
$job_id = sanitize_text_field( $_GET['apply'] );
echo do_shortcode( '[job_apply id="'.$job_id.'"]' );

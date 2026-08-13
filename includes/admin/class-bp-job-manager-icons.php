<?php
/**
 * Inline Lucide icons for the admin UI.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bpjm_icon' ) ) :

	/**
	 * Return an inline Lucide icon.
	 *
	 * Inline SVG rather than an icon font. Dashicons was doing this job, and it
	 * is already loaded in wp-admin so it cost nothing in weight - but the design
	 * system is Lucide, and an icon font cannot be given a per-icon stroke or be
	 * recoloured per state without fighting the font. These are the same 24x24
	 * stroke geometry the rest of the portfolio uses.
	 *
	 * Paths only. The wrapper carries the size, colour and aria state, so a caller
	 * never has to know SVG.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name  Icon name.
	 * @param array  $args  Optional. 'class' extra classes, 'size' px, 'label' accessible name.
	 * @return string Escaped SVG markup, or an empty string for an unknown icon.
	 */
	function bpjm_icon( $name, $args = array() ) {
		$paths = array(
			'bar-chart'   => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
			'settings'    => '<path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/>',
			'life-buoy'   => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m4.93 19.07 4.24-4.24"/>',
			'package'     => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
			'lightbulb'   => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
			'plug'        => '<path d="M12 22v-5"/><path d="M9 8V2"/><path d="M15 8V2"/><path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/>',
			'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
			'alert'       => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
			'book'        => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
			'external'    => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6"/>',
			'info'        => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
			'briefcase'   => '<path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/>',
		);

		if ( ! isset( $paths[ $name ] ) ) {
			return '';
		}

		$args = wp_parse_args(
			$args,
			array(
				'class' => '',
				'size'  => 20,
				'label' => '',
			)
		);

		$classes = trim( 'bpjm-icon ' . $args['class'] );
		$size    = (int) $args['size'];

		$a11y = '' !== $args['label']
			? ' role="img" aria-label="' . esc_attr( $args['label'] ) . '"'
			: ' aria-hidden="true" focusable="false"';

		return sprintf(
			'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%3$s>%4$s</svg>',
			esc_attr( $classes ),
			$size,
			$a11y, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_attr() above.
			$paths[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static path data defined in this file.
		);
	}

endif;

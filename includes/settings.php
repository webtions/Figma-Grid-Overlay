<?php
/**
 * Register plugin settings for Figma Grid Overlay.
 *
 * @package Figma_Grid_Overlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register plugin settings and ensure defaults are merged when retrieving them.
 */
function fgo_register_settings() {
	// This plugin uses a custom sanitizer (fgo_sanitize_grid_settings) that strictly validates all settings.
	register_setting(
		'fgo_settings_group',
		'fgo_settings',
		[
			'type'              => 'array',
			'sanitize_callback' => 'fgo_sanitize_grid_settings',
			'show_in_rest'      => false,
		]
	);

	add_filter( 'option_fgo_settings', 'fgo_merge_defaults' );
}
add_action( 'admin_init', 'fgo_register_settings' );

/**
 * Sanitize grid layout settings array submitted via the admin form.
 *
 * @param array $input Raw settings submitted by the admin user.
 * @return array Sanitized settings ready to be saved.
 */
function fgo_sanitize_grid_settings( $input ) {
	$output            = [];
	$output['enabled'] = isset( $input['enabled'] ) ? 1 : 0;

	$screens = [ 'mobile', 'tablet', 'desktop', 'extended' ];

	foreach ( $screens as $screen ) {
		$output[ $screen ] = [
			'enabled'      => isset( $input[ $screen ]['enabled'] ) ? 1 : 0,
			'columns'      => isset( $input[ $screen ]['columns'] ) ? intval( $input[ $screen ]['columns'] ) : 0,
			'gutter'       => isset( $input[ $screen ]['gutter'] ) ? intval( $input[ $screen ]['gutter'] ) : 0,
			'outer_margin' => isset( $input[ $screen ]['outer_margin'] ) ? intval( $input[ $screen ]['outer_margin'] ) : 0,
			'min_width'    => isset( $input[ $screen ]['min_width'] ) ? intval( $input[ $screen ]['min_width'] ) : 0,
		];
	}

	return $output;
}

/**
 * Set default plugin options upon activation.
 */
function fgo_set_default_options() {
	$defaults = [
		'enabled' => 1,
		'mobile'  => [
			'enabled'      => 1,
			'columns'      => 2,
			'gutter'       => 30,
			'outer_margin' => 30,
			'min_width'    => 0,
		],
		'tablet' => [
			'enabled'      => 1,
			'columns'      => 4,
			'gutter'       => 40,
			'outer_margin' => 40,
			'min_width'    => 768,
		],
		'desktop' => [
			'enabled'      => 1,
			'columns'      => 6,
			'gutter'       => 60,
			'outer_margin' => 60,
			'min_width'    => 1280,
		],
		'extended' => [
			'enabled'      => 1,
			'columns'      => 6,
			'gutter'       => 120,
			'outer_margin' => 160,
			'min_width'    => 1920,
		],
	];

	if ( ! get_option( 'fgo_settings' ) ) {
		add_option( 'fgo_settings', $defaults );
	}
}

/**
 * Merge stored options with fallback defaults to ensure all fields are populated.
 *
 * @param array $options Stored plugin options.
 * @return array Options merged with defaults.
 */
function fgo_merge_defaults( $options ) {
	$defaults = [
		'mobile'   => [ 'enabled' => 1, 'columns' => 2, 'gutter' => 30, 'outer_margin' => 30, 'min_width' => 0 ],
		'tablet'   => [ 'enabled' => 1, 'columns' => 4, 'gutter' => 40, 'outer_margin' => 40, 'min_width' => 768 ],
		'desktop'  => [ 'enabled' => 1, 'columns' => 6, 'gutter' => 60, 'outer_margin' => 60, 'min_width' => 1280 ],
		'extended' => [ 'enabled' => 1, 'columns' => 6, 'gutter' => 120, 'outer_margin' => 160, 'min_width' => 1920 ],
	];

	foreach ( $defaults as $screen => $screen_defaults ) {
		if ( ! isset( $options[ $screen ] ) ) {
			$options[ $screen ] = $screen_defaults;
		} else {
			foreach ( $screen_defaults as $key => $val ) {
				if ( ! isset( $options[ $screen ][ $key ] ) ) {
					$options[ $screen ][ $key ] = $val;
				}
			}
		}
	}

	if ( ! isset( $options['enabled'] ) ) {
		$options['enabled'] = 1;
	}

	return $options;
}
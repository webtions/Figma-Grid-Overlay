<?php
/**
 * Enqueue and render the frontend grid overlay (DOM-based transparent red version).
 *
 * @package Grid_Overlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the overlay assets if the plugin is enabled.
 */
function gridoverlay_enqueue_overlay_assets() {
	$settings = get_option( 'gridoverlay_settings' );

	if ( empty( $settings['enabled'] ) ) {
		return;
	}

	add_action( 'wp_footer', 'gridoverlay_output_grid_overlay_dom' );

	// Enqueue a small inline script to generate the DOM grid.
	wp_register_script( 'gridoverlay-dom-script', false, [], GRIDOVERLAY_PLUGIN_VERSION, true );
	wp_enqueue_script( 'gridoverlay-dom-script' );
	wp_add_inline_script( 'gridoverlay-dom-script', gridoverlay_generate_grid_dom_js( $settings ) );

	// Enqueue basic styles.
	wp_register_style( 'gridoverlay-dom-style', false, [], GRIDOVERLAY_PLUGIN_VERSION );
	wp_enqueue_style( 'gridoverlay-dom-style' );
	wp_add_inline_style( 'gridoverlay-dom-style', gridoverlay_generate_grid_dom_css() );
}
add_action( 'wp_enqueue_scripts', 'gridoverlay_enqueue_overlay_assets' );

/**
 * Output the overlay container div in the footer.
 */
function gridoverlay_output_grid_overlay_dom() {
	echo '<div class="gridoverlay-grid-overlay-wrapper" id="gridoverlay-grid-overlay"></div>';
}

/**
 * Generate the JavaScript to dynamically create the grid columns.
 *
 * @param array $settings Grid settings from the plugin options.
 * @return string JavaScript code.
 */
function gridoverlay_generate_grid_dom_js( $settings ) {
	$mobile    = $settings['mobile'] ?? [ 'columns' => 2, 'gutter' => 30, 'outer_margin' => 30, 'min_width' => 0 ];
	$tablet    = $settings['tablet'] ?? [ 'columns' => 4, 'gutter' => 40, 'outer_margin' => 40, 'min_width' => 768 ];
	$desktop   = $settings['desktop'] ?? [ 'columns' => 6, 'gutter' => 60, 'outer_margin' => 60, 'min_width' => 1280 ];
	$extended  = $settings['extended'] ?? [ 'columns' => 6, 'gutter' => 120, 'outer_margin' => 160, 'min_width' => 1920 ];

	return "
	(function() {
		const container = document.getElementById('gridoverlay-grid-overlay');

		function updateGridOverlay() {
			if (!container) return;
			container.innerHTML = '';

			let columns = {$mobile['columns']};
			let gutter = {$mobile['gutter']};
			let outerMargin = {$mobile['outer_margin']};

			if (window.innerWidth >= {$extended['min_width']}) {
				columns = {$extended['columns']};
				gutter = {$extended['gutter']};
				outerMargin = {$extended['outer_margin']};
			} else if (window.innerWidth >= {$desktop['min_width']}) {
				columns = {$desktop['columns']};
				gutter = {$desktop['gutter']};
				outerMargin = {$desktop['outer_margin']};
			} else if (window.innerWidth >= {$tablet['min_width']}) {
				columns = {$tablet['columns']};
				gutter = {$tablet['gutter']};
				outerMargin = {$tablet['outer_margin']};
			}

			document.documentElement.style.setProperty('--gridoverlay-grid-columns', columns);
			document.documentElement.style.setProperty('--gridoverlay-grid-gutter', gutter + 'px');
			document.documentElement.style.setProperty('--gridoverlay-grid-outer-margin', outerMargin + 'px');
			document.documentElement.style.setProperty('--gridoverlay-grid-corrected-width', 'calc(100vw - (100vw - 100%))');
			document.documentElement.style.setProperty('--gridoverlay-grid-column-width',
				`calc((var(--gridoverlay-grid-corrected-width) - (var(--gridoverlay-grid-outer-margin) * 2) - (var(--gridoverlay-grid-gutter) * (\${columns} - 1))) / \${columns})`
			);

			for (let i = 0; i < columns; i++) {
				const div = document.createElement('div');
				div.className = 'gridoverlay-grid-column';
				container.appendChild(div);
			}
		}

		window.addEventListener('resize', updateGridOverlay);
		window.addEventListener('DOMContentLoaded', updateGridOverlay);
	})();
	";
}

/**
 * Generate the basic CSS for the grid overlay.
 *
 * @return string CSS code.
 */
function gridoverlay_generate_grid_dom_css() {
	return "
	.gridoverlay-grid-overlay-wrapper {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		pointer-events: none;
		z-index: 9999;
		width: calc(100vw - (100vw - 100%));
		height: 100%;
		display: flex;
		box-sizing: border-box;
		justify-content: flex-start;
		align-items: stretch;
	}

	.gridoverlay-grid-column {
		flex: 0 0 auto;
		width: var(--gridoverlay-grid-column-width);
		height: 100%;
		background-color: rgba(255, 0, 0, 0.1);
		margin-right: var(--gridoverlay-grid-gutter);
		box-sizing: border-box;
	}

	.gridoverlay-grid-column:first-child {
		margin-left: var(--gridoverlay-grid-outer-margin);
	}

	.gridoverlay-grid-column:last-child {
		margin-right: var(--gridoverlay-grid-outer-margin);
	}
	";
}

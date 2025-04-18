<?php
/**
 * Plugin Name:       Grid Overlay Figma-Style
 * Plugin URI:        https://themeist.com/plugins/wordpress/grid-overlay/
 * Description:       Add a Figma-style visual grid overlay to the frontend to test alignment and spacing in real time. Configure values for different screen sizes from the admin.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Harish Chouhan
 * Author URI:        https://harishchouhan.com/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       grid-overlay
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin version and path constants.
define( 'GRIDOVERLAY_PLUGIN_VERSION', '1.0.0' );
define( 'GRIDOVERLAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GRIDOVERLAY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include core plugin files.
require_once GRIDOVERLAY_PLUGIN_DIR . 'includes/settings.php';
require_once GRIDOVERLAY_PLUGIN_DIR . 'includes/enqueue.php';
require_once GRIDOVERLAY_PLUGIN_DIR . 'admin/settings-page.php';

/**
 * Set default plugin options on activation.
 */
register_activation_hook( __FILE__, 'gridoverlay_set_default_options' );

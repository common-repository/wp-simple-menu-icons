<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Setup the assets.
 *
 * @package   wp-simple-menu-icons
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Load the font icon css on the frontend.
 *
 * @return void
 */
function wp_smi_frontend_assets() {

	wp_register_style( 'wp-smi-icon-font', WP_SMI_PLUGIN_URL . 'dist/css/all.min.css', array(), WP_SMI_VERSION );
	wp_register_style( 'wp-smi-frontend', WP_SMI_PLUGIN_URL . 'dist/css/frontend.css', array(), WP_SMI_VERSION );

	wp_enqueue_style( 'wp-smi-icon-font' );
	wp_enqueue_style( 'wp-smi-frontend' );

}
add_action( 'wp_enqueue_scripts', 'wp_smi_frontend_assets' );

<?php // phpcs:ignore WordPress.Files.FileName
/**
 * The class that loads the whole plugin after requirements have been met
 *
 * @package   wp-simple-menu-icons
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace Pressmodo\MenuIcons;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Boot the plugin.
 */
class Plugin {

	/**
	 * Instance of the plugin.
	 *
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Plugin templates.
	 *
	 * @var Templates
	 */
	private $templates;

	/**
	 * Setup the instance.
	 *
	 * @param string $file the plugin's file.
	 * @return Plugin
	 */
	public static function instance( $file = '' ) {

		// Return if already instantiated.
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton.
		self::setup_instance( $file );

		self::$instance->setup_constants();
		self::$instance->setup_files();

		Selector::get_instance();
		Parser::get_instance();

		// Return the instance.
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-simple-menu-icons' ), '0.1.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-simple-menu-icons' ), '0.1.0' );
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean True if instantiated. False if not.
	 */
	private static function is_instantiated() {
		// Return true if instance is correct class.
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Plugin ) ) {
			return true;
		}
		// Return false if not instantiated correctly.
		return false;
	}

	/**
	 * Helper method to setup the instance.
	 *
	 * @param string $file the file of the plugin.
	 * @return void
	 */
	private static function setup_instance( $file = '' ) {
		self::$instance       = new Plugin();
		self::$instance->file = $file;
	}

	/**
	 * Setup helper constants.
	 *
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'WP_SMI_VERSION' ) ) {
			define( 'WP_SMI_VERSION', '1.0.0' );
		}
		// Plugin Root File.
		if ( ! defined( 'WP_SMI_PLUGIN_FILE' ) ) {
			define( 'WP_SMI_PLUGIN_FILE', $this->file );
		}
		// Plugin Base Name.
		if ( ! defined( 'WP_SMI_PLUGIN_BASE' ) ) {
			define( 'WP_SMI_PLUGIN_BASE', plugin_basename( WP_SMI_PLUGIN_FILE ) );
		}
		// Plugin Folder Path.
		if ( ! defined( 'WP_SMI_PLUGIN_DIR' ) ) {
			define( 'WP_SMI_PLUGIN_DIR', plugin_dir_path( WP_SMI_PLUGIN_FILE ) );
		}
		// Plugin Folder URL.
		if ( ! defined( 'WP_SMI_PLUGIN_URL' ) ) {
			define( 'WP_SMI_PLUGIN_URL', plugin_dir_url( WP_SMI_PLUGIN_FILE ) );
		}

		define( 'WP_SMI_DB_KEY', '_menu_item_wpsmi' );

	}

	/**
	 * Load required files.
	 *
	 * @return void
	 */
	public function setup_files() {

		require_once WP_SMI_PLUGIN_DIR . 'includes/assets.php';
		require_once WP_SMI_PLUGIN_DIR . 'includes/filters.php';

	}

	/**
	 * Allow translations.
	 *
	 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( 'wp-simple-menu-icons', false, WP_SMI_PLUGIN_DIR . '/languages' );
	}
}

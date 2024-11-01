<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Parse the YAML definition of font awesome into json file.
 *
 * @package   wp-simple-menu-icons
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://directorystack.com
 */

namespace Pressmodo\MenuIcons;

use Symfony\Component\Yaml\Yaml;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Yaml font-awesome icons list parser.
 */
class Parser {

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self() ) : self::$instance;
	}

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {

		if ( ! defined( 'WP_DEBUG' ) || ! current_user_can( 'manage_options' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG !== true ) || ! defined( 'WP_SMI_DEBUG' ) ) {
			return;
		}

		add_action( 'admin_bar_menu', array( $this, 'add_parse_trigger_link' ), 100 );

		add_action( 'admin_init', array( $this, 'trigger_parser' ) );

	}

	/**
	 * Adds a link to trigger the fontawesome icons parser.
	 *
	 * @param object $admin_bar
	 * @return void
	 */
	public function add_parse_trigger_link( $admin_bar ) {
		$admin_bar->add_menu(
			array(
				'id'    => 'wpsmi-parse-fa',
				'title' => esc_html__( 'Parse FA Icons', 'wp-simple-menu-icons' ),
				'href'  => $this->get_parse_trigger_link(),
				'meta'  => array(
					'title' => esc_html__( 'Parse FA Icons', 'wp-simple-menu-icons' ),
				),
			)
		);
	}

	/**
	 * Get the link that triggers the parser.
	 *
	 * @return string
	 */
	private function get_parse_trigger_link() {
		return add_query_arg( array( 'wp_smi_parse' => true ), admin_url() );
	}

	/**
	 * Do parsing.
	 *
	 * @return void
	 */
	public function trigger_parser() {

		if ( ! defined( 'WP_DEBUG' ) || ! current_user_can( 'manage_options' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG !== true ) || ! defined( 'WP_SMI_DEBUG' ) ) {
			return;
		}

		if ( ! isset( $_GET['wp_smi_parse'] ) || ( isset( $_GET['wp_smi_parse'] ) && $_GET['wp_smi_parse'] !== '1' ) ) {
			return;
		}

		$yaml = Yaml::parse( file_get_contents( WP_SMI_PLUGIN_DIR . 'dist/metadata/icons.yml' ) );

		$data = array();

		if ( ! empty( $yaml ) && is_array( $yaml ) ) {
			foreach ( $yaml as $id => $icon ) {
				if ( ! isset( $icon['unicode'] ) ) {
					continue;
				}
				$data[] = array(
					'id'      => sanitize_text_field( $id ),
					'unicode' => sanitize_text_field( $icon['unicode'] ),
					'style'   => sanitize_text_field( $this->parse_style_class( $icon['styles'][0] ?? '' ) ),
				);
			}
		}

		$file_content = wp_json_encode( $data );

		WP_Filesystem();

		global $wp_filesystem;

		$wp_filesystem->put_contents( WP_SMI_PLUGIN_DIR . 'dist/icons.json', $file_content, FS_CHMOD_FILE );

		wp_die( esc_html__( 'Font file successfully generated.', 'wp-simple-menu-icons' ) );

	}

	/**
	 * Get style class.
	 *
	 * @param string $style style class
	 * @return string
	 */
	private function parse_style_class( $style ) {

		$class = '';

		if ( $style === 'solid' ) {
			$class = 'fas';
		} else {
			$class = 'fab';
		}

		return $class;

	}

}

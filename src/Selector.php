<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Load the icon selector into the admin panel.
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
 * Icon selector handler into the admin panel.
 */
class Selector {

	/**
	 * Default values of icons.
	 *
	 * @var array
	 */
	public $default_values = array(
		'label'    => 0,
		'position' => 'before',
		'align'    => 'middle',
		'size'     => 1,
		'icon'     => '',
		'color'    => '',
	);

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
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_nav_menu_item_icon' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'setup_fields' ), 10, 4 );
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'setup_icon' ), 100, 4 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 3 );

		add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
	}

	/**
	 * Load assets
	 *
	 * @return void
	 */
	public function enqueue() {
		global $pagenow;

		if ( $pagenow !== 'nav-menus.php' ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style( 'wpsmi-admin', WP_SMI_PLUGIN_URL . 'dist/css/admin.css', array(), WP_SMI_VERSION );
		wp_enqueue_style( 'wpsmi-fa', WP_SMI_PLUGIN_URL . 'dist/css/all.min.css', array(), WP_SMI_VERSION );

		wp_enqueue_script(
			'wpsmi-admin',
			WP_SMI_PLUGIN_URL . 'dist/js/admin.js',
			array(
				'jquery',
				'backbone',
				'underscore',
				'wp-util',
			),
			WP_SMI_VERSION,
			true
		);

		wp_localize_script(
			'wpsmi-admin',
			'wpsmi_admin_l10n',
			array(
				'legacy_pick'    => esc_html__( 'Select', 'wp-simple-menu-icons' ),
				'legacy_current' => esc_html__( 'Color', 'wp-simple-menu-icons' ),
				'nonce'          => wp_create_nonce( 'wpsmi' ),
			)
		);
	}

	/**
	 * Register metabox.
	 *
	 * @return void
	 */
	public function register_menu_metabox() {
		add_meta_box( 'wpsmi_metabox', esc_html__( 'Simple menu icons', 'wp-simple-menu-icons' ), array( $this, 'metabox' ), 'nav-menus', 'side', 'high' );
	}

	/**
	 * Setup fields where values of the selector will be stored.
	 *
	 * @param string $menu_item_id
	 * @param object $item
	 * @param string $depth
	 * @param object $args
	 * @return void
	 */
	public function setup_fields( $menu_item_id, $item, $depth, $args ) {

		foreach ( $this->default_values as $key => $value ) {
			?>
			<input id="<?php echo esc_attr( "wpsmi-input-{$key}" ); ?>" class="wpsmi-input" type="hidden" name="<?php echo esc_attr( 'wpsmi[' . $menu_item_id . '][' . $key . ']' ); ?>" value="<?php echo esc_attr( $item->wpsmi->{$key} ); ?>">
			<?php
		}
	}

	/**
	 * Setup icons into menu
	 *
	 * @param string $menu_item_id
	 * @param object $item
	 * @param string $depth
	 * @param object $args
	 * @return void
	 */
	public function setup_icon( $menu_item_id, $item, $depth, $args ) {
		?>
		<div class="icon-btn-wrapper">
			<a href="#" class="button menu-item-wpsmi_open">
				<span>
					<?php if ( ! empty( $item->wpsmi->icon ) ) : ?>
						<i class="menu-item-wpsmi_icon <?php echo esc_attr( $item->wpsmi->icon ); ?>"></i>
					<?php endif; ?>
				</span>
				<?php esc_html_e( 'Setup icon', 'wp-simple-menu-icons' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Save custom fields values into the menu data.
	 *
	 * @param string $menu_id
	 * @param string $menu_item_db_id
	 * @param array  $menu_item_args
	 * @return void
	 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( ! wp_doing_ajax() ) {

			$menu_item_wpsmi = array();

			check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

			if ( ! empty( $_POST['wpsmi'][ $menu_item_db_id ] ) ) {

				$menu_item_wpsmi['label']    = absint($_POST['wpsmi'][$menu_item_db_id]['label']); //phpcs:ignore
				$menu_item_wpsmi['position'] = sanitize_html_class($_POST['wpsmi'][$menu_item_db_id]['position']); //phpcs:ignore
				$menu_item_wpsmi['align']    = sanitize_html_class($_POST['wpsmi'][$menu_item_db_id]['align']); //phpcs:ignore
				$menu_item_wpsmi['size']     = sanitize_text_field($_POST['wpsmi'][$menu_item_db_id]['size']); //phpcs:ignore
				$menu_item_wpsmi['icon']     = sanitize_text_field($_POST['wpsmi'][$menu_item_db_id]['icon']); //phpcs:ignore
				$menu_item_wpsmi['color']    = sanitize_text_field($_POST['wpsmi'][$menu_item_db_id]['color']); //phpcs:ignore

				$this->update( $menu_item_db_id, $menu_item_wpsmi );
			}
		}
	}

	/**
	 * Trigger storage into the database.
	 *
	 * @param string $id
	 * @param mixed  $value
	 * @return void
	 */
	private function update( $id, $value ) {

		$value = apply_filters( 'wp_menu_icons_item_meta_values', $value, $id );

		if ( ! empty( $value ) ) {
			update_post_meta( $id, WP_SMI_DB_KEY, $value );
		} else {
			delete_post_meta( $id, WP_SMI_DB_KEY );
		}
	}

	/**
	 * Load modal templates.
	 *
	 * @return void
	 */
	public function print_media_templates() {
		$menu_id = $this->nav_menu_selected_id();

		include WP_SMI_PLUGIN_DIR . 'resources/views/media-template.php';
	}

	/**
	 * Get the ID number of the selected menu.
	 *
	 * @return string
	 */
	private function nav_menu_selected_id() {
		$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

		$menu_count = count( $nav_menus );

		// Get recently edited nav menu
		$recently_edited = (int) get_user_option( 'nav_menu_recently_edited' );

		$nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) $_REQUEST['menu'] : 0; //phpcs:ignore

		// Are we on the add new screen?
		$add_new_screen = (isset($_GET['menu']) && 0 == $_GET['menu']) ? true : false; //phpcs:ignore

		$page_count = wp_count_posts( 'page' );

		//phpcs:ignore
		$one_theme_location_no_menus = (1 == count(get_registered_nav_menus()) && !$add_new_screen && empty($nav_menus) && !empty($page_count->publish)) ? true : false;

		if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) ) {
			$recently_edited = $nav_menu_selected_id;
		}

		// Use $recently_edited if none are selected.
		if (empty($nav_menu_selected_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited)) { //phpcs:ignore
			$nav_menu_selected_id = $recently_edited;
		}

		// On deletion of menu, if another menu exists, show it.
		if (!$add_new_screen && 0 < $menu_count && isset($_GET['action']) && 'delete' == $_GET['action']) { //phpcs:ignore
			$nav_menu_selected_id = $nav_menus[0]->term_id;
		}

		// Set $nav_menu_selected_id to 0 if no menus.
		if ( $one_theme_location_no_menus ) {
			$nav_menu_selected_id = 0;
		} elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
			// if we have no selection yet, and we have menus, set to the first one in the list.
			$nav_menu_selected_id = $nav_menus[0]->term_id;
		}

		return $nav_menu_selected_id;
	}

	/**
	 * Setup the icon for the menu item.
	 *
	 * @param object $item menu item.
	 * @return object
	 */
	public function setup_nav_menu_item_icon( $item ) {

		$item->wpsmi = new \stdClass();

		//phpcs:ignore
		if ($wpsmi = wp_parse_args((array) get_post_meta($item->ID, WP_SMI_DB_KEY, true), $this->default_values)) {
			if ( count( $wpsmi ) ) {
				foreach ( $wpsmi as $key => $value ) {
					$item->wpsmi->{$key} = $value;
				}
			}
		}

		return $item;
	}
}

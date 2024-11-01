<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Setup the filters.
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
 * Setup the output of the icons within menu titles.
 *
 * @param string $title title
 * @param string $menu_item_id id number
 * @return string
 */
function wp_smi_setup_frontend_icon( $title, $menu_item_id ) {

	$classes = array();

	$wpsmi = '';
	$style = '';
	$size  = '';
	$color = '';

	$new_title = $title;

	if ( ! is_admin() && ! wp_doing_ajax() ) {

		$wpsmi = get_post_meta( $menu_item_id, WP_SMI_DB_KEY, true );

		if ( $wpsmi && isset( $wpsmi['icon'] ) && $wpsmi['icon'] !== '' ) {

			foreach ( $wpsmi as $key => $value ) {
				if ( ! in_array( $key, array( 'icon', 'color' ), true ) && $value !== '' ) {
					$classes[] = "wpsmi-{$key}-{$value}";
				}

				if ( $key === 'icon' ) {
					$classes[] = $value;
				}
			}

			if ( ! empty( $wpsmi['label'] ) ) {
				$title = '';
			}

			if ( ! empty( $wpsmi['size'] ) ) {
				$size = 'font-size:' . $wpsmi['size'] . 'em;';
			}

			if ( ! empty( $wpsmi['color'] ) ) {
				$color = 'color:' . $wpsmi['color'];
			}

			$style = ' style="' . $size . $color . '"';

			$icon = '<i' . $style . ' class="wpsmi-icon ' . join( ' ', array_map( 'esc_attr', $classes ) ) . '"></i>';

			if ( isset( $wpsmi['position'] ) && $wpsmi['position'] === 'after' ) {
				$new_title = $title . $icon;
			} else {
				$new_title = $icon . $title;
			}
		}
	}

	/**
	 * Filter: allow developers to modify the output of the icon within menu titles.
	 *
	 * @param string $new_title title
	 * @param string $menu_item_id id
	 * @param array $wpsmi menu item details
	 * @param string $title original title
	 * @return string
	 */
	return apply_filters( 'wp_menu_icons_item_title', $new_title, $menu_item_id, $wpsmi, $title );

}
add_filter( 'the_title', 'wp_smi_setup_frontend_icon', 999, 2 );

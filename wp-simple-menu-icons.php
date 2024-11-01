<?php
/**
 * Plugin Name:     WP Simple Menu Icons
 * Plugin URI:      https://pressmodo.com
 * Description:     An easy way to add icons to your navigation menus.
 * Author:          Sematico LTD
 * Author URI:      https://sematico.com
 * Text Domain:     wp-simple-menu-icons
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * WP Simple Menu Icons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Simple Menu Icons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Simple Menu Icons. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package wp-simple-menu-icons
 * @author Sematico LTD
 */

namespace Pressmodo\MenuIcons;

use Pressmodo\Requirements\Requirements;

defined( 'ABSPATH' ) || exit;

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}

$requirements = new Requirements(
	'WP Simple Menu Icons',
	array(
		'php' => '7.2',
		'wp'  => '5.3',
	)
);

/**
 * Run all the checks and check if requirements has been satisfied.
 * If not - display the admin notice and exit from the file.
 */
if ( ! $requirements->satisfied() ) {
	$requirements->print_notice();
	return;
}

/**
 * Finally load the addon.
 */
add_action(
	'plugins_loaded',
	function () {

		$plugin = Plugin::instance( __FILE__ );

		add_action( 'plugins_loaded', array( $plugin, 'textdomain' ), 11 );
	}
);

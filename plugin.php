<?php
/**
 * Plugin Name:       Power Course | 可能是 WordPress 最好用的課程外掛
 * Plugin URI:        https://github.com/j7-dev/wp-power-course
 * Description:       可能是 WordPress 最好用的課程外掛
 * Version:           0.0.1
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            J7
 * Author URI:        https://github.com/j7-dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       power_course
 * Domain Path:       /languages
 * Tags: LMS, online course, vite, react, tailwind, typescript, react-query, scss, WordPress, WordPress plugin, refine
 */

declare (strict_types = 1);

namespace J7\PowerCourse;

if ( ! \class_exists( 'J7\PowerCourse\Plugin' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';

	/**
		* Class Plugin
		*/
	final class Plugin {
		use \J7\WpUtils\Traits\PluginTrait;
		use \J7\WpUtils\Traits\SingletonTrait;

		/**
		 * Constructor
		 */
		public function __construct() {
			require_once __DIR__ . '/inc/class/class-bootstrap.php';

			$this->required_plugins = array(
				array(
					'name'     => 'WooCommerce',
					'slug'     => 'woocommerce',
					'required' => true,
					'version'  => '7.6.0',
				),
				// array(
				// 'name'     => 'WP Toolkit',
				// 'slug'     => 'wp-toolkit',
				// 'source'   => 'Author URL/wp-toolkit/releases/latest/download/wp-toolkit.zip',
				// 'required' => true,
				// ),
			);

			$this->init(
				array(
					'app_name'    => 'Power Course',
					'github_repo' => 'https://github.com/j7-dev/wp-power-course',
					'callback'    => array( Bootstrap::class, 'instance' ),
				)
			);

			\add_action( 'plugins_loaded', array( $this, 'check_required_plugins' ) );
		}
	}

	Plugin::instance();
}

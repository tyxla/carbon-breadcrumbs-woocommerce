<?php
/**
 * Plugin Name: Carbon Breadcrumbs - WooCommerce
 * Description: A WooCommerce addon for Carbon Breadcrumbs. Requires the Woocommerce and Carbon Breadcrumbs plugins.
 * Version: 1.0
 * Author: tyxla
 * Author URI: https://github.com/tyxla
 * License: GPL2
 * Requires at least: 3.8
 * Tested up to: 4.1
 */

// allows the plugin to be included as a library in themes
if (class_exists('Carbon_Breadcrumb_Woocommerce_Trail')) {
	return;
}

/**
 * The main Carbon Breadcrumbs - WooCommerce plugin class.
 *
 * Singleton, used as a bootstrap to include the plugin files.
 */
final class Carbon_Breadcrumbs_WooCommerce {
	/**
	 * Instance container.
	 *
	 * @static
	 * @access private
	 *
	 * @var Carbon_Breadcrumbs_WooCommerce
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *  
	 * Private so only the get_instance() can instantiate it.
	 *
	 * @access private
	 */
	private function __construct() {
		$dir = dirname(__FILE__);
		
		// include files
		include_once($dir . '/includes/Carbon_Breadcrumb_Woocommerce_Trail.php');
		include_once($dir . '/includes/Carbon_Breadcrumb_Woocommerce_Template.php');

		// initialize trail functionality
		$trail = new Carbon_Breadcrumb_Woocommerce_Trail();

		// initialize template functionality
		$template = new Carbon_Breadcrumb_Woocommerce_Template();
	}

	/**
	 * Whether the WooCommerce functionality should be enabled.
	 *
	 * @access public
	 *
	 * @return bool $is_enabled True if the WooCommerce functionality should be enabled.
	 */
	public function is_enabled() {
		// the Carbon Breadcrumbs main class should exist
		if ( !class_exists('Carbon_Breadcrumbs') ) {
			return false;
		}

		// the WooCommerce plugin main class should exist
		if ( !class_exists('WooCommerce') ) {
			return false;
		}

		// everything is included
		return true;
	}


	/**
	 * Retrieve or create the Carbon_Breadcrumbs_WooCommerce instance.
	 *
	 * @static
	 * @access public
	 *
	 * @return Carbon_Breadcrumbs_WooCommerce $instance
	 */
	public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private __clone() to prevent cloning the singleton instance.
	 *
	 * @access private
	 */
	private function __clone() {}

	/**
	 * Private __wakeup() to prevent singleton instance unserialization.
	 *
	 * @access private
	 */
	private function __wakeup() {}

}

// initialize the plugin
Carbon_Breadcrumbs_WooCommerce::get_instance();
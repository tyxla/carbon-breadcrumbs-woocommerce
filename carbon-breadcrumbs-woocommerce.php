<?php
/**
 * Plugin Name: Carbon Breadcrumbs - WooCommerce
 * Description: A WooCommerce addon for Carbon Breadcrumbs. Requires the Woocommerce and Carbon Breadcrumbs plugins.
 * Version: 1.0
 * Author: tyxla
 * Author URI: https://github.com/tyxla
 * License: GPL2
 */

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
		// initialize the plugin
		add_action('init', array($this, 'init'));
	}

	/**
	 * Initialize the plugin and its features.
	 *
	 * @access public
	 */
	public function init() {
		// skip if the Carbon Breadcrumbs main class does not exist
		if ( !class_exists('Carbon_Breadcrumbs') ) {
			return;
		}

		// skip if the WooCommerce plugin is not activated
		if ( !class_exists('WooCommerce') ) {
			return;
		}

		// modify the WooCommerce breadcrumbs template
		add_filter('wc_get_template', array($this, 'wc_get_template'), 10, 5);

		// add custom WooCommerce-related breadcrumb items
		add_action('carbon_breadcrumbs_after_setup_trail', array($this, 'setup'), 100);
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
	 * Modify the default breadcrumbs template of WooCommerce
	 *
	 * @static
	 * @access public
	 *
	 * @param string $located The original template path.
	 * @param string $template_name The original template name.
	 * @param array $args The args that the breadcrumbs function was called with.
	 * @param string $template_path Path to templates.
	 * @param string $default_path The default path to templates.
	 * @return $string $located The new template path.
	 */
	public function wc_get_template($located, $template_name, $args, $template_path = '', $default_path = '') {
		if ($template_name == 'global/breadcrumb.php') {
			$located = dirname(__FILE__) . '/templates/breadcrumbs-template.php';
		}
		return $located;
	}

	/**
	 * Modify the trail by adding custom WooCommerce-related breadcrumb items.
	 *
	 * @access public
	 *
	 * @param Carbon_Breadcrumb_Trail $trail The breadcrumb trail.
	 */
	public function setup($trail) {

		// starting setup
		do_action('carbon_breadcrumbs_woocommerce_before_setup_trail', $trail);

		// get the current items
		$items = $trail->get_items();

		// remove page for posts on WooCommerce pages
		if ( is_woocommerce() && $page_for_posts = get_option('page_for_posts') ) {
			$page_for_posts_url = get_permalink($page_for_posts);
			foreach ($items as $priority => &$priority_items) {
				foreach ($priority_items as $priority_item_key => $priority_item) {
					if ($priority_item->get_link() == $page_for_posts_url) {
						unset($priority_items[$priority_item_key]);
					}
				}
			}
		}

		// update the items
		$trail->set_items($items);

		// add product category hierarchy to single products
		if (is_single() && get_post_type() == 'product') {
			$taxonomy = 'product_cat';
			$categories = wp_get_object_terms(get_the_ID(), $taxonomy, 'orderby=term_id');
			$last_category = array_pop($categories);
			$locator = Carbon_Breadcrumb_Locator::factory('term', $taxonomy);
			$new_items = $locator->get_items(700, $last_category->term_id);
			if ($new_items) {
				$trail->add_item($new_items);
			}
		}

		// add product main page
		if ( is_woocommerce() || is_cart() || is_checkout() ) {
			$shop_page_id = woocommerce_get_page_id( 'shop' );
			if ($shop_page_id) {
				$shop_title = get_the_title($shop_page_id);
				$shop_link = get_permalink($shop_page_id);
				$trail->add_custom_item($shop_title, $shop_link, 500);
			}
		}

		// completing setup
		do_action('carbon_breadcrumbs_woocommerce_after_setup_trail', $trail);

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
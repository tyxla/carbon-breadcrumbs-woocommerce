<?php
/**
 * The Carbon Breadcrumbs / WooCommerce Trail class.
 *
 * Used to handle the breadcrumb trail functionality for WooCommerce.
 */
class Carbon_Breadcrumb_Woocommerce_Trail {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		// initialize the trail functionality
		add_action('init', array($this, 'init'));
	}

	/**
	 * Initialize the trail functionality.
	 *
	 * @access public
	 */
	public function init() {
		// make sure we should continue
		$cbw = Carbon_Breadcrumbs_WooCommerce::get_instance();
		if ( !$cbw->is_enabled() ) {
			return false;
		}

		// add custom WooCommerce-related breadcrumb items
		add_action('carbon_breadcrumbs_after_setup_trail', array($this, 'setup'), 100);
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

}
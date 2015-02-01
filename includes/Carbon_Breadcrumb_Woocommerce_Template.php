<?php
/**
 * The Carbon Breadcrumbs / WooCommerce Template class.
 *
 * Used to handle the breadcrumb template functionality for WooCommerce.
 */
class Carbon_Breadcrumb_Woocommerce_Template {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		// initialize the template functionality
		add_action('init', array($this, 'init'));
	}

	/**
	 * Initialize the template functionality.
	 *
	 * @access public
	 */
	public function init() {
		// make sure we should continue
		$cbw = Carbon_Breadcrumbs_WooCommerce::get_instance();
		if ( !$cbw->is_enabled() ) {
			return false;
		}

		// modify the WooCommerce breadcrumbs template
		add_filter('wc_get_template', array($this, 'wc_get_template'), 10, 5);
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
			$located = dirname(dirname(__FILE__)) . '/templates/breadcrumbs-template.php';
		}
		return $located;
	}

}
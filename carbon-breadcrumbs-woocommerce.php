<?php
/**
 * Plugin Name: Carbon Breadcrumbs - WooCommerce
 * Description: A WooCommerce addon for Carbon Breadcrumbs.
 * Version: 1.0
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
     *
     * @var Carbon_Breadcrumbs_WooCommerce
     */
    static $instance = null;

    /**
     * Constructor.
     *  
     * Private so only the get_instance() can instantiate it.
     *
     * @access private
     */
    private function __construct() {
        add_action( 'admin_init', array($this, 'check_dependencies') );
        add_filter('wc_get_template', array($this, 'wc_get_template'), 10, 5);
        add_action('carbon_breadcrumb_after_setup_trail', array($this, 'setup'), 100);
    }

    /**
     * Retrieve or create the Carbon_Breadcrumbs_WooCommerce instance.
     *
     * @static
     * @access public
     *
     * @return Carbon_Breadcrumbs_WooCommerce $instance
     */
    static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check for the plugin dependencies and deactivate if they are not present.
     *
     * @access public
     */
    function check_dependencies() {
        if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'carbon-breadcrumbs/carbon-breadcrumbs.php' ) ) {
            add_action( 'admin_notices', array($this, 'plugin_notice') );

            deactivate_plugins( plugin_basename( __FILE__ ) ); 

            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    }

    /**
     * The notice that we display when the dependencies are not present.
     *
     * @access public
     */
    function plugin_notice() {
        ?>
        <div class="error"><p><?php _e('Sorry, but Carbon Breadcrumbs - WooCoomerce requires the Carbon Breadcrumbs and WooCommerce plugins to be installed and active.', 'crb'); ?></p></div>
        <?php
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
     * @return $string $located
     */
    function wc_get_template($located, $template_name, $args, $template_path = '', $default_path = '') {
        if ($template_name == 'global/breadcrumb.php') {
            $located = dirname(__FILE__) . '/breadcrumbs-template.php';
        }
        return $located;
    }

    /**
     * Modify the trail by adding custom WooCommerce-related breadcrumb items.
     *
     * @static
     * @access public
     *
     * @param Carbon_Breadcrumb_Trail $trail The breadcrumb trail.
     */
    function setup($trail) {

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

    }

}

// initialize the plugin
Carbon_Breadcrumbs_WooCommerce::get_instance();
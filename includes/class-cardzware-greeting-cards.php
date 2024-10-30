<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 * @author     Printzware <hello@cardzware.com>
 */
class Cardzware_Greeting_Cards {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cardzware_Greeting_Cards_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CARDZWARE_PLUGIN_VERSION' ) ) {
			$this->version = CARDZWARE_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.15';
		}
		$this->plugin_name = 'cardzware-greeting-cards';

        $has_valid_requirements = Cardzware_Greeting_Cards_Install_Checker::has_valid_install_requirements();

        $this->load_minimal_dependencies();
        if ($has_valid_requirements) {
            $this->load_dependencies();
        }

		$this->set_locale();
		$this->define_admin_hooks();
        if ($has_valid_requirements) {
            $this->define_order_admin_hooks();
            $this->define_public_hooks();
            $this->initialize_cardzware_rest_endpoints();
            $this->add_cardzware_product_hooks();
        }
	}

	/**
	 * Loads the minimal Cardzware plugin dependencies.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cardzware_Greeting_Cards_Loader. Orchestrates the hooks of the plugin.
	 * - Cardzware_Greeting_Cards_i18n. Defines internationalization functionality.
	 * - Cardzware_Greeting_Cards_Admin. Defines all hooks for the admin area.
     * - Cardzware_Greeting_Cards_Order_Admin. Defines order actions.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
     * @return   void
	 */
	private function load_minimal_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cardzware-greeting-cards-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cardzware-greeting-cards-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cardzware-greeting-cards-admin.php';

		$this->loader = new Cardzware_Greeting_Cards_Loader();
	}

    /**
     * Loads dependencies for a valid Cardzware plugin installation.
     *
     * @since    1.0.0
     * @access   private
     * @return   void
     */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cardzware-greeting-cards-public.php';

        /**
         * Cardzware dependencies
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cw-includes/class-cardzware-greeting-cards-woocommerce.php';

        /**
         * Cardzware REST resources and validators
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/validators/class-cardzware-greeting-cards-save-settings-validator.php';
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/validators/class-cardzware-greeting-cards-update-api-url-validator.php';
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/validators/class-cardzware-greeting-cards-update-branding-validator.php';
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/validators/class-cardzware-greeting-cards-update-seo-validator.php';
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-cardzware-greeting-cards-rest-controller.php';

        /**
         * Cardzware product actions
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cardzware-greeting-cards-product-actions.php';

        /**
         * Cardzware shipping method
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cw-includes/class-cardzware-greeting-cards-shipping-method.php';

        /**
         * The class responsible for defining new status action.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cardzware-greeting-cards-order-admin.php';

        /**
         * Cardzware SEO
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cw-includes/class-cardzware-greeting-cards-seo-service.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cardzware_Greeting_Cards_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cardzware_Greeting_Cards_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cardzware_Greeting_Cards_Admin( $this->get_plugin_name(), $this->get_version() );
        $plugin_admin->register_admin_page();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'cardzware_display_admin_notice');
	}

    /**
     * Register all of the hooks related to the order admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_order_admin_hooks() {

        $plugin_order_admin = new Cardzware_Greeting_Cards_Order_Admin();

        $this->loader->add_action( 'woocommerce_order_actions', $plugin_order_admin, 'wc_custom_order_action', 10, 1);
        $this->loader->add_action( 'woocommerce_order_action_cardzware_fulfillment_cancel', $plugin_order_admin, 'cardzware_fulfillment_cancel', 10, 1);
        $this->loader->add_action( 'woocommerce_order_action_cardzware_fulfillment_retry', $plugin_order_admin, 'cardzware_fulfillment_retry', 10, 1);
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Cardzware_Greeting_Cards_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'create_cardzware_categories_page' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
        $this->loader->add_action( 'init', $plugin_public, 'add_greeting_card_category_rewrite_rule' );
        $this->loader->add_action( 'query_vars', $plugin_public, 'add_greeting_card_category_query_var' );


        /**
         * 	Hook in plugin functions to Woocommerce
         */
        $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
        if (stripos(implode($all_plugins), 'woocommerce.php')) {
            $this->loader->add_filter( 'woocommerce_cart_item_name',  $plugin_public ,'cw_show_cart_product_personalize', 10, 3 );
			$this->loader->add_filter( 'woocommerce_after_cart_item_quantity_update',  $plugin_public ,'cw_update_quantity_product', 10, 4 );
            $this->loader->add_filter( 'woocommerce_cart_item_thumbnail', $plugin_public, 'cw_custom_thumbnail_cart_product', 10, 2 );
            $this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'cw_split_product_individual_cart_items', 10, 2 );
            $this->loader->add_filter( 'woocommerce_remove_cart_item', $plugin_public, 'cw_remove_post_meta_cart_item', 10, 2 );
            $this->loader->add_filter( 'woocommerce_add_order_item_meta', $plugin_public, 'cw_add_order_item_meta', 10, 2);
            $this->loader->add_filter( 'woocommerce_cart_actions', $plugin_public, 'custom_cart_button');
            $this->loader->add_filter( 'woocommerce_thankyou', $plugin_public, 'cw_checkout_remove_all_post_meta_product', 10, 1 );

            $this->loader->add_action( 'wp_ajax_update_product_meta_values', $plugin_public, 'update_product_meta_values' );
            $this->loader->add_action( 'wp_ajax_nopriv_update_product_meta_values', $plugin_public, 'update_product_meta_values' ); //executes for users that are not logged in.

            $this->loader->add_action( 'wp_ajax_get_values_js_variables', $plugin_public, 'get_values_js_variables' );
            $this->loader->add_action( 'wp_ajax_nopriv_get_values_js_variables', $plugin_public, 'get_values_js_variables' ); //executes for users that are not logged in.


        }
	}

    /**
     * Initializes REST endpoints for Cardzware
     *
     * @return void
     */
	private function initialize_cardzware_rest_endpoints() {
		$cardzware_greeting_cards_rest_controller = new Cardzware_Greeting_Cards_Rest_Controller();
        $this->loader->add_action( 'rest_api_init', $cardzware_greeting_cards_rest_controller, 'register_routes' );
	}

    /**
     * Initializes Cardzware product hooks
     *
     * @return void
     */
	private function add_cardzware_product_hooks() {
		$cardzware_greeting_cards_product_actions = new Cardzware_Greeting_Cards_Product_Actions();
        $this->loader->add_action( 'woocommerce_new_product', $cardzware_greeting_cards_product_actions, 'save_product_id' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cardzware_Greeting_Cards_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

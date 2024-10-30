<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/admin
 * @author     Printzware <hello@cardzware.com>
 */
class Cardzware_Greeting_Cards_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private const WOOCOMMERCE_PLUGIN_NAME = 'woocommerce/woocommerce.php';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    public function register_admin_page() {
        add_action( 'plugins_loaded', [$this, 'add_cardzware_admin_page'] );
    }

    public function add_cardzware_admin_page() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards-admin-menu.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards-admin-page.php';
        $cardzware_admin_page = new Cardzware_Greeting_Cards_Admin_Page();
        $plugin = new Cardzware_Greeting_Cards_Admin_Menu($cardzware_admin_page);
        $plugin->init();

        add_action( 'admin_post_save_cw_config', [$cardzware_admin_page, 'save'] );
        add_action( 'admin_post_delete_cw_config', [$cardzware_admin_page, 'delete'] );
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cardzware_Greeting_Cards_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cardzware_Greeting_Cards_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/cardzware-greeting-cards-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap-5_2_3.css', array(), $this->version, 'all' );

	}

    public function cardzware_display_admin_notice() {
        if (!is_plugin_active(self::WOOCOMMERCE_PLUGIN_NAME)) {
            $woocommerce_url_page = esc_url('https://wordpress.org/plugins/woocommerce');
			echo sprintf(__('<div class="notice notice-error"><p><b>[Cardzware]</b>Our plugin requires <a href="%1$s">WooCommerce</a> to be installed and activated.</p></div>', 'cardzware-greeting-cards'), $woocommerce_url_page);
        } elseif (get_transient('cardzware_activation_notice')) {
			$custom_admin_page_url = esc_url(admin_url('admin.php?page=cw-admin'));
			echo sprintf(__('<div style="border-left-color:#00a0df" class="notice notice-success is-dismissible"><p><b>[Cardzware]</b> Thank you for activating Cardzware! To get started, please go to the <a class="button-primary" style="color:white;background: linear-gradient(133deg, #994393, #674598)!important;padding: 0.2rem;border:none;" href="%s">Cardzware admin page</a></p></div>', 'cardzare-plugin'), $custom_admin_page_url);
			delete_transient('cardzware_activation_notice');
		} elseif (get_transient('cardzware_redirect_has_not_valid_install_requirements')) {
			delete_transient('cardzware_redirect_has_not_valid_install_requirements');
			wp_redirect(admin_url('admin.php?page=cw-admin'));
			exit;
		}
    }
}

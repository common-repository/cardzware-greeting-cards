<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Fired during plugin activation
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 * @author     Printzware <hello@cardzware.com>
 */
class Cardzware_Greeting_Cards_Activator {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Cardzware_Greeting_Cards_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    public static function register($file) {
        register_activation_hook( $file, 'Cardzware_Greeting_Cards_Activator::cardzware_activate_plugin');
    }

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function cardzware_activate_plugin() {
        if (Cardzware_Greeting_Cards_Install_Checker::has_valid_install_requirements()) {
            Cardzware_Greeting_Cards_Config::set_page_view('start');
            set_transient('cardzware_activation_notice', true, 5);
        } else {
            set_transient('cardzware_redirect_has_not_valid_install_requirements', true, 5);
        }
    }
}

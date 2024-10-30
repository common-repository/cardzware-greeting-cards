<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Fired during plugin deactivation
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/includes
 * @author     Printzware <hello@cardzware.com>
 */
class Cardzware_Greeting_Cards_Deactivator {

    public static function register($file) {
        register_deactivation_hook( $file, 'Cardzware_Greeting_Cards_Deactivator::cardzware_deactivate_plugin' );
    }

	public static function cardzware_deactivate_plugin() {
        remove_shortcode('cw_cards_iframe');
        flush_rewrite_rules();
	}

}

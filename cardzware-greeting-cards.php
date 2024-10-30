<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cardzware.com
 * @since             1.0.0
 * @package           Cardzware_Greeting_Cards
 *
 * @wordpress-plugin
 * Plugin Name:       Cardzware: Greeting Cards
 * Plugin URI:        https://cardzware.com
 * Description:       Cardzware's easy-to-install App enables online retailers to sell personalized greeting cards to their customers. All cards are printed on-demand from one of our global printing facilities.
 * Version:           1.0.15
 * Author:            Cardzware Support
 * Author URI:        https://help.cardzware.com/support/solutions/folders/101000429529
 * License:           private
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cardzware-greeting-cards
 * Domain Path:       /languages
 */

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */

require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards-deactivator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards-activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards-install-checker.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-cardzware-greeting-cards.php';

require_once plugin_dir_path( __FILE__ ) . 'admin/includes/class-cardzware-greeting-cards-admin-page.php';


Cardzware_Greeting_Cards_Activator::register(__FILE__);
Cardzware_Greeting_Cards_Deactivator::register(__FILE__);

function run_cardzware_greeting_cards()
{

    $plugin = new Cardzware_Greeting_Cards();
    $plugin->run();

}
run_cardzware_greeting_cards();

add_action('parse_query', function (WP_Query $query)
{
    if (($name = $query->get('name'))) {
        $query->set('name', sanitize_title($name));
    }
});

function cardzware_greeting_cards_links($links, $file) {
    $plugin_base = plugin_basename(__FILE__);

    if ($file == $plugin_base) {
        $links[1] = '<a href="https://help.cardzware.com/support/solutions/folders/101000429529" target="_blank">Cardzware Support</a>';
        $links[2] = '<a href="https://cardzware.com/" aria-label="Visit plugin site for Cardzware: Greeting Cards" target="_blank">Visit plugin site</a>';
    }

    return $links;
}
add_filter( 'plugin_row_meta', 'cardzware_greeting_cards_links', 10, 2 );

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Admin_Menu {

    private $menu_page;

    public function __construct( $menu_page ) {
        $this->menu_page = $menu_page;
    }

    public function init() {
        add_action( 'admin_menu', array( $this, 'add_cardzware_greeting_cards_page' ) );
    }

    public function add_cardzware_greeting_cards_page() {
        add_menu_page(
            __('Cardzware: Personalised Greeting Cards', 'cardzware-greeting-cards'),
            __('Cardzware', 'cardzware-greeting-cards'),
            'manage_options',
            'cw-admin',
            [ $this->menu_page, 'render' ],
            'dashicons-cardzware-greeting-cards',
            7
        );
    }
}

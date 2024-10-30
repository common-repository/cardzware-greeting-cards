<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Config
{
    const CW_ALL_SETTINGS = 'cw_all_settings';
    const CW_ALL_DATA = 'cw_all_data';
    const CW_API_KEY = 'api_key';
    const CW_CLIENT_ID = 'client_id';
    const CW_PRODUCT_ID = 'product_id';
    const CW_API_URL = 'api_url';
    const CW_PAGE_VIEW = 'cw_page_view';
    const CW_SHIPPING_METHOD = 'cw_shipping_method';

    const CW_IFRAME_ID = 'pw_iframe_id';
    const CW_MODAL_ID = 'pw_modal';
    const IS_DEBUG_MODE = false;
    const CW_BRANDING = 'branding';
    const  CW_SETUP_COMPLETE = 'setup_complete';
    const CW_ONBOARDING_COMPLETE = 'onboarding_complete';
    const CW_SEO = 'seo';
    const CW_HAS_ENABLE_SEO = 'enable_seo';
    const CW_SHOW_CART_BUTTON = 'show_cart_button';
    const CW_CART_BUTTON_TEXT = 'cart_button_text';
    const CW_CART_BUTTON_TEXT_DEFAULT = 'ADD A PERSONALISED CARD';
    const CW_PAGE_SLUG = 'greeting-cards';

    const CW_CATEGORY_ENDPOINT_DEFAULT_VALUE = 'a';

    const CW_BRANDING_DEFAULT_VALUE = [
        "tiles_size" => "181",
        "tiles_bg_color" => "rgb(120,120,120,0.15)",
        "tiles_color" => "rgba(120,120,120,0.85)",
        "tiles_hover_color" => "rgba(120,120,120,0.95)",
        "bc_color" => "rgba(120,120,120,0.85)",
        "bc_hover_color" => "rgba(120,120,120,0.95)"
    ];
    const ADMIN_DASHBOARD_EXT = '/admin/dashboard';
    const ADMIN_ORDERS_EXT = '/admin/orders';
    const ADMIN_CARDS_EXT = '/admin/cards';
    const ADMIN_BILLING_EXT = '/admin/billing';
    const ADMIN_BRANDING_COLOUR_SCHEME_EXT = '/admin/branding/colour-scheme';
    const ADMIN_WEBSITE_SETTINGS_EXT = '/admin/website-settings';
    const HELP_URL = 'https://help.cardzware.com/support/home';

    public static function has_saved_configuration(): bool {
        return self::get_client_id() != null && self::get_api_key() != null && self::get_api_url() != null;
    }

    public static function get_base_url(): string {

        if (self::get_debug_mode()) {
            $url = "https://dev.cardzware.com";
        } else {
            $url = "https://app.cardzware.com";
        }

        return $url;
    }

    public static function get_dashboard_url(): string {
        return self::get_base_url() . self::ADMIN_DASHBOARD_EXT;
    }

    public static function get_orders_url(): string {
        return self::get_base_url() . self::ADMIN_ORDERS_EXT;
    }

    public static function get_cards_url(): string {
        return self::get_base_url() . self::ADMIN_CARDS_EXT;
    }

    public static function get_billing_url(): string {
        return self::get_base_url() . self::ADMIN_BILLING_EXT;
    }

    public static function get_colour_scheme_url(): string {
        return self::get_base_url() . self::ADMIN_BRANDING_COLOUR_SCHEME_EXT;
    }

    public static function get_website_settings_url(): string {
        return self::get_base_url() . self::ADMIN_WEBSITE_SETTINGS_EXT;
    }

    public static function get_help_url(): string {
        return self::HELP_URL;
    }

    public static function get_connect_url(): string {
        return esc_url(self::get_base_url() . "/admin/wc-connect?domain=" . urlencode(get_site_url()) . "&suffix=" . urlencode(self::get_rest_suffix()));
    }

    // CW_ALL_SETTINGS ----------------------------------
    public static function get_all_settings() {
        return json_decode(get_option(self::CW_ALL_SETTINGS, NULL), true);
    }

    public static function set_all_settings($data) {
        return update_option(self::CW_ALL_SETTINGS, json_encode($data));
    }

    public static function is_setup_complete() {
        return self::get_all_data()[self::CW_SETUP_COMPLETE] ?? false;
    }

    public static function is_onboarding_complete() {
        if (!isset(self::get_all_data()[self::CW_ONBOARDING_COMPLETE])) {
            return self::get_product_id() != NULL;
        }

        return self::get_all_data()[self::CW_ONBOARDING_COMPLETE] ?? false;
    }

    public static function get_api_url() {
        $api_url = self::get_all_settings()[self::CW_API_URL] ?? NULL;
        if (empty($api_url)) {
            return NULL;
        }

        return str_contains($api_url, 'https://') ? $api_url : 'https://' . $api_url;
    }

    public static function set_api_url($api_url) {
        $all_data = self::get_all_settings();
        $all_data[self::CW_API_URL] = $api_url;
        self::set_all_settings($all_data);
    }

    public static function get_client_id() {
        return self::get_all_settings()[self::CW_CLIENT_ID] ?? NULL;
    }

    public static function set_client_id($client_id) {
        $all_data = self::get_all_settings();
        $all_data[self::CW_CLIENT_ID] = $client_id;
        self::set_all_settings($all_data);
    }

    public static function get_api_key() {
        return self::get_all_settings()[self::CW_API_KEY] ?? NULL;
    }

    public static function set_api_key($api_key) {
        $all_data = self::get_all_settings();
        $all_data[self::CW_API_KEY] = $api_key;
        self::set_all_settings($all_data);
    }

    // CW_ALL_DATA ----------------------------------

    public static function get_all_data() {
        return json_decode(get_option(self::CW_ALL_DATA, NULL), true);
    }

    public static function set_all_data($data) {
        return update_option(self::CW_ALL_DATA, json_encode($data));
    }

    public static function get_page_view() {
        return get_option(self::CW_PAGE_VIEW, NULL);
    }

    public static function set_page_view($page_view) {
        return update_option(self::CW_PAGE_VIEW, $page_view);
    }

    public static function get_product_id() {
        return self::get_all_data()[self::CW_PRODUCT_ID] ?? NULL;
    }

    public static function set_product_id($product_id) {
        $all_data = self::get_all_data();
        $all_data[self::CW_PRODUCT_ID] = $product_id;
        self::set_all_data($all_data);
    }

    public static function get_iframe_id() {
        return self::CW_IFRAME_ID;
    }

    public static function get_modal_id() {
        return self::CW_MODAL_ID;
    }

    public static function get_debug_mode() {
        return self::IS_DEBUG_MODE;
    }

    public static function get_branding() {
        return self::get_all_data()[self::CW_BRANDING] ?? self::CW_BRANDING_DEFAULT_VALUE;
    }

    public static function get_seo() {
        $all_data = self::get_all_data();

        if (isset($all_data[self::CW_HAS_ENABLE_SEO]) && $all_data[self::CW_HAS_ENABLE_SEO]) {
            return self::get_all_data()[self::CW_SEO] ?? NULL;
        }

        return NULL;
    }

    public static function get_seo_for_category($category_id, $breadcrumb, $domain) {

        $seo = self::get_seo();

        if (!$seo) {
            return null;
        }

        $current_seo = new Cardzware_Greeting_Cards_Seo_Service($category_id, $breadcrumb, $domain, $seo);
        foreach($seo as $key => $value) {
            if ($value[$current_seo::CARD_CATEGORY_ID] == $category_id) {
                $current_seo->set_page_title($value[$current_seo::PAGE_TITLE]);
                $current_seo->set_meta_description($value[$current_seo::META_DESCRIPTION]);
                $current_seo->set_page_text_below($value[$current_seo::PAGE_TEXT_BELOW]);
                $current_seo->set_page_text_above($value[$current_seo::PAGE_TEXT_ABOVE]);
                $current_seo->set_card_category_id($value[$current_seo::CARD_CATEGORY_ID]);
                break;
            }
        }

        if ($current_seo->all_current_category_has_seo()) {
            return $current_seo->get_seo();
        }

        $current_seo->set_page_title($current_seo->get_page_title());
        $current_seo->set_meta_description($current_seo->get_meta_description());
        $current_seo->set_page_text_above($current_seo->get_page_text_above());
        $current_seo->set_page_text_below($current_seo->get_page_text_below());


        return $current_seo->get_seo();
    }

    public static function get_category_endpoint() {
        return self::CW_CATEGORY_ENDPOINT_DEFAULT_VALUE;
    }

    public static function get_show_cart_button() {
        return self::get_all_data()[self::CW_SHOW_CART_BUTTON] ?? false;
    }

    public static function get_cart_button_text() {
        $all_data = self::get_all_data();
        return $all_data[self::CW_CART_BUTTON_TEXT] ?? self::CW_CART_BUTTON_TEXT_DEFAULT;
    }

    public static function get_page_slug() {
        return self::CW_PAGE_SLUG;
    }

    private static function get_rest_suffix() {
        return str_replace(get_site_url(), '', get_rest_url());
    }
}

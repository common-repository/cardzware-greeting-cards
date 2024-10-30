<?php

require_once plugin_dir_path( __FILE__ ) . '../../cw-includes/class-cardzware-greeting-cards-config.php';

class Cardzware_Greeting_Cards_Admin_Page
{
    private static $must_be_numbers = ['client_id', 'product_id'];
    public function save() {

        $this->checkNonce('admin_post_save_cw_config');
        $config = $this->santizedFields($_POST);
        $result = $this->checkValues($config);

        if (!$result) {
            wp_redirect("admin.php?page=cw-admin&a=save&error");
            exit;
        }

        Cardzware_Greeting_Cards_Config::set_api_key($config['api_key']);
        Cardzware_Greeting_Cards_Config::set_api_url($config['api_url']);
        Cardzware_Greeting_Cards_Config::set_client_id($config['client_id']);
        Cardzware_Greeting_Cards_Config::set_product_id($config['product_id']);

        wp_redirect("admin.php?page=cw-admin&a=save&success");
        exit;
    }

    public function delete() {

        $this->checkNonce('admin_post_delete_cw_config');
    }

    public function render() {
        include_once( plugin_dir_path( __FILE__ ) . '/../views/cardzware-greeting-cards-admin-display.php' );
    }

    private function checkNonce($action) {
        if ( ! isset($_POST)
            || ! isset( $_POST['cw_config_nonce'] )
            || ! check_admin_referer($action, 'cw_config_nonce')
        ) {
            wp_redirect("admin.php?page=cw-admin&a=nonce&error");
            exit;
        }
    }

    private function santizedFields($fields) {
        return [
            'api_key' => sanitize_text_field($fields['api_key'], true),
            'api_url' => sanitize_text_field($fields['api_url'], true),
            'client_id' => sanitize_text_field($fields['client_id'], true),
            'product_id' => sanitize_text_field($fields['product_id'], true)
        ];
    }

    private function checkValues($config) {
        foreach($config as $key => $value) {
            if (in_array($key, self::$must_be_numbers) && !is_numeric($value)) {
                return false;
            }
        }
        return true;
    }
}

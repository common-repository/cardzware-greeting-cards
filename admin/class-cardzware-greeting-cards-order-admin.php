<?php

class Cardzware_Greeting_Cards_Order_Admin
{
    const PRODUCT_TYPE_CARD = 'card';
    const CANCELLED_STATE = 'Cancelled';
    const CANCELLATION_ERROR_STATE = 'Cancelation error';
    const SUCCESS_STATE = 'Success';
    const SUCCESS_ERROR_STATE = 'Success error';
    const PENDING_PAYMENT_STATE = 'pending_payment';
    const ORDER_STATUS_PROCESSING = 'processing';
    private static array $allowed_html = [
        'b'     => [],
        'br'    => []
    ];

    public static function cw_order_success_state($confirm_order_result, $wc_order) {
        $order_state = self::SUCCESS_ERROR_STATE;
        $confirm_order_result = print_r($confirm_order_result, true);
        if (empty($confirm_order_result)) {
            $msg = wp_kses(__('<b>Cardzware:</b><br>The fulfillment process failed (blank error).', 'cardzware-greeting-cards'), self::$allowed_html);
        } elseif (stripos($confirm_order_result, 'Error')) {
            $msg = sprintf(__('<b>Cardzware:</b><br>The fulfillment process failed for this reason: %s. <br>click "Cardzware Retry Fulfillment" in Order actions', 'cardzware-greeting-cards'), esc_html($confirm_order_result));
        } else {
            $order_state = Cardzware_Greeting_Cards_Order_Admin::SUCCESS_STATE;
            $get_base_url = Cardzware_Greeting_Cards_Config::get_base_url() . '/admin/orders';
            $msg = sprintf(__('<b>Cardzware:</b><br>Fulfillment request sent successfully for order %1$s.<br>You can go <a href="%2$s">here</a> to see more information about the order.', 'cardzware-greeting-cards'), esc_html($wc_order->get_id()), esc_url($get_base_url));
        }

        $wc_order->add_order_note($msg);

        return $order_state;
    }

    /**
     * If the current order has a cardzware product, add two new actions
     * @param $actions
     * @return mixed
     */
    public function wc_custom_order_action( $actions ) {
        $cw_current_order_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_current_order(sanitize_text_field($_GET['post'] ?? ''));
        $show_cancel_option = [self::SUCCESS_STATE];
        $show_retry_option = [self::CANCELLED_STATE, self::CANCELLATION_ERROR_STATE, self::SUCCESS_ERROR_STATE];

        // Check if there are current order products and if the $actions array exists
        if (!is_null($cw_current_order_products) && is_array($actions)) {
            // Show cancel option if the current state is in the $show_cancel_option array
            if (in_array($cw_current_order_products['state'], $show_cancel_option)) {
                $actions['cardzware_fulfillment_cancel'] = esc_html__( 'Cardzware Cancel Fulfillment', 'cardzware-greeting-cards' );
            }
            // Show retry option if the current state is in the $show_retry_option array
            elseif (in_array($cw_current_order_products['state'], $show_retry_option)) {
                $actions['cardzware_fulfillment_retry'] = esc_html__('Cardzware Retry Fulfillment', 'cardzware-greeting-cards');
            }
            // Show confirm fulfillment option if the current state is PENDING_PAYMENT_STATE and the order status is PROCESSING
            else {
                $wc_order = wc_get_order($cw_current_order_products['wc_order_id']);
                if ($cw_current_order_products['state'] == self::PENDING_PAYMENT_STATE && $wc_order->get_status() == self::ORDER_STATUS_PROCESSING) {
                    $actions['cardzware_fulfillment_retry'] = esc_html__('Cardzware Confirm Fulfillment', 'cardzware-greeting-cards');
                }
            }
        }

        return $actions;
    }


    /**
     * Filter name is woocommerce_order_action_{$action_slug}
     */
    function cardzware_fulfillment_cancel( $order ) {
        $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();
        $cw_current_order_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_current_order($order->get_id());

        $cancel_fulfillment_response = $cw_rest_client->cancel_order(
            Cardzware_Greeting_Cards_Config::get_api_url(),
            Cardzware_Greeting_Cards_Config::get_api_key(),
            Cardzware_Greeting_Cards_Config::get_client_id(),
            $cw_current_order_products['cardzwareOrderIds']
        );

        $cancel_fulfillment_response = print_r($cancel_fulfillment_response, true);
        if(empty($cancel_fulfillment_response) || stripos($cancel_fulfillment_response, 'Error') && !stripos($cancel_fulfillment_response, 'cancelled previously')) {
            $msg = sprintf(__('<b>Cardzware:</b><br>%1$s', 'cardzware-greeting-cards'), esc_html($cancel_fulfillment_response));
            $state = self::CANCELLATION_ERROR_STATE;
        } else {
            $msg = wp_kses(__('<b>Cardzware:</b><br>Fulfillment request has been cancelled successfully.<br>If you want to resend a request fulfillment, click on "Cardzware Retry Fulfillment" in Order actions. ', 'cardzware-greeting-cards'), self::$allowed_html);
            $state = self::CANCELLED_STATE;
        }

        $this->change_cw_order_state($state, $cw_current_order_products);
        $order->add_order_note($msg);
    }

    /**
     * Filter name is woocommerce_order_action_{$action_slug}
     */
    function cardzware_fulfillment_retry( $order ) {
        $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();
        $cw_current_order_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_current_order($order->get_id());

        $order_list = $cw_current_order_products['cardzwareOrderIds'];
        $quantity_list = $cw_current_order_products['quantities'];

        $cw_rest_client->send_fulfillment_request(
            $order->get_id(),
            $order_list,
            $quantity_list,
            self::PRODUCT_TYPE_CARD,
            Cardzware_Greeting_Cards_Config::get_api_url(),
            Cardzware_Greeting_Cards_Config::get_api_key(),
            Cardzware_Greeting_Cards_Config::get_client_id()
        );
    }

    private function change_cw_order_state($state, $cw_current_order_products) {
        $cw_all_order_products = Cardzware_Greeting_Cards_Woocommerce::get_all_cw_orders();
        $cw_all_order_products[$cw_current_order_products['wc_order_id']]['state'] = $state;
        Cardzware_Greeting_Cards_Woocommerce::update_all_cw_orders($cw_all_order_products);
    }
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Woocommerce
{
    const CARDZWARE_CART_VALUES = 'cw_cart_values';
    const CARDZWARE_ORDER_VALUES = 'cw_order_values';
    const HASH_LINKS_CART_ORDER = 'cardzware_products';
    const CURRENT_CART_HASH = 'current_cart_hash';

    public static function get_cw_products_current_cart()
    {
        $all_cw_cart_products = self::get_all_cw_products_current_cart();
        $current_cw_cart_products = [];

        $current_cart_hash = WC()->session->get(self::HASH_LINKS_CART_ORDER);
        foreach ($all_cw_cart_products as $cart_item_hash => $value) {
            if ($value[self::CURRENT_CART_HASH] == $current_cart_hash) {
                $current_cw_cart_products[$cart_item_hash] = $value;
            }
        }

        return $current_cw_cart_products;
    }

    public static function get_all_cw_products_current_cart()
    {
        return get_post_meta( Cardzware_Greeting_Cards_Config::get_product_id(), self::CARDZWARE_CART_VALUES, true );
    }

    public static function update_cw_quantity_product_cart( $hash, $quantity )
    {
        $all_cw_cart_products = self::get_all_cw_products_current_cart();
        $all_cw_cart_products[$hash]['quantity'] = $quantity;
        self::update_all_cw_products_current_cart($all_cw_cart_products);
    }

    public static function update_all_cw_products_current_cart( $data )
    {
        update_post_meta( Cardzware_Greeting_Cards_Config::get_product_id(), self::CARDZWARE_CART_VALUES, $data );
    }

    public static function delete_cw_product_current_cart( $cart_item_hash )
    {
        $all_cw_cart_products = self::get_all_cw_products_current_cart();
        if (!empty($all_cw_cart_products)) {
            unset($all_cw_cart_products[$cart_item_hash]);
            Cardzware_Greeting_Cards_Woocommerce::update_all_cw_products_current_cart($all_cw_cart_products);
        }
    }

    public static function delete_all_cw_products_current_cart()
    {
        $all_cw_cart_products = self::get_all_cw_products_current_cart();
        $current_cw_cart_products = self::get_cw_products_current_cart();

        foreach ($current_cw_cart_products as $hash => $value) {
            unset($all_cw_cart_products[$hash]);
        }

        Cardzware_Greeting_Cards_Woocommerce::update_all_cw_products_current_cart($all_cw_cart_products);
    }

    public static function get_cw_current_order( $order_id )
    {
        $all_cw_order_products = self::get_all_cw_orders();
        return $all_cw_order_products[$order_id];
    }

    public static function get_all_cw_orders()
    {
        return get_post_meta( Cardzware_Greeting_Cards_Config::get_product_id(), self::CARDZWARE_ORDER_VALUES, true );
    }

    public static function update_all_cw_orders( $data )
    {
        update_post_meta( Cardzware_Greeting_Cards_Config::get_product_id(), self::CARDZWARE_ORDER_VALUES, $data );
    }

    public static function delete_all_cw_orders() {
        delete_post_meta( Cardzware_Greeting_Cards_Config::get_product_id(), self::CARDZWARE_ORDER_VALUES );
    }

    public static function set_order_ids_and_quantities_in_string($values) {
        $order_ids_string = '';
        $quantities_string = '';

        foreach($values as $value) {
            $order_ids_string .= $value['orderId'] . ',';
            $quantities_string .= $value['quantity'] . ',';
        }

        return [rtrim($order_ids_string, ','), rtrim($quantities_string, ',')];
    }
}

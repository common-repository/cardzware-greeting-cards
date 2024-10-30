<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Product_Actions
{
    private const META_CW_TAG = 'Cardzware';

    public function save_product_id($product_id)
    {
        $product = wc_get_product($product_id);
        if ($product->get_meta(self::META_CW_TAG)) {
            Cardzware_Greeting_Cards_Config::set_product_id($product_id);
        }
    }
}

<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    function cardzware_shipping_init() {
        if ( !class_exists( 'WC_Cardzware_Shipping' ) ) {
            class WC_Cardzware_Shipping extends WC_Shipping_Method {

                const CARDZWARE_SHIPPING = 'cardzware_shipping';
                const PLATFORM_NAME = 'WooCommerce';
                private $product_id;
                private $client_id;
                private $api_key;
                private const CARDZWARE_SHOPIFY_APP_URL_TEST_DEV = 'https://engine-test.cardzware.com';
                private const CARDZWARE_SHOPIFY_APP_URL_LIVE = 'https://engine.cardzware.com';


                /**
                 * Constructor for the shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                       = self::CARDZWARE_SHIPPING;
                    $this->method_title             = __('Cardzware Shipping', 'cardzware-greeting-cards');
                    $this->method_description       = __('This shipping method must be enabled in order to process any Cardzware product purchases.', 'cardzware-greeting-cards'); // Description shown in admin

                    $this->enabled                  = "yes";
                    $this->title                    = "Cardzware Shipping";
                    $this->product_id               = Cardzware_Greeting_Cards_Config::get_product_id();
                    $this->client_id                = Cardzware_Greeting_Cards_Config::get_client_id();
                    $this->api_key                  = Cardzware_Greeting_Cards_Config::get_api_key();

                    $this->init();
                    $this->has_cardzware_products   = false;
                    $this->has_other_products       = false;
                }


                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    //Initialize shipping methods for specific package (or no package)
                    add_filter( 'woocommerce_load_shipping_methods', [$this, 'woocommerce_load_shipping_methods'], 10);
                }


                /**
                 * calculate_shipping function.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package = [] ) {
                    $cardzware_products_quantity = 0;
                    foreach ($package['contents'] as $content) {
                        if($content['product_id'] == $this->product_id) {
                            $cardzware_products_quantity += intval($content['quantity']);
                        }
                    }

                    $customer_country_code = $package['destination']['country'];
                    if ($cardzware_products_quantity == 0 || empty($customer_country_code)) {
                        return;
                    }

                    try {
                        $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();
                        $api_url = Cardzware_Greeting_Cards_Config::get_debug_mode()
                            ? self::CARDZWARE_SHOPIFY_APP_URL_TEST_DEV : self::CARDZWARE_SHOPIFY_APP_URL_LIVE;
                        $get_carrier_service = json_decode($cw_rest_client->get_carrier_service(
                            $api_url,
                            $this->api_key,
                            $this->client_id,
                            $cardzware_products_quantity,
                            $customer_country_code,
                            self::PLATFORM_NAME,
                            get_woocommerce_currency(),
                        ), true);

                        if (!is_array($get_carrier_service) || !isset($get_carrier_service['rates']) || count($get_carrier_service['rates']) === 0) {
                            return;
                        }

                        $get_carrier_service = $get_carrier_service['rates'][0];
                        if ($this->cart_only_has_cardzware_products($package)) {
                            $rateData = [
                                'id' => $this->id . '_' . sanitize_text_field($get_carrier_service['service_name']),
                                'label' => sanitize_text_field($get_carrier_service['description']),
                                'cost' => sanitize_text_field($get_carrier_service['total_price']),
                                'calc_tax' => 'per_order'
                            ];
                            $this->add_rate($rateData);
                        } else {
                            $fee_label = esc_html($get_carrier_service['description']);
                            WC()->cart->add_fee($fee_label, $get_carrier_service['total_price'], true, '');
                        }

                        // Reset class ID after adding rate so ID name does not stack as huge string in foreach
                        $this->id = self::CARDZWARE_SHIPPING;
                    } catch (\Exception $e) {
                        return;
                    }
                }


                /**
                 * Enable only Cardzware shipping method for Cardzware packages
                 * @param array $package
                 */
                public function woocommerce_load_shipping_methods( $package ) {
                    if (isset($package['contents'])) {
                        if ($this->cart_only_has_cardzware_products($package)) {
                            WC()->shipping()->unregister_shipping_methods($this);
                            WC()->shipping()->register_shipping_method($this);
                        }

                        if (empty($package)) {
                            WC()->shipping()->register_shipping_method($this);
                        }
                    }
                }

                private function cart_only_has_cardzware_products($package) :bool {
                    $has_cardzware_products = false;
                    $has_other_products = false;

                    foreach($package['contents'] as $content) {
                        if($content['product_id'] == $this->product_id) {
                            $has_cardzware_products = true;
                        } else {
                            $has_other_products = true;
                        }
                    }
                    return $has_cardzware_products && !$has_other_products;
                }
            }
        }
    }

    add_action( 'woocommerce_shipping_init', 'cardzware_shipping_init' );

    function add_cardzware_shipping_method($methods) {
        $methods['cardzware_shipping'] = 'WC_Cardzware_Shipping';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_cardzware_shipping_method');
}

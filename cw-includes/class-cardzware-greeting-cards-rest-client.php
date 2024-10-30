<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Rest_Client
{
    const REQUEST_TYPE_CARDS = 'card';

    const FUNCTIONS_GET_CARD_CATEGORIES = 'get_menu';
    const FUNCTIONS_GET_CARD_THUMBNAIL = 'get_thumb';
    const FUNCTIONS_GET_OPEN_URL = 'open';
    const FUNCTIONS_CANCEL_ORDER = 'cancel';
    const FUNCTIONS_CONFIRM_ORDER = 'confirm';
    const HTTP_RESPONSE_OK = 200;
    const DELETE_IMAGE_DIRECTORY_IF_OLDER_THAN_SEVENTY_TWO_HOURS = (72 * 60 * 60);
    const LOADING_GIF_URL = 'https://pwcdn.net/images/loading2.gif';

    /**
     * Builds open URL for JS modal window
     *
     * @param $api_url
     * @param $api_key
     * @param $client_id
     * @param $order_id
     * @param $tag
     * @param $client_cat
     * @param $mode
     * @param $type
     * @param $prod_id
     * @param $prod_options
     * @return string
     */
    public function open_url($api_url, $api_key, $client_id, $order_id, $tag, $client_cat = "", $mode = "modal", $type = self::REQUEST_TYPE_CARDS, $prod_id = "", $prod_options = "")
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'order_id' => $order_id,
            'tag' => $tag,
            'client_cat' => $client_cat,
            'mode' => $mode,
            'type' => $type,
            'prod_id' => $prod_id,
            'prod_options' => $prod_options
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_order_id,
            $sanitized_tag,
            $sanitized_client_cat,
            $sanitized_mode,
            $sanitized_type,
            $sanitized_prod_id,
            $sanitized_prod_options
        ] = array_values($sanitized_data);

        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $url = $protocol . sanitize_text_field($_SERVER['SERVER_NAME']);

        $data['c'] = $sanitized_client_id;
        $data['t'] = $sanitized_type;
        $data['f'] = self::FUNCTIONS_GET_OPEN_URL;
        $data['m'] = $sanitized_mode == 'modal' ? '' :  $sanitized_mode;
        $data['o'] = $sanitized_order_id;
        $data['tag'] = $sanitized_tag;
        $data['clientCat'] = $sanitized_client_cat;
        $data['p'] = $sanitized_prod_id;
        $data['ts'] = time();

        $hash_data = $data['c'] . $data['t'] . $data['f'] . $data['m'] . $data['o'] . $data['tag'] . $data['clientCat'] . $data['p'] . $data['ts'];
        $get_data = http_build_query($data);
        $client_hash = hash_hmac('sha256', $hash_data, $sanitized_api_key);
        $escaped_url = esc_url_raw($url);
        return "$sanitized_api_url/open.php?h=$client_hash&$get_data&opt=$sanitized_prod_options&url=" . urlencode($escaped_url);
    }

    /**
     *
     * Confirms orders have been paid for and requests PDFs to be sent via delivery mechanism
     *
     * @param $api_url
     * @param $api_key
     * @param $client_id
     * @param $order_id
     * @param $order_id_string
     * @param $barcode_string
     * @param $type
     * @param $encrypted_data
     * @param $basket_id
     * @param $quantities
     * @param $static_prod_ids
     * @param $static_product_options
     * @return bool|string
     */
    public function confirm_order($api_url, $api_key, $client_id, $order_id, $order_id_string, $barcode_string, $type = self::REQUEST_TYPE_CARDS, $encrypted_data = "", $basket_id = "", $quantities = "", $static_prod_ids = "", $static_product_options = "")
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'order_id' => $order_id,
            'type' => $type,
            'order_id_string' => $order_id_string,
            'barcode_string' => $barcode_string,
            'basket_id' => $basket_id,
            'quantities' => $quantities,
            'static_prod_ids' => $static_prod_ids,
            'static_product_options' => $static_product_options
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_order_id,
            $sanitized_type,
            $sanitized_order_id_string,
            $sanitized_barcode_string,
            $sanitized_basket_id,
            $sanitized_quantities,
            $sanitized_static_prod_ids,
            $sanitized_static_product_options

        ] = array_values($sanitized_data);

        $data['c'] = $sanitized_client_id;
        $data['t'] = $sanitized_type;
        $data['f'] = self::FUNCTIONS_CONFIRM_ORDER;
        $data['o'] = $sanitized_order_id;
        $data['no'] = $sanitized_order_id_string;
        $data['bc'] = $sanitized_barcode_string;
        $data['ed'] = $encrypted_data;
        $data['b'] = $sanitized_basket_id;
        $data['q'] = $sanitized_quantities;
        $data['sp'] = $sanitized_static_prod_ids;
        $data['spo'] = $sanitized_static_product_options;
        $data['ts'] = time();

        return $this->get_printzware_api_response($data, $sanitized_api_key, $sanitized_api_url);
    }

    /**
     * Cancels orders and removes from PW system
     *
     * @param $api_url
     * @param $api_key
     * @param $client_id
     * @param $order_id
     * @param $type
     * @return bool|string
     */
    public function cancel_order($api_url, $api_key, $client_id, $order_id, $type = self::REQUEST_TYPE_CARDS)
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'order_id' => $order_id,
            'type' => $type
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_order_id,
            $sanitized_type
        ] = array_values($sanitized_data);

        $data['c'] = $sanitized_client_id;
        $data['t'] = $sanitized_type;
        $data['f'] = self::FUNCTIONS_CANCEL_ORDER;
        $data['o'] = $sanitized_order_id;
        $data['ts'] = time();

        return $this->get_printzware_api_response($data, $sanitized_api_key, $sanitized_api_url);
    }

    /**
     *
     * Gets the card category menu
     *
     * @param $api_url
     * @param $api_key
     * @param $client_id
     * @param $type
     * @return bool|string
     */
    public function get_categories($api_url, $api_key, $client_id, $type = self::REQUEST_TYPE_CARDS)
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'type' => $type
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_type
        ] = array_values($sanitized_data);

        $data['c'] = $sanitized_client_id;
        $data['t'] = $sanitized_type;
        $data['f'] = self::FUNCTIONS_GET_CARD_CATEGORIES;
        $data['ts'] = time();

        return $this->get_printzware_api_response($data, $sanitized_api_key, $sanitized_api_url);
    }

    /**
     *
     * Returns the thumbnail image
     *
     * @param $api_url
     * @param $api_key
     * @param $client_id
     * @param $order_id
     * @param $type
     * @return bool|string
     */
    public function get_thumb($api_url, $api_key, $client_id, $order_id, $type = self::REQUEST_TYPE_CARDS)
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'order_id' => $order_id,
            'type' => $type
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_order_id,
            $sanitized_type
        ] = array_values($sanitized_data);

        $data['c'] = $sanitized_client_id;
        $data['t'] = $sanitized_type;
        $data['f'] = self::FUNCTIONS_GET_CARD_THUMBNAIL;
        $data['o'] = $sanitized_order_id;
        $data['ts'] = time();

        return $this->get_printzware_api_response($data, $sanitized_api_key, $sanitized_api_url);
    }

    /**
     *
     * The below function is used for dropping it encrypts the customers name, address and postage before sending to PW API
     *
     * @param $api_key
     * @param $SHIP_TO_NAME
     * @param $S_ADDRESS1
     * @param $S_ADDRESS2
     * @param $S_ADDRESS3
     * @param $S_ADDRESS4
     * @param $S_COUNTY
     * @param $S_COUNTRY
     * @param $S_ISO_COUNTRY
     * @param $S_ISO_STATE
     * @param $S_POSTCODE
     * @param $S_POSTAGE
     * @param $S_PHONE
     * @param $S_EMAIL
     * @return string
     */
    public function encrypt_data($api_key, $SHIP_TO_NAME, $S_ADDRESS1, $S_ADDRESS2, $S_ADDRESS3, $S_ADDRESS4, $S_COUNTY, $S_COUNTRY, $S_ISO_COUNTRY, $S_ISO_STATE, $S_POSTCODE, $S_POSTAGE, $S_PHONE, $S_EMAIL, $S_COMPANY_NAME)
    {
        define('AES_256_CBC', 'aes-256-cbc');
        $arr = array(
            'SHIP_TO_NAME'      => $SHIP_TO_NAME,
            'S_ADDRESS1'        => $S_ADDRESS1,
            'S_ADDRESS2'        => $S_ADDRESS2,
            'S_ADDRESS3'        => $S_ADDRESS3,
            'S_ADDRESS4'        => $S_ADDRESS4,
            'S_COUNTY'          => $S_COUNTY,
            'S_COUNTRY'         => $S_COUNTRY,
            'S_ISO_COUNTRY'     => $S_ISO_COUNTRY,
            'S_ISO_STATE'       => $S_ISO_STATE,
            'S_POSTCODE'        => $S_POSTCODE,
            'S_POSTAGE'         => $S_POSTAGE,
            'S_PHONE'           => $S_PHONE,
            'S_EMAIL'           => $S_EMAIL,
            'S_COMPANY_NAME'    => $S_COMPANY_NAME);

        $data = json_encode($arr);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
        $encrypted = openssl_encrypt($data, AES_256_CBC, $api_key, 0, $iv);

        return urlencode($iv . $encrypted);
    }

    /**
     *
     * Search the category JSON object by catID and returns the matching category branch
     *
     * @param $array
     * @param $value
     * @return mixed|null
     */
    public function category_search($array, $cat_id)
    {
        $result = null;
        foreach ($array as $sub_array) {
            if ($sub_array->catID == $cat_id) {
                if (isset($sub_array->subCategories)) {
                    $result = $sub_array->subCategories;
                }
            } else {
                if (($sub_array->hasChildren == 1) && ($result == null)) {
                    $result = $this->category_search($sub_array->subCategories, $cat_id);
                }
            }
        }
        return $result;
    }

    /**
     *
     * Category JSON search wrapper
     *
     * @param $categories
     * @param $catID
     * @return mixed
     */
    public function get_category($categories, $catID)
    {
        $categories = $categories->Categories;
        if ($catID == 0) {
            return $categories;
        } else {
            return $this->category_search($categories, $catID);
        }
    }

    /**
     *
     * Searches the array for the matching catID and returns properties
     *
     * @param $array
     * @param $catID
     * @return array
     */
    public function get_cat_details($array, $catID)
    {
        $result = null;
        foreach ($array as $sub_array) {
            if (($sub_array->catID == $catID)) {
                $result = array();
                $result['catName'] = $sub_array->catName;
                $result['parentID'] = $sub_array->parentID;
                $result['hasChildren'] = $sub_array->hasChildren;
            } else {
                if (($sub_array->hasChildren == 1) && ($result == null)) {
                    $result = $this->get_cat_details($sub_array->subCategories, $catID);
                }
            }
        }
        return $result;
    }

    /**
     *
     * Returns a php array containing the category breadcrumb
     *
     * @param $array
     * @param $catID
     * @return array|void
     */
    public function build_breadcrumb($array, $catID)
    {
        $continue = true;

        while ($continue) {
            if ($catID != 0) {
                $search = $this->get_cat_details($array, $catID);
                $result[] = array("catID" => $catID, "catName" => $search['catName'], "hasChildren" => $search['hasChildren']);
            }
            if (isset($search)) {
                $catID = $search['parentID'];

            } else {
                $catID = null;
            }
            if ($catID == 0) {
                $result[] = array("catID" => 0, "catName" => 'Cards', "hasChildren" => 1);
                return array_reverse($result);
            }

        }
    }

    /**
     *
     * Converts category name to category slug for URL
     *
     * @param $catName
     * @return string
     */
    public function cat_slug($catName)
    {
        return strtolower(str_replace(" ", "-", $catName));
    }

    /**
     *
     * Get the category ID based on the category name
     *
     * @param $array
     * @param $catName
     * @return array
     */
    public function get_category_id($array, $catName)
    {
        $result = null;
        $catName = stripslashes($catName);

        foreach ($array as $sub_array) {
            if ((str_replace("'", '', $sub_array->catName) == $catName)) {

                $result['catID'] = $sub_array->catID;
                $result['hasChildren'] = $sub_array->hasChildren;
            } else {
                if (($sub_array->hasChildren == 1) && ($result == null)) {
                    $result = $this->get_category_id($sub_array->subCategories, $catName);
                }
            }
        }

        return $result;
    }

    public function get_carrier_service($api_url, $api_key, $client_id, $quantity, $country, $platform_name, $store_currency)
    {
        $data_to_sanitize = [
            'api_url' => $api_url,
            'api_key' => $api_key,
            'client_id' => $client_id,
            'quantity' => $quantity,
            'country' => $country,
            'platform_name' => $platform_name,
            'store_currency' => $store_currency
        ];

        $sanitized_data = $this->sanitize_and_validate_data($data_to_sanitize);

        [
            $sanitized_api_url,
            $sanitized_api_key,
            $sanitized_client_id,
            $sanitized_quantity,
            $sanitized_country,
            $sanitized_platform_name,
            $sanitized_store_currency,
        ] = array_values($sanitized_data);

        $data['rate'] = [
            'items' => [
                [
                    'quantity' => $sanitized_quantity,
                ]
            ],
            'destination' => [
                'country' => $sanitized_country
            ],
            'store' => [
                'currency' => $sanitized_store_currency
            ]
        ];

        $data['platform'] = $sanitized_platform_name;
        $data['clientId'] = $sanitized_client_id;

        return $this->get_carrier_service_response($data, $sanitized_api_key, $sanitized_api_url);
    }

    public function send_fulfillment_request( $wc_order_id, $order_list, $quantity_list, $product_type, $api_url, $api_key, $client_id)
    {
        $order_id = $order_list;
        $order_id_string = $order_id;

        $wc_order = wc_get_order($wc_order_id);
        $barcode_string = $wc_order->get_order_number() ?? $wc_order_id;
        $encryptedData = $this->cw_order_shipping($api_key, $wc_order);
        $basket_id = $wc_order_id;
        $quantities = $quantity_list;

        $confirm_order_result = $this->confirm_order($api_url, $api_key, $client_id,
            $order_id, $order_id_string, $barcode_string, $product_type, $encryptedData,
            $basket_id, $quantities);

        $order_state = Cardzware_Greeting_Cards_Order_Admin::cw_order_success_state($confirm_order_result, $wc_order);

        $order_meta_values = Cardzware_Greeting_Cards_Woocommerce::get_all_cw_orders();
        $new_order_meta = [
            'cardzwareOrderIds' => $order_id,
            'quantities' => $quantities,
            'state' => $order_state,
            'wc_order_id' => $wc_order_id
        ];

        $order_meta_values[$wc_order_id] = $new_order_meta;
        Cardzware_Greeting_Cards_Woocommerce::update_all_cw_orders($order_meta_values);
    }

    public function cw_order_shipping( $api_key, $wc_order )
    {
        /* Get the WC order object by the WC order ID */
        $shipping_address = $wc_order->get_address('shipping');
        $billing_address = $wc_order->get_address('billing');

        $first_name = (array_key_exists('first_name', $shipping_address) && !empty($shipping_address['first_name'])) ? $shipping_address['first_name'] : $billing_address['first_name'];
        $last_name = (array_key_exists('last_name', $shipping_address) && !empty($shipping_address['last_name'])) ? $shipping_address['last_name'] : $billing_address['last_name'];
        $ship_to_name = $first_name . ' ' . $last_name;
        $address_1 = (array_key_exists('address_1', $shipping_address) && !empty($shipping_address['address_1'])) ? $shipping_address['address_1'] : $billing_address['address_1'];
        $address_2 = (array_key_exists('address_2', $shipping_address) && !empty($shipping_address['address_2'])) ? $shipping_address['address_2'] : $billing_address['address_2'];
        $address_3 = (array_key_exists('city', $shipping_address) && !empty($shipping_address['city'])) ? $shipping_address['city'] : $billing_address['city'];
        $address_4 = '';
        $country = '';
        $iso_country = (array_key_exists('country', $shipping_address) && !empty($shipping_address['country'])) ? $shipping_address['country'] : $billing_address['country'];
        $postcode = (array_key_exists('postcode', $shipping_address) && !empty($shipping_address['postcode'])) ? $shipping_address['postcode'] : $billing_address['postcode'];
        $postage = '';
        $company_name = (array_key_exists('company', $shipping_address) && !empty($shipping_address['company'])) ? $shipping_address['company'] : $billing_address['company'];

        // Added two additional fields EMAIL and PHONE
        $phone = $billing_address['phone'];
        $email = $billing_address['email'];

        // Pass the address fields and create the encrypted string for pw system
        if ($iso_country == "US" || $iso_country == "CA") {
            $iso_state = (array_key_exists('state', $shipping_address) && !empty($shipping_address['state'])) ? $shipping_address['state'] : $billing_address['state'];
            $county = '';
        } else{
            $iso_state = '';
            $county = (array_key_exists('state', $shipping_address) && !empty($shipping_address['state'])) ? $shipping_address['state'] : $billing_address['state'];
        }

        return $this->encrypt_data(
            $api_key,
            $ship_to_name,
            $address_1,
            $address_2,
            $address_3,
            $address_4,
            $county,
            $country,
            $iso_country,
            $iso_state,
            $postcode,
            $postage,
            $phone,
            $email,
            $company_name
        );
    }

    public function thumbnail_url_works($url)
    {
        $response = wp_remote_head($url);

        if (is_wp_error($response)) {
            return false;
        }

        $http_code = wp_remote_retrieve_response_code($response);
        return $http_code == self::HTTP_RESPONSE_OK;
    }

    function download_and_save_images($categories_tiles, $additional_images=[]) {
        $this->delete_image_directory();

        $image_directory = plugin_dir_path(dirname(__FILE__)) . 'public/views/images/';

        if (!file_exists($image_directory)) {
            wp_mkdir_p($image_directory);
        }

        $all_images = array_merge($categories_tiles, $additional_images);

        foreach($all_images as $image_info) {
            if (is_object($image_info)) {
                $image_filename = $image_info->catID . '.png';
                $image_url = 'https://pwcdn.net/cat_tiles/' . $image_filename;
            } else {
                $image_url = $image_info;
                $path_parts = pathinfo($image_url);
                $image_filename = $path_parts['basename'];
            }

            if (!file_exists($image_directory . $image_filename)) {
                $response = wp_remote_get($image_url, ['timeout' => 300]);

                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == self::HTTP_RESPONSE_OK) {
                    $image_data = wp_remote_retrieve_body($response);
                    file_put_contents($image_directory . $image_filename, $image_data);
                }
            }
        }
    }

    private function delete_image_directory() {
        $image_directory = plugin_dir_path(dirname(__FILE__)) . 'public/views/images/';

        if (is_dir($image_directory)) {
            $dir_mtime = filemtime($image_directory);

            if (time() - $dir_mtime >= self::DELETE_IMAGE_DIRECTORY_IF_OLDER_THAN_SEVENTY_TWO_HOURS) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($image_directory, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach($files as $file_info) {
                    if ($file_info->isDir()) {
                        rmdir($file_info->getRealPath());
                    } else {
                        unlink($file_info->getRealPath());
                    }
                }

                rmdir($image_directory);
            }
        }
    }

    private function sanitize_and_validate_data($data)
    {
        $sanitized_data = [];

        $fields = [
            'api_url'                => FILTER_SANITIZE_URL,
            'api_key'                => FILTER_SANITIZE_URL,
            'client_id'              => 'text',
            'order_id'               => 'text',
            'tag'                    => 'text',
            'client_cat'             => 'text',
            'mode'                   => 'text',
            'type'                   => 'text',
            'prod_id'                => 'text',
            'prod_options'           => 'text',
            'order_id_string'        => 'text',
            'barcode_string'         => 'text',
            'basket_id'              => 'text',
            'quantities'             => 'text',
            'static_prod_ids'        => 'text',
            'static_product_options' => 'text',
            'quantity'               => 'text',
            'country'                => 'text',
            'platform_name'          => 'text',
            'store_currency'         => 'text'
        ];

        foreach ($fields as $field => $sanitization) {
            if (isset($data[$field])) {
                if ($sanitization === 'text') {
                    $sanitized_data[$field] = sanitize_text_field($data[$field]);
                } elseif (filter_var($data[$field], $sanitization)) {
                    $sanitized_data[$field] = $data[$field];
                } else {
                    // Handle invalid data
                }
            }
        }

        return $sanitized_data;
    }

    private function get_printzware_api_response($data, $sanitized_api_key, $sanitized_api_url)
    {
        $post_data = http_build_query($data);
        $client_hash = hash_hmac('sha256', $post_data, $sanitized_api_key);

        $args = [
            'body' => $post_data,
            'headers' => [
                'clientID'    => $data['c'],
                'clientHash'  => $client_hash
            ],
            'sslverify' => true // Optional, but recomended
        ];

        $response = wp_remote_post("$sanitized_api_url/api.php", $args);

        if (is_wp_error($response)) {
            $result = json_encode(['error' => $response->get_error_message()]);
        } else {
            $result = wp_remote_retrieve_body($response);
        }

        return $result;
    }

    private function get_carrier_service_response($data, $sanitized_api_key, $sanitized_api_url)
    {
        $post_data = http_build_query($data);
        $client_hash = hash_hmac('sha256', $post_data, $sanitized_api_key);

        $args = [
            'body' => $post_data,
            'headers' => [
                'clientID'    => $data['clientId'],
                'clientHash'  => $client_hash
            ],
            'sslverify' => true // Optional, but recomended
        ];

        $response = wp_remote_post("$sanitized_api_url/carrier_service", $args);

        if (is_wp_error($response)) {
            $result = json_encode(['error' => $response->get_error_message()]);
        } else {
            $result = wp_remote_retrieve_body($response);
        }

        return $result;
    }

}

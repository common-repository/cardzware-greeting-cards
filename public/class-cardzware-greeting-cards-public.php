<?php
require_once plugin_dir_path(__FILE__) . '../cw-includes/class-cardzware-greeting-cards-rest-client.php';
require_once plugin_dir_path( __FILE__ ) . '../cw-includes/class-cardzware-greeting-cards-config.php';

defined('DONOTCACHEPAGE') or define('DONOTCACHEPAGE', true);

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/public
 * @author     Printzware <hello@cardzware.com>
 */
class Cardzware_Greeting_Cards_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $client_id;
    private $cardzware_product_id;
    private $api_url;
    private $api_key;
    private $page_slug;
    private $iframe_id;
    private $modal_id;
    private $cw_cart_button_text;
    private const PRODUCT_TYPE_CARD = 'card';
    private const GBL_TEXT = 'gbl.';
    private const GBL_TEST_TEXT = 'gbl-test.';
    private const CARD_BUTTON_ID = 'cardzware_card_button';
    private const ADD_CARD_CLASS_BUTTON = 'cw-add-card-cart-button';
    private const UPDATE_BASKET_BUTTON_NAME = 'update_cart';

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->client_id = Cardzware_Greeting_Cards_Config::get_client_id();
        $this->cardzware_product_id = Cardzware_Greeting_Cards_Config::get_product_id();
        $this->api_url = Cardzware_Greeting_Cards_Config::get_api_url();
        $this->api_key = Cardzware_Greeting_Cards_Config::get_api_key();
        $this->page_slug = Cardzware_Greeting_Cards_Config::get_page_slug();
        $this->iframe_id = Cardzware_Greeting_Cards_Config::get_iframe_id();
        $this->modal_id = Cardzware_Greeting_Cards_Config::get_modal_id();
        $this->cw_cart_button_text = Cardzware_Greeting_Cards_Config::get_cart_button_text();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cardzware_Greeting_Cards_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cardzware_Greeting_Cards_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style('cw', plugin_dir_url(__FILE__) . 'css/cw.css', array(), $this->version, 'all');
        wp_enqueue_style('custom_cardzware', plugin_dir_url(__FILE__) . 'css/custom_cardzware.css', array(), $this->version, 'all');
        $this->add_cardzware_branding_styles();
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cardzware_Greeting_Cards_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cardzware_Greeting_Cards_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('cardzware-greeting-cards-functions', plugin_dir_url(__FILE__) . 'js/cardzware-greeting-cards-functions.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/cardzware-greeting-cards-public.js', array('jquery'), $this->version, true);
        wp_enqueue_script('cardzware-greeting-cards-carts', plugin_dir_url(__FILE__) . 'js/cardzware-greeting-cards-carts.js', array('jquery'), $this->version, true);

        /*
         * Ajax Calls
         */
        wp_localize_script( $this->plugin_name, 'ajax_var', [
            'url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'my-ajax-nonce' ),
            'action' => 'update_product_meta_values'
        ] );

        wp_localize_script( $this->plugin_name, 'ajax_call', [
            'url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'my-ajax-nonce' ),
            'action' => 'get_values_js_variables'
        ] );

        add_action('wp_enqueue_scripts', $this->plugin_name);
    }

    public function add_cardzware_branding_styles() {

        $branding = Cardzware_Greeting_Cards_Config::get_branding();

        $css = "
            .pw_category_block{
                padding-right: 0;
                padding-left: 0;
                display: inline-block;
                width: " . esc_attr( $branding['tiles_size'] ) . "px;
                height: " . esc_attr( $branding['tiles_size'] ) . "px;
                position: relative;
                margin: 0 11px 11px 0;
                overflow: hidden;
                border: 1px solid #d4cdd5;
                background-color: " . esc_attr( $branding['tiles_bg_color'] ) . ";
            }
    
            .pw_category_block_title {
                width: 100%;
                position: absolute;
                display: block;
                height: 50px;
                background: " . esc_attr( $branding['tiles_color'] ) . ";
                font-family: arial;
                font-weight: bold;
                color: #FFF;
                font-size: 20px;
                text-align: center;
                -webkit-text-stroke-width: 0;
                line-height: 50px;
                bottom: 0;
            }
    
            .pw_category_block img {
                width: 100%;
                height: auto;
                margin-top: 0;
                transition: all .5s ease;
            }
    
            .pw_category_block:hover img {
                transition: all 1s ease;
                transform:scale(1.25);
            }
    
            .pw_category_block:hover .pw_category_block_title {
                background: " . esc_attr( $branding['tiles_hover_color'] ) . ";
            }
    
            .pw_breadcrumb {
                list-style: none !important;
                overflow: hidden;
                font-size: 18px;
            }
    
            .pw_breadcrumb li {
                float: left;
                margin-bottom: 0;
            }
    
            .pw_breadcrumb li a,
            .pw_breadcrumb li span {
                color: " . esc_attr( $branding['bc_color'] ) . ";
                text-decoration: none;
                padding: 5px 0 5px 0.8rem;
                position: relative;
                display: block;
                float: left;
            }
    
            .pw_breadcrumb li:first-child a {
                padding-left: 15px;
            }
    
            .pw_breadcrumb li a:hover {
                color: " . esc_attr( $branding['bc_hover_color'] ) . ";
             }
        ";

        wp_add_inline_style('custom_cardzware', $css);
    }

    public function create_cardzware_categories_page() {
        $page_title = 'Personalised Greeting Cards';
        $new_page = array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_content' => '[cw-cards-iframe]',
            'post_status' => 'draft',
            'comment_status' => 'closed',
            'post_author' => 1,
            'post_name' => $this->page_slug
        );
        if (!get_page_by_path($this->page_slug, OBJECT, 'page')) {
            wp_insert_post($new_page);
        }
    }

    public function register_shortcodes()
    {
        function cw_cards_iframe($atts) {
            ob_start();
            include_once( plugin_dir_path(__FILE__) . 'views/cardzware-greeting-cards-public-display.php' );
            return ob_get_clean();
        }
        add_shortcode('cw-cards-iframe', 'cw_cards_iframe');
    }

    public function deregister_shortcodes() {
        remove_shortcode('cw-cards-iframe');
    }

    public function add_greeting_card_category_rewrite_rule()
    {
        $post = get_page_by_path($this->page_slug, OBJECT, 'page');
        $post_slug = $post->post_name;

        add_rewrite_tag('%category_slug%', '([^&]+)');
        add_rewrite_rule(
            '(.*/)?' . $post_slug . '/a/([^&]+)/?$',
            'index.php?pagename=' . $post_slug . '&category_slug=$matches[1]',
            'top'
        );

        add_rewrite_tag('%category_path%', '([^&]+)');
        add_rewrite_rule(
            '(.*/)?' . $post_slug . '/a/([^&]+)/([^&]+)/?$',
            'index.php?pagename=' . $post_slug . '&category_slug=$matches[1]&category_path=$matches[2]',
            'top'
        );

        flush_rewrite_rules();
    }

    public function add_greeting_card_category_query_var($query_vars)
    {
        $query_vars[] = 'category_slug';
        $query_vars[] = 'category_path';
        return $query_vars;
    }

    public function cw_show_cart_product_personalize( $item_name, $cart_item, $cart_item_key )
    {
        if ($cart_item['product_id'] != $this->cardzware_product_id) {
            return esc_html($item_name);
        }

        if (!is_cart()) {
            return '<span class="cart-item-name">' . esc_html($item_name) . ' </span>';
        } else {
            $current_product_id = $cart_item['product_id'];
            $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();

            $cardzware_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_products_current_cart();
            if (!array_key_exists($cart_item_key, $cardzware_products)) {
                return esc_html($item_name) . ' (old, please remove it)';
            }

            $order_id = $cardzware_products[$cart_item_key]['orderId'];
            $cw_rest_client->download_and_save_images([], [$cw_rest_client::LOADING_GIF_URL]);
            $printzware_open_url = $cw_rest_client->open_url($this->api_url, $this->api_key, $this->client_id, $order_id, '', '', 'modal', 'edit', $current_product_id, '');
            return $this->generate_cart_product_personalize_html($item_name, $order_id, $printzware_open_url, $this->iframe_id, $this->modal_id);
        }
    }

    public function cw_update_quantity_product($cart_item_key, $quantity, $old_quantity, $that)
    {
        Cardzware_Greeting_Cards_Woocommerce::update_cw_quantity_product_cart($cart_item_key, $quantity);
    }

    public function cw_custom_thumbnail_cart_product( $thumbnail, $cart_item )
    {
        if ($cart_item['product_id'] != $this->cardzware_product_id) {
            return $thumbnail;
        }

        $cardzware_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_products_current_cart();
        if (!array_key_exists($cart_item['key'], $cardzware_products)) {
            return $thumbnail;
        }

        $class = 'attachment-shop_thumbnail wp-post-image'; // Default cart thumbnail class.
        $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();
        $current_cart_product = $cardzware_products[$cart_item['key']];

        if ($cw_rest_client->thumbnail_url_works($current_cart_product['thumbnailUrl'])) {
            $thumbnail_url = $current_cart_product['thumbnailUrl'];
        } else {
            $thumbnail_url = $current_cart_product['viewThumbnailUrl'];
        }
        $thumbnail_url .= '?r=' . random_int(10000, 9999999);
        return '<img src="' . $thumbnail_url . '" class="' . $class . '"/>';
    }

    public function cw_split_product_individual_cart_items( $cart_item_data, $product_id )
    {
        $cart_item_data['unique_key'] = uniqid();
        return $cart_item_data;
    }

    public function cw_remove_post_meta_cart_item( $cart_item_key, $instance )
    {
        Cardzware_Greeting_Cards_Woocommerce::delete_cw_product_current_cart($cart_item_key);
    }

    public function cw_add_order_item_meta( $item_id, $values )
    {
        $cardzware_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_products_current_cart();

        foreach ($cardzware_products as $key => $value) {
            if ($values['key'] == $key) {
                wc_update_order_item_meta($item_id, 'PersonalisationID', $value['orderId']);
                break;
            }
        }
    }

    public function cw_checkout_remove_all_post_meta_product( $wc_order_id ) {
        $cardzware_products = Cardzware_Greeting_Cards_Woocommerce::get_cw_products_current_cart();

        if (!empty($cardzware_products)) {
            list($order_list, $quantity_list) = Cardzware_Greeting_Cards_Woocommerce::set_order_ids_and_quantities_in_string($cardzware_products);

            $cw_all_order_products = Cardzware_Greeting_Cards_Woocommerce::get_all_cw_orders();
            if (empty($cw_all_order_products)) {
                $cw_all_order_products = [];
            }

            $new_cardzware_order = [
                'cardzwareOrderIds' => $order_list,
                'quantities' => $quantity_list,
                'state' => Cardzware_Greeting_Cards_Order_Admin::PENDING_PAYMENT_STATE,
                'wc_order_id' => $wc_order_id
            ];

            $cw_all_order_products[$wc_order_id] = $new_cardzware_order;
            Cardzware_Greeting_Cards_Woocommerce::update_all_cw_orders($cw_all_order_products);

            $order = wc_get_order($wc_order_id);
            if ($order->get_status() == Cardzware_Greeting_Cards_Order_Admin::ORDER_STATUS_PROCESSING) {
                $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();

                if (!empty($order_list) && !empty($quantity_list)) {
                    $cw_rest_client->send_fulfillment_request($wc_order_id, $order_list, $quantity_list,
                        self::PRODUCT_TYPE_CARD, $this->api_url, $this->api_key, $this->client_id);
                }
            } else {
                $allowed_html = [
                    'b'     => [],
                    'br'    => []
                ];
                $order->add_order_note(wp_kses(__('<b>Cardzware:</b><br>When you receive the payment and change the order status to "processing", click on "Cardzware Confirm Fulfillment" in Order actions.', 'cardzware-greeting-cards'), $allowed_html));
            }

            Cardzware_Greeting_Cards_Woocommerce::delete_all_cw_products_current_cart();
        }
    }

    public function custom_cart_button() {
        if (Cardzware_Greeting_Cards_Config::get_show_cart_button()) {
            $cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();
            $order_id = time() . mt_rand(100, 999);
            $cardzware_open_url = $cw_rest_client->open_url($this->api_url, $this->api_key, $this->client_id, $order_id, '', '', 'modal');

            return $this->generate_cardzware_cart_product_button_html($cardzware_open_url);
        }
        return '';
    }

    public function update_product_meta_values() {
        $product_id = sanitize_text_field($_POST['productId']);
        $order_id = sanitize_text_field($_POST['orderId']);
        $thumb_url = sanitize_text_field($_POST['thumbUrl']);

        if (Cardzware_Greeting_Cards_Config::get_debug_mode()) {
            $thumb_url = str_replace(self::GBL_TEXT, self::GBL_TEST_TEXT, $thumb_url);
        }

        if ($product_id != $this->cardzware_product_id) { return; }

        $cardzware_products = Cardzware_Greeting_Cards_Woocommerce::get_all_cw_products_current_cart();
        if (empty($cardzware_products)) {
            $cardzware_products = [];
        }

        $key = '';
        foreach (WC()->cart->get_cart() as $data_hash_product => $cart_item) {
            if (!array_key_exists($data_hash_product, $cardzware_products) && ($this->cardzware_product_id == $product_id)
            && ($cart_item['product_id'] == $this->cardzware_product_id)) {
                $key = $data_hash_product;
                break;
            }
        }

        $current_cart_hash = WC()->session->get(Cardzware_Greeting_Cards_Woocommerce::HASH_LINKS_CART_ORDER);
        if (is_null($current_cart_hash)) {
            $current_cart_hash = hash('sha256', $order_id);
            WC()->session->set(Cardzware_Greeting_Cards_Woocommerce::HASH_LINKS_CART_ORDER, $current_cart_hash);
        }

        $thumbnail_cart = 'https://pwcdn.net/thumbnails/' . $this->client_id . '/' . $order_id . '.jpg';
        $cardzware_products[$key] = [
            'thumbnailUrl'                                  => $thumbnail_cart,
            'viewThumbnailUrl'                              => $thumb_url,
            'orderId'                                       => $order_id,
            'quantity'                                      => 1,
            Cardzware_Greeting_Cards_Woocommerce::CURRENT_CART_HASH => $current_cart_hash
        ];

        Cardzware_Greeting_Cards_Woocommerce::update_all_cw_products_current_cart($cardzware_products);
    }

    public function get_values_js_variables()
    {
        $cardzware_greeting_cards = new Cardzware_Greeting_Cards();
        return wp_send_json([
            'productId'         => esc_js( $this->cardzware_product_id ),
            'iframeId'          => esc_js( $this->iframe_id ),
            'modalId'           => esc_js( $this->modal_id ),
            'apiUrl'            => esc_url( $this->api_url ),
            'cwPluginVersion'   => esc_js( $cardzware_greeting_cards->get_version() )
        ]);
    }

    private function generate_cart_product_personalize_html($item_name, $order_id, $printzware_open_url, $iframe_id, $modal_id)
    {
        return sprintf('<span class="cart-item-name">%s</span>
                 <p class="lead">
				 	<span>PersonalisationID: %s</span>

                    <a href="#" data-openurl="%s" data-iframeid="%s" data-modalid="%s" class="btn btn-default btn-xs card-edit-order-link" role="button">Edit</a>
                </p>',
            esc_html($item_name),
            esc_html($order_id),
            esc_url($printzware_open_url),
            esc_js($iframe_id),
            esc_js($modal_id)
        );
    }

    private function generate_cardzware_cart_product_button_html($cardzware_open_url)
    {
        return sprintf("<button type='button' class='%s' id='%s' onclick='window.openDialog(\"%s\", \"%s\", \"%s\")'>%s</button>
            <script>
                let update_basket_button = document.querySelector('button[name=\"%s\"]');
                if (update_basket_button) {
                    let cardzware_card_button = document.getElementById(\"%s\");
                    cardzware_card_button.classList.remove(\"%s\");
                    let update_basket_classes = update_basket_button.classList.value.split(' ');
                    for (let i = 0; i < update_basket_classes.length; i++) {
                        cardzware_card_button.classList.add(update_basket_classes[i]);
                    }
                }
            </script>",
            self::ADD_CARD_CLASS_BUTTON,
            self::CARD_BUTTON_ID,
            esc_url($cardzware_open_url),
            esc_html($this->iframe_id),
            esc_html($this->modal_id),
            esc_html($this->cw_cart_button_text),
            esc_js( self::UPDATE_BASKET_BUTTON_NAME),
            self::CARD_BUTTON_ID,
            esc_js(self::ADD_CARD_CLASS_BUTTON),
        );
    }
}


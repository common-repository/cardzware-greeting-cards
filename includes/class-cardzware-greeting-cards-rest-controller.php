<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Rest_Controller extends WP_REST_Controller
{
    protected $namespace = 'wc/v3';
    protected $rest_base = 'cardzware';

    private const SAVE_SETTINGS = 'settings';
    private const UPDATE_API_URL = 'api-url';
    private const UPDATE_BRANDING = 'branding';
    private const UPDATE_SEO = 'seo';
    private const DATA_SYNC = 'data-sync';
    private const SETTINGS_SYNC = 'settings-sync';
    private const CHECK_SYNC_NEEDED = 'check-sync';

    /**
     * Register Cardzware REST endpoints
     *
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, $this->url(self::SAVE_SETTINGS), [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'save_cardzware_settings' ],
                'permission_callback' => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'       => false
            ]
        ]);

        register_rest_route($this->namespace,  $this->url(self::UPDATE_API_URL), [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'update_cardzware_api_url' ],
                'permission_callback' => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'       => false
            ]
        ]);

        register_rest_route($this->namespace, $this->url(self::UPDATE_BRANDING), [
           [
               'methods'              => WP_REST_Server::CREATABLE,
               'callback'             => [ $this, 'update_cardzware_branding' ],
               'permission_callback'  => [ $this, 'save_cardzware_settings_permissions_check' ],
               'show_in_index'        => false
           ]
        ]);

        register_rest_route($this->namespace, $this->url(self::UPDATE_SEO), [
            [
                'methods'              => WP_REST_Server::CREATABLE,
                'callback'             => [ $this, 'update_cardzware_seo' ],
                'permission_callback'  => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'        => false
            ]
        ]);

        register_rest_route($this->namespace, $this->url(self::DATA_SYNC), [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'sync_cardzware_data' ],
                'permission_callback' => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'       => false
            ]
        ]);

        register_rest_route($this->namespace, $this->url(self::SETTINGS_SYNC), [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'sync_cardzware_settings' ],
                'permission_callback' => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'       => false
            ]
        ]);

        register_rest_route($this->namespace, $this->url(self::CHECK_SYNC_NEEDED), [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [ $this, 'check_cardzware_sync_needed' ],
                'permission_callback' => [ $this, 'save_cardzware_settings_permissions_check' ],
                'show_in_index'       => false
            ]
        ]);
    }

    /**
     * Save Cardzware settings in config
     *
     * @param $request
     * @return array|bool[]
     */
    public function save_cardzware_settings($request)
    {
        if (!Cardzware_Greeting_Cards_Save_Settings_Validator::is_valid($request)) {
            return [
                'success' => false,
                'errors' => 'Invalid settings received'
            ];
        }

        Cardzware_Greeting_Cards_Config::set_api_key($request['api_key']);
        Cardzware_Greeting_Cards_Config::set_client_id($request['client_id']);
        Cardzware_Greeting_Cards_Config::set_api_url($request['api_url']);

        Cardzware_Greeting_Cards_Config::set_page_view('dashboard');

        return ['success' => true, 'currency' => get_woocommerce_currency() ];
    }

    /**
     * Updates Cardzware api_url in settings
     *
     * @param $request
     * @return array|bool[]
     */
    public function update_cardzware_api_url($request)
    {
        if (!Cardzware_Greeting_Cards_Update_Api_Url_Validator::is_valid($request)) {
            return [
                'success' => false,
                'errors' => 'Invalid api url received'
            ];
        }

        Cardzware_Greeting_Cards_Config::set_api_url($request['api_url']);

        return ['success' => true];
    }

    /**
     * Updates Cardzware branding
     *
     * @param $request
     * @return array|bool[]
     */
    public function update_cardzware_branding($request)
    {
        if (!Cardzware_Greeting_Cards_Update_Branding_Validator::is_valid($request)) {
            return [
                'success'   => false,
                'errors'    => 'Invalid branding configuration received'
            ];
        }

        Cardzware_Greeting_Cards_Config::set_branding($request[Cardzware_Greeting_Cards_Update_Branding_Validator::BRANDING_KEY]);

        return ['success' => true];
    }

    /**
     * Updates Cardzware seo
     *
     * @param $request
     * @return array|bool[]
     */
    public function update_cardzware_seo($request): array
    {
        if (!Cardzware_Greeting_Cards_Update_Seo_Validator::is_valid($request)) {
            return [
                'success'   => false,
                'errors'    => 'Invalid SEO details received'
            ];
        }

        Cardzware_Greeting_Cards_Config::set_seo_for_category($request[Cardzware_Greeting_Cards_Update_Seo_Validator::SEO_KEY]);

        return ['success' => true];
    }

    /**
     * Check if Cardzware data has changed and needs syncing
     *
     * @param $request
     * @return array|bool[]
     */
    public function check_cardzware_sync_needed($request)
    {
        $needsSyncing = false;
        if ($request['checksum'] !== md5(Cardzware_Greeting_Cards_Config::get_all_data())) {
            $needsSyncing = true;
        }

        return ['needs_syncing' => $needsSyncing];
    }

    /**
     * Sync all Cardzware data
     *
     * @param $request
     * @return array|bool[]
     */
    public function sync_cardzware_data($request)
    {
        Cardzware_Greeting_Cards_Config::set_all_data($request->get_params());

        return ['success' => true];
    }

    /**
     * Sync all Cardzware settings
     *
     * @param $request
     * @return true[]
     */
    public function sync_cardzware_settings($request)
    {
        Cardzware_Greeting_Cards_Config::set_all_settings($request->get_params());

        return ['success' => true];
    }

    /**
     * Check whether a given request has permission to save Cardzware settings.
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function save_cardzware_settings_permissions_check($request)
    {
        if (!function_exists('wc_rest_check_user_permissions') || !wc_rest_check_user_permissions('read')) {
            return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce-rest-api' ), array( 'status' => rest_authorization_required_code() ) );
        }
        return true;
    }

    private function url($path) {
        return "/$this->rest_base/".$path;
    }
}

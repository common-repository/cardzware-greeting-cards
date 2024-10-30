<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Cardzware_Greeting_Cards_Install_Checker
{
    private const REQUIREMENT_VERSION_PHP = '7.3.0';
    private const REQUIREMENT_VERSION_WORDPRESS = '6.1.0';
    private const MINIMUM_MEMORY_LIMIT_ACCEPTED_MB = 128;
    private const MINIMUM_PHP_TIME_EXECUTION_SECONDS = 30;
    private const REQUIREMENT_VERSION_WOOCOMMERCE = '7.1.0';

    public static function has_valid_install_requirements(): bool
    {
        $requirements_fulfilled = true;
        $requirements = self::get_install_requirements_list();
        foreach ($requirements as $requirement) {
            if (!$requirement['success']) {
                $requirements_fulfilled = false;
            }
        }
        return $requirements_fulfilled;
    }

    public static function get_install_requirements_list(): array
    {
        return [
            [
                'name'                => esc_html__( 'PHP version', 'cardzware-greeting-cards' ),
                'description-fail'    => sprintf(esc_html__( 'Your PHP version is insufficient (%1$s). The minimum PHP version for this plugin is %2$s. 
                                            Contact your hosting provider if you need help with this.', 'cardzware-greeting-cards' ), phpversion(), self::REQUIREMENT_VERSION_PHP),
                'description-success' => sprintf(esc_html__( 'Your PHP version is ok (%s).', 'cardzware-greeting-cards' ), phpversion()),
                'success'             => self::php_version_superior_than(self::REQUIREMENT_VERSION_PHP)
            ],
            [
                'name'                => esc_html__( 'WordPress version', 'cardzware-greeting-cards' ),
                'description-fail'    => sprintf(esc_html__( 'WordPress should always be updated to the latest version. Updates can be installed from your WordPress admin dashboard (Dashboard -> Updates).
                                                Now your version is (%s). Minimum required version (%s)', 'cardzware-greeting-cards' ), self::get_content_management_system_version(), self::REQUIREMENT_VERSION_WORDPRESS),
                'description-success' => sprintf(esc_html__( 'Your WordPress version is ok (%s).', 'cardzware-greeting-cards' ), self::get_content_management_system_version()),
                'success'             => version_compare(self::get_content_management_system_version(), self::REQUIREMENT_VERSION_WORDPRESS, 'ge')
            ],
            [
                'name'                => esc_html__( 'Woocommerce active', 'cardzware-greeting-cards' ),
                'description-fail'    => esc_html__( 'Woocommerce need to be active for use this plugin. Please, go to plugins and active it.', 'cardzware-greeting-cards' ),
                'description-success' => esc_html__( 'Woocommerce is active', 'cardzware-greeting-cards' ),
                'success'             => self::is_woocommerce_active()
            ],
            [
                'name'                => esc_html__( 'Woocommerce version', 'cardzware-greeting-cards' ),
                'description-fail'    => sprintf(wp_kses( __('Your version of Woocommerce is insufficient (%1$s). The minimum version is %2$s. '.
                                            'Please, check the link <a href="https://woocommerce.com/document/how-to-update-woocommerce/?quid=2e2e8cc25a43890c25a4cfbd34358444">Update woocommerce</a>.', 'cardzware-greeting-cards' ),
                    [
                        'a' => [
                            'href' => [],
                            'target' => []
                        ],
                    ]),
                    self::get_woocommerce_version(), self::REQUIREMENT_VERSION_WOOCOMMERCE),
                'description-success' => sprintf(esc_html__( 'Your version of Woocommerce is ok (%s)', 'cardzware-greeting-cards' ), self::get_woocommerce_version()),
                'success'             => version_compare(self::get_woocommerce_version(), self::REQUIREMENT_VERSION_WOOCOMMERCE, '>=')
            ],
            [
                'name'                => esc_html__( 'PHP memory limit', 'cardzware-greeting-cards' ),
                'description-fail'    => sprintf(esc_html__( 'Please extend your PHP variable "memory_limit" the minimum recommended is %s MB. 
                                                Contact your hosting provider if you need help with this.', 'cardzware-greeting-cards' ), self::MINIMUM_MEMORY_LIMIT_ACCEPTED_MB),
                'description-success' => sprintf(esc_html__( 'Your PHP variable "memory_limit" is ok (≥ %s MB).', 'cardzware-greeting-cards' ), self::MINIMUM_MEMORY_LIMIT_ACCEPTED_MB),
                'success'             => self::is_memory_limit_superior_than(self::MINIMUM_MEMORY_LIMIT_ACCEPTED_MB)
            ],
            [
                'name'                => esc_html__( 'PHP script time limit', 'cardzware-greeting-cards' ),
                'description-fail'    => sprintf(esc_html__( 'Please extend your PHP variable "time_execution" the minimum recommended is $s seconds. 
                                                Contact your hosting provider if you need help with this.', 'cardzware-greeting-cards' ), self::MINIMUM_PHP_TIME_EXECUTION_SECONDS),
                'description-success' => sprintf(esc_html__( 'Your PHP variable "time_execution" is ok (≥ %s seconds).', 'cardzware-greeting-cards' ), self::MINIMUM_PHP_TIME_EXECUTION_SECONDS),
                'success'             => self::is_time_execution_superior_than(self::MINIMUM_PHP_TIME_EXECUTION_SECONDS)
            ],
            [
                'name'                => esc_html__( 'Permalinks', 'cardzware-greeting-cards' ),
                'description-fail'    => esc_html__( 'Cardzware will not work as your Permalinks setting is set to "Plain".
                                                 Please select another setting via Settings > Permalinks.', 'cardzware-greeting-cards' ),
                'description-success' => esc_html__( 'Wordpress permalinks is active.', 'cardzware-greeting-cards' ),
                'success'             => self::is_perma_links_active()
            ]
        ];
    }

    private static function is_woocommerce_active()
    {
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        return is_plugin_active( 'woocommerce/woocommerce.php' );
    }

    private static function php_version_superior_than(string $version_PHP): bool
    {
        return version_compare(phpversion(), $version_PHP, 'ge');
    }

    private static function get_content_management_system_version(): string
    {
        return get_bloginfo( 'version' ) . '.0';
    }

    private static function get_woocommerce_version(): string
    {
        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];
        }
        return '0.0_error_version_no_detect';
    }

    private static function is_memory_limit_superior_than(int $memory_necessary_in_MB = 128): bool
    {
        $memory_limit = ini_get( 'memory_limit' );

        if ( preg_match( '/^(\d+)(.)$/', $memory_limit, $matches ) ) {
            if ( $matches[2] == 'M' ) {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ( $matches[2] == 'K' ) {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        return $memory_limit >= $memory_necessary_in_MB * 1024 * 1024;
    }

    private static function is_time_execution_superior_than(int $time_execution_necessary_in_seconds = 30): bool
    {
        $time_limit = ini_get( 'max_execution_time' );

        return $time_limit || $time_limit >= $time_execution_necessary_in_seconds;
    }

    private static function is_perma_links_active(): bool
    {
        $perma_links = get_option( 'permalink_structure', false );

        return $perma_links && strlen( $perma_links ) > 0;
    }
}

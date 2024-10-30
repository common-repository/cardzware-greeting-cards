<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cardzware.com
 * @since      1.0.0
 *
 * @package    Cardzware_Greeting_Cards
 * @subpackage Cardzware_Greeting_Cards/admin/views
 */
?>

<div id="cw-dashboard-page" class="container text-center" style="padding-top: 5rem;">
    <div class="row justify-content-md-center">
        <?php if (!Cardzware_Greeting_Cards_Config::is_onboarding_complete()) { ?>
            <div class="row text-center">
                <div class="col-12 cw-setup-onboarding">
                    <h5>Congratulations! You successfully connected your store to Cardzware!</h5><br />
                    <h5>To get started you first need to log into your Cardzware account and complete a simple onboarding process.</h5><br />
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_dashboard_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <?php echo esc_html__('Cardzware Log In', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
       <?php } else if (!Cardzware_Greeting_Cards_Config::is_setup_complete()) { ?>
            <div class="row text-center">
                <div class="col-12 cw-setup-onboarding">
                    <h5>You're almost there!</h5><br />
                    <h5>Follow the setup steps on the Cardzware website to start selling greeting cards on your Woocommerce store!</h5><br /><br />
                    <h5><small>If you encounter any issues during this process, don't hesitate to contact us using the chat available in the Cardzware site and we will gladly help you complete this process!</small></h5><br />
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_dashboard_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <?php echo esc_html__('Cardzware Log In', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <div class="row text-center">
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_orders_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-cart"></span>
                                    <br />
                                    <?php echo esc_html__('Orders', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_cards_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-email-alt"></span>
                                    <br />
                                    <?php echo esc_html__('Cards', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_billing_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-list-view"></span>
                                    <br />
                                    <?php echo esc_html__('Invoices', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_colour_scheme_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-admin-customizer"></span>
                                    <br />
                                    <?php echo esc_html__('Branding', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_website_settings_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-admin-generic"></span>
                                    <br />
                                    <?php echo esc_html__('Settings', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-2 cw-links">
                    <a class="text-decoration-none" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_help_url()); ?>" target="_blank" rel="noopener">
                        <div class="card mb-4 rounded-3 shadow-sm mt-0 cw-install-success-button">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span style="font-size: 5rem;display: contents;" class="dashicons dashicons-editor-help"></span>
                                    <br />
                                    <?php echo esc_html__('Help', 'cardzware-greeting-cards'); ?>
                                </h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>


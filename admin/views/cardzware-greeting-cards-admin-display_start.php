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

<div id="cw-register-page" class="container text-center">
    <div class="row justify-content-md-center cw_install-success">
        <div class="row text-center">
            <div class="col-4 offset-4">
                <div class="card mb-4 rounded-3 shadow-sm border-primary mt-0" style="border-color: #00a0df;">
                    <div class="card-body">
                        <h1 class="card-title pricing-card-title"><?php echo esc_html__("Let's get started...", 'cardzware-greeting-cards'); ?></h1>
                        <p class="mt-3 mb-4">
                            <?php echo esc_html__("Now that you've installed our plugin, let's proceed and connect you to the Cardzware system.", 'cardzware-greeting-cards'); ?>
                        </p>
                        <div class="cw_install-success-card-start">
                            <a class="button button-primary button-large" href="<?php echo esc_url(Cardzware_Greeting_Cards_Config::get_connect_url()); ?>"
                               target="_blank" rel="noopener noreferrer"
                               style="background: linear-gradient(133deg, #994393, #674598)!important;color: white!important;
                                   border-color: #674598!important;padding: 1em!important;font-size: 1em!important;"
                            >
                                <?php echo esc_html__('Connect to Cardzware', 'cardzware-greeting-cards') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

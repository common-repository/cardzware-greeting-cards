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

const PAGE = 'cardzware-greeting-cards-admin-display_';
const REQUIREMENTS_CHECK = 'check-fail';
const START_CONFIGURATION = 'start';
const DASHBOARD = 'dashboard';

include_once(plugin_dir_path( __FILE__ ) . '/../views/class-cardzware-greeting-cards-template-renderer.php');

$template = new Cardzware_Greeting_Cards_Template_Renderer(plugin_dir_path( __FILE__ ) . '/../views');
?>

<div class="wrap">

    <?php

    if (isset($_GET['success'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong>
                    <?php
                    switch (sanitize_text_field($_GET['a'])) {
                        case 'save':
                            echo esc_html__('Changes saved successfully.', 'cardzware-greeting-cards');
                            break;
                        case 'delete':
                            echo esc_html__('Changes deleted successfully.', 'cardzware-greeting-cards');
                            break;
                        case 'nonce':
                            echo esc_html__('Invalid form submission.', 'cardzware-greeting-cards');
                            break;
                    }
                    ?>
                </strong>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'cardzware-greeting-cards') ?></span>
            </button>
        </div>
        <?php
    }

    if (isset($_GET['error'])) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <strong>
                    <?php
                    switch (sanitize_text_field($_GET['a'])){
                        case 'save':
                            echo esc_html__('The configuration has not been saved. Try again. ', 'cardzware-greeting-cards');
                            break;
                        case 'delete':
                            echo esc_html__('The configuration has not been deleted. Try again. ', 'cardzware-greeting-cards');
                            break;
                        default:
                            echo esc_html__('Nothing','cardzware-greeting-cards');
                            break;
                    }
                    ?>
                </strong>
            </p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'cardzware-greeting-cards') ?></span>
            </button>
        </div>
        <?php
    }
    ?>

    <h1>
        <img style="width: 10vw;" src="https://app.cardzware.com/img/logo.svg"  alt="Cardzware logo" />
        <button id="cw-settings-button" class="btn btn-link" style="float:right;" onclick="showCwSettingsPage()" ><?php echo esc_html__('Settings', 'cardzware-greeting-cards') ?></button>
        <button id="cw-back-button" class="btn btn-link" style="float:right;display: none;" onclick="showCwRegisterPage()"><?php echo esc_html__('Back', 'cardzware-greeting-cards') ?></button>
    </h1>

    <?php

    if (!Cardzware_Greeting_Cards_Install_Checker::has_valid_install_requirements()) {
        $pageView = REQUIREMENTS_CHECK;
    } else {
        $pageView = Cardzware_Greeting_Cards_Config::get_page_view();

        if ($pageView === 'start' && Cardzware_Greeting_Cards_Config::has_saved_configuration()) {
            Cardzware_Greeting_Cards_Config::set_page_view(DASHBOARD);
            $pageView = DASHBOARD;
        }
    }

    $variables = NULL;
    switch ($pageView) {
        case REQUIREMENTS_CHECK:
            $page = REQUIREMENTS_CHECK;
            $variables['list_of_variables'] = Cardzware_Greeting_Cards_Install_Checker::get_install_requirements_list();
            break;
        case DASHBOARD:
            $page = DASHBOARD;
            $elementToHideId = 'cw-dashboard-page';
            break;
        case START_CONFIGURATION:
        default:
            $page = START_CONFIGURATION;
            $elementToHideId = 'cw-register-page';
    }

    print $template->render( PAGE.$page, [
        "variables" => $variables
    ]);
    ?>

    <div id="cw-settings-page" style="display: none;" class="container text-center">
        <div class="col-4 offset-4">

            <p><?php echo esc_html__('Here yo can see the current settings of your Cardzware plugin.', 'cardzware-greeting-cards') ?></p>

            <input type="hidden" name="action" value="save_cw_config">

            <table class="form-table">
                <tbody>
                <tr>
                    <th><label for="api_key"><?php echo esc_html__('Cardzware API key', 'cardzware-greeting-cards') ?></label></th>
                    <td><input disabled required name="api_key" id="api_key" type="text" value="<?php echo esc_attr(Cardzware_Greeting_Cards_Config::get_api_key() ?? '') ?>" /></td>
                </tr>
                <tr>
                    <th><label for="api_url"><?php echo esc_html__('Cardzware API URL', 'cardzware-greeting-cards') ?></label></th>
                    <td><input disabled required name="api_url" id="api_url" type="text" value="<?php echo esc_attr(Cardzware_Greeting_Cards_Config::get_api_url() ?? '') ?>" /></td>
                </tr>
                <tr>
                    <th><label for="client_id"><?php echo esc_html__('Cardzware ID', 'cardzware-greeting-cards') ?></label></th>
                    <td><input disabled required name="client_id" id="client_id" type="text" value="<?php echo esc_attr(Cardzware_Greeting_Cards_Config::get_client_id() ?? '') ?>" /></td>
                </tr>
                <tr>
                    <th><label for="product_id"><?php echo esc_html__('Cardzware Product ID', 'cardzware-greeting-cards') ?></label></th>
                    <td><input disabled required name="product_id" id="product_id" type="text" value="<?php echo esc_attr(Cardzware_Greeting_Cards_Config::get_product_id() ?? '') ?>" /></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="element-to-hide-id-container" data-element-to-hide-id="<?php echo esc_attr($elementToHideId); ?>"</div>
</div>

<script type="text/javascript">
    function showCwSettingsPage() {
        let elemToHideId = document.getElementById('element-to-hide-id-container').getAttribute('data-element-to-hide-id');

        document.getElementById('cw-settings-button').style.display = 'none';
        document.getElementById(elemToHideId).style.display = 'none';
        document.getElementById('cw-settings-page').style.display = 'block';
        document.getElementById('cw-back-button').style.display = 'block';
    }

    function showCwRegisterPage() {
        let elemToHideId = document.getElementById('element-to-hide-id-container').getAttribute('data-element-to-hide-id');

        document.getElementById('cw-settings-button').style.display = 'block';
        document.getElementById(elemToHideId).style.display = 'block';
        document.getElementById('cw-settings-page').style.display = 'none';
        document.getElementById('cw-back-button').style.display = 'none';
    }

    let noticeDismissButton = document.getElementsByClassName('notice-dismiss')[0];
    if (noticeDismissButton !== undefined) {
        noticeDismissButton.addEventListener('click', (e) => {
            let currentUrl = window.location.href;
            window.location.href = currentUrl.substring(0, currentUrl.indexOf('&'));
        });
    }
</script>

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


<div class="wrap">
    <div id="cw-register-page" class="container text-center">
        <div class="row justify-content-md-center cw_install-success">
            <div class="row text-center">
                <div class="row col col-12">
                    <div class="card mb-2 rounded-3 shadow-sm border-primary mt-0">
                        <div class="card-header" style="background-color: transparent;">
                            <h1 class="card-title pricing-card-title"><?php echo esc_html__('Oh no, something\'s wrong!', 'cardzware-greeting-cards'); ?></h1>
                        </div>
                        <div class="card-body">
                            <p><?php echo esc_html__('Some of the requirements are not being satisfactorily met.', 'cardzware-greeting-cards') ?></p>
                            <table class="table">
                                <thead>
                                    <th></th>
                                    <th class='text-start'><?php echo esc_html__('Title', 'cardzware-greeting-cards'); ?></th>
                                    <th class='text-start'><?php echo esc_html__('Description', 'cardzware-greeting-cards'); ?></th>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($variables['list_of_variables'] as $variable) {
                                            if ($variable["success"]) {
                                                $classStyleSuccess = "table-success";
                                                $description = $variable['description-success'];
                                                $iconSuccess = '<svg width="26px" height="26px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 23.0781 48.0273 C 24.0859 48.0273 24.8828 47.5820 25.4453 46.7148 L 47.5937 11.8398 C 48.0156 11.1602 48.2031 10.6445 48.2031 10.1054 C 48.2031 8.8164 47.3359 7.9727 46.0469 7.9727 C 45.1328 7.9727 44.5937 8.2773 44.0313 9.1680 L 22.9844 42.7070 L 12.0625 28.4102 C 11.4766 27.5898 10.9140 27.2617 10.0469 27.2617 C 8.7344 27.2617 7.7969 28.1758 7.7969 29.4649 C 7.7969 30.0039 8.0313 30.6133 8.4766 31.1758 L 20.6406 46.6680 C 21.3672 47.5820 22.0703 48.0273 23.0781 48.0273 Z"/></svg>';
                                            } else {
                                                $classStyleSuccess = "table-danger";
                                                $description = $variable['description-fail'];
                                                $iconSuccess = '<svg width="26px" height="26px" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"><path d="M 10.0234 43.0234 C 9.2266 43.8203 9.2031 45.1797 10.0234 45.9766 C 10.8438 46.7734 12.1797 46.7734 13.0000 45.9766 L 28.0000 30.9766 L 43.0000 45.9766 C 43.7969 46.7734 45.1563 46.7969 45.9766 45.9766 C 46.7734 45.1562 46.7734 43.8203 45.9766 43.0234 L 30.9531 28.0000 L 45.9766 13.0000 C 46.7734 12.2031 46.7969 10.8437 45.9766 10.0469 C 45.1328 9.2266 43.7969 9.2266 43.0000 10.0469 L 28.0000 25.0469 L 13.0000 10.0469 C 12.1797 9.2266 10.8203 9.2031 10.0234 10.0469 C 9.2266 10.8672 9.2266 12.2031 10.0234 13.0000 L 25.0234 28.0000 Z"/></svg>';
                                            }

                                            echo "<tr class='" . esc_attr($classStyleSuccess) . "'>
                                                    <td>" . $iconSuccess . "</td>
                                                    <td class='text-start'>" . esc_html($variable["name"]) . "</td>
                                                    <td class='text-start'>" . esc_html($description) . "</td>
                                                 </tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


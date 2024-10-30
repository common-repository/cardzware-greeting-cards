<?php

require_once plugin_dir_path(__FILE__) . '../../cw-includes/class-cardzware-greeting-cards-rest-client.php';
$cw_rest_client = new Cardzware_Greeting_Cards_Rest_Client();

$api_url = Cardzware_Greeting_Cards_Config::get_api_url();
$api_key = Cardzware_Greeting_Cards_Config::get_api_key();
$client_id = Cardzware_Greeting_Cards_Config::get_client_id();
$iframe_id = Cardzware_Greeting_Cards_Config::get_iframe_id();
$modal_id = Cardzware_Greeting_Cards_Config::get_modal_id();
$product_id = Cardzware_Greeting_Cards_Config::get_product_id();
$endpoint = Cardzware_Greeting_Cards_Config::get_category_endpoint();
$page_slug = Cardzware_Greeting_Cards_Config::get_page_slug();
$cardzware_greeting_cards = new Cardzware_Greeting_Cards();

defined('CW_GATEWAY_TIMEOUT_HTTP_RESPONSE') or define('CW_GATEWAY_TIMEOUT_HTTP_RESPONSE', 504);
defined('CW_EXPIRATION_CATEGORY_JSON_TIME_IN_FOUR_HOURS') or define('CW_EXPIRATION_CATEGORY_JSON_TIME_IN_FOUR_HOURS', 4 * 60 * 60);
defined('CW_DELETE_IMAGE_DIRECTORY_IF_OLDER_THAN_FORTY_EIGHT_HOURS') or define('DELETE_IMAGE_DIRECTORY_IF_OLDER_THAN_FORTY_EIGHT_HOURS', 48 * 60 * 60);
defined('CW_CUSTOM_CARD_HEIGHT') or define('CUSTOM_CARD_HEIGHT', 550);
defined('CW_DEFAULT_SEO_MESSAGE') or define('CW_DEFAULT_SEO_MESSAGE', 'You can choose from over 7000 designs, and upload your own photo or message to make it unique. All cards cost {cardPrice} and postage is calculated at checkout.');
defined('DONOTCACHEPAGE') or define('DONOTCACHEPAGE', true);

function generate_categories($client_id, $api_url, $api_key, $cw_rest_client) {
    $cw_categories_directory = plugin_dir_path(__FILE__) . '/categories/' . $client_id . '/';
    $cw_categories_json_file = $cw_categories_directory . 'pw_categories.json';

    try {
        if (file_exists($cw_categories_json_file) && filesize($cw_categories_json_file) > 0) {
            $current_time = time();
            $file_time = filemtime($cw_categories_json_file);
            if (($file_time + CW_EXPIRATION_CATEGORY_JSON_TIME_IN_FOUR_HOURS) >= $current_time) {
                $file_content = file_get_contents($cw_categories_json_file);
                if (is_string($file_content) && !str_contains($file_content, "Auth failed") && !str_contains($file_content, "SSL certificate problem")) {
                    return json_decode($file_content);
                } else {
                    unlink($cw_categories_json_file);
                }
            }
        }

        if (!file_exists($cw_categories_directory) || !is_dir($cw_categories_directory)) {
            mkdir($cw_categories_directory, 0755, true);
        }

        $json_respose = $cw_rest_client->get_categories($api_url, $api_key, $client_id);
        file_put_contents($cw_categories_json_file, $json_respose);
        return json_decode($json_respose);

    } catch (\Exception $e) {
        if ($e->getCode() == CW_GATEWAY_TIMEOUT_HTTP_RESPONSE) {
            if (file_exists($cw_categories_json_file)) {
                unlink($cw_categories_json_file);
            }
            $json_respose = $cw_rest_client->get_categories($api_url, $api_key, $client_id);
            file_put_contents($cw_categories_json_file, $json_respose);
            return json_decode($json_respose);
        }
    }
}

function get_iframe_configuration_if_search($category_names, $type, $tag) {
    if ($category_names[0] == 's' || $category_names[0] == 'search') {
        $type = 'search';
        array_shift($category_names);
        $tag = implode(',', $category_names);
    }

    return [$type, $tag];
}

function get_current_category($category_names, $first_level_categories_copy, $cw_rest_client) {
    $current_category = 0;
    foreach ($category_names as $categoryName){
        $categoryName = str_replace("'", '', ucwords(str_replace("-"," ",$categoryName)));
        $current_category = $cw_rest_client->get_category_id($first_level_categories_copy, $categoryName);

        $currentCatId = NULL;
        if (!empty($current_category)) {
            $currentCatId = $current_category['catID'];
        }
        $first_level_categories_copy = $cw_rest_client->category_search($first_level_categories_copy, $currentCatId);
    }

    return $current_category;
}

function generate_breadcrumb_html($cw_rest_client, $breadcrumb, $cw_root_url, $endpoint) {
    $html = '';
    $i = 0;

    $html .= '<ul class="pw_breadcrumb">';
    $cw_subcategory_url = $cw_root_url . $endpoint . '/';
    $numItems = count($breadcrumb);
    foreach ($breadcrumb as $crumb) {
        if ($crumb['catName'] == 'Cards') {
            $href = $cw_root_url;
        } else {
            $cw_subcategory_url .= $cw_rest_client->cat_slug($crumb['catName']) . '/';
            $href = $cw_subcategory_url;
        }

        $html .= '<li><a href="' . esc_url($href) .'">' . esc_html($crumb['catName']) . '</a>';
        if (++$i !== $numItems) {
            $html .= "<span> > </span>";
        }

        $html .= '</li>';
    }

    $html .= '</ul>';

    return $html;
}

function displayErrorMessage() {
    return '<div class="pwGreetingCardsMain" style="text-align: center; margin-top: 5em;"><span>We are experiencing difficulties with purchasing cards at the moment. We are working on it. Please try again later.</span></div>';
}

$error_message = '';
$page_slug = '/' . $page_slug . '/';
if (is_null($api_url) || is_null($api_key) || is_null($client_id) || is_null($product_id)) {
    $error_message = 'api_url, api_key, client_id or product_id is null. Please, check it.';
    echo wp_kses_post(displayErrorMessage());
} else {
    $permalink = rtrim(get_home_url() . sanitize_url($_SERVER['REQUEST_URI']), '/') . '/';
    $cw_root_url = explode($page_slug, $permalink)[0] . $page_slug;

    $order_id = time() . mt_rand(100, 999);
    $tag = '';
    $mode = 'iframe';
    $isIframe = false;
    $type = 'card';

    $product_variant['price'] = get_woocommerce_currency_symbol() . wc_get_product($product_id)->get_price() ;

    $categories = generate_categories($client_id, $api_url, $api_key, $cw_rest_client);
    if (isset($categories->error)) {
        $error_message = $categories->error;
        echo wp_kses_post(displayErrorMessage());
    }

    $first_level_categories = $cw_rest_client->get_category($categories,0);

    global $wp;
    $current_category = null;
    $remaining_path = str_replace($page_slug, '', urldecode($wp->request));
    $pathArray = explode('/' . $endpoint . '/', $remaining_path);
    $path = !empty($pathArray) && count($pathArray) == 1 && $pathArray[0] == trim($page_slug, '/') ? "" : $pathArray[1];
    if ($path == "") {
        $category_id = 0;
        $breadcrumb = $cw_rest_client->build_breadcrumb($first_level_categories, $category_id);
        $seo = Cardzware_Greeting_Cards_Config::get_seo_for_category($current_category, $breadcrumb, get_site_url());
        $permalink .= $endpoint . '/';
    } else {
        $cat_names = explode("/", trim($path,"/"));
        list($type, $tag) = get_iframe_configuration_if_search($cat_names, $type, $tag);
        $first_level_categories_copy = $first_level_categories;
        $current_category = get_current_category($cat_names, $first_level_categories_copy, $cw_rest_client);

        $category_id = $current_category['catID'] ?? NULL;
        $isIframe = isset($current_category['hasChildren']) && $current_category['hasChildren'] == 0;
        $breadcrumb = $cw_rest_client->build_breadcrumb($first_level_categories, $category_id);
        $seo = Cardzware_Greeting_Cards_Config::get_seo_for_category($category_id, $breadcrumb, get_site_url());
    }

    $category_tiles = $cw_rest_client->get_category($categories, $category_id) ?? [];

    if ($isIframe) { ?>
        <div class="pwGreetingCardsMain" style="center center no-repeat;background-size: 10%;">
    <?php  } else { ?>
        <div class="pwGreetingCardsMain">
    <?php } ?>
    <div class="bootstrap-wrapper">
        <div>
            <div class="cards-page-element">
                <?php echo wp_kses_post(generate_breadcrumb_html($cw_rest_client, $breadcrumb, $cw_root_url, $endpoint)); ?>
            </div>
        </div>

        <div class="container-fluid pwGreetingCardsText">
            <div class="row-fluid text-center" id="page_text_above" style="width: 75%;margin: auto;padding-bottom: 15px;"></div>
        </div>
        <?php if(!$isIframe) { ?>
            <div id="pw_tiles_inline" class="container-fluid pwGreetingCardsTiles">
                <div class="row-fluid text-center">
                    <?php
                    $cw_rest_client->download_and_save_images($category_tiles, [$cw_rest_client::LOADING_GIF_URL]);
                    foreach ($category_tiles as $category) {
                        $category_name = $cw_rest_client->cat_slug($category->catName);
                        $local_image_path = plugin_dir_url(__FILE__) . 'images/' . $category->catID . '.png';
                        ?>
                        <div class="span4 pw_category_block">
                            <a href="<?php echo esc_url($permalink . urlencode($category_name)); ?>/">
                                <img src='<?php echo esc_url($local_image_path); ?>' alt="card category <?php echo esc_html($category_name) ?>">
                                <div class="pw_category_block_title"><?php echo esc_html($category->catName); ?></div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <script async type="text/javascript">loadPW=true;</script>
            <iframe id='<?php echo esc_html($iframe_id); ?>' class="pwGreetingCardsIframe" width="100%" height="500px" frameborder="0" scrolling="no"
                    src='<?php echo esc_url($cw_rest_client->open_url($api_url, $api_key, $client_id, $order_id, $tag, $category_id, $mode)); ?>'>
            </iframe>
        <?php } ?>

    </div>

    <script async type="text/javascript">
        function replaceSeoText(text, idPlace, textToReplace, price) {
            let elem = document.getElementById(idPlace);
            elem.innerHTML = text.replace(textToReplace, price);
        }

        let product_variant_price = '<?php echo esc_js($product_variant['price']); ?>';
        var iframeId = '<?php echo esc_js($iframe_id); ?>';
        <?php if (!$seo) { ?>
            replaceSeoText('<?php echo wp_kses_post(CW_DEFAULT_SEO_MESSAGE); ?>', 'page_text_above', '{cardPrice}', product_variant_price);
        <?php } else {
            $page_title = $seo['page_title'];
        }
        ?>
        document.title = '<?php echo str_replace('&gt;', '>', esc_html($page_title)); ?>';

        var meta = document.createElement('meta');
        meta.name = 'description';
        meta.content = '<?php echo esc_html(wp_strip_all_tags($seo["meta_description"])); ?>';
        document.getElementsByTagName('head')[0].appendChild(meta);

        <?php if (!empty($seo['page_text_above'])) { ?>
            replaceSeoText('<?php echo wp_kses_post($seo["page_text_above"]); ?>', 'page_text_above', '{cardPrice}', product_variant_price);
            document.getElementById('page_text_above').style.textAlign = 'center';
        <?php } else { ?>
            replaceSeoText('<?php echo wp_kses_post(CW_DEFAULT_SEO_MESSAGE); ?>', 'page_text_above', '{cardPrice}', product_variant_price);
        <?php } ?>

        <?php if (!empty($seo['page_text_below'] && !is_null($product_variant))) { ?>
            let iframeFlag = document.getElementById(iframeId);
            if (iframeFlag == null) {
                iframeFlag = document.getElementById('pw_tiles_inline');
            }

            let page_text_below_div = document.createElement('div');
            page_text_below_div.id = 'page_text_below';
            iframeFlag.parentNode.insertBefore(page_text_below_div, iframeFlag.nextSibling);
            replaceSeoText('<?php echo wp_kses_post($seo["page_text_below"]); ?>', 'page_text_below', '{cardPrice}', product_variant_price);
            document.getElementById('page_text_below').style.textAlign = 'center';
        <?php }
        } ?>

        var intervalCheckIframe = setInterval('checkIframe()', 500);
        function checkIframe() {
            let iframe = document.getElementById('<?php echo wp_kses_post(Cardzware_Greeting_Cards_Config::CW_IFRAME_ID); ?>');
            let counter = 0;
            if (counter === 3) {
                if (!isIframe) {
                    var pwTilesInline = document.getElementById('pw_tiles_inline').children[0];
                    if (pwTilesInline != null && pwTilesInline.innerHTML == 0) {
                        document.getElementById('pw_tiles_inline').innerHTML = '<?php echo wp_kses_post(displayErrorMessage()); ?>';
                        console.log('Cardzware error: <?php echo esc_html($error_message); ?>');
                    }
                }
                clearInterval(intervalCheckIframe);
            } else {
                if (iframe == null) {
                    counter += 1;
                } else {
                    counter = 3;
                }
            }
        }
    </script>
</div>

<?php

use Automattic\WooCommerce\Admin\Features\Features;

/*
Plugin Name: My Demo Plugin
 */

define("PAGE_TITLE", "Demo");
define("MENU_TEXT", "Demo");
define("PERMISSIONS", "manage_options");
define("SLUG", "demo");
define("INCLUDES_PATH", plugin_dir_path(__FILE__) . "src");
define("VIEWS_PATH", plugin_dir_path(__FILE__) . "src/views");

// include_once plugin_dir_path(__FILE__) . "includes/demo_add_menu.php";
// include_once plugin_dir_path(__FILE__) . "includes/demo_settings.php";
// include_once plugin_dir_path(__FILE__) . "includes/demo_woocommerce_add_new_tab_and_section.php";
// include_once plugin_dir_path(__FILE__) . "includes/olt_settings_wc_product.php";
// include_once plugin_dir_path(__FILE__) . "includes/demo_react_plugin.php";

//  是的，'plugins_loaded' 鉤子會在所有 WordPress 插件都加載完成後觸發。
// 這包括所有已啟用的插件和主題中的功能。因此，當 'plugins_loaded' 鉤子被觸發時
// ，您可以確信所有已啟用的插件都已經加載完成，並且可以使用它們的功能。

add_action('plugins_loaded', 'my_plugin_init');
function my_plugin_init() {
    // 檢查 WooCommerce 是否已經加載
    if (class_exists('WooCommerce')) {
        // 在這裡加載您的插件
        require_once WC()->plugin_path() . "/includes/admin/settings/class-wc-settings-page.php";
        // 獲取 WooCommerce 對象
        include INCLUDES_PATH . "/demo_woocommerce_payment.php";
        include INCLUDES_PATH . "/functions.php";
    }

}

add_action("admin_menu", "registerTopMenu");
function registerTopMenu() {
    add_menu_page(
        PAGE_TITLE,
        MENU_TEXT,
        PERMISSIONS,
        SLUG, // page = SLUG
        'renderPage', // this function prints the page content
        'dashicons-saved', // icon (from Dashicons for example) https://developer.wordpress.org/resource/dashicons/
        4// menu position
    );
}

function renderPage() {
    enqueueWooCommerceAssets();
    $olt_WC_PaymentWays = Olt_WC_PaymentWays::getInstanec();
    echo "<div class='wrap woocommerce'>";
    echo $olt_WC_PaymentWays->renderHtml();
    echo "</div>";
}

// * woocommerce需要的相關css javascript localization
function enqueueWooCommerceAssets() {
    // 參考原檔案 wp-content\plugins\woocommerce\includes\admin\class-wc-admin-assets.php

    // admin css
    wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC()->version);

    // 註冊腳本
    // 註冊 WooCommerce 主要後台腳本，用於處理各種後台功能，如產品管理、訂單處理、報告等。
    wp_register_script('woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.min.js', array('jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'), WC()->version);

    // 註冊 jQuery TipTip 插件，用於為 WooCommerce 管理區域的按鈕和其他元素提供簡單的懸停提示（上下文信息）。
    wp_register_script('jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), WC()->version, true);

    // 註冊 Select2 jQuery 插件，是原生 <select> 元素的替代品，提供更多功能和美觀的下拉選單。WooCommerce 使用它來實現可搜索選擇器（例如，向產品數據添加標籤和類別）。
    wp_register_script('select2', WC()->plugin_url() . '/assets/js/select2/select2.full.min.js', array('jquery'), '4.0.3');

    // 註冊 SelectWoo 函式庫，是專為 WooCommerce 定制的 Select2 分支，以提高可訪問性和性能。它包括了 Select2 的所有功能，並有一些針對 WooCommerce 的改進。
    wp_register_script('selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full.min.js', array('jquery'), '1.0.6');

    // 註冊 WooCommerce 增強選擇腳本，基於 selectWoo 函式庫（實際上取代了 Select2），提供額外的功能和集成。主要用於處理後台管理界面上的可搜索下拉選單（如產品標籤、類別等）。
    wp_register_script('wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array('jquery', 'selectWoo'), WC()->version);

    // (本地化參數)後臺資料傳遞給wc-enhanced-select這個script來使用
    wp_localize_script(
        'wc-enhanced-select',
        'wc_enhanced_select_params',
        array(
            'i18n_no_matches'                 => _x('No matches found', 'enhanced select', 'woocommerce'),
            'i18n_ajax_error'                 => _x('Loading failed', 'enhanced select', 'woocommerce'),
            'i18n_input_too_short_1'          => _x('Please enter 1 or more characters', 'enhanced select', 'woocommerce'),
            'i18n_input_too_short_n'          => _x('Please enter %qty% or more characters', 'enhanced select', 'woocommerce'),
            'i18n_input_too_long_1'           => _x('Please delete 1 character', 'enhanced select', 'woocommerce'),
            'i18n_input_too_long_n'           => _x('Please delete %qty% characters', 'enhanced select', 'woocommerce'),
            'i18n_selection_too_long_1'       => _x('You can only select 1 item', 'enhanced select', 'woocommerce'),
            'i18n_selection_too_long_n'       => _x('You can only select %qty% items', 'enhanced select', 'woocommerce'),
            'i18n_load_more'                  => _x('Loading more results&hellip;', 'enhanced select', 'woocommerce'),
            'i18n_searching'                  => _x('Searching&hellip;', 'enhanced select', 'woocommerce'),
            'ajax_url'                        => admin_url('admin-ajax.php'),
            'search_products_nonce'           => wp_create_nonce('search-products'),
            'search_customers_nonce'          => wp_create_nonce('search-customers'),
            'search_categories_nonce'         => wp_create_nonce('search-categories'),
            'search_taxonomy_terms_nonce'     => wp_create_nonce('search-taxonomy-terms'),
            'search_product_attributes_nonce' => wp_create_nonce('search-product-attributes'),
            'search_pages_nonce'              => wp_create_nonce('search-pages'),
        )
    );

    // 原woocommerce的if condition是判斷是不是在woocommerce page的頁面才執行
    if (true) {
        wp_enqueue_script('wc-enhanced-select'); // woocommerce初始化select2
        wp_enqueue_script('woocommerce_admin'); // 運行woocommerce admin所需要的全部相關操作eg tiptip

        $locale        = localeconv();
        $decimal_point = isset($locale['decimal_point']) ? $locale['decimal_point'] : '.';
        $decimal       = (!empty(wc_get_price_decimal_separator())) ? wc_get_price_decimal_separator() : $decimal_point;

        $params = array(
            /* translators: %s: decimal */
            'i18n_decimal_error'                => sprintf(__('Please enter a value with one decimal point (%s) without thousand separators.', 'woocommerce'), $decimal),
            /* translators: %s: price decimal separator */
            'i18n_mon_decimal_error'            => sprintf(__('Please enter a value with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce'), wc_get_price_decimal_separator()),
            'i18n_country_iso_error'            => __('Please enter in country code with two capital letters.', 'woocommerce'),
            'i18n_sale_less_than_regular_error' => __('Please enter in a value less than the regular price.', 'woocommerce'),
            'i18n_delete_product_notice'        => __('This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'woocommerce'),
            'i18n_remove_personal_data_notice'  => __('This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'woocommerce'),
            'i18n_confirm_delete'               => __('Are you sure you wish to delete this item?', 'woocommerce'),
            'decimal_point'                     => $decimal,
            'mon_decimal_point'                 => wc_get_price_decimal_separator(),
            'ajax_url'                          => admin_url('admin-ajax.php'),
            'strings'                           => array(
                'import_products' => __('Import', 'woocommerce'),
                'export_products' => __('Export', 'woocommerce'),
            ),
            'nonces'                            => array(
                'gateway_toggle' => wp_create_nonce('woocommerce-toggle-payment-gateway-enabled'),
            ),
            'urls'                              => array(
                'add_product'     => Features::is_enabled('new-product-management-experience') ? esc_url_raw(admin_url('admin.php?page=wc-admin&path=/add-product')) : null,
                'import_products' => current_user_can('import') ? esc_url_raw(admin_url('edit.php?post_type=product&page=product_importer')) : null,
                'export_products' => current_user_can('export') ? esc_url_raw(admin_url('edit.php?post_type=product&page=product_exporter')) : null,
            ),
        );

        // 本地化woocommerce_admin to woocommerce_admin script
        wp_localize_script('woocommerce_admin', 'woocommerce_admin', $params);
    }

    // setiing for 選擇國家 and 全部選擇, 全部不選
    wp_enqueue_script('woocommerce_settings', WC()->plugin_url() . '/assets/js/admin/settings.js', array('jquery', 'wp-util', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'iris', 'selectWoo'), WC()->version, true);

    wp_localize_script(
        'woocommerce_settings',
        'woocommerce_settings_params',
        array(
            'i18n_nav_warning'                    => __('The changes you made will be lost if you navigate away from this page.', 'woocommerce'),
            'i18n_moved_up'                       => __('Item moved up', 'woocommerce'),
            'i18n_moved_down'                     => __('Item moved down', 'woocommerce'),
            'i18n_no_specific_countries_selected' => __('Selecting no country / region to sell to prevents from completing the checkout. Continue anyway?', 'woocommerce'),
        )
    );
}

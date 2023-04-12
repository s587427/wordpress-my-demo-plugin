<?php
/*
    Plugin Name: My Demo Plugin
*/

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
    }
}

add_action("admin_menu", "renderTopMenu");

function renderTopMenu() {
    add_menu_page(
        'Custom Settings Page Title', // page <title>Title</title>
        'Custom Settings', // link text
        'manage_options', // user capabilities 
        'myCustomPage', // page slug eg: http://localhost/wordpress/wp-admin/admin.php?page=myCustomPage
        'myCustomPageCallback', // this function prints the page content
        'dashicons-saved', // icon (from Dashicons for example) https://developer.wordpress.org/resource/dashicons/
        4 // menu position
    );
}

function myCustomPageCallback() {

    $settings_product = require WC()->plugin_path() . "/includes/admin/settings/class-wc-settings-products.php";
    $settings_general = require WC()->plugin_path() . "/includes/admin/settings/class-wc-settings-general.php";


    $settings = $settings_product->get_settings_for_section("inventory");



    print_r($settings);


?>
    <form method="POST">
        <input type="number" name="age" value="18">
        <input type="text" name="name" value="孫悟空">
        <input type="hidden" name="secrue" value="ok">

        <button type="submit">save</button>
    </form>
<?php
}

if (isset($_POST["secrue"])) {
    print_r($_POST);
    exit;
}

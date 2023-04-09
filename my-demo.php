<?php
/*
    Plugin Name: My Demo Plugin
*/

// include_once plugin_dir_path(__FILE__) . "includes/demo_add_menu.php";
include_once plugin_dir_path(__FILE__) . "includes/demo_settings.php";
include_once plugin_dir_path(__FILE__) . "includes/demo_woocommerce_add_new_tab_and_section.php";

include_once plugin_dir_path(__FILE__) . "includes/olt_settings_wc_product.php";


function my_plugin_settings_page() {
    add_menu_page(
        'My Plugin Settings',
        'My Plugin',
        'manage_options',
        'my-plugin-settings',
        'my_plugin_settings_callback'
    );
}
add_action('admin_menu', 'my_plugin_settings_page');

function my_plugin_settings_callback() {
    $general_settings = get_option('woocommerce_general_settings');
    $email_settings = get_option('woocommerce_email_settings');

?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('my-plugin-settings-group'); ?>
            <?php do_settings_sections('my-plugin-settings-group'); ?>
            <h2>General Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Store Address', 'my-plugin'); ?></th>
                    <td><input type="text" name="woocommerce_general_settings[woocommerce_store_address]" value="<?php echo esc_attr($general_settings['woocommerce_store_address']); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Shipping Location(s)', 'my-plugin'); ?></th>
                    <td><input type="text" name="woocommerce_general_settings[woocommerce_allowed_countries]" value="<?php echo esc_attr($general_settings['woocommerce_allowed_countries']); ?>" /></td>
                </tr>
            </table>
            <h2>Email Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('From Name', 'my-plugin'); ?></th>
                    <td><input type="text" name="woocommerce_email_settings[woocommerce_email_from_name]" value="<?php echo esc_attr($email_settings['woocommerce_email_from_name']); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('From Address', 'my-plugin'); ?></th>
                    <td><input type="text" name="woocommerce_email_settings[woocommerce_email_from_address]" value="<?php echo esc_attr($email_settings['woocommerce_email_from_address']); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function my_plugin_settings_init() {
    register_setting(
        'my-plugin-settings-group',
        'woocommerce_general_settings'
    );
    register_setting(
        'my-plugin-settings-group',
        'woocommerce_email_settings'
    );
}
add_action('admin_init', 'my_plugin_settings_init');

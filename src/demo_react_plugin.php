<?php

function my_plugin_enqueue_scripts() {

    wp_enqueue_style("index", plugin_dir_url(__FILE__) . '../assets/css/main.073c9b0a.css');
    wp_enqueue_style("index", plugin_dir_url(__FILE__) . '../assets/css/main.073c9b0a.css.map');

    wp_enqueue_script(
        'my-plugin-admin',
        plugin_dir_url(__FILE__) . '../assets/js/main.1bcfece3.js',
        array('wp-i18n', 'wp-components', 'wp-element'),
        '1.0.0',
        true
    );
    wp_enqueue_script(
        'my-plugin-admin',
        plugin_dir_url(__FILE__) . '../assets/js/787.a6b6611a.chunk.js',
        array('wp-i18n', 'wp-components', 'wp-element'),
        '1.0.0',
        true
    );
}

add_action('admin_enqueue_scripts', 'my_plugin_enqueue_scripts');

add_action("admin_menu", "renderTopMenu");
function renderTopMenu() {
    add_menu_page(
        'react ', // page <title>Title</title>
        'settings for react ', // link text
        'manage_options', // user capabilities 
        'react', // page slug eg: http://localhost/wordpress/wp-admin/admin.php?page=myCustomPage
        'my_plugin_render_settings_page', // this function prints the page content
        'dashicons-saved', // icon (from Dashicons for example) https://developer.wordpress.org/resource/dashicons/
        4 // menu position
    );
}

function my_plugin_render_settings_page() {
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div id="my-plugin-settings"></div>
    </div>
<?php
}

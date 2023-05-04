<?php

/* add top level menu*/
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
    echo 'What is up?';
}

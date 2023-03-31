<?php

/* add sub menu */
add_action('admin_menu', 'renderSubMenu');

function renderSubMenu() {
    // 下面兩者都可以
    add_submenu_page(
        'tools.php', // parent page slug
        'Custom Settings Sub Option Title', // page title
        'Custom Sub Option', // link text
        'manage_options',
        'myCustomSubPage', // page slug
        'myCustomSubPageCallback',
        0 // menu position
    );

    // add_management_page只是直接的api裡面也是用add_submenu_page實現
    // add_management_page(
    // 'Slider Settings',
    // 'Slider',
    // 'manage_options',
    // 'myCustomSubPage',
    // 'myCustomSubPageCallback',
    // 0 // menu position
    // );
}

?>
<?php
function myCustomSubPageCallback() {
?>
    <div class="wrap">
        <h1><?= get_admin_page_title() ?></h1>
        <form method="post" action="options.php">
            <?php
            /**  validate fields(驗證)如不需要可以不用添加
             * 使用兩個api settings_errors打("setting")印錯誤, add_settings_error("setting", ..., ...) 註冊錯誤, 這兩者的第一個參數必須相同
             */
            settings_errors('rudr_slider_settings_errors'); // 位置可以依照你想呈現錯誤的地方擺放
            // main output
            settings_fields("myCustomOptionGroup"); //settings group name , prints hidden fields of this settings page.
            do_settings_sections("myCustomSubPageSlug"); //  prints actually the fields. Pass your settings page slug
            submit_button(); // "Save Changes" button
            ?>
        </form>
    </div>
<?php
}

// Register a setting and create a field ()
add_action("admin_init", "myCustomSettingsInit");
function myCustomSettingsInit() {
    /**
     * 這邊$pageSlug 只要替換成系統內預設的就可以在原本的general, writing, reading, discussion, permalink 添加額外欄位
     * 而且無需使用settings_fields(), do_settings_sections(),submit_button() 因為那些設置在Wordpress是自動調用的
     * 但自己定義的就需要手動調用
     */
    $pageSlug = "myCustomSubPageSlug"; // slug-name of the settings page (可以跟menu的不一樣這是分開的), bulit in general, writing, reading, discussion, permalink.
    $optionGroup = 'myCustomOptionGroup';

    // setp1. 建立section
    add_settings_section(
        "myCustomSectionId",
        "",  // title (optional)
        "",  // callback function to display the section (optional)
        $pageSlug,
    );

    // step2. 註冊欄位 register fields
    register_setting($optionGroup, "slider_on", 'rudr_sanitize_checkbox');
    register_setting($optionGroup, 'num_of_slides', "myValidateCallback");

    // step3. 新增欄位 add fields, 記住先註冊在新增
    add_settings_field(
        'slider_on', // just is field的id, 儲存是看register_setting提供的name來儲存的
        'Display slider', // field describe
        'rudr_checkbox', // function to print the field
        $pageSlug,
        'myCustomSectionId', // section ID,
        array(
            "name" => "slider_on",
        )
    );;

    add_settings_field(
        'num_of_slides',
        'Number of slides',
        'rudr_number', // function to print the field
        $pageSlug,
        'myCustomSectionId',
        array( // 此array會被傳入到打印這個field callback作為參數
            'label_for' => 'num_of_slides',
            'class' => 'hello', // for <tr> element
            'name' => 'num_of_slides' // pass any custom parameters
        )
    );
}

// 打印field的callback
function rudr_checkbox($args) {
    // print_r($args);
    $value = get_option('slider_on'); // 對應的是 register_setting註冊的name
?>
    <label>
        <input type="checkbox" name="<?= $args["name"] ?>" <?php checked($value, 'yes') ?> /> Yes
    </label>
    <?php
}
function rudr_number($args) {
    printf(
        '<input type="number" id="%s" name="%s" value="%d" />',
        $args['name'],
        $args['name'],
        get_option($args['name'], 2) // 2 is the default number of slides
    );
}


// custom sanitization function for a checkbox field
function rudr_sanitize_checkbox($value) {
    return 'on' === $value ? 'yes' : 'no';
}


function myValidateCallback($input) {
    // “sanitize”通常指的是对输入数据进行清理或过滤，以确保其中不包含任何恶意代码或不必要的内容
    // sanitize in the first place
    $input = absint($input);

    if ($input < 2) { // some conditions
        add_settings_error(
            'rudr_slider_settings_errors',
            'not-enough', // part of error message ID id="setting-error-not-enough"
            'The minimum amount of slides should be at least 2!',
            'error' // success, warning, info
        );
        // get the previous field value if validation fails
        $input = get_option('num_of_slides');
    }
    return $input;
}



// Show success message (保存成功顯示的hook)
add_action('admin_notices', 'rudr_notice');
function rudr_notice() {

    if (
        isset($_GET['page'])
        && 'myCustomSubPage' == $_GET['page']  // 這邊是指的是menu的plug, 即是網址上面後面的page: http://localhost/wordpress/wp-admin/tools.php?page=myCustomSubPage
        && isset($_GET['settings-updated'])
        && true == $_GET['settings-updated']
    ) {
    ?>
        <?php
        $settings_errors = get_settings_errors('rudr_slider_settings_errors');
        if (!empty($settings_errors)) {
            return;
        }
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong>Slider settings saved.</strong>
            </p>
        </div>
<?php
    }
}

<?php

require_once plugin_dir_path(__FILE__) . "test.php";




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
            settings_fields("myCustomOptionGroup"); // use the correct option group name
            settings_fields("anortherOptionGroup"); // use the correct option group name
            do_settings_sections("myCustomSubPageSlug");
            submit_button(); // "Save Changes" button
            ?>
        </form>
    </div>

<?php

    // OltSettingsWCProduct::getInstance()->printAddSettingsSections();
    // OltSettingsWCProduct::getInstance()->printRegisterSettings();
    // OltSettingsWCProduct::getInstance()->printAddSettingsFields();
    OltSettingsWCProduct::getInstance()->outputForm();
    // OltSettingsWCProduct::getInstance()->outputForm2();
    // print_r(OltSettingsWCProduct::getInstance()->pages);
    // print_r(OltSettingsWCProduct::getInstance()->optionGroups);
}

// Register a setting and create a field ()
add_action("admin_init", "myCustomSettingsInit");
function myCustomSettingsInit() {
    $pageSlug = "myCustomSubPageSlug";
    $optionGroup1 = 'myCustomOptionGroup';
    $optionGroup2 = 'anortherOptionGroup';

    add_settings_section(
        "myCustomSectionId",
        "",
        "",
        $pageSlug,
    );

    add_settings_section(
        "myCustomSectionId2",
        "第二個settion",
        "",
        $pageSlug,
    );

    register_setting($optionGroup1, "slider_on", 'rudr_sanitize_checkbox');
    register_setting($optionGroup1, 'num_of_slides', "myValidateCallback");
    register_setting($optionGroup2, "nameOfanortherOptionGroup");

    add_settings_field(
        'slider_on',
        'Display slider',
        'rudr_checkbox',
        $pageSlug,
        'myCustomSectionId',
        array(
            "name" => "slider_on",
        )
    );

    add_settings_field(
        'num_of_slides',
        'Number of slides',
        'rudr_number',
        $pageSlug,
        'myCustomSectionId',
        array(
            'label_for' => 'num_of_slides',
            'class' => 'hello',
            'name' => 'num_of_slides'
        )
    );

    add_settings_field(
        "nameOfanortherOptionGroup",
        "nameOfanortherOptionGroup Title",
        function ($args) {
            $value = get_option($args["name"], "");

            echo "<input id='" . $args["name"] . "' 
                    name='" . $args["name"] . "'
                    type='text' 
                    value='" . $value . "'
                 >";
        },
        $pageSlug,
        "myCustomSectionId2",
        array("name" => "nameOfanortherOptionGroup")
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

//  test 

add_action("admin_init", "settingsInitTest");

function settingsInitTest() {
    // add_settings_section('general_store_page', '商店頁面', '', 'olt-wc-product-general', 'Array ( ) ');
    // add_settings_section('general_size', '尺寸', '', 'olt-wc-product-general', 'Array ( ) ');
    // add_settings_section('stock_stock', '庫存', '', 'olt-wc-product-stock', 'Array ( ) ');
}

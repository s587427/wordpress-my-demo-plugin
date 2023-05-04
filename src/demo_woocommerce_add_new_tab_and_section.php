<?php

/**
 * 需要的hook
 * woocommerce_settings_tabs_array 新增新的tab
 * woocommerce_settings_tabs_{just a your added the tab_id} 將settings新入新增的tab內
 * woocommerce_settings_save_{just a your added the tab_id} 保存settings to database
 * woocommerce_sections_{just a your added the tab_id} 新增section to tab
 * wc_{just a your added the tab_id}_tabs_stettings 你的settings需要透過此hook過濾
 * add validate https://stackoverflow.com/questions/59370947/woocommerce-custom-settings-tab-validation
 * Add a custom WooCommerce settings page, including page sections https://stackoverflow.com/questions/72816886/add-a-custom-woocommerce-settings-page-including-page-sections
 * How do I add a custom field to the "Products Inventory" tab in the WooCommerce settings? https://stackoverflow.com/questions/63702931/how-do-i-add-a-custom-field-to-the-products-inventory-tab-in-the-woocommerce-s
 */



class WooCommerceTabDemo {

    private $tabId = "newtabs"; // !!! 必須為英文小寫否則會有bug
    private $tabName = "新選單";  // 

    public function __construct() {

        add_filter("woocommerce_settings_tabs_array", array($this, "addTab"), 21);
        add_action("woocommerce_settings_tabs_" . $this->tabId, array($this, "addSettinsgToTab"));
        // add section inside tab
        add_action("woocommerce_sections_" . $this->tabId, array($this, "ouputSectionToTab"));
        add_action("woocommerce_settings_save_" . $this->tabId, array($this, "saveSettingsToDb"));
    }

    public function addTab($tabs) {
        $tabs[$this->tabId] = $this->tabName;
        return $tabs;
    }

    public function addSettinsgToTab() {
        $settings = $this->getSettings();
        // woocommerce_admin_fields 藉由這個api可以將 $settings轉換出對應的html
        woocommerce_admin_fields($settings);
    }

    public function saveSettingsToDb() {
        global $current_section;
        $settings = $this->getSettings();
        WC_Admin_Settings::save_fields($settings);

        if ($current_section) {
            do_action('woocommerce_update_options_' . $this->tabId . '_' . $current_section);
        }
    }

    public function getSettings() {
        //  $current_section 已經是woocommerce內置的global變數拿來用即可 
        global $current_section;
        $settings = array();
        if ($current_section == 'my-section-1') {
            // My section 1
            $settings = array(

                // Title
                array(
                    'title'     => __('Your title 1', 'woocommerce'),
                    'type'      => 'title',
                    'id'        => 'custom_settings_1'
                ),

                // Text
                array(
                    'title'     => __('Your title 1.1', 'text-domain'),
                    'type'      => 'text',
                    'desc'      => __('Your description 1.1', 'woocommerce'),
                    'desc_tip'  => true,
                    'id'        => 'custom_settings_1_text',
                    'css'       => 'min-width:300px;'
                ),

                // Select
                array(
                    'title'     => __('Your title 1.2', 'woocommerce'),
                    'desc'      => __('Your description 1.2', 'woocommerce'),
                    'id'        => 'custom_settings_1_select',
                    'class'     => 'wc-enhanced-select',
                    'css'       => 'min-width:300px;',
                    'default'   => 'aa',
                    'type'      => 'select',
                    'options'   => array(
                        'aa'        => __('aa', 'woocommerce'),
                        'bb'        => __('bb', 'woocommerce'),
                        'cc'        => __('cc', 'woocommerce'),
                        'dd'        => __('dd', 'woocommerce'),
                    ),
                    'desc_tip' => true,
                ),

                // Section end
                array(
                    'type'      => 'sectionend',
                    'id'        => 'custom_settings_1'
                ),
            );
        } elseif ($current_section == 'my-section-2') {

            // My section 2
            $settings = array(

                // Title
                array(
                    'title'     => __('Your title 2', 'woocommerce'),
                    'type'      => 'title',
                    'id'        => 'custom_settings_2'
                ),

                // Text
                array(
                    'title'     => __('Your title 2.2', 'text-domain'),
                    'type'      => 'text',
                    'desc'      => __('Your description 2.1', 'woocommerce'),
                    'desc_tip'  => true,
                    'id'        => 'custom_settings_2_text',
                    'css'       => 'min-width:300px;'
                ),

                // Section end
                array(
                    'type'      => 'sectionend',
                    'id'        => 'custom_settings_2'
                ),
            );
        } else {
            // Overview
            $settings = array(
                array(
                    "name" => "Section title",
                    "type" => "title",
                    "desc" => "",
                    "id" => "SectionTitleId"
                ),
                array(
                    "name" => "title",
                    "type" => "text",
                    "desc" => "This is some helper text",
                    "id"  => 'titleId'
                ),
                array(
                    "name" => "Description",
                    "type" => "textarea",
                    "desc" => "This is a paragraph describing the setting",
                    "id"  => "descriptionId"
                ),
                array(
                    "type" => "sectionend",
                    "id" => "sectionEndId"
                )
            );
        }

        // 如果沒有section只需要做下面這樣
        // $settings = array(
        //     array(
        //         "name" => "Section title",
        //         "type" => "title",
        //         "desc" => "",
        //         "id" => "SectionTitleId"
        //     ),
        //     array(
        //         "name" => "title",
        //         "type" => "text",
        //         "desc" => "This is some helper text",
        //         "id"  => 'titleId'
        //     ),
        //     array(
        //         "name" => "Description",
        //         "type" => "textarea",
        //         "desc" => "This is a paragraph describing the setting",
        //         "id"  => "descriptionId"
        //     ),
        //     array(
        //         "type" => "sectionend",
        //         "id" => "sectionEndId"
        //     )
        // );
        return apply_filters("wc_" . $this->tabId . "_settings", $settings);
    }


    public function ouputSectionToTab() {
        global $current_section;

        $tabId = $this->tabId;

        // Must contain more than one section to display the links
        // Make first element's key empty ('')
        $sections = array(
            "" => __("Overview", "woocommerce"),
            "my-section-1" => __("My section 1", "woocommerce"),
            "my-section-2" => __("My section 2", "woocommerce")
        );

        echo "<ul class='subsubsub'>";

        $array_keys = array_keys($sections);

        foreach ($sections as $id => $label) {
            echo '<li><a href="' . admin_url('admin.php?page=wc-settings&tab=' . $tabId . '&section=' . sanitize_title($id)) . '" class="' . ($current_section == $id ? 'current' : '') . '">' . $label . '</a> ' . (end($array_keys) == $id ? '' : '|') . ' </li>';
        }

        echo '</ul><br class="clear" />';
    }
}

$wooCommerceTabDemo = new WooCommerceTabDemo();


// 可以藉由此hook的到以添加的woocommerce settins page woocommerce_get_settings_pages
// add_filter("woocommerce_get_settings_pages", "getExistSettingsPages", 50);
// function getExistSettingsPages($settings) {
//     // wp_send_json_success(var_export($settings, true));
//     return $settings;
// }

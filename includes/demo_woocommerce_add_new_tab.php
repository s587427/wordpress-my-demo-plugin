<?php

/**
 * 需要的hook
 * woocommerce_settings_tabs_array 新增新的tab
 * woocommerce_settings_tabs_{just a your added the tab_id} 將settings新入新增的tab內
 * woocommerce_update_options_{just a your added the tab_id} 保存settings to database
 * wc_{just a your added the tab_id}_tabs_stettings 你的settings需要透過此hook過濾
 */

class WooCommerceTabDemo {

    private $tabId = "newtabs"; // !!! 必須為英文小寫否則會有bug
    private $tabName = "新選單";  // 

    public function __construct() {

        add_filter("woocommerce_settings_tabs_array", array($this, "addTab"), 50);
        add_action("woocommerce_settings_tabs_" . $this->tabId, array($this, "addSettinsgToTab"));
        add_action("woocommerce_update_options_" . $this->tabId, array($this, "updSettingsToDb"));
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

    public function updSettingsToDb() {
        $settings = $this->getSettings();
        // woocommerce_update_options 使用此api 只要傳入設定的$settings即可以save to db
        woocommerce_update_options($settings);
    }

    public function getSettings() {
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
        return apply_filters("wc_" . $this->tabId . "_settings", $settings);
    }
}

$wooCommerceTabDemo = new WooCommerceTabDemo();

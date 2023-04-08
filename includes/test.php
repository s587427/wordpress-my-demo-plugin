<?php

// page(woo、elemotor、) > tab > subtab
// page = tab 
//  
//         - section field 
//         -  subtab
//             - section field 


function printinput($args) {
    echo "<input type='text' />";
}

class SettingBase {

    protected $page;
    protected $sections;
    protected $settings;
    protected $tab;
    protected $subtabs;


    function __construct() {
        add_action("admin_init", array($this, "settingsInit"));
    }

    function settingsInit() {
        $this->addSettingsSections($this->sections);
        $this->registerSettings($this->settings);
        $this->addSettingsFields($this->settings);
    }

    function addSettingsSections($sections) {

        foreach ($sections as $subTab => $sectionsForTab) {
            foreach ($sectionsForTab as $sectionItem) {
                add_settings_section(
                    $sectionItem["id"],
                    $sectionItem["title"],
                    $sectionItem["callback"],
                    $subTab, // 這邊變成使用一個subtab當作不同page
                    $sectionItem["args"]
                );
            }
        }
    }

    function registerSettings($settings) {
        foreach ($settings as $settingsForSectionId) {

            foreach ($settingsForSectionId as $settingItem) {
                register_setting(
                    $settingItem["option_group"],
                    $settingItem["option_name"],
                    $settingItem["args"],
                );
            }
        }
    }

    function addSettingsFields($settings) {

        foreach ($settings as $sectionId => $settingsForSectionId) {

            foreach ($settingsForSectionId as $settingItem) {
                add_settings_field(
                    $settingItem["id"],
                    $settingItem["tile"],
                    $settingItem["callback"], // function to print the field
                    $this->page,
                    $sectionId,
                    $settingItem["args"]
                );
            }
        }
    }
}


class WCSettingGeneral extends SettingBase {
    const tab = "general";
    function __construct() {
        $this->page = "olt-woocommerce";
    }
}


class WCSettingProduct extends SettingBase {

    static $instance = null;

    function __construct() {

        $this->page = "olt-woocommerce";
        $this->tab = "product";
        $this->subtabs = $this->getSubTabs();
        $this->sections = $this->getSections();
        $this->settings = $this->getSettings();
        parent::__construct();
    }

    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function getSections() {

        $sections = array(
            "general" => array(
                array(
                    "id" => "store_address",
                    "title" => "商店地址",
                    "callback" => function () {
                        echo "<p>這是你的商店實際的地理位置，稅率及運送費率都將參考此位置 </p>";
                    },
                    "args" => array(),
                ),
                array(
                    "id" => "store_address",
                    "title" => "商店地址2",
                    "callback" => function () {
                        echo "<p>這是你的商店實際的地理位置，稅率及運送費率都將參考此位置2 </p>";
                    },
                    "args" => array(),
                )
            ),
            "stock" => array(
                array(
                    "id" => "stock",
                    "title" => "庫存地址",
                    "callback" => function () {
                        echo "<p>庫存地址. 這是你的商店實際的地理位置，稅率及運送費率都將參考此位置 </p>";
                    },
                    "args" => array(),
                ),
                array(
                    "id" => "stock",
                    "title" => "庫存地址2",
                    "callback" => function () {
                        echo "<p>庫存地址. 這是你的商店實際的地理位置，稅率及運送費率都將參考此位置2 </p>";
                    },
                    "args" => array(),
                )
            ),
            // .....
        );

        return $sections;
    }

    function getOptionGroupsBySubTab($subTab) {
        // 先濾出當前subTab的所有sectionid
        $subTabSections = $this->getSections()[$subTab];
        $subTabSectionsIds = array();
        foreach ($subTabSections as $sectionItem) {
            if (!in_array($sectionItem["id"],  $subTabSectionsIds)) {
                $subTabSectionsIds[] = $sectionItem["id"];
            }
        }

        $optionGroups = array();
        // print_r($subTabSectionsIds);
        foreach ($subTabSectionsIds as $subTabSectionsId) {
            foreach ($this->getSettings()[$subTabSectionsId] as $settingItem) {
                if (!in_array($settingItem["option_group"],  $optionGroups)) {
                    $optionGroups[] = $settingItem["option_group"];
                }
            }
        }

        return $optionGroups;
    }

    function getSettings() {
        // store_address , stock
        $settings = array(

            "store_address" => array(
                array(
                    "option_group" => "store_address_group",
                    "option_name" => "store_address_name",
                    "id" => "store_address_name",
                    "title" => "地址第 1 行",
                    "callback" => function () {
                        echo "<input type='text'>";
                    },
                    "args" => array("type" => "text")
                ),
                array(
                    "option_group" => "store_address_group",
                    "option_name" => "store_address_name2",
                    "id" => "store_address_name2",
                    "title" => "地址第 2 行",
                    "callback" => "printinput",
                    "args" => array("type" => "number")
                ),
            ),
            "stock" => array(
                array(
                    "option_group" => "stock_group",
                    "option_name" => "stock_name",
                    "id" => "stock_name",
                    "title" => "庫存第 1 行",
                    "callback" => "printinput",
                    "args" => array("type" => "text")
                ),
                array(
                    "option_group" => "stock_group",
                    "option_name" => "stock_name2",
                    "id" => "stock_name2",
                    "title" => "庫存第 2 行",
                    "callback" => "printinput",
                    "args" => array("type" => "number")
                ),
            ),
        );

        return  $settings;
    }

    function getSubTabs() {
        return array_keys($this->getSections());
    }

    function outputForm() {

        ob_start();
?>

        <div class="woocommerce-tab-general">

            <?php foreach ($this->getSubTabs() as $subtab) : ?>
                <div class="woocommerce-tab-<?= $this->tab ?>-subtab-<?= $subtab ?>">
                    <form method="post" action="options.php">
                        <?php
                        foreach ($this->getOptionGroupsBySubTab($subtab) as $optionGroup) {
                            settings_fields($optionGroup);
                            // 每一個tab基本上就是一個page
                        }
                        do_settings_sections($subtab);
                        submit_button();
                        ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
<?php
        $html = ob_get_contents();
        echo  $html;
    }
}


WCSettingProduct::getInstance();

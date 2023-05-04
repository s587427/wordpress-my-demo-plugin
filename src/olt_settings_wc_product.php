<?php

class OltSettingsWCProduct {

    static $instance = null;
    public $pages;
    public $sections;
    public $settings;
    public $optionGroups;


    function __construct() {

        $this->pages = $this->getPages();
        $this->sections = $this->getSections();
        $this->settings = $this->getSettings();
        $this->optionGroups = $this->getOptionGroups();

        add_action("admin_init", array($this, "settingsInit"));
    }

    function settingsInit() {
        $this->addSettingsSections();
        $this->registerSettings();
        $this->addSettingsFields();
    }

    function getPages() {
        return array_keys($this->getSections());
    }

    function getSections() {
        $sections = array(
            "olt-wc-product-general" => array(
                "store_page_section" => array(
                    "title" => "商店頁面",
                    "callback" => function () {
                        echo "<p>這商店頁面下方補充 </p>";
                    },
                    "args" => array(),
                ),
                "size_section" => array(
                    "title" => "尺寸",
                    "callback" => function () {
                        echo "<p>尺寸下方補充 </p>";
                    },
                    "args" => array(),
                ),
            ),
            "olt-wc-product-stock" => array(
                "stock_stock_section" => array(
                    "title" => "庫存",
                    "callback" => function () {
                        echo "<p>庫存下方補充 </p>";
                    },
                    "args" => array(),
                ),
            )
        );
        return $sections;
    }

    function getSettings() {
        // settings 都在section裡面
        // page > sectionId > optionGroup
        $settings = array(
            "olt-wc-product-general" => array(
                "store_page_section" => array(
                    "store_page_section_group1" => array(
                        array(
                            "option_name" => "general_store_page_store_page",
                            "title" => "商店頁面",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "general_store_page_store_page",
                                "name" =>  "general_store_page_store_page",
                                "type" => "text"
                            )
                        ),
                        array(
                            "option_name" => "general_store_page_addshopcar",
                            "title" => "加入購物車行為",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "general_store_page_addshopcar",
                                "name" =>  "general_store_page_addshopcar",
                                "type" => "text"
                            )
                        )
                    ),
                ),
                "size_section" => array(
                    "store_page_section_group1" => array(
                        array(
                            "option_name" => "general_size_weightunit",
                            "title" => "重量單位",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "general_size_weightunit",
                                "name" =>  "general_size_weightunit",
                                "type" => "text"
                            )
                        ),
                        array(
                            "option_name" => "general_size_sizeunit",
                            "title" => "尺寸單位",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "general_size_sizeunit",
                                "name" =>  "general_size_sizeunit",
                                "type" => "text"
                            )
                        ),
                    )
                ),
            ),
            "olt-wc-product-stock" => array(
                "stock_stock_section" => array(
                    "stock_stock_section_group1" => array(
                        array(
                            "option_name" => "stock_stock_stockmanagement",
                            "title" => "庫存管理",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "stock_stock_stockmanagement",
                                "name" =>  "stock_stock_stockmanagement",
                                "type" => "text"
                            )
                        ),
                        array(
                            "option_name" => "stock_stock_savestock",
                            "title" => "保留庫存",
                            "callback" => array($this, "renderInput"),
                            "args" => array(
                                "id" => "stock_stock_savestock",
                                "name" =>  "stock_stock_savestock",
                                "type" => "text"
                            )
                        ),
                    )
                )
            )
        );

        return $settings;
    }

    function getOptionGroups() {
        $optionGroups = array();
        foreach ($this->getSettings() as $page => $sections) {
            foreach ($sections as $sectionId => $options) {
                $optionsKeys = array_keys($options);
                // 加入
                foreach ($optionsKeys as $optionGroup) {
                    $optionGroups[$page][] = $optionGroup;
                }
            }
        }

        return $optionGroups;
    }

    // 註冊部分
    function addSettingsSections() {
        foreach ($this->sections as $page => $section) {
            foreach ($section as $sectionId => $sectionItem) {
                add_settings_section(
                    $sectionId,
                    $sectionItem["title"],
                    $sectionItem["callback"],
                    $page,
                    $sectionItem["args"]
                );
            }
        }
    }
    function printAddSettingsSections() {

        foreach ($this->sections as $page => $section) {
            foreach ($section as $sectionId => $sectionItem) {
                printf(
                    "<p>add_settings_section('%s', '%s', '%s', '%s', '%s');</p>",
                    $sectionId,
                    $sectionItem["title"],
                    "",
                    $page,
                    print_r($sectionItem["args"], true)

                );
            }
        }
    }

    function registerSettings() {
        foreach ($this->settings as $page => $section) {
            foreach ($section as $sectionId => $settings) {
                foreach ($settings as $optionGroup => $value) {
                    foreach ($this->settings[$page][$sectionId][$optionGroup] as $setting) {
                        register_setting(
                            $optionGroup,
                            $setting["option_name"],
                            $setting["args"],
                        );
                    }
                }
            }
        }
    }

    function printRegisterSettings() {
        foreach ($this->settings as $page => $section) {
            foreach ($section as $sectionId => $settings) {
                foreach ($settings as $optionGroup => $value) {
                    foreach ($this->settings[$page][$sectionId][$optionGroup] as $setting) {
                        printf(
                            "<p>register_setting(' %s ', ' %s ',' %s ');</p>",
                            $optionGroup,
                            $setting["option_name"],
                            $setting["args"],
                        );
                    }
                }
            }
        }
    }

    function addSettingsFields() {
        foreach ($this->settings as $page => $section) {
            foreach ($section as $sectionId => $settings) {
                foreach ($settings as $optionGroup => $value) {
                    foreach ($this->settings[$page][$sectionId][$optionGroup] as $setting) {
                        add_settings_field(
                            array_key_exists("id",  $setting) ? $setting["id"] :  $setting["option_name"],
                            $setting["title"],
                            $setting["callback"], // function to print the field
                            $page,
                            $sectionId,
                            $setting["args"]
                        );
                    }
                }
            }
        }
    }

    function printAddSettingsFields() {
        foreach ($this->settings as $page => $section) {
            foreach ($section as $sectionId => $settings) {
                foreach ($settings as $optionGroup => $value) {
                    foreach ($this->settings[$page][$sectionId][$optionGroup] as $setting) {
                        printf(
                            "<p>
                            add_settings_field(
                            ' %s ',
                            ' %s ',
                            ' %s ',
                            ' %s ',
                            ' %s ',
                            ' %s ',                  
                            );</p>",
                            array_key_exists("id",  $setting) ? $setting["id"] :  $setting["option_name"],
                            $setting["title"],
                            $setting["callback"],
                            $page,
                            $sectionId,
                            "arg" // print_r($setting["args"], true)
                        );
                    }
                }
            }
        }
    }


    // 這之後會更複雜我先用這這個測試
    function renderInput($args) {
        $value = get_option($args["name"], "");

        echo "<input id='" . $args["name"] . "' 
                name='" . $args["name"] . "'
                type='text' 
                value='" . $value . "'
             >";
    }


    function outputForm2() {
?>
        <form method="post" action="options.php">

            <?php settings_fields("size_section_group1") ?>
            <?php settings_fields("store_page_section_group1") ?>

            <?php do_settings_sections("olt-wc-product-general") ?>

            <?php submit_button() ?>
        </form>
    <?php
    }
    function outputForm() {
    ?>
        <?php foreach ($this->pages as $page) : ?>
            <div class="page-<?= $page ?>" style="background-color: white; 
                    padding: 16px;
                    box-shadow: 4px 4px 8px rgba(0, 0, 0, .3);
                    border-radius:4px;
                    margin-bottom:16px">
                <h1>PAGE - <?= $page ?></h1>
                <form method="post" action="options.php">
                    <?php foreach ($this->optionGroups[$page] as $optionGroup) : ?>
                        <?php settings_fields($optionGroup) ?>
                        <?php echo "settings_fields(" . $optionGroup . ")" ?>
                    <?php endforeach; ?>
                    <?php do_settings_sections($page) ?>
                    <?php submit_button() ?>
                </form>
            </div>
        <?php endforeach; ?>
<?php
    }

    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new OltSettingsWCProduct();
        }
        return self::$instance;
    }
}

OltSettingsWCProduct::getInstance();

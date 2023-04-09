<?php

class OltSettingsWCProduct {

    static $instance = null;
    public $tabs;
    public $sections;
    public $settings;
    public $optionGroups;




    function __construct() {
        $this->tabs = $this->getTabs();
        $this->sections = $this->getSections();
        $this->settings = $this->getSettins();
        $this->optionGroups = $this->getOptionGroups();

        add_action("admin_init", array($this, "settingsInit"));
    }

    function settingsInit() {
    }

    function getTabs() {
        return array_keys($this->getSections());
    }

    function getSections() {
        $sections = array(
            "general" => array(
                "general_store_page" => array(
                    "title" => "商店頁面",
                    "callback" => function () {
                        echo "<p>這商店頁面下方補充 </p>";
                    },
                    "args" => array(),
                ),
                "general_size" => array(
                    "title" => "尺寸",
                    "callback" => function () {
                        echo "<p>尺寸下方補充 </p>";
                    },
                    "args" => array(),
                ),
            ),
            "stock" => array(
                "stock_stock" => array(
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

    function getSettins() {
        // settings 都在section裡面
        $settings = array(
            "general" => array(
                "general_store_page" => array(
                    "general_store_page_group" => array(
                        array(
                            "option_name" => "general_store_page_store_page",
                            "title" => "商店頁面",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        ),
                        array(
                            "option_name" => "general_store_page_addshopcar",
                            "title" => "加入購物車行為",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        )
                    ),
                ),
                "general_size" => array(
                    "general_size_group" => array(
                        array(
                            "option_name" => "general_size_weightunit",
                            "title" => "重量單位",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        ),
                        array(
                            "option_name" => "general_size_sizeunit",
                            "title" => "尺寸單位",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        ),
                    )
                ),
            ),
            "stock" => array(
                "stock_stock" => array(
                    "stock_stock_group" => array(
                        array(
                            "option_name" => "stock_stock_stockmanagement",
                            "title" => "庫存管理",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        ),
                        array(
                            "option_name" => "stock_stock_savestock",
                            "title" => "保留庫存",
                            "callback" => function () {
                                echo "<input type='text'>";
                            },
                            "args" => array("type" => "text")
                        ),
                    )
                )
            )
        );


        return $settings;
    }

    function getOptionGroups() {
        $optionGroups = array();
        foreach ($this->getSettins() as $tab => $sections) {
            foreach ($sections as $sectionId => $options) {
                $optionsKeys = array_keys($options);
                // 加入
                foreach ($optionsKeys as $optionGroup) {
                    $optionGroups[$tab][] = $optionGroup;
                }
            }
        }

        return $optionGroups;
    }

    function outputForm() {
?>
        <?php foreach ($this->tabs as $tab) : ?>
            <div class="tab-<?= $tab ?>" style="background-color: white; 
                    padding: 16px;
                    box-shadow: 4px 4px 8px rgba(255, 255, 255, 0.3);
                    margin-bottom:16px">
                <h1><?= $tab ?></h1>
                <form method="post" action="options.php">
                    <?php foreach ($this->optionGroups[$tab] as $optionGroup) : ?>
                        <p> <?= "settings_fields($optionGroup)" ?></p>
                    <?php endforeach; ?>
                    <p><?= "do_settings_sections($tab)" ?></p>
                    <?php submit_button() ?>
                </form>
            </div>
        <?php endforeach; ?>
<?php
    }

    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

    <?php
    // class-my-mint-admin.php.php

    /**
     * Admin functionality for My Mint Plugin.
     */

    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly.
    }

    require_once 'class-admin-functions-tab.php';
    require_once 'class-general-settings-tab.php';
    require_once 'class-style-settings-tab.php';

    class My_Mint_Admin
    {
        /**
         * Class constructor.
         */
        public function __construct()
        {
            // Add plugin settings link to the plugin list page.
            add_filter('plugin_action_links_' . plugin_basename(MY_MINT_PLUGIN_FILE), array($this, 'add_settings_link'));

            // Register and enqueue admin scripts and styles.
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            // Add menu page for plugin settings.
            add_action('admin_menu', array($this, 'add_menu_page'));

            // Register plugin settings.
            add_action('admin_init', array($this, 'register_settings'));

            // Add activation hook to set default values.
            register_activation_hook(MY_MINT_PLUGIN_FILE, array($this, 'my_mint_plugin_activate'));

            // Add script tag type module on the footer
            add_action('admin_footer', array($this, 'enqueue_connect_wallet_script'));
        }


        /**
         * Enqueue the connect-wallet-wagmi.js file.
         */
        public function enqueue_connect_wallet_script()
        {
            echo '<script type="module" src="' . plugin_dir_url(MY_MINT_PLUGIN_FILE) . '/assets/js/connect-wallet-wagmi.js"/>';
        }

        /**
         * Enqueue admin scripts and styles.
         */
        public function enqueue_scripts()
        {
            // Enqueue ethers script from the CDN
            wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);

            // Enqueue your custom admin script file
            // Define the data to be passed to the JavaScript file
            $my_mint_plugin_settings = array(
                'contractAddress' => get_option('my_mint_plugin_contract_address'),
                'contractABI' => get_option('my_mint_plugin_contract_abi'),
                'mintPrice' => get_option('my_mint_plugin_nft_price'),
                'maxQuantity' => get_option('my_mint_plugin_max_quantity'),
                'discount' => get_option('my_mint_plugin_discount_percentage'),
                'connectButtonIdOrClass' => get_option('my_mint_plugin_connect_button'),
                'activeChain' => get_option('my_mint_plugin_active_chain'),
                'mintButtonIdOrClass' => get_option('my_mint_plugin_mint_button'),
                'mintQuantityIdOrClass' => get_option('my_mint_plugin_mint_quantity'),
                'mintercounter' => get_option('my_mint_plugin_minter_counter')
            );

            // Enqueue non owner write smart contract operatons
            wp_enqueue_script('sc-write', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/sc-write-admin.js', array('jquery', 'ethers'), '0.0.1', true);

            // Enqueue read smart contract operations
            wp_enqueue_script('sc-read', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/sc-read.js', array('jquery', 'ethers', 'sc-write'), '0.0.1', true);

            // Enqueue js logic for mint ui component
            wp_enqueue_script('mint-admin', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/mint-admin-comp', array('jquery', 'ethers', 'sc-write', 'sc-read'), '0.0.1', true);

            // Localize the script with the plugin settings
            wp_localize_script('sc-read', 'myMintPluginSettings', $my_mint_plugin_settings);

            // Enqueue your custom admin styles
            wp_enqueue_style('my-mint-admin-style', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/css/my-mint-admin.css', array(), '0.0.1');

        }
        /**
         * Add plugin settings link to the plugin list page.
         *
         * @param array $links Array of plugin action links.
         * @return array Modified array of plugin action links.
         */
        public function add_settings_link($links)
        {
            $settings_link = '<a href="admin.php?page=my-mint-plugin-settings">' . __('Settings', 'my-mint-plugin') . '</a>';
            array_push($links, $settings_link);
            return $links;
        }
        public function my_mint_plugin_activate()
        {
            // Check if the options are already set
            $connect_button = get_option('my_mint_plugin_connect_button');
            $mint_button = get_option('my_mint_plugin_mint_button');
            $mint_quantity = get_option('my_mint_plugin_mint_quantity');
            $mint_counter = get_option('my_mint_plugin_minter_counter');

            // If the options are not set, initialize them with default values
            if (empty($connect_button)) {
                update_option('my_mint_plugin_connect_button', '.connect-wallet-button');
            }
            if (empty($mint_button)) {
                update_option('my_mint_plugin_mint_button', '.mint-button');
            }
            if (empty($mint_quantity)) {
                update_option('my_mint_plugin_mint_quantity', '.mint-quantity');
            }
            if (empty($mint_counter)) {
                update_option('my_mint_plugin_minter_counter', '#left-to-mint');
            }
        }

        /** 
         * Add menu page for plugin settings.
         */
        public function add_menu_page()
        {
            add_menu_page(
                __('My Mint Plugin Settings', 'my-mint-plugin'),
                __('My Mint Plugin', 'my-mint-plugin'),
                'manage_options',
                'my-mint-plugin-settings',
                array($this, 'render_settings_page'),
                'dashicons-admin-plugins',
                99
            );
        }

        /**
         * Render the content of the plugin settings page.
         */
        public function render_settings_page()
        {
            ?>
            <div class="pwrap">
                <h1>
                    <?php echo esc_html__('My Mint Plugin Settings', 'my-mint-plugin'); ?>

                </h1>
                <?php
                $this->render_wallet_connect_button();
                ?>
                <h2 class="nav-tab-wrapper">
                    <a href="?page=my-mint-plugin-settings" class="nav-tab <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'general')
                        echo 'nav-tab-active'; ?>">
                        <?php echo esc_html__('General Settings', 'my-mint-plugin'); ?>
                    </a>
                    <a href="?page=my-mint-plugin-settings&tab=admin_functions" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'admin_functions')
                        echo 'nav-tab-active'; ?>">
                        <?php echo esc_html__('Admin Functions', 'my-mint-plugin'); ?>
                    </a>
                    <a href="?page=my-mint-plugin-settings&tab=buttons_style" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'buttons_style')
                        echo 'nav-tab-active'; ?>">
                        <?php echo esc_html__('Buttons Style', 'my-mint-plugin'); ?>
                    </a>
                </h2>

                <?php
                if (isset($_GET['tab']) && $_GET['tab'] === 'admin_functions') {
                    Admin_Functions_Tab::render();
                } else if (isset($_GET['tab']) && $_GET['tab'] === 'buttons_style') {
                    Admin_Style_Tab::render();
                } else {
                    General_Settings_Tab::render();
                }
                ?>
            </div>
            <?php
        }

        /**
         * Render the wallet connect button.
         */
        public function render_wallet_connect_button()
        {
            ?>
            <w3m-core-button icon='hide'></w3m-core-button>
            <?php
        }
        /**
         * Register  plugin settings.
         */
        public function register_settings()
        {
            Admin_Functions_Tab::register_settings();
            General_Settings_Tab::register_settings();
            Admin_Style_Tab::register_settings();
        }
    }
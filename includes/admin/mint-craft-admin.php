<?php

// mint-craft-admin.php

/**
 * Admin functionality for Mint Craft.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once dirname(__FILE__) . '/../constants.php';

require_once 'mint-craft-general-settings-tab.php';
require_once 'mint-craft-admin-functions-tab.php';
require_once 'mint-craft-admin-style-tab.php';

class Mint_Craft_Admin
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Add plugin settings link to the plugin list page.
        add_filter('plugin_action_links_' . plugin_basename(PLUGIN_ROOT_PATH), array($this, 'add_settings_link'));

        // Register and enqueue admin scripts and styles.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add menu page for plugin settings.
        add_action('admin_menu', array($this, 'add_menu_page'));

        // Register plugin settings.
        add_action('admin_init', array($this, 'register_settings'));

        // Add activation hook to set default values.
        register_activation_hook(PLUGIN_ROOT_PATH, array($this, 'activate_this_plugin'));

        // Add script tag type module on the footer
        add_action('admin_footer', array($this, 'enqueue_connect_wallet_script'));

        add_action('admin_notices', array($this, 'your_plugin_show_copyright'));

    }
    public function your_plugin_show_copyright()
    {
        // Check if the current user has the capability to manage options (Administrator)
        if (current_user_can('manage_options')) {
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong>This plugin is developed and copyrighted by Bicatalyst.</strong><br>
                    Unauthorized distribution or reproduction of this plugin is prohibited.
                    For inquiries and licensing, contact us at info@bicatalyst.com .
                </p>
            </div>
            <?php
        }
    }

    /**
     * Enqueue the connect-wallet-wagmi.js file.
     */
    public function enqueue_connect_wallet_script($hook)
    {
        if (isset($_GET['page']) && $_GET['page'] === 'mint-craft-settings') {
            echo '<script type="module" src="' . plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/connect-wallet-wagmi.js"/>';
        }
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_scripts($hook)
    {
        if ($hook == 'toplevel_page_mint-craft-settings') {
            // Enqueue ethers script from the CDN
            wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);

            // Enqueue your custom admin script file
            // Define the data to be passed to the JavaScript file
            // Define the data to be passed to the JavaScript file
            $my_mint_plugin_settings = array(
                'pluginName' => MC_PLUGIN_NAME,
                // mint-craft-function_MAX_SUPPLY
                'maxSupply' => get_option(MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'MAX_SUPPLY'),
                'mintPrice' => get_option(MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'mintPrice'),
                'maxQuantity' => get_option(MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'maxQuantity'),
                'totalSupply' => get_option(MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'totalSupply'),
                'contractAddress' => get_option(MC_ADMIN_CONTRACT_ADDRESS_FIELD),
                'contractABI' => get_option(MC_ADMIN_CONTRACT_ABI_FIELD),
                'activeChain' => get_option(MC_ADMIN_ACTIVE_CHAIN_FIELD),

                // wallet connect
                'wcProjectId' => get_option(MC_ADMIN_WALLETCONNECT_FIELD),
                // alchemy
                'alchemyProvider' => get_option(MC_ADMIN_ALCHEMYPROVIDER_FIELD),
                // styles
                'minterCounterIdOrClass' => get_option(MC_PLUGIN_MINTER_COUNTER),
                'connectButtonIdOrClass' => get_option(MC_PLUGIN_CONNECT_BUTTON),
                'mintButtonIdOrClass' => get_option(MC_PLUGIN_MINT_BUTTON),
                'mintQuantityIdOrClass' => get_option(MC_PLUGIN_MINT_QUANTITY),
                // 
                'popup' => array(
                    'errorQuantity' => __('Maximum quantity allowed is', 'mint-craft'),
                    'errorChain' => __('Please switch to active chain', 'mint-craft'),
                    'errorTermAndCondition' => __('Please accept the terms and conditions to buy NFT.', 'mint-craft'),
                    'errorFunds' => __('Not enough funds in the wallet.', 'mint-craft'),
                    'generalError' => __('An error occurred.', 'mint-craft'),
                    'sucessMint' => __('Mint transaction is submitted successfully, you can view it', 'mint-craft'),
                )
            );

            // Enqueue non owner write smart contract operatons
            wp_enqueue_script('sc-write', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/sc-write-admin.js', array('jquery', 'ethers'), '0.0.1', true);

            // Enqueue read smart contract operations
            wp_enqueue_script('sc-read', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/sc-read.js', array('jquery', 'ethers', 'sc-write'), '0.0.1', true);

            // Enqueue js logic for mint ui component
            wp_enqueue_script('mint-admin', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/mint-craft-admin.js', array('jquery', 'ethers', 'sc-write', 'sc-read'), '0.0.1', true);

            // Localize the script with the plugin settings
            wp_localize_script('sc-read', 'myMintPluginSettings', $my_mint_plugin_settings);

            // Enqueue your custom admin styles
            wp_enqueue_style('mint-craft-admin-style', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/css/mint-craft-admin.css', array(), '0.0.1');
        }



    }
    /**
     * Add plugin settings link to the plugin list page.
     *
     * @param array $links Array of plugin action links.
     * @return array Modified array of plugin action links.
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=' . MC_ADMIN_MENU_SLUG . '">' . __('Settings', MC_PLUGIN_NAME) . '</a>';
        array_push($links, $settings_link);
        return $links;
    }
    public function activate_this_plugin()
    {
        // Check if the options are already set
        $connect_button = get_option(MC_PLUGIN_CONNECT_BUTTON);
        $mint_button = get_option(MC_PLUGIN_MINT_BUTTON);
        $mint_quantity = get_option(MC_PLUGIN_MINT_QUANTITY);
        $mint_counter = get_option(MC_PLUGIN_MINTER_COUNTER);

        // If the options are not set, initialize them with default values
        if (empty($connect_button)) {
            update_option(MC_PLUGIN_CONNECT_BUTTON, '.connect-wallet-button');
        }
        if (empty($mint_button)) {
            update_option(MC_PLUGIN_MINT_BUTTON, '.mint-button');
        }
        if (empty($mint_quantity)) {
            update_option(MC_PLUGIN_MINT_QUANTITY, '.mint-quantity');
        }
        if (empty($mint_counter)) {
            update_option(MC_PLUGIN_MINTER_COUNTER, '#left-to-mint');
        }
    }

    /** 
     * Add menu page for plugin settings.
     */
    public function add_menu_page()
    {
        add_menu_page(
            __('SmartCtrl' . ' Settings', 'mint-craft'),
            __('SmartCtrl', 'mint-craft'),
            'manage_options',
            MC_ADMIN_MENU_SLUG,
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
        <div class="mint-craft">
            <h1>
                <?php echo esc_html__('SmartCtrl', 'mint-craft'); ?>

            </h1>
            <?php
            $this->render_wallet_connect_button();
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo MC_ADMIN_MENU_SLUG; ?>" class="nav-tab <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'general')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('General Settings', 'mint-craft'); ?>
                </a>
                <a href="?page=<?php echo MC_ADMIN_MENU_SLUG; ?>&tab=admin_functions" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'admin_functions')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('Admin Operations', 'mint-craft'); ?>
                </a>
                <a href="?page=<?php echo MC_ADMIN_MENU_SLUG; ?>&tab=buttons_style" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'buttons_style')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('Buttons Style', 'mint-craft'); ?>
                </a>
            </h2>
            <?php
            if (isset($_GET['tab']) && $_GET['tab'] === 'admin_functions') {
                Mint_Craft_Admin_Functions_Tab::render();
            } else if (isset($_GET['tab']) && $_GET['tab'] === 'buttons_style') {
                Mint_Craft_Admin_Style_Tab::render();
            } else {
                Mint_Craft_General_Settings_Tab::render();
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
        Mint_Craft_Admin_Functions_Tab::register_settings();
        Mint_Craft_General_Settings_Tab::register_settings();
        Mint_Craft_Admin_Style_Tab::register_settings();
    }
}
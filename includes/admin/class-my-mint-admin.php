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
            'connectButtonIdOrClass' => get_option('my_mint_plugin_connect_button'),
            'mintButtonIdOrClass' => get_option('my_mint_plugin_mint_button'),
            'mintQuantityIdOrClass' => get_option('my_mint_plugin_mint_quantity'),
            'mintercounter' => get_option('my_mint_plugin_minter_counter')
        );

        wp_enqueue_script('core', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/my-mint-plugin-core.js', array('jquery', 'ethers'), '0.0.1', true);

        wp_enqueue_script('my-mint-admin-script', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/my-mint-admin.js', array('jquery', 'ethers', 'core'), '0.0.1', true);
        // Localize the script with the plugin settings
        wp_localize_script('my-mint-admin-script', 'myMintPluginSettings', $my_mint_plugin_settings);
        // Enqueue your custom admin styles
        wp_enqueue_style('my-mint-admin-style', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/css/my-mint-admin.css', array(), '0.0.1');

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
        <button class="connect-wallet-button">Connect Wallet</button>
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
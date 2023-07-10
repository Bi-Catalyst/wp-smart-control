<?php
// class-my-mint-admin.php.php

/**
 * Admin functionality for My Mint Plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once dirname(__FILE__) . '/../constants.php';

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
        register_activation_hook(MY_MINT_PLUGIN_FILE, array($this, 'activate_this_plugin'));

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
            'contractAddress' => get_option(ADMIN_CONTRACT_ADDRESS_FIELD),
            'contractABI' => get_option(ADMIN_CONTRACT_ABI_FIELD),
            'mintPrice' => get_option('my_mint_plugin_nft_price'),
            'maxQuantity' => get_option('my_mint_plugin_max_quantity'),
            'discount' => get_option('my_mint_plugin_discount_percentage'),
            'connectButtonIdOrClass' => get_option(MY_MINT_PLUGIN_CONNECT_BUTTON),
            'activeChain' => get_option(ADMIN_ACTIVE_CHAIN_FIELD),
            'mintButtonIdOrClass' => get_option(MY_MINT_PLUGIN_MINT_BUTTON),
            'mintQuantityIdOrClass' => get_option(MY_MINT_PLUGIN_MINT_QUANTITY),
            'mintercounter' => get_option(MY_MINT_PLUGIN_MINTER_COUNTER)
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
        $settings_link = '<a href="admin.php?page=' . ADMIN_MENU_SLUG . '">' . __('Settings', PLUGIN_NAME) . '</a>';
        array_push($links, $settings_link);
        return $links;
    }
    public function activate_this_plugin()
    {
        // Check if the options are already set
        $connect_button = get_option(MY_MINT_PLUGIN_CONNECT_BUTTON);
        $mint_button = get_option(MY_MINT_PLUGIN_MINT_BUTTON);
        $mint_quantity = get_option(MY_MINT_PLUGIN_MINT_QUANTITY);
        $mint_counter = get_option(MY_MINT_PLUGIN_MINTER_COUNTER);

        // If the options are not set, initialize them with default values
        if (empty($connect_button)) {
            update_option(MY_MINT_PLUGIN_CONNECT_BUTTON, '.connect-wallet-button');
        }
        if (empty($mint_button)) {
            update_option(MY_MINT_PLUGIN_MINT_BUTTON, '.mint-button');
        }
        if (empty($mint_quantity)) {
            update_option(MY_MINT_PLUGIN_MINT_QUANTITY, '.mint-quantity');
        }
        if (empty($mint_counter)) {
            update_option(MY_MINT_PLUGIN_MINTER_COUNTER, '#left-to-mint');
        }
    }

    /** 
     * Add menu page for plugin settings.
     */
    public function add_menu_page()
    {
        add_menu_page(
            __('My Mint Plugin Settings', PLUGIN_NAME),
            __('My Mint Plugin', PLUGIN_NAME),
            'manage_options',
            ADMIN_MENU_SLUG,
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
                <?php echo esc_html__('My Mint Plugin Settings', PLUGIN_NAME); ?>

            </h1>
            <?php
            $this->render_wallet_connect_button();
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo ADMIN_MENU_SLUG; ?>" class="nav-tab <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'general')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('General Settings', PLUGIN_NAME); ?>
                </a>
                <a href="?page=<?php echo ADMIN_MENU_SLUG; ?>&tab=admin_functions" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'admin_functions')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('Admin Functions', PLUGIN_NAME); ?>
                </a>
                <a href="?page=<?php echo ADMIN_MENU_SLUG; ?>&tab=buttons_style" class="nav-tab <?php if (isset($_GET['tab']) && $_GET['tab'] === 'buttons_style')
                       echo 'nav-tab-active'; ?>">
                    <?php echo esc_html__('Buttons Style', PLUGIN_NAME); ?>
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
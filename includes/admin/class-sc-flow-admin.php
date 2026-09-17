<?php

/**
 * Admin functionalities for Smart Contracts Flow.
 * This tab will be containing the general setting for the smart contract
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {class-sc-flow-admin.php}
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once dirname(__FILE__) . '/../constants.php';

require_once 'class-sc-flow-general-settings-tab.php';
require_once 'class-sc-flow-admin-functions-tab.php';
require_once 'class-sc-flow-admin-style-tab.php';
require_once 'class-sc-flow-crossmint-tab.php';

class SC_Flow_Admin
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Add plugin settings link to the plugin list page.
        add_filter('plugin_action_links_' . plugin_basename(SC_FLOW_PLUGIN_FILE), array($this, 'add_settings_link'));

        // Register and enqueue admin scripts and styles.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add menu page for plugin settings.
        add_action('admin_menu', array($this, 'add_menu_page'));

        // Register plugin settings.
        add_action('admin_init', array($this, 'register_settings'));

        // Add activation hook to set default values.
        register_activation_hook(SC_FLOW_PLUGIN_FILE, array($this, 'activate_this_plugin'));

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
                    <strong>&copy; 2023 Bicatalyst. All rights reserved.</strong><br>
                    Unauthorized distribution or reproduction of this plugin is strictly prohibited.
                    For licensing and inquiries, please contact us at <a href="mailto:info@bicatalyst.com">info@bicatalyst.com</a>.
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
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_' . SC_FLOW_ADMIN_MENU_SLUG) {
            echo '<script type="module" src="' . plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/connect-wallet-wagmi.js"/>';
        }
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_scripts($hook)
    {
        if ($hook == 'toplevel_page_sc-flow-settings') {
            // Enqueue ethers script from the CDN
            wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);

            // Enqueue your custom admin script file
            // Define the data to be passed to the JavaScript file
            // Define the data to be passed to the JavaScript file
            $sc_flow_plugin_settings = array(
                'pluginName' => SC_FLOW_PLUGIN_NAME,
                // sc-flow-function_MAX_SUPPLY
                'maxSupply' => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'maxSupply'),
                'mintPrice' => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'mintPrice'),
                'maxQuantity' => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'maxQuantity'),
                'totalSupply' => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'totalSupply'),
                'contractAddress' => get_option(SC_FLOW_ADMIN_CONTRACT_ADDRESS_FIELD),
                'contractABI' => get_option(SC_FLOW_ADMIN_CONTRACT_ABI_FIELD),
                'activeChain' => get_option(SC_FLOW_ADMIN_ACTIVE_CHAIN_FIELD),

                // wallet connect
                'wcProjectId' => get_option(SC_FLOW_ADMIN_WALLETCONNECT_FIELD),
                // Alchemy Provider
                'alchemyProvider' => get_option(SC_FLOW_ADMIN_ALCHEMYPROVIDER_FIELD),
                // Fiat currency 
                'fiatCurrency' => get_option(SC_FLOW_ADMIN_FIAT_CURRENCY_FIELD),
                // styles
                'minterCounterIdOrClass' => get_option(SC_FLOW_PLUGIN_MINTER_COUNTER),
                'connectButtonIdOrClass' => get_option(SC_FLOW_PLUGIN_CONNECT_BUTTON),
                'mintButtonIdOrClass' => get_option(SC_FLOW_PLUGIN_MINT_BUTTON),
                'mintQuantityIdOrClass' => get_option(SC_FLOW_PLUGIN_MINT_QUANTITY),
                // 
                'popup' => array(
                    'errorQuantity' => __('Maximum quantity allowed is', 'sc-flow'),
                    'errorChain' => __('Please switch to active chain', 'sc-flow'),
                    'errorTermAndCondition' => __('Please accept the terms and conditions to buy NFT.', 'sc-flow'),
                    'errorFunds' => __('Not enough funds in the wallet.', 'sc-flow'),
                    'generalError' => __('An error occurred.', 'sc-flow'),
                    'sucessMint' => __('Mint transaction is submitted successfully, you can view it', 'sc-flow'),
                )
            );
            // Enqueue non owner write smart contract operatons
            wp_enqueue_script('sc-flow-helper', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-helpers.js', array('jquery', 'ethers'), '0.0.1', true);

            // Enqueue non owner write smart contract operatons
            wp_enqueue_script('sc-flow-write', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-write-admin.js', array('jquery', 'ethers', 'sc-flow-helper'), '0.0.1', true);

            // Enqueue read smart contract operations
            wp_enqueue_script('sc-flow-read', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-read.js', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-write'), '0.0.1', true);

            // Enqueue js logic for mint ui component
            wp_enqueue_script('sc-flow-admin', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-admin.js', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-write', 'sc-flow-read'), '0.0.1', true);

            // Localize the script with the plugin settings
            // Localize the script with the plugin settings
            wp_localize_script('sc-flow-read', 'SCFlowPluginSettings', $sc_flow_plugin_settings);
            wp_localize_script('sc-flow-admin', 'SCFlowPluginSettings', $sc_flow_plugin_settings);

            // Enqueue your custom admin styles
            wp_enqueue_style('sc-flow-admin-style', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/css/sc-flow-admin.css', array(), '0.0.1');
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
        $settings_link = '<a href="admin.php?page=' . SC_FLOW_ADMIN_MENU_SLUG . '">' . __('Settings', 'sc-flow') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }
    public function activate_this_plugin()
    {
        // Check if the options are already set
        $connect_button = get_option(SC_FLOW_PLUGIN_CONNECT_BUTTON);
        $mint_button = get_option(SC_FLOW_PLUGIN_MINT_BUTTON);
        $mint_quantity = get_option(SC_FLOW_PLUGIN_MINT_QUANTITY);
        $mint_counter = get_option(SC_FLOW_PLUGIN_MINTER_COUNTER);
        $fiat_currency = get_option(SC_FLOW_ADMIN_FIAT_CURRENCY_FIELD);

        // If the options are not set, initialize them with default values
        if (empty($connect_button)) {
            update_option(SC_FLOW_PLUGIN_CONNECT_BUTTON, '.connect-wallet-button');
        }
        if (empty($mint_button)) {
            update_option(SC_FLOW_PLUGIN_MINT_BUTTON, '.mint-btn-one');
        }
        if (empty($mint_quantity)) {
            update_option(SC_FLOW_PLUGIN_MINT_QUANTITY, '.nft-quantity');
        }
        if (empty($mint_counter)) {
            update_option(SC_FLOW_PLUGIN_MINTER_COUNTER, '#left-to-mint');
        }
        if (empty($fiat_currency)) {
            update_option(SC_FLOW_ADMIN_FIAT_CURRENCY_FIELD, 'chf');
        }
    }

    /** 
     * Add menu page for plugin settings.
     */
    public function add_menu_page()
    {
        add_menu_page(
            __('Smart Contract Flow' . ' Settings', 'sc-flow'),
            __('Smart Contract Flow', 'sc-flow'),
            'manage_options',
            SC_FLOW_ADMIN_MENU_SLUG,
            array($this, 'render_settings_page'),
            'dashicons-admin-plugins',
            99
        );
    }

    /**
     * Tab slugs and their labels, in display order.
     *
     * @return array<string,string>
     */
    private function tabs()
    {
        return array(
            'general'            => __('General Settings', 'sc-flow'),
            'admin_functions'    => __('Admin Operations', 'sc-flow'),
            'crossmint_settings' => __('Crossmint configuration', 'sc-flow'),
            'buttons_style'      => __('Buttons attributes', 'sc-flow'),
        );
    }

    /**
     * Current tab from the query string, restricted to known tabs.
     *
     * @return string
     */
    private function current_tab()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tab selection.
        $requested = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'general';
        return array_key_exists($requested, $this->tabs()) ? $requested : 'general';
    }

    /**
     * Render the content of the plugin settings page.
     */
    public function render_settings_page()
    {
        $current = $this->current_tab();
        ?>
        <div class="sc-flow">
            <h1><?php esc_html_e('Smart Contract Flow', 'sc-flow'); ?></h1>
            <?php $this->render_wallet_connect_button(); ?>
            <h2 class="nav-tab-wrapper">
                <?php foreach ($this->tabs() as $slug => $label) : ?>
                    <a href="<?php echo esc_url(add_query_arg(array('page' => SC_FLOW_ADMIN_MENU_SLUG, 'tab' => $slug), admin_url('admin.php'))); ?>"
                       class="nav-tab <?php echo $slug === $current ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </h2>
            <?php $this->render_tab($current); ?>
        </div>
        <?php
    }

    /**
     * Render the body of one settings tab.
     *
     * @param string $tab Tab slug.
     */
    private function render_tab($tab)
    {
        switch ($tab) {
            case 'admin_functions':
                SC_Flow_Admin_Functions_Tab::render();
                break;
            case 'buttons_style':
                SC_Flow_Admin_Style_Tab::render();
                break;
            case 'crossmint_settings':
                SC_Flow_Admin_Crossmint_Tab::render();
                break;
            default:
                SC_Flow_General_Settings_Tab::render();
        }
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
        SC_Flow_Admin_Functions_Tab::register_settings();
        SC_Flow_General_Settings_Tab::register_settings();
        SC_Flow_Admin_Style_Tab::register_settings();
        SC_Flow_Admin_Crossmint_Tab::register_settings();

    }
}
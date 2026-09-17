<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {class-sc-flow-public.php}
 */

require_once 'constants.php';

class SmartContract_Flow_Public
{

    /**
     * The constructor.
     */
    public function __construct()
    {
        // Enqueue necessary scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add script tag type module on the footer
        add_action('wp_footer', array($this, 'enqueue_connect_wallet_script'), 1);

        add_action('wp_nav_menu_items', array($this, 'add_logo_nav_menu'), 10, 2);

        // Register shortcodes
        add_shortcode('connect_wallet', array($this, 'connect_wallet_shortcode'));

        add_shortcode('mint_button', array($this, 'mint_button_shortcode'));

        add_shortcode('crossmint_payment_button', array($this, 'crossmint_shortcode'));
    }
    public function add_logo_nav_menu($items, $args)
    {
        $items .= '<li class="cstm-m-cnct-wlt"><a title="' . esc_attr__('Connect Wallet', 'sc-flow') . '" href="#"><w3m-core-button icon="hide"></w3m-core-button></a></li>';
        return $items;
    }

    public function enqueue_connect_wallet_script()
    {
        // This will work on browsers that support newer Javascript syntax
        // Get the saved slugs option.
        $slugs_option = get_option(SC_FLOW_ADMIN_SLUGS_FIELD);
        // Check if we have saved slugs.
        if (!empty($slugs_option)) {
            // Convert the comma-separated slugs into an array.
            $slugs = array_map('trim', explode(',', $slugs_option));

            // Check if the current page's slug matches any of the saved slugs.
            $current_slug = basename(get_permalink());
            if (in_array($current_slug, $slugs)) {
                $sc_flow_plugin_settings = $this->get_sc_flow_plugin_settings();
    
                // Move the script enqueue and inline script inside the slug check
                wp_enqueue_script('placeholder-for-inline-script', '', array('jquery'), true);
                $inline_script = 'window.SCFlowPluginSettings = ' . json_encode($sc_flow_plugin_settings) . ';';
                wp_add_inline_script('placeholder-for-inline-script', $inline_script, 'before');
    
                echo '<script type="module" src="' . plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/connect-wallet-wagmi.js"></script>';
            }
        }
    }

    public function enqueue_scripts()
    {


        // Get the saved slugs option.
        $slugs_option = get_option(SC_FLOW_ADMIN_SLUGS_FIELD);

        // Check if we have saved slugs.
        if (!empty($slugs_option)) {
            // Convert the comma-separated slugs into an array.
            $slugs = array_map('trim', explode(',', $slugs_option));

            // Check if the current page's slug matches any of the saved slugs.
            $current_slug = basename(get_permalink());

            if (in_array($current_slug, $slugs)) {

                $sc_flow_plugin_settings = $this->get_sc_flow_plugin_settings();

                wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);


                // Enqueue non owner write smart contract operatons
                wp_enqueue_script('sc-flow-helper', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-helpers.js', array('jquery', 'ethers'), '0.0.1', true);

                // Enqueue read smart contract operations
                wp_enqueue_script('sc-flow-read', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-read.js', array('jquery', 'ethers', 'sc-flow-helper'), '0.0.1', true);

                // Enqueue js logic for mint ui component
                wp_enqueue_script('sc-flow-frontend', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-frontend.js?v=0.0.3', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-read'), '0.0.1', true);

                // Enqueue crossmint script
                wp_enqueue_script('crossmint', 'https://unpkg.com/@crossmint/client-sdk-vanilla-ui@1.0.1-alpha.6/lib/index.global.js', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-read', 'sc-flow-frontend'), '0.1.0', true);

                // Localize the script with the plugin settings
                wp_localize_script('sc-flow-read', 'SCFlowPluginSettings', $sc_flow_plugin_settings);
                wp_localize_script('sc-flow-frontend', 'SCFlowPluginSettings', $sc_flow_plugin_settings);
                wp_localize_script('sc-flow-helper', 'SCFlowPluginSettings', $sc_flow_plugin_settings);


                // Enqueue your custom styles
                wp_enqueue_style('sc-flow-style', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/css/sc-flow-frontend.css', array(), '0.0.1');
            }
        }

    }

    protected function get_sc_flow_plugin_settings()
    {
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
        return $sc_flow_plugin_settings;

    }
    /**
     * Connect wallet shortcode.
     *
     * @return string The shortcode output.
     */
    public function connect_wallet_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'id' => 'connect-wallet-button',
                'class' => 'connect-wallet-button',
            ),
            $atts
        );

        return '<w3m-core-button icon="hide" id="' . esc_attr($atts['id']) . '" class="' . esc_attr($atts['class']) . '"></w3m-core-button>';
    }

    /**
     * Mint button shortcode.
     *
     * @return string The shortcode output.
     */
    public function mint_button_shortcode($atts)
    {
        // Parse the attributes and provide default values
        $a = shortcode_atts(
            array(
                'class' => 'mint-btn-one',
                'id' => 'wallet-mint-btn',
            ),
            $atts
        );

        return '<button class="' . esc_attr($a['class']) . '" id="' . esc_attr($a['id']) . '">' . esc_html__('Buy with wallet', 'sc-flow') . '</button>';
    }

    /**
     * cross mint payment button
     *
     * @return string The shortcode output.
     */

    public function crossmint_shortcode()
    {
        $mint_config = array(
            'type'       => get_option(SC_FLOW_PLUGIN_CROSSMINT_ERC_TYPE),
            'quantity'   => '1',
            'totalPrice' => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'mintPrice'),
        );
        return sprintf(
            '<crossmint-pay-button class="xmint-btn" collectionId="%s" projectId="%s" environment="%s" mintConfig="%s"></crossmint-pay-button>',
            esc_attr(get_option(SC_FLOW_PLUGIN_CROSSMINT_COLLECTION_ID)),
            esc_attr(get_option(SC_FLOW_PLUGIN_CROSSMINT_PROJECT_ID)),
            esc_attr(get_option(SC_FLOW_PLUGIN_CROSSMINT_ENVIRONMENT)),
            esc_attr(wp_json_encode($mint_config))
        );
    }
}

// Instantiate the public class.
new SmartContract_Flow_Public();
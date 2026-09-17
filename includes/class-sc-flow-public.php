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
require_once 'class-sc-flow-settings.php';

class SmartContract_Flow_Public
{

    /**
     * The constructor.
     */
    public function __construct()
    {
        // Enqueue necessary scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

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

    /**
     * Whether the current request is a singular page listed in the Page Slugs setting.
     *
     * @return bool
     */
    private function is_enabled_page()
    {
        if (!is_singular()) {
            return false;
        }
        $slugs = array_filter(array_map('trim', explode(',', get_option(SC_FLOW_ADMIN_SLUGS_FIELD, ''))));
        return in_array(get_post_field('post_name', get_queried_object_id()), $slugs, true);
    }

    public function enqueue_scripts()
    {
        if (!$this->is_enabled_page()) {
            return;
        }
        $sc_flow_plugin_settings = SC_Flow_Settings::for_script();

        wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);


        // Enqueue non owner write smart contract operatons
        wp_enqueue_script('sc-flow-helper', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-helpers.js', array('jquery', 'ethers'), SC_FLOW_VERSION, true);

        // Enqueue read smart contract operations
        wp_enqueue_script('sc-flow-read', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-read.js', array('jquery', 'ethers', 'sc-flow-helper'), SC_FLOW_VERSION, true);

        // Enqueue js logic for mint ui component
        wp_enqueue_script('sc-flow-frontend', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/js/sc-flow-frontend.js', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-read'), SC_FLOW_VERSION, true);

        // Enqueue crossmint script
        wp_enqueue_script('crossmint', 'https://unpkg.com/@crossmint/client-sdk-vanilla-ui@1.0.1-alpha.6/lib/index.global.js', array('jquery', 'ethers', 'sc-flow-helper', 'sc-flow-read', 'sc-flow-frontend'), '0.1.0', true);

        // Localize the script with the plugin settings
        wp_localize_script('sc-flow-read', 'SCFlowPluginSettings', $sc_flow_plugin_settings);
        wp_localize_script('sc-flow-frontend', 'SCFlowPluginSettings', $sc_flow_plugin_settings);
        wp_localize_script('sc-flow-helper', 'SCFlowPluginSettings', $sc_flow_plugin_settings);


        // Enqueue your custom styles
        wp_enqueue_style('sc-flow-style', plugin_dir_url(SC_FLOW_PLUGIN_FILE) . 'assets/css/sc-flow-frontend.css', array(), SC_FLOW_VERSION);

        SmartContract_Flow::enqueue_wallet_module();
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
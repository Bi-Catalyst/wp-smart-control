<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {sc-flow-public.php}
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
        $items .= '<li class="cstm-m-cnct-wlt"><a title="Connect Wallet" href="#" ><w3m-core-button icon="hide"></w3m-core-button></a></li>';
        return $items;
    }

    public function enqueue_connect_wallet_script()
    {
        // This will work on browsers that support newer Javascript syntax
        // Get the saved slugs option.
        $slugs_option = get_option(MC_ADMIN_SLUGS_FIELD);

        // Check if we have saved slugs.
        if (!empty($slugs_option)) {
            // Convert the comma-separated slugs into an array.
            $slugs = array_map('trim', explode(',', $slugs_option));

            // Check if the current page's slug matches any of the saved slugs.
            $current_slug = basename(get_permalink());

            if (in_array($current_slug, $slugs)) {

                echo '<script type="module" src="' . plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/connect-wallet-wagmi.js"/>';
            }
        }
    }

    public function enqueue_scripts()
    {


        // Get the saved slugs option.
        $slugs_option = get_option(MC_ADMIN_SLUGS_FIELD);

        // Check if we have saved slugs.
        if (!empty($slugs_option)) {
            // Convert the comma-separated slugs into an array.
            $slugs = array_map('trim', explode(',', $slugs_option));

            // Check if the current page's slug matches any of the saved slugs.
            $current_slug = basename(get_permalink());

            if (in_array($current_slug, $slugs)) {
                wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);

                // Define the data to be passed to the JavaScript file
                $sc_flow_plugin_settings = array(
                    'pluginName' => MC_PLUGIN_NAME,
                    // sc-flow-function_MAX_SUPPLY
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
                        'errorQuantity' => __('Maximum quantity allowed is', 'sc-flow'),
                        'errorChain' => __('Please switch to active chain', 'sc-flow'),
                        'errorTermAndCondition' => __('Please accept the terms and conditions to buy NFT.', 'sc-flow'),
                        'errorFunds' => __('Not enough funds in the wallet.', 'sc-flow'),
                        'generalError' => __('An error occurred.', 'sc-flow'),
                        'sucessMint' => __('Mint transaction is submitted successfully, you can view it', 'sc-flow'),
                    )
                );

                // Enqueue non owner write smart contract operatons
                // wp_enqueue_script('sc-write-fe', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/sc-write-frontend.js', array('jquery', 'ethers'), '0.0.1', true);

                // Enqueue read smart contract operations
                wp_enqueue_script('sc-flow-read', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/sc-flow-read.js', array('jquery', 'ethers'), '0.0.1', true);

                // Enqueue js logic for mint ui component
                wp_enqueue_script('sc-flow-frontend', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/js/sc-flow-frontend.js', array('jquery', 'ethers', 'sc-flow-read'), '0.0.1', true);

                // Enqueue crossmint script
                wp_enqueue_script('crossmint', 'https://unpkg.com/@crossmint/client-sdk-vanilla-ui@1.0.1-alpha.6/lib/index.global.js', array('jquery', 'ethers', 'sc-flow-read', 'sc-flow-frontend'), '0.1.0', true);

                // Localize the script with the plugin settings
                wp_localize_script('sc-flow-plugin-settings', 'SCFlowPluginSettings', $sc_flow_plugin_settings);

                // Enqueue your custom styles
                wp_enqueue_style('sc-flow-style', plugin_dir_url(PLUGIN_ROOT_PATH) . 'assets/css/sc-flow-frontend.css', array(), '0.0.1');
            }
        }

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

        $id = esc_attr($atts['id']);
        $class = esc_attr($atts['class']);

        ob_start();
        ?>
        <!-- <div class="wallet-dropdown" style="display: none;"></div> -->
        <w3m-core-button icon='hide'></w3m-core-button>
        <?php
        return ob_get_clean();
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

        ob_start();
        ?>
        <button class="<?php echo esc_attr($a['class']); ?>" id="<?php echo esc_attr($a['id']); ?>">KAUF MIT WALLET</button>
        <?php
        return ob_get_clean();
    }

    /**
     * cross mint payment button
     *
     * @return string The shortcode output.
     */

    public function crossmint_shortcode()
    {
        ob_start();
        ?>
        <crossmint-pay-button class="xmint-btn" collectionId="095ec891-3c1f-4ec1-ae25-0387b38bd3ac"
            projectId="d053727b-90ce-45fb-bb65-ba9c833009d0" environment="staging" mintConfig='<?php echo json_encode([
                "type" => "erc-721",
                "quantity" => "1",
                "totalPrice" => get_option(MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . "mintPrice"),
            ]); ?>' />
        <?php
        return ob_get_clean();

    }
}

// Instantiate the public class.
new SmartContract_Flow_Public();
<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://yourwebsite.com
 * @since      0.0.1
 *
 * @package    My_Mint_Plugin
 * @subpackage My_Mint_Plugin/public
 */

class My_Mint_Public
{

    /**
     * The constructor.
     */
    public function __construct()
    {
        // Enqueue necessary scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add script tag type module on the footer
        add_action('wp_footer', array($this, 'enqueue_connect_wallet_script'));

        add_action('wp_nav_menu_items', array($this, 'add_logo_nav_menu'), 10, 2);

        // Register shortcodes
        add_shortcode('connect_wallet', array($this, 'connect_wallet_shortcode'));

        add_shortcode('mint_button', array($this, 'mint_button_shortcode'));

        add_shortcode('mint_box', array($this, 'mint_box_shortcode'));

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
        echo '<script type="module" src="' . plugin_dir_url(MY_MINT_PLUGIN_FILE) . '/assets/js/connect-wallet-wagmi.js"/>';
    }

    public function enqueue_scripts()
    {
        // Enqueue ethers script from the CDN
        wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);

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

        // Enqueue non owner write smart contract operatons
        wp_enqueue_script('sc-write-fe', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/sc-write-frontend.js', array('jquery', 'ethers'), '0.0.1', true);

        // Enqueue read smart contract operations
        wp_enqueue_script('sc-read', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/sc-read.js', array('jquery', 'ethers', 'sc-write-fe'), '0.0.1', true);

        // Enqueue js logic for mint ui component
        wp_enqueue_script('mint-frontend', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/mint-comp-fe.js', array('jquery', 'ethers', 'sc-write-fe', 'sc-read'), '0.0.1', true);

        // Enqueue crossmint script
        wp_enqueue_script('crossmint', 'https://unpkg.com/@crossmint/client-sdk-vanilla-ui@0.1.0/lib/index.global.js', array('jquery', 'ethers', 'sc-write-fe', 'sc-read', 'mint-frontend'), '0.1.0', true);

        // Localize the script with the plugin settings
        wp_localize_script('mint-frontend', 'myMintPluginSettings', $my_mint_plugin_settings);

        // Enqueue your custom styles
        wp_enqueue_style('my-mint-plugin-style', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/css/my-mint-plugin.css', array(), '0.0.1');
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
    public function mint_button_shortcode()
    {
        ob_start();
        ?>
        <button class="mint-button">KAUF MIT WALLET</button>
        <w3m-core-button icon="hide"></w3m-core-button>
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
        <crossmint-pay-button class="xmint-btn" clientId="14bea3bf-c1dc-4f44-b847-93b8425f0989" environment="staging"
            mintConfig='{
            "type": "erc-721",
            "quantity": "1",
            "totalPrice": "0.001"
        }' />
        <?php
        return ob_get_clean();
    }

    /**
     * Mint button shortcode.
     *
     * @return string The shortcode output.
     */
    public function mint_box_shortcode()
    {
        ob_start();
        ?>
        <div class="mint-form-wrapper">
            <div class="inner-wrapper">
                <span class="inner-label">Verkauft:</span>
                <span class="supply-left-num" id="left-to-mint">5/3000</span>
            </div>
            <div class="inner-wrapper inner-wrapper-quantity ">
                <p class="inner-label">Anzahl:</p>
                <div class="quantity-input-section">
                    <button type="button" class="decrease-btn">-</button>
                    <input type="text" class="nft-quantity" value="1">
                    <button type="button" class="increase-btn">+</button>
                </div>
            </div>
            <div class="inner-wrapper">
                <span class="inner-label">Preis:</span>
                <span class="price-right-col"><span class="eth-price">0.1ETH</span>
                    <span> ~ </span>
                    <span class="fiat-price">120USD</span>
                </span>
            </div>
            <div>
                <p class="term-input-message"><input type="checkbox" checked="" name="terms" class="terms-checkbox">Mit dem Kauf
                    bestätige ich die <a href="" class="terms-link-stl">AGB’s</a>, <a href=""
                        class="terms-link-stl">Datenschutzbestimmungen </a>und dass ich das 18. Lebensjahr erreicht habe.
                </p>
            </div>
            <div class="mint-form-btns">
                <button type="button" class="mint-btn-one" id="wallet-mint-btn">
                    KAUF MIT WALLET
                </button>
                <button type="button" class="mint-btn-two" id="cross-mint-btn">
                    KAUF MIT KREDITKARTE
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}

// Instantiate the public class.
new My_Mint_Public();
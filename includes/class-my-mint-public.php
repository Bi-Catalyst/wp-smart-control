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

        // Register shortcodes
        add_shortcode('connect_wallet', array($this, 'connect_wallet_shortcode'));
        add_shortcode('mint_button', array($this, 'mint_button_shortcode'));
        add_shortcode('mint_box', array($this, 'mint_box_shortcode'));
        add_shortcode('crossmint_payment_button', array($this, 'crossmint_shortcode'));
    }

    public function enqueue_scripts()
    {
        // Enqueue ethers script from the CDN
        wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.5.1/ethers.umd.min.js', array(), '6.5.1', true);
        // wp_enqueue_script('ethers', 'https://cdn.ethers.io/lib/ethers-5.2.umd.min.js', array(), '5.2', true);
        wp_enqueue_script('web3modal', 'https://unpkg.com/web3modal', array(), '1.9.12', true);
        wp_enqueue_script('walletconnect', 'https://unpkg.com/@walletconnect/web3-provider', array(), '1.9.12', true);



        // wp_enqueue_script('ethers', 'https://www.unpkg.com/browse/walletconnect@1.7.8/dist/umd/index.min.js', array(), '2.4.0', true);
        // wp_enqueue_script('ethers', 'https://unpkg.com/browse/@web3modal/ui@2.4.0/dist/index.js', array(), '2.4.0', true);

        // Enqueue Web3.js
        // wp_enqueue_script('web3', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/web3.min.js', array(), '1.3.6', true);

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

        wp_enqueue_script('commonWallet', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/commonWallet.js', array('jquery', 'ethers', 'web3modal', 'walletconnect'), '0.0.1', true);

        wp_enqueue_script('core', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/my-mint-plugin-core.js', array('jquery', 'ethers', 'web3modal', 'walletconnect', 'commonWallet'), '0.0.1', true);

        // Enqueue your custom script file that contains the wallet connection logic
        wp_enqueue_script('my-mint-plugin-script', plugin_dir_url(MY_MINT_PLUGIN_FILE) . 'assets/js/my-mint-plugin.js', array('jquery', 'ethers', 'web3modal', 'walletconnect', 'commonWallet', 'core'), '0.0.1', true);

        // Enqueue crossmint script
        wp_enqueue_script('crossmint', 'https://unpkg.com/@crossmint/client-sdk-vanilla-ui@0.1.0/lib/index.global.js', array('jquery', 'ethers', 'web3modal', 'walletconnect', 'commonWallet', 'core', 'my-mint-plugin-script'), '0.1.0', true);


        // Localize the script with the plugin settings
        wp_localize_script('my-mint-plugin-script', 'myMintPluginSettings', $my_mint_plugin_settings);

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
        <button id="<?php echo $id; ?>" class="<?php echo $class; ?>">Connect Wallet</button>
        <div class="wallet-dropdown" style="display: none;"></div>
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
        <crossmint-pay-button clientId="14bea3bf-c1dc-4f44-b847-93b8425f0989" environment="staging" mintConfig='{
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
<?php
/**
 * Plugin settings exposed to JavaScript.
 *
 * Single owner of the SCFlowPluginSettings payload used by both the
 * admin page and the public mint page.
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {class-sc-flow-settings.php}
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/constants.php';

class SC_Flow_Settings
{
    /**
     * Build the settings array localized as window.SCFlowPluginSettings.
     *
     * @return array<string,mixed>
     */
    public static function for_script()
    {
        return array(
            'pluginName'             => SC_FLOW_PLUGIN_NAME,
            'maxSupply'              => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'maxSupply'),
            'mintPrice'              => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'mintPrice'),
            'maxQuantity'            => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'maxQuantity'),
            'totalSupply'            => get_option(SC_FLOW_ADMIN_FUNCTIONS_FIELDS_PREFIX . 'totalSupply'),
            'contractAddress'        => get_option(SC_FLOW_ADMIN_CONTRACT_ADDRESS_FIELD),
            'contractABI'            => get_option(SC_FLOW_ADMIN_CONTRACT_ABI_FIELD),
            'activeChain'            => get_option(SC_FLOW_ADMIN_ACTIVE_CHAIN_FIELD),
            'wcProjectId'            => self::secret('SC_FLOW_WALLETCONNECT_PROJECT_ID', SC_FLOW_ADMIN_WALLETCONNECT_FIELD),
            'alchemyProvider'        => self::secret('SC_FLOW_ALCHEMY_API_KEY', SC_FLOW_ADMIN_ALCHEMYPROVIDER_FIELD),
            'fiatCurrency'           => get_option(SC_FLOW_ADMIN_FIAT_CURRENCY_FIELD),
            'minterCounterIdOrClass' => get_option(SC_FLOW_PLUGIN_MINTER_COUNTER),
            'connectButtonIdOrClass' => get_option(SC_FLOW_PLUGIN_CONNECT_BUTTON),
            'mintButtonIdOrClass'    => get_option(SC_FLOW_PLUGIN_MINT_BUTTON),
            'mintQuantityIdOrClass'  => get_option(SC_FLOW_PLUGIN_MINT_QUANTITY),
            'popup'                  => array(
                'errorQuantity'         => __('Maximum quantity allowed is', 'sc-flow'),
                'errorChain'            => __('Please switch to active chain', 'sc-flow'),
                'errorTermAndCondition' => __('Please accept the terms and conditions to buy NFT.', 'sc-flow'),
                'errorFunds'            => __('Not enough funds in the wallet.', 'sc-flow'),
                'generalError'          => __('An error occurred.', 'sc-flow'),
                'sucessMint'            => __('Mint transaction is submitted successfully, you can view it', 'sc-flow'),
            ),
        );
    }

    /**
     * Read a credential from wp-config.php when defined, else from the option.
     *
     * Lets deployments keep API keys out of the database.
     *
     * @param string $constant_name Constant that may be defined in wp-config.php.
     * @param string $option_name   Fallback option name.
     * @return string
     */
    private static function secret($constant_name, $option_name)
    {
        if (defined($constant_name)) {
            return (string) constant($constant_name);
        }
        return (string) get_option($option_name, '');
    }
}

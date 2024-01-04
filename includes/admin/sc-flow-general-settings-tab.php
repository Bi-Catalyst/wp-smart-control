<?php

/**
 * Admin General settings tab
 * This tab will be containing the general setting for the smart contract
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {sc-flow-general-settings-tab.php}
 */

require_once dirname(__FILE__) . '/../constants.php';

class SC_Flow_General_Settings_Tab
{
    /**
     * Render the content of the general settings tab.
     */
    public static function render()
    {
        ?>
        <div class="sc-flow">
            <form method="post" action="options.php">

                <?php
                // Output security fields.
                settings_fields(MC_ADMIN_GENERAL_FIELDS);
                // Output setting sections.
                do_settings_sections(MC_ADMIN_GENERAL_PAGE);
                // Output submit button.
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function register_settings()
    {
        // Register a settings section.
        add_settings_section(
            MC_ADMIN_GENERAL_SECTION_TITLE,
            __('General Settings', 'sc-flow'),
            array(__CLASS__, 'render_general_settings_section'),
            MC_ADMIN_GENERAL_PAGE
        );

        // Register settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_CONTRACT_ADDRESS_FIELD,
            'sanitize_text_field'
        );
        // Register slug settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_SLUGS_FIELD,
            'sanitize_text_field'
        );

        // Register walletconnect settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_WALLETCONNECT_FIELD,
            'sanitize_text_field'
        );

        // Register alchemyprovider settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_ALCHEMYPROVIDER_FIELD,
            'sanitize_text_field'
        );

        // Register fiat currency symbol settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_FIAT_CURRENCY_FIELD,
            'sanitize_text_field'
        );
        // Register a settings field for contract ABI.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_CONTRACT_ABI_FIELD,
            array(__CLASS__, 'sanitize_contract_abi_field')
        );

        // Register a settings field for active chain.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_ACTIVE_CHAIN_FIELD,
            'sanitize_text_field'
        );

        // Add a field for contract address.
        add_settings_field(
            MC_ADMIN_CONTRACT_ADDRESS_FIELD,
            __('Contract Address', 'sc-flow'),
            array(__CLASS__, 'render_contract_address_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );
        // Add a field for Slugs
        add_settings_field(
            MC_ADMIN_SLUGS_FIELD,
            __('Page Slugs', 'sc-flow'),
            array(__CLASS__, 'render_slugs_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );
        // Add a field for contract ABI.
        add_settings_field(
            MC_ADMIN_CONTRACT_ABI_FIELD,
            __('Smart Contract ABI', 'sc-flow'),
            array(__CLASS__, 'render_contract_abi_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE,
            array('label_for' => MC_ADMIN_CONTRACT_ABI_FIELD)
        );

        // Add a field for active chain selection.
        add_settings_field(
            MC_ADMIN_ACTIVE_CHAIN_FIELD,
            __('Active Chain', 'sc-flow'),
            array(__CLASS__, 'render_active_chain_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );

        // Add a field for Slugs
        add_settings_field(
            MC_ADMIN_WALLETCONNECT_FIELD,
            __('Wallet connect Project ID', 'sc-flow'),
            array(__CLASS__, 'render_walletconnect_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );

        // Add a field for Slugs
        add_settings_field(
            MC_ADMIN_ALCHEMYPROVIDER_FIELD,
            __('Alchemy provider project ID', 'sc-flow'),
            array(__CLASS__, 'render_alchemyprovider_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );

        // Add a field for currency
        add_settings_field(
            MC_ADMIN_FIAT_CURRENCY_FIELD,
            __('Fiat Currency', 'sc-flow'),
            array(__CLASS__, 'render_fiat_currency_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );
    }

    /**
     * Render the general settings section.
     */
    public static function render_general_settings_section()
    {
        echo '<p>' . esc_html__('Smart contract settings', 'sc-flow') . '</p>';
    }



    /**
     * Render wallet connect project id field
     */
    public static function render_walletconnect_field()
    {
        $wallet_connect = get_option(MC_ADMIN_WALLETCONNECT_FIELD);
        ?>
        <input type="text" name="<?php echo MC_ADMIN_WALLETCONNECT_FIELD; ?>" value="<?php echo esc_attr($wallet_connect); ?>"
            class="regular-text" />
        <p class="description">
            <?php __('Enter wallet connect project ID', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }


    /**
     * Render alchemy provider project ID field
     */
    public static function render_fiat_currency_field()
    {
        $fiat_currency = get_option(MC_ADMIN_FIAT_CURRENCY_FIELD);
        ?>
        <input type="text" name="<?php echo MC_ADMIN_FIAT_CURRENCY_FIELD; ?>" value="<?php echo esc_attr($fiat_currency); ?>"
            class="regular-text" />
        <p class="description">
            <?php __('Enter fiat currency symbol (ex: chf)', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }
    /**
     * Render alchemy provider project ID field
     */
    public static function render_alchemyprovider_field()
    {
        $alchemy_provider = get_option(MC_ADMIN_ALCHEMYPROVIDER_FIELD);
        ?>
        <input type="text" name="<?php echo MC_ADMIN_ALCHEMYPROVIDER_FIELD; ?>" value="<?php echo esc_attr($alchemy_provider); ?>"
            class="regular-text" />
        <p class="description">
            <?php __('Enter alchemy provider project ID', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }
    /**
     * Render slug field
     */
    public static function render_slugs_field()
    {
        $slugs = get_option(MC_ADMIN_SLUGS_FIELD);
        ?>
        <input type="text" name="<?php echo MC_ADMIN_SLUGS_FIELD; ?>" value="<?php echo esc_attr($slugs); ?>"
            class="regular-text" />
        <p class="description">
            <?php __('Enter the slugs like this ex: slug1, slug2, slug3,.', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }
    /**
     * Render the contract address field.
     */
    public static function render_contract_address_field()
    {
        $contract_address = get_option(MC_ADMIN_CONTRACT_ADDRESS_FIELD);
        ?>
        <input type="text" name="<?php echo MC_ADMIN_CONTRACT_ADDRESS_FIELD; ?>"
            value="<?php echo esc_attr($contract_address); ?>" class="regular-text" />
        <p class="description">
            <?php __('Enter the contract address for your NFTs.', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }

    /**
     * Render the Smart contract ABI field.
     *
     * @param array $args The field arguments.
     */
    public static function render_contract_abi_field($args)
    {
        $field_id = $args['label_for'];
        $field_value = get_option($field_id);
        $field_value = is_array($field_value) ? json_encode($field_value) : $field_value;
        $field_value = esc_textarea($field_value);

        echo '<textarea id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" rows="5" cols="50">' . $field_value . '</textarea>';
    }

    /**
     * Sanitize the smart contract ABI field.
     *
     * @param mixed $input The input value to sanitize.
     * @return array The sanitized smart contract ABI array.
     */
    public static function sanitize_contract_abi_field($input)
    {
        $contract_abi = is_array($input) ? $input : json_decode($input, true);
        if (!is_array($contract_abi)) {
            $contract_abi = array();
        }
        return $contract_abi;
    }

    /**
     * Render the active chain field.
     */
    public static function render_active_chain_field()
    {
        $active_chain = get_option(MC_ADMIN_ACTIVE_CHAIN_FIELD);
        ?>
        <select name="<?php echo MC_ADMIN_ACTIVE_CHAIN_FIELD; ?>">
            <option value="80001" <?php selected($active_chain, '80001'); ?>>Mumbai</option>
            <option value="137" <?php selected($active_chain, '137'); ?>>Polygon Mainnet</option>
        </select>
        <p class="description">
            <?php __('Select the active chain for your plugin.', MC_PLUGIN_NAME); ?>
        </p>
        <?php
    }

}

?>
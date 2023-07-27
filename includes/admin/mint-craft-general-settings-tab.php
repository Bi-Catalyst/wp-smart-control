<?php
// mint-craft-general-settings-tab.php
/**
 * Admin General settings tab
 * This tab will be containing the general setting for the smart contract
 */

 require_once dirname(__FILE__) . '/../constants.php';

class Mint_Craft_General_Settings_Tab
{
    /**
     * Render the content of the general settings tab.
     */
    public static function render()
    {
        ?>
        <div class="mint-craft">
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
            __('General Settings', 'mint-craft'),
            array(__CLASS__, 'render_general_settings_section'),
            MC_ADMIN_GENERAL_PAGE
        );

        // Register settings fields.
        register_setting(
            MC_ADMIN_GENERAL_FIELDS,
            MC_ADMIN_CONTRACT_ADDRESS_FIELD,
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
            __('Contract Address', 'mint-craft'),
            array(__CLASS__, 'render_contract_address_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );

        // Add a field for contract ABI.
        add_settings_field(
            MC_ADMIN_CONTRACT_ABI_FIELD,
            __('Smart Contract ABI', 'mint-craft'),
            array(__CLASS__, 'render_contract_abi_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE,
            array('label_for' => MC_ADMIN_CONTRACT_ABI_FIELD)
        );

        // Add a field for active chain selection.
        add_settings_field(
            MC_ADMIN_ACTIVE_CHAIN_FIELD,
            __('Active Chain', 'mint-craft'),
            array(__CLASS__, 'render_active_chain_field'),
            MC_ADMIN_GENERAL_PAGE,
            MC_ADMIN_GENERAL_SECTION_TITLE
        );
    }

    /**
     * Render the general settings section.
     */
    public static function render_general_settings_section()
    {
        echo '<p>' . esc_html__('Smart contract and network settings.', 'mint-craft') . '</p>';
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
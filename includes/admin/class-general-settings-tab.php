<?php
// class-general-settings-tab.php
/**
 * Admin General settings tab
 * This tab will be containing the general setting for the smart contract
 */
class General_Settings_Tab
{
    /**
     * Render the content of the general settings tab.
     */
    public static function render()
    {
        ?>
        <div class="pwrap">
            <form method="post" action="options.php">

                <?php
                // Output security fields.
                settings_fields('general_settings_fields_tab');
                // Output setting sections.
                do_settings_sections('general_settings_tab');
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
            'my_mint_plugin_general',
            __('General Settings', 'my-mint-plugin'),
            array(__CLASS__, 'render_general_settings_section'),
            'general_settings_tab'
        );

        // Register settings fields.
        register_setting(
            'general_settings_fields_tab',
            'my_mint_plugin_contract_address',
            'sanitize_text_field'
        );

        // Register a settings field for contract ABI.
        register_setting(
            'general_settings_fields_tab',
            'my_mint_plugin_contract_abi',
            array(__CLASS__, 'sanitize_contract_abi_field')
        );

        // Register a settings field for active chain.
        register_setting(
            'general_settings_fields_tab',
            'my_mint_plugin_active_chain',
            'sanitize_text_field'
        );

        // Add a field for contract address.
        add_settings_field(
            'my_mint_plugin_contract_address',
            __('Contract Address', 'my-mint-plugin'),
            array(__CLASS__, 'render_contract_address_field'),
            'general_settings_tab',
            'my_mint_plugin_general'
        );

        // Add a field for contract ABI.
        add_settings_field(
            'my_mint_plugin_contract_abi',
            __('Contract ABI', 'my-mint-plugin'),
            array(__CLASS__, 'render_contract_abi_field'),
            'general_settings_tab',
            'my_mint_plugin_general',
            array('label_for' => 'my_mint_plugin_contract_abi')
        );

        // Add a field for active chain selection.
        add_settings_field(
            'my_mint_plugin_active_chain',
            __('Active Chain', 'my-mint-plugin'),
            array(__CLASS__, 'render_active_chain_field'),
            'general_settings_tab',
            'my_mint_plugin_general'
        );
    }

    /**
     * Render the general settings section.
     */
    public static function render_general_settings_section()
    {
        echo '<p>' . esc_html__('Smart contract and network settings.', 'my-mint-plugin') . '</p>';
    }

    /**
     * Render the contract address field.
     */
    public static function render_contract_address_field()
    {
        $contract_address = get_option('my_mint_plugin_contract_address');
        ?>
        <input type="text" name="my_mint_plugin_contract_address" value="<?php echo esc_attr($contract_address); ?>"
            class="regular-text" />
        <p class="description">
            <?php _e('Enter the contract address for your NFTs.', 'my-mint-plugin'); ?>
        </p>
        <?php
    }

    /**
     * Render the contract ABI field.
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
     * Sanitize the contract ABI field.
     *
     * @param mixed $input The input value to sanitize.
     * @return array The sanitized contract ABI array.
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
        $active_chain = get_option('my_mint_plugin_active_chain');
        ?>
        <select name="my_mint_plugin_active_chain">
            <option value="mumbai" <?php selected($active_chain, 'mumbai'); ?>>Mumbai</option>
            <option value="polygon_mainnet" <?php selected($active_chain, 'polygon_mainnet'); ?>>Polygon Mainnet</option>
        </select>
        <p class="description">
            <?php _e('Select the active chain for your plugin.', 'my-mint-plugin'); ?>
        </p>
        <?php
    }
}
?>
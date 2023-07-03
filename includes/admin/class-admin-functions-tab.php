<?php
// class-admin-functions-tab.php
/**
 * Admin functions settings tab
 * This tab will be responsible of calling smart contract functions which requires the owner
 */
class Admin_Functions_Tab
{
    /**
     * Render the content of the admin functions tab.
     */
    public static function render()
    {
        ?>
        <div class="pwrap">
            <form method="post" action="options.php">

                <?php
                // Output security fields.
                settings_fields('admin_settings_tab'); // Add this line
                // Output setting sections.
                do_settings_sections('admin_settings_tab');
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
            'my_mint_plugin_admin_functions',
            __('Admin Functions', 'my-mint-plugin'),
            array(__CLASS__, 'render_admin_functions_section'),
            'admin_settings_tab'
        );

        // Add a field for the NFT price.
        add_settings_field(
            'my_mint_plugin_nft_price',
            __('NFT Price', 'my-mint-plugin'),
            array(__CLASS__, 'render_nft_price_field'),
            'admin_settings_tab',
            'my_mint_plugin_admin_functions'
        );

        // Add a field for the discount percentage.
        add_settings_field(
            'my_mint_plugin_discount_percentage',
            __('Discount', 'my-mint-plugin'),
            array(__CLASS__, 'render_discount_percentage_field'),
            'admin_settings_tab',
            'my_mint_plugin_admin_functions'
        );

        // Add a field for the max quantity.
        add_settings_field(
            'my_mint_plugin_max_quantity',
            __('Maximum Quantity', 'my-mint-plugin'),
            array(__CLASS__, 'render_max_quantity_field'),
            'admin_settings_tab',
            'my_mint_plugin_admin_functions'
        );

        // Register settings to be stored.
        register_setting('admin_settings_tab', 'my_mint_plugin_nft_price');
        register_setting('admin_settings_tab', 'my_mint_plugin_discount_percentage');
        register_setting('admin_settings_tab', 'my_mint_plugin_max_quantity');
    }


    /**
     * Render the admin functions section.
     */
    public static function render_admin_functions_section()
    {
        echo '<p>' . esc_html__('This section allows you to configure the NFT price and discount.', 'my-mint-plugin') . '</p>';
    }
    /**
     * Render the NFT price field.
     */
    public static function render_nft_price_field()
    {
        $nft_price = get_option('my_mint_plugin_nft_price');
        ?>
        <input type="number" id="nft_price_input" name="my_mint_plugin_nft_price" value="<?php echo esc_attr($nft_price); ?>"
            step="any" min="0" required>
        <button id="nft_price_button">Set Mint Price</button>
        <p class="description">
            <?php _e('Enter the price of the NFT.', 'my-mint-plugin'); ?>
        </p>
        <?php
    }

    /**
     * Render the discount percentage field.
     */
    public static function render_discount_percentage_field()
    {
        $discount_percentage = get_option('my_mint_plugin_discount_percentage');
        ?>
        <input type="number" id="mint_discount_input" name="my_mint_plugin_discount_percentage"
            value="<?php echo esc_attr($discount_percentage); ?>" step="1" min="0" max="100" required>
        <button id="mint_discount_button">Set discount</button>
        <p class="description">
            <?php _e('Enter the discount percentage.', 'my-mint-plugin'); ?>
        </p>
        <?php
    }

    /**
     * Render the discount percentage field.
     */
    public static function render_max_quantity_field()
    {
        $max_quantity = get_option('my_mint_plugin_max_quantity');
        ?>
        <input type="number" id="max_quantity_input" name="my_mint_plugin_max_quantity"
            value="<?php echo esc_attr($max_quantity); ?>" step="1" min="1" max="100" required>
        <button id="max_quantity_button">Set max quantity</button>
        <p class="description">
            <?php _e('Enter the maximum quantity of NFTs that can be purchased or minted.', 'my-mint-plugin'); ?>
        </p>
        <?php
    }
}
?>
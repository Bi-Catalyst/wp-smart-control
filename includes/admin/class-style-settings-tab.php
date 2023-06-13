<?php
// class-style-settings-tab.php
/**
 * Admin style settings tab
 * This tab will be having the style IDs or classes for buttons
 */
class Admin_Style_Tab
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
                settings_fields('admin_style_tab'); // Add this line
                // Output setting sections.
                do_settings_sections('admin_style_tab');
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
            'my_mint_plugin_admin_styles',
            __('Admin Functions', 'my-mint-plugin'),
            array(__CLASS__, 'render_admin_style_section'),
            'admin_style_tab'
        );

        // Add a field for the Connect Button.
        add_settings_field(
            'my_mint_plugin_connect_button',
            __('Connect button', 'my-mint-plugin'),
            array(__CLASS__, 'render_connect_button_field'),
            'admin_style_tab',
            'my_mint_plugin_admin_styles'
        );

        // Add a field for the mint button
        add_settings_field(
            'my_mint_plugin_mint_button',
            __('Mint Button', 'my-mint-plugin'),
            array(__CLASS__, 'render_mint_button_field'),
            'admin_style_tab',
            'my_mint_plugin_admin_styles'
        );

        // Add a field for the mint quantity
        add_settings_field(
            'my_mint_plugin_mint_quantity',
            __('Mint Quantity', 'my-mint-plugin'),
            array(__CLASS__, 'render_mint_quantity_field'),
            'admin_style_tab',
            'my_mint_plugin_admin_styles'
        );

        // Add a field for the mint quantity
        add_settings_field(
            'my_mint_plugin_minter_counter',
            __('Minter Counter', 'my-mint-plugin'),
            array(__CLASS__, 'render_minter_counter_field'),
            'admin_style_tab',
            'my_mint_plugin_admin_styles'
        );
        // Register settings to be stored.
        register_setting('admin_style_tab', 'my_mint_plugin_connect_button');
        register_setting('admin_style_tab', 'my_mint_plugin_mint_button');
        register_setting('admin_style_tab', 'my_mint_plugin_mint_quantity');
        register_setting('admin_style_tab', 'my_mint_plugin_minter_counter');

    }


    /**
     * Render the admin functions section.
     */
    public static function render_admin_style_section()
    {
        echo '<p>' . esc_html__('This section allows you to configure fields classes which will linked to events', 'my-mint-plugin') . '</p>';
    }
    /**
     * Render the connect button.
     */
    public static function render_connect_button_field()
    {
        $connect_button = get_option('my_mint_plugin_connect_button');
        ?>
        <input type="text" name="my_mint_plugin_connect_button" value="<?php echo esc_attr($connect_button); ?>">
        <p class="description">
            <?php _e('Enter the mint button class or id (ex: #connect-button or .connect-button', 'my-mint-plugin'); ?>
        </p>
        <?php
    }

    /**
     * Render the mint button field.
     */
    public static function render_mint_button_field()
    {
        $mint_button = get_option('my_mint_plugin_mint_button');
        ?>
        <input type="text" name="my_mint_plugin_mint_button" value="<?php echo esc_attr($mint_button); ?>">
        <p class="description">
            <?php _e('Enter the mint button class or id (ex: #mint-button or .mint-button', 'my-mint-plugin'); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_mint_quantity_field()
    {
        $mint_quantity = get_option('my_mint_plugin_mint_quantity');
        ?>
        <input type="text" name="my_mint_plugin_mint_quantity" value="<?php echo esc_attr($mint_quantity); ?>">
        <p class="description">
            <?php _e('Enter the mint button class or id (ex: #mint-quantity or .mint-quantity', 'my-mint-plugin'); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_minter_counter_field()
    {
        $minter_counter = get_option('my_mint_plugin_minter_counter');
        ?>
        <input type="text" name="my_mint_plugin_minter_counter" value="<?php echo esc_attr($minter_counter); ?>">
        <p class="description">
            <?php _e('Enter the minter counter class or id (ex: #minter-counter or .minter-counter', 'my-mint-plugin'); ?>
        </p>
        <?php
    }
}
?>
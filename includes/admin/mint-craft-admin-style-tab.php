<?php
// class-style-settings-tab.php
/**
 * Admin style settings tab
 * This tab will be having the style IDs or classes for buttons
 */
require_once dirname(__FILE__) . '/../constants.php';

class Mint_Craft_Admin_Style_Tab
{
    /**
     * Render the content of the admin functions tab.
     */
    public static function render()
    {
        ?>
        <div class="mint-craft">
            <form method="post" action="options.php">

                <?php
                // Output security fields.
                settings_fields(MC_ADMIN_STYLE_TAB); // Add this line
                // Output setting sections.
                do_settings_sections(MC_ADMIN_STYLE_TAB);
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
            MC_ADMIN_STYLE_SECTION_TITLE,
            __('Admin Functions', 'mint-craft'),
            array(__CLASS__, 'render_admin_style_section'),
            MC_ADMIN_STYLE_TAB
        );

        // Add a field for the Connect Button.
        add_settings_field(
            MC_PLUGIN_CONNECT_BUTTON,
            __('Connect button', 'mint-craft'),
            array(__CLASS__, 'render_connect_button_field'),
            MC_ADMIN_STYLE_TAB,
            MC_ADMIN_STYLE_SECTION_TITLE
        );

        // Add a field for the mint button
        add_settings_field(
            MC_PLUGIN_MINT_BUTTON,
            __('Mint Button', 'mint-craft'),
            array(__CLASS__, 'render_mint_button_field'),
            MC_ADMIN_STYLE_TAB,
            MC_ADMIN_STYLE_SECTION_TITLE
        );

        // Add a field for the mint quantity
        add_settings_field(
            MC_PLUGIN_MINT_QUANTITY,
            __('Mint Quantity', 'mint-craft'),
            array(__CLASS__, 'render_mint_quantity_field'),
            MC_ADMIN_STYLE_TAB,
            MC_ADMIN_STYLE_SECTION_TITLE
        );

        // Add a field for the mint quantity
        add_settings_field(
            MC_PLUGIN_MINTER_COUNTER,
            __('Minter Counter', 'mint-craft'),
            array(__CLASS__, 'render_minter_counter_field'),
            MC_ADMIN_STYLE_TAB,
            MC_ADMIN_STYLE_SECTION_TITLE
        );
        // Register settings to be stored.
        register_setting(MC_ADMIN_STYLE_TAB, MC_PLUGIN_CONNECT_BUTTON);
        register_setting(MC_ADMIN_STYLE_TAB, MC_PLUGIN_MINT_BUTTON);
        register_setting(MC_ADMIN_STYLE_TAB, MC_PLUGIN_MINT_QUANTITY);
        register_setting(MC_ADMIN_STYLE_TAB, MC_PLUGIN_MINTER_COUNTER);

    }


    /**
     * Render the admin functions section.
     */
    public static function render_admin_style_section()
    {
        echo '<p>' . esc_html__('This section allows you to configure fields classes which will linked to events', 'mint-craft') . '</p>';
    }
    /**
     * Render the connect button.
     */
    public static function render_connect_button_field()
    {
        $connect_button = get_option(MC_PLUGIN_CONNECT_BUTTON);
        ?>
        <input type="text" name="<?php echo MC_PLUGIN_CONNECT_BUTTON; ?>" value="<?php echo esc_attr($connect_button); ?>">
        <p class="description">
            <?php __('Enter the mint button class or id (ex: #connect-button or .connect-button)', 'mint-craft'); ?>
        </p>
        <?php
    }

    /**
     * Render the mint button field.
     */
    public static function render_mint_button_field()
    {
        $mint_button = get_option(MC_PLUGIN_MINT_BUTTON);
        ?>
        <input type="text" name="<?php echo MC_PLUGIN_MINT_BUTTON; ?>" value="<?php echo esc_attr($mint_button); ?>">
        <p class="description">
            <?php __('Enter the mint button class or id (ex: #mint-button or .mint-button)', 'mint-craft'); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_mint_quantity_field()
    {
        $mint_quantity = get_option(MC_PLUGIN_MINT_QUANTITY);
        ?>
        <input type="text" name="<?php echo MC_PLUGIN_MINT_QUANTITY; ?>" value="<?php echo esc_attr($mint_quantity); ?>">
        <p class="description">
            <?php __('Enter the mint button class or id (ex: #mint-quantity or .mint-quantity)', 'mint-craft'); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_minter_counter_field()
    {
        $minter_counter = get_option(MC_PLUGIN_MINTER_COUNTER);
        ?>
        <input type="text" name="<?php echo MC_PLUGIN_MINTER_COUNTER; ?>" value="<?php echo esc_attr($minter_counter); ?>">
        <p class="description">
            <?php __('Enter the minter counter class or id (ex: #minter-counter or .minter-counter)', 'mint-craft'); ?>
        </p>
        <?php
    }
}
?>
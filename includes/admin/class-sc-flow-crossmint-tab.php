<?php
/**
 * Admin setting the style tab
 * This tab will be having the style IDs or classes for buttons
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {class-sc-flow-admin-style-tab.php}
 */

require_once dirname(__FILE__) . '/../constants.php';

class SC_Flow_Admin_Crossmint_Tab
{
    /**
     * Render the content of the admin functions tab.
     */
    public static function render()
    {
        ?>
        <div class="sc-flow">
            <form method="post" action="options.php">

                <?php
                // Output security fields.
                settings_fields(SC_FLOW_ADMIN_CROSSMINT_TAB); // Add this line
                // Output setting sections.
                do_settings_sections(SC_FLOW_ADMIN_CROSSMINT_TAB);
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
            SC_FLOW_ADMIN_CROSSMINT_SECTION_TITLE,
            __('Crossmint settings', 'sc-flow'),
            array(__CLASS__, 'render_admin_crossmint_section'),
            SC_FLOW_ADMIN_CROSSMINT_TAB
        );

        // Add a field for the Connect Button.
        add_settings_field(
            SC_FLOW_PLUGIN_CROSSMINT_PROJECT_ID,
            __('Project ID', 'sc-flow'),
            array(__CLASS__, 'render_crossmint_project_id'),
            SC_FLOW_ADMIN_CROSSMINT_TAB,
            SC_FLOW_ADMIN_CROSSMINT_SECTION_TITLE
        );

        // Add a field for the mint button
        add_settings_field(
            SC_FLOW_PLUGIN_CROSSMINT_COLLECTION_ID,
            __('Collection ID', 'sc-flow'),
            array(__CLASS__, 'render_crossmint_collection_id'),
            SC_FLOW_ADMIN_CROSSMINT_TAB,
            SC_FLOW_ADMIN_CROSSMINT_SECTION_TITLE
        );

        // Add a field for the mint quantity
        add_settings_field(
            SC_FLOW_PLUGIN_CROSSMINT_ENVIRONMENT,
            __('Environment', 'sc-flow'),
            array(__CLASS__, 'render_crossmint_collection_environment'),
            SC_FLOW_ADMIN_CROSSMINT_TAB,
            SC_FLOW_ADMIN_CROSSMINT_SECTION_TITLE
        );

        // Add a field for the mint quantity
        add_settings_field(
            SC_FLOW_PLUGIN_CROSSMINT_ERC_TYPE,
            __('ERC Type', 'sc-flow'),
            array(__CLASS__, 'render_crossmint_erc_type'),
            SC_FLOW_ADMIN_CROSSMINT_TAB,
            SC_FLOW_ADMIN_CROSSMINT_SECTION_TITLE
        );
        // Register settings to be stored.
        register_setting(SC_FLOW_ADMIN_CROSSMINT_TAB, SC_FLOW_PLUGIN_CROSSMINT_PROJECT_ID);
        register_setting(SC_FLOW_ADMIN_CROSSMINT_TAB, SC_FLOW_PLUGIN_CROSSMINT_COLLECTION_ID);
        register_setting(SC_FLOW_ADMIN_CROSSMINT_TAB, SC_FLOW_PLUGIN_CROSSMINT_ENVIRONMENT);
        register_setting(SC_FLOW_ADMIN_CROSSMINT_TAB, SC_FLOW_PLUGIN_CROSSMINT_ERC_TYPE);

    }


    /**
     * Render the admin functions section.
     */
    public static function render_admin_crossmint_section()
    {
        echo '<p>' . esc_html__('Crossmint configuration', 'sc-flow') . '</p>';
    }
    /**
     * Render the connect button.
     */
    public static function render_crossmint_project_id()
    {
        $connect_button = get_option(SC_FLOW_PLUGIN_CROSSMINT_PROJECT_ID);
        ?>
        <input type="text" name="<?php echo SC_FLOW_PLUGIN_CROSSMINT_PROJECT_ID; ?>"
            value="<?php echo esc_attr($connect_button); ?>">
        <p class="description">
            <?php __('Enter the mint button class or id (ex: #connect-button or .connect-button)', 'sc-flow'); ?>
        </p>
        <?php
    }

    /**
     * Render the mint button field.
     */
    public static function render_crossmint_collection_id()
    {
        $mint_button = get_option(SC_FLOW_PLUGIN_CROSSMINT_COLLECTION_ID);
        ?>
        <input type="text" name="<?php echo SC_FLOW_PLUGIN_CROSSMINT_COLLECTION_ID; ?>"
            value="<?php echo esc_attr($mint_button); ?>">
        <p class="description">
            <?php __('Enter the mint button class or id (ex: #mint-button or .mint-button)', 'sc-flow'); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_crossmint_collection_environment()
    {
        $crossmint_env = get_option(SC_FLOW_PLUGIN_CROSSMINT_ENVIRONMENT);
        ?>
        <select name="<?php echo SC_FLOW_PLUGIN_CROSSMINT_ENVIRONMENT; ?>">
            <option value="staging" <?php selected($crossmint_env, 'staging'); ?>>Staging</option>
            <option value="production" <?php selected($crossmint_env, 'production'); ?>>Production</option>
        </select>
        <p class="description">
            <?php __('Select the environment (staging = test environment).', SC_FLOW_PLUGIN_NAME); ?>
        </p>
        <?php
    }
    /**
     * Render the mint quantity field.
     */
    public static function render_crossmint_erc_type()
    {
        $minter_counter = get_option(SC_FLOW_PLUGIN_CROSSMINT_ERC_TYPE);
        ?>
        <input type="text" name="<?php echo SC_FLOW_PLUGIN_CROSSMINT_ERC_TYPE; ?>" value="<?php echo esc_attr($minter_counter); ?>">
        <p class="description">
            <?php __('Enter the minter counter class or id (ex: #minter-counter or .minter-counter)', 'sc-flow'); ?>
        </p>
        <?php
    }
}
?>
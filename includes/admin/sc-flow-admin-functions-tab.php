<?php
/**
 * Admin setting the smart contract operations tab
 * This tab will be automatically generated from the smart contract ABI 
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 * @subpackage {sc-flow-admin-functions-tab.php}
 */

require_once dirname(__FILE__) . '/../constants.php';


class SC_Flow_Admin_Functions_Tab
{
    /**
     * Render the content of the admin functions tab.
     */
    public static function render()
    {
        $default_sub_tab = MC_ADMIN_FUNCTIONS_SECTION_PREFIX . 'nonpayable';

        $active_sub_tab = isset($_GET['sub_tab']) ? $_GET['sub_tab'] : $default_sub_tab;
        $tab_groups = self::get_tab_groups();
        ?>

        <div class="sc-flow">
            <h2 class="nav-tab-wrapper">
                <?php
                // Render the tab navigation
                foreach ($tab_groups as $group) {
                    $active_class = ($group['id'] === $active_sub_tab) ? 'nav-tab-active' : '';
                    $url = add_query_arg('sub_tab', $group['id'], admin_url('admin.php?page=' . MC_ADMIN_MENU_SLUG . '&tab=admin_functions'));
                    echo '<a href="' . esc_url($url) . '" class="nav-tab ' . $active_class . '">' . $group['label'] . '</a>';
                }
                ?>
            </h2>

            <form method="post" action="options.php">
                <?php
                settings_fields($active_sub_tab);
                ?>
                <div class="tab-content-wrapper">
                    <?php
                    // Render the tab content and settings fields
                    foreach ($tab_groups as $group) {
                        $active_class = ($group['id'] === $active_sub_tab) ? ' active' : '';
                        echo '<div id="' . $group['id'] . '" class="tab-content' . $active_class . '">';
                        if ($group['id'] === $active_sub_tab) {
                            do_settings_sections($group['id']);
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                <?php
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    public static function register_settings()
    {
        // Register a settings section for each tab group.
        $tab_groups = self::get_tab_groups();
        foreach ($tab_groups as $group) {
            add_settings_section(
                $group['id'],
                $group['label'],
                '__return_false',
                $group['id']
            );
        }

        // Retrieve the contract ABI
        $contract_abi = get_option(MC_ADMIN_CONTRACT_ABI_FIELD);
        if (!empty($contract_abi)) {
            // Group the functions based on stateMutability
            $functions_by_state = array(
                'nonpayable' => array(),
                'payable' => array(),
                'view' => array(),
                'event' => array()
            );

            foreach ($contract_abi as $function) {
                // Check if the function is an event
                if ($function['type'] === 'event') {
                    $functions_by_state['event'][] = $function;
                    continue;
                }

                // Check if the function has stateMutability defined
                if (isset($function['stateMutability'])) {
                    $state = $function['stateMutability'];
                    $functions_by_state[$state][] = $function;
                } else {
                    $functions_by_state['event'][] = $function;
                }
            }

            // Loop through each group and add the fields
            foreach ($functions_by_state as $state => $functions) {
                // Skip the group if it doesn't have any functions
                if (empty($functions)) {
                    continue;
                }

                // Add the group title
                $group_id = MC_ADMIN_FUNCTIONS_SECTION_PREFIX . $state;
                $group_title = self::get_state_mutability_title($state);

                // Loop through the functions and add the fields
                foreach ($functions as $function) {
                    // Generate the unique field ID
                    if (!isset($function['name'])) {
                        continue;
                    }
                    $field_id = MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . $function['name'];
                    $readable_function_name = self::get_readable_function_name($function['name']);

                    // Add a field for the function
                    add_settings_field(
                        $field_id,
                        '<div class="function-header"><h4>' . $readable_function_name . '</h4></div>',
                        array(__CLASS__, 'render_function_field'),
                        $group_id,
                        $group_id,
                        array('function' => $function)
                    );

                    // Register the settings for the function
                    register_setting(
                        $group_id,
                        $field_id,
                        array(__CLASS__, 'sanitize_function_field')
                    );
                }
            }
        }
    }

    /**
     * Get the title for a stateMutability group.
     *
     * @param string $state The stateMutability value.
     * @return string The group title.
     */
    private static function get_state_mutability_title($state)
    {
        switch ($state) {
            case 'nonpayable':
                return __('Non Payable Operations', 'sc-flow');
            case 'payable':
                return __('Payable Operations', 'sc-flow');
            case 'view':
                return __('View Operations', 'sc-flow');
            case 'event':
                return __('Events', 'sc-flow');
            default:
                return __('Other Operations', 'sc-flow');
        }
    }

    /**
     * Render the field for a smart contract function.
     *
     * @param array $args The field arguments.
     */
    public static function render_function_field($args)
    {
        $function = $args['function'];

        if (isset($function['name'])) {
            $field_id = MC_ADMIN_FUNCTIONS_FIELDS_PREFIX . $function['name'];
            $field_value = get_option($field_id);
            $has_inputs = !empty($function['inputs']);
            $has_outputs = !empty($function['outputs']);

            echo '<div class="function-field">';
            // echo '<div class="function-header">';
            // echo '<h3>' . self::get_readable_function_name($function['name']) . '</h3>';
            // echo '</div>'; // close function-header

            echo '<div class="function-actions">';

            if ($has_inputs) {
                echo '<div class="function-inputs">';
                foreach ($function['inputs'] as $input) {
                    echo '<div><label for="' . esc_attr($field_id) . '_' . $input['name'] . '">' . self::get_readable_function_name($input['name']) . '</label>';
                    echo '<input type="text" data-input-type="' . esc_attr($input['type']) . '" id="' . esc_attr($field_id) . '_' . $input['name'] . '" name="' . esc_attr($field_id) . '" class="regular-text" data-type="' . esc_attr($input['type']) . '" /></div>';
                }
                echo '</div>'; // close function-inputs
            }

            $f_r = empty($field_value) ? 'no value' : esc_attr($field_value);
            if ($has_outputs) {
                echo '<div class="function-result"><input type="text" name="' . esc_attr($field_id) . '" value="' . $f_r . '" id="' . esc_attr($field_id) . '_result" /></div>';
            }

            echo '<button class="button button-primary trigger-function" data-setting-key="' . esc_attr($field_id) . '" data-state-mutability="' . esc_attr($function['stateMutability']) . '">' . __('Trigger', MC_PLUGIN_NAME) . '</button>';

            echo '</div>'; // close function-actions
            echo '</div>'; // close function-field
        } else {
            echo '<p class="description">' . __('Invalid smart contract opeartion', 'sc-flow') . '</p>';
        }
    }

    /**
     * Sanitize the field value for a smart contract function.
     *
     * @param mixed $input The input value to sanitize.
     * @return mixed The sanitized field value.
     */
    public static function sanitize_function_field($input)
    {
        return sanitize_text_field($input);
    }

    /**
     * Get the tab groups for the second level of the admin functions.
     *
     * @return array The tab groups.
     */
    private static function get_tab_groups()
    {
        $contract_abi = get_option(MC_ADMIN_CONTRACT_ABI_FIELD);
        $tab_groups = array();

        if (!empty($contract_abi)) {
            // Get the unique stateMutability values from the contract ABI
            $state_mutabilities = array_unique(array_column($contract_abi, 'stateMutability'));

            // Sort the stateMutability values
            sort($state_mutabilities);

            // Create the tab groups based on stateMutability values
            foreach ($state_mutabilities as $state) {
                if ($state === '') {
                    $state = 'event';
                }
                $group_id = MC_ADMIN_FUNCTIONS_SECTION_PREFIX . $state;
                $group_label = self::get_state_mutability_title($state);
                $active = ($state === 'nonpayable'); // Set the first tab as active

                $tab_groups[] = array(
                    'id' => $group_id,
                    'label' => $group_label,
                    'active' => $active,
                );
            }
        }

        return $tab_groups;
    }
    /**
     * Get the user-readable function name.
     *
     * @param string $functionName The raw function name.
     * @return string The user-readable function name.
     */
    private static function get_readable_function_name($functionName)
    {
        // Split the function name based on camel case, Pascal case, or underscore
        $words = preg_split('/(?<=[a-z])(?=[A-Z])|_/', $functionName);

        // Capitalize the first letter of each word
        $words = array_map('ucfirst', $words);

        // Join the words with spaces to form the readable function name
        $readableName = implode(' ', $words);

        return $readableName;
    }
}

?>
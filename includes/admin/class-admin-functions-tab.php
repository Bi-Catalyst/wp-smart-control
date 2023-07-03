<?php
// class-admin-functions-tab.php
/**
 * Admin functions settings tab
 * This tab will be responsible for calling smart contract functions that require the owner
 */
class Admin_Functions_Tab
{
    /**
     * Render the content of the admin functions tab.
     */
    public static function render()
    {
        $default_sub_tab = 'my_mint_plugin_admin_functions_nonpayable';
        $active_sub_tab = isset($_GET['sub_tab']) ? $_GET['sub_tab'] : $default_sub_tab;
        $tab_groups = self::get_tab_groups();
        ?>

        <div class="pwrap">
            <h2 class="nav-tab-wrapper">
                <?php
                // Render the tab navigation
                foreach ($tab_groups as $group) {
                    $active_class = ($group['id'] === $active_sub_tab) ? 'nav-tab-active' : '';
                    $url = add_query_arg('sub_tab', $group['id'], admin_url('admin.php?page=my-mint-plugin-settings&tab=admin_functions'));
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
        $contract_abi = get_option('my_mint_plugin_contract_abi');
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
                $group_id = 'my_mint_plugin_admin_functions_' . $state;
                $group_title = self::get_state_mutability_title($state);

                // Loop through the functions and add the fields
                foreach ($functions as $function) {
                    // Generate the unique field ID
                    if (!isset($function['name'])) {
                        continue;
                    }
                    $field_id = 'my_mint_plugin_function_' . $function['name'];

                    // Add a field for the function
                    add_settings_field(
                        $field_id,
                        $function['name'],
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
                return __('Nonpayable Functions', 'my-mint-plugin');
            case 'payable':
                return __('Payable Functions', 'my-mint-plugin');
            case 'view':
                return __('View Functions', 'my-mint-plugin');
            case 'event':
                return __('Events', 'my-mint-plugin');
            default:
                return __('Other Functions', 'my-mint-plugin');
        }
    }

    /**
     * Render the field for a smart contract function.
     *
     * @param array $args The field arguments.
     */
    public static function render_function_field($args) {
        $function = $args['function'];
    
        if (isset($function['name'])) {
            $field_id = 'my_mint_plugin_function_' . $function['name'];
            $field_value = get_option($field_id);
            $has_inputs = !empty($function['inputs']);
            $has_outputs = !empty($function['outputs']);
    
            echo '<div class="function-field">';
            // echo '<div class="function-header">';
            // echo '<h3>' . $function['name'] . '</h3>';
            // echo '</div>'; // close function-header
    
            echo '<div class="function-actions">';
            
            if ($has_inputs) {
                echo '<div class="function-inputs">';
                foreach ($function['inputs'] as $input) {
                    echo '<label for="' . esc_attr($field_id) . '_' . $input['name'] . '">' . $input['name'] . '</label>';
                    echo '<input type="' . esc_attr($input['type']) . '" id="' . esc_attr($field_id) . '_' . $input['name'] . '" name="' . esc_attr($field_id) . '_' . $input['name'] . '" class="regular-text" data-type="' . esc_attr($input['type']) . '" />';
                }
                echo '</div>'; // close function-inputs
            }
    
            echo '<button class="button button-primary trigger-function" data-setting-key="' . esc_attr($field_id) . '" data-state-mutability="' . esc_attr($function['stateMutability']) . '">' . __('Trigger', 'my-mint-plugin') . '</button>';
    
            if ($has_outputs) {
                echo '<div class="function-result" id="' . esc_attr($field_id) . '_result"></div>';
            }
    
            echo '</div>'; // close function-actions
            echo '</div>'; // close function-field
        } else {
            echo '<p class="description">' . __('Invalid function', 'my-mint-plugin') . '</p>';
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
        $contract_abi = get_option('my_mint_plugin_contract_abi');
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
                $group_id = 'my_mint_plugin_admin_functions_' . $state;
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
}
?>
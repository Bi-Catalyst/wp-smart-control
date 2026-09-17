<?php

/**
 * Plugin Name: Smart Contract Flow
 * Description: WordPress plugin to Seamlessly integrate EVM compatible smart contracts operations into WordPress
 * Version: 0.0.1
 * 
 *
 * @link       https://bicatalyst.ch
 * @since      0.0.1
 * @author Mohamed Habbat <mohamed.habbat@bicatalyst.ch>
 * @package    SmartContract_Flow
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/includes/constants.php';

define('SC_FLOW_PLUGIN_FILE', __FILE__);

/**
 * The main class that initializes the plugin.
 */
class SmartContract_Flow
{
    /**
     * Plugin text domain.
     *
     * @var string
     */
    private $plugin_text_domain;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->plugin_text_domain = SC_FLOW_PLUGIN_NAME;

        // Load plugin text domain for translations.
        add_action('init', array($this, 'load_plugin_textdomain'));

        $this->load_dependencies();
        $this->initialize();
    }

    /**
     * Load the required dependencies for the plugin.
     */
    private function load_dependencies()
    {
        // Include the admin functionality.
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'includes/admin/class-sc-flow-admin.php';
        }
        // Include the public functionality.
        require_once plugin_dir_path(__FILE__) . 'includes/class-sc-flow-public.php';
    }

    /**
     * Load the plugin text domain for translations.
     */
    public function load_plugin_textdomain()
    {
        $plugin_dir = dirname(plugin_basename(__FILE__));
        $languages_dir = trailingslashit($plugin_dir) . 'languages';

        // Load the plugin translation file
        load_plugin_textdomain($this->plugin_text_domain, false, $languages_dir);

        // Translatable strings wrapped in translation functions
        __('Connect Wallet', $this->plugin_text_domain);
        __('Mint', $this->plugin_text_domain);
    }
    /**
     * Initialize the plugin.
     */
    private function initialize()
    {
        // Instantiate the admin class.
        if (is_admin()) {
            $admin = new SC_Flow_Admin();
        }
    }
}

// Instantiate the main plugin class.
new SmartContract_Flow();
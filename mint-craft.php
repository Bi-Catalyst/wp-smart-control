<?php

/**
 * Plugin Name: Mint Craft
 * Description: WordPress plugin to execute any smart contract function from the admin and mint NFTs on frontend
 * Version: 0.0.1
 * Author: Mohamed Habbat
 *
 * @package Min_Craft
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/includes/constants.php';

define('PLUGIN_ROOT_PATH', __FILE__);

/**
 * The main class that initializes the plugin.
 */
class Min_Craft
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
        $this->plugin_text_domain = MC_PLUGIN_NAME;

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
        require_once plugin_dir_path(__FILE__) . 'includes/admin/mint-craft-admin.php';

        // Include the public functionality.
        require_once plugin_dir_path(__FILE__) . 'includes/mint-craft-public.php';
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
            $admin = new Mint_Craft_Admin();
        }
    }
}

// Instantiate the main plugin class.
new Min_Craft();
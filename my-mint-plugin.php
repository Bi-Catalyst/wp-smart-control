<?php

/**
 * Plugin Name: My Mint Plugin
 * Description: WordPress plugin for connecting wallets and minting NFTs.
 * Version: 0.0.1
 * Author: Mohamed Habbat
 *
 * @package My_Mint_Plugin
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/includes/constants.php';

define('MY_MINT_PLUGIN_FILE', __FILE__);

/**
 * The main class that initializes the plugin.
 */
class My_Mint_Plugin
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
        $this->plugin_text_domain = PLUGIN_NAME;

        // Load plugin text domain for translations.
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        $this->load_dependencies();
        $this->initialize();
    }

    /**
     * Load the required dependencies for the plugin.
     */
    private function load_dependencies()
    {
        // Include the admin functionality.
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-my-mint-admin.php';

        // Include the public functionality.
        require_once plugin_dir_path(__FILE__) . 'includes/class-my-mint-public.php';
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
            $admin = new My_Mint_Admin();
        }
    }
}

// Instantiate the main plugin class.
new My_Mint_Plugin();
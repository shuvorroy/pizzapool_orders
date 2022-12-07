<?php
/**
 * WPPool Assignment
 *
 * @package shuvorroy/pizzapool_orders
 * 
 * Plugin Name:       PizzaPool Orders
 * Plugin URI:        https://github.com/shuvorroy
 * Description:       A plugin for custom order processing
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Shuvo Roy
 * Author URI:        https://github.com/shuvorroy
 * License:           GNU General Public License v2 or later
 * Text Domain:       pizzapool_orders
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The main plugin class
 */
final class PizzaPool_Orders {
    /**
     * Instance of self
     *
     * @var PizzaPool_Orders
     */
    private static $instance = null;

    /**
	 * Class construcotr
	 */
	private function __construct() {
		$this->define_constants();
		$this->load_classes();

		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

    /**
     * Initializes the Main class
     *
     * Checks for an existing Main instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
	 * Define the required plugin constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'PIZZAPOOLORDERS', __FILE__ );
		define( 'PIZZAPOOLORDERS_NAME', 'pizzapool_orders' );
		define( 'PIZZAPOOLORDERS_VERSION', '1.0.0' );
		define( 'PIZZAPOOLORDERS_DIR', trailingslashit( plugin_dir_path( PIZZAPOOLORDERS ) ) );
		define( 'PIZZAPOOLORDERS_URL', trailingslashit( plugin_dir_url( PIZZAPOOLORDERS ) ) );
		define( 'PIZZAPOOLORDERS_ASSETS', trailingslashit( PIZZAPOOLORDERS_URL . 'assets' ) );
		define( 'PIZZAPOOLORDERS_REST_BASE', 'pizzapool_orders/' );
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function load_classes() {
		foreach ( glob( PIZZAPOOLORDERS_DIR . "includes/classes/*.php" ) as $class_file ) {
			include $class_file;
		}
	}

    /**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function init_plugin() {
		new PizzaPool_Orders\Product();
        new PizzaPool_Orders\Order();
	}

    /**
	 * Initialize plugin for localization
	 *
	 * @uses load_plugin_textdomain()
	 */
	public function localization_setup() {
		load_plugin_textdomain( PIZZAPOOLORDERS_NAME, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

    /**
	 * Placeholder for activation function
	 *
	 * @return void
	 */
	public function activate() {
		
	}

    /**
	 * Placeholder for deactivation function
	 *
	 * @return void
	 */
	public function deactivate() {

	}
}

/**
 * Load PizzaPool_Orders when all plugins loaded
 */

$pizzapool_orders = PizzaPool_Orders::init();
<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://www.peta.org/
 * @since             1.0.0
 * @package           PETA\plugin
 *
 * @wordpress-plugin
 * Plugin Name:       PETA
 * Plugin URI:        
 * Description:       
 * Version:           1.0.0
 * Author:            Adam Carter
 * Author URI:        https://www.adamkristopher.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       peta
 * Domain Path:       /languages
 */
namespace PETA\plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217?' );
}

/**
 * Setup the plugin's constants.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_constants() {
	$plugin_url = plugin_dir_url( __FILE__ );
	define( 'PETA_URL', $plugin_url );
	define( 'PETA_DIR', plugin_dir_path( __DIR__ ) );
	define( 'PETA_VER', '1.0.0' );
}

/**
 * Enqueue public scripts and styles
 *
 * @since 1.0.0
 * @return void
 */
function public_scripts() {
	wp_enqueue_style(   'peta_grid',   PETA_URL . 'assets/css/unsemantic-grid-responsive.css', PETA_VER );
	wp_enqueue_style(   'peta_styles', PETA_URL . 'assets/css/pub.css' );
	wp_enqueue_script(  'peta_script', PETA_URL . 'assets/js/pub-script.js', array( 'jquery' ), PETA_VER, false );
}

/**
 * Enqueue admin scripts and styles
 *
 * @since 1.0.0
 * @return void
 */
function admin_scripts() {
	wp_enqueue_style(  'peta_admin_styles',  PETA_URL . 'assets/css/admin.css', PETA_VER );
	wp_enqueue_style(  'select2_css',        PETA_URL . 'assets/css/select2.min.css', PETA_VER );
	wp_enqueue_script( 'select2_js',         PETA_URL . 'assets/js/select2.min.js', array( 'jquery' ), PETA_VER, false );
	wp_enqueue_script( 'peta_admin_script',  PETA_URL . 'assets/js/admin-script.js', array( 'jquery' ), PETA_VER, false );
	wp_enqueue_media();
}

/**
 * Initialize the plugin hooks
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_hooks() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\public_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_scripts' );
	register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );
	register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );
	register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall_plugin' );
}

/**
 * Plugin activation handler
 *
 * @since 1.0.0
 *
 * @return void
 */
function activate_plugin() {
	init_autoloader();
	flush_rewrite_rules();
}

/**
 * The plugin is deactivating.  Delete out the rewrite rules option.
 *
 * @since 1.0.1
 *
 * @return void
 */
function deactivate_plugin() {
	delete_option( 'rewrite_rules' );
}

/**
 * Uninstall plugin handler
 *
 * @since 1.0.1
 *
 * @return void
 */
function uninstall_plugin() {
	delete_option( 'rewrite_rules' );
}

/**
 * Kick off the plugin by initializing the plugin files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_autoloader() {
	// Admin files
	require_once 'admin/custom/post-types.php';
	require_once 'admin/custom/render-posts.php';

	// Testing
	require_once 'admin/root.php';
}

init_autoloader();
init_constants();
init_hooks();

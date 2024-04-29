<?php
/**
 * Plugin Name: RoleMaster Suite
 * Plugin URI:  https://jeweltheme.com/rolemaster-suite
 * Description: User Roles & Capability Plugin
 * Version:     1.0.1
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com/rolemaster-suite
 * Text Domain: rolemaster-suite
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package rolemaster-suite
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$rolemaster_suite_plugin_data = get_file_data(
	__FILE__,
	array(
		'Version'     => 'Version',
		'Plugin Name' => 'Plugin Name',
		'Author'      => 'Author',
		'Description' => 'Description',
		'Plugin URI'  => 'Plugin URI',
	),
	false
);

// Define Constants.
if ( ! defined( 'ROLEMASTER' ) ) {
	define( 'ROLEMASTER', $rolemaster_suite_plugin_data['Plugin Name'] );
}

if ( ! defined( 'ROLEMASTER_VER' ) ) {
	define( 'ROLEMASTER_VER', $rolemaster_suite_plugin_data['Version'] );
}

if ( ! defined( 'ROLEMASTER_AUTHOR' ) ) {
	define( 'ROLEMASTER_AUTHOR', $rolemaster_suite_plugin_data['Author'] );
}

if ( ! defined( 'ROLEMASTER_DESC' ) ) {
	define( 'ROLEMASTER_DESC', $rolemaster_suite_plugin_data['Author'] );
}

if ( ! defined( 'ROLEMASTER_URI' ) ) {
	define( 'ROLEMASTER_URI', $rolemaster_suite_plugin_data['Plugin URI'] );
}

if ( ! defined( 'ROLEMASTER_DIR' ) ) {
	define( 'ROLEMASTER_DIR', __DIR__ );
}

if ( ! defined( 'ROLEMASTER_FILE' ) ) {
	define( 'ROLEMASTER_FILE', __FILE__ );
}

if ( ! defined( 'ROLEMASTER_SLUG' ) ) {
	define( 'ROLEMASTER_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'ROLEMASTER_BASE' ) ) {
	define( 'ROLEMASTER_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'ROLEMASTER_PATH' ) ) {
	define( 'ROLEMASTER_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'ROLEMASTER_URL' ) ) {
	define( 'ROLEMASTER_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}

if ( ! defined( 'ROLEMASTER_INC' ) ) {
	define( 'ROLEMASTER_INC', ROLEMASTER_PATH . '/Inc/' );
}

if ( ! defined( 'ROLEMASTER_LIBS' ) ) {
	define( 'ROLEMASTER_LIBS', ROLEMASTER_PATH . 'Libs' );
}

if ( ! defined( 'ROLEMASTER_ASSETS' ) ) {
	define( 'ROLEMASTER_ASSETS', ROLEMASTER_URL . 'assets/' );
}

if ( ! defined( 'ROLEMASTER_IMAGES' ) ) {
	define( 'ROLEMASTER_IMAGES', ROLEMASTER_ASSETS . 'images/' );
}

if ( ! class_exists( '\\ROLEMASTER\\Rolemaster_Suite' ) ) {
	// Autoload Files.
	include_once ROLEMASTER_DIR . '/vendor/autoload.php';
	// Instantiate Rolemaster_Suite Class.
	include_once ROLEMASTER_DIR . '/class-rolemaster-suite.php';
}

// Activation and Deactivation hooks.
if ( class_exists( '\\ROLEMASTER\\Rolemaster_Suite' ) ) {
	register_activation_hook( ROLEMASTER_FILE, array( '\\ROLEMASTER\\Rolemaster_Suite', 'rolemaster_suite_activation_hook' ) );
	// register_deactivation_hook( ROLEMASTER_FILE, array( '\\ROLEMASTER\\Rolemaster_Suite', 'rolemaster_suite_deactivation_hook' ) );
}

<?php
namespace ROLEMASTER;

use ROLEMASTER\Libs\Assets;
use ROLEMASTER\Libs\Helper;
use ROLEMASTER\Libs\Featured;
use ROLEMASTER\Inc\Classes\Recommended_Plugins;
use ROLEMASTER\Inc\Classes\Notifications\Notifications;
use ROLEMASTER\Inc\Classes\Pro_Upgrade;
use ROLEMASTER\Inc\Classes\Upgrade_Plugin;
use ROLEMASTER\Inc\Classes\Feedback;

/**
 * Main Class
 *
 * @RoleMaster Suite
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.0.1
 */

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rolemaster_Suite Class
 */
if ( ! class_exists( '\ROLEMASTER\Rolemaster_Suite' ) ) {

	/**
	 * Class: Rolemaster_Suite
	 */
	final class Rolemaster_Suite {

		const VERSION            = ROLEMASTER_VER;
		private static $instance = null;

		/**
		 * what we collect construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			$this->includes();
			add_action( 'plugins_loaded', array( $this, 'rolemaster_suite_plugins_loaded' ), 999 );
			// Body Class.
			add_filter( 'admin_body_class', array( $this, 'rolemaster_suite_body_class' ) );
			// This should run earlier .
			// add_action( 'plugins_loaded', [ $this, 'rolemaster_suite_maybe_run_upgrades' ], -100 ); .

			add_action( 'admin_menu', array( $this, 'rolemaster_suite_admin_menu' ) );
		}


		/**
		 * Register Main Menu.
		 *
		 * @return void
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function rolemaster_suite_admin_menu()
		{
			add_menu_page(
				__('Rolemaster Suite', 'rolemaster-suite'),
				__('Rolemaster Suite', 'rolemaster-suite'),
				'manage_options',
				'rolemaster_suite_editor' . '-settings',
				array($this, 'rolemaster_suite_settings_page'),
				ROLEMASTER_IMAGES . 'menu-icon.svg',
				40
			);

			add_submenu_page(
				'rolemaster_suite_editor' . '-settings',
				__('Rolemaster Suite Settings', 'rolemaster-suite'),
				__('Settings', 'rolemaster-suite'),
				'manage_options',
				'rolemaster_suite_editor' . '-settings',
				array($this, 'rolemaster_suite_settings_page'),
				10
			);
		}

		public function rolemaster_suite_settings_page(){
			echo '<h2>Settings Page</h2>';
		}


		/**
		 * plugins_loaded method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function rolemaster_suite_plugins_loaded() {
			$this->rolemaster_suite_activate();
		}

		/**
		 * Version Key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function plugin_version_key() {
			return Helper::rolemaster_suite_slug_cleanup() . '_version';
		}

		/**
		 * Activation Hook
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function rolemaster_suite_activate() {
			$current_rolemaster_suite_version = get_option( self::plugin_version_key(), null );

			if ( get_option( 'rolemaster_suite_activation_time' ) === false ) {
				update_option( 'rolemaster_suite_activation_time', strtotime( 'now' ) );
			}

			if ( is_null( $current_rolemaster_suite_version ) ) {
				update_option( self::plugin_version_key(), self::VERSION );
			}

			$allowed = get_option( Helper::rolemaster_suite_slug_cleanup() . '_allow_tracking', 'no' );

			// if it wasn't allowed before, do nothing .
			if ( 'yes' !== $allowed ) {
				return;
			}
			// re-schedule and delete the last sent time so we could force send again .
			$hook_name = Helper::rolemaster_suite_slug_cleanup() . '_tracker_send_event';
			if ( ! wp_next_scheduled( $hook_name ) ) {
				wp_schedule_event( time(), 'weekly', $hook_name );
			}
		}


		/**
		 * Add Body Class
		 *
		 * @param [type] $classes .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function rolemaster_suite_body_class( $classes ) {
			$classes .= ' rolemaster-suite ';
			return $classes;
		}

		/**
		 * Run Upgrader Class
		 *
		 * @return void
		 */
		public function rolemaster_suite_maybe_run_upgrades() {
			if ( ! is_admin() && ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Run Upgrader .
			$upgrade = new Upgrade_Plugin();

			// Need to work on Upgrade Class .
			if ( $upgrade->if_updates_available() ) {
				$upgrade->run_updates();
			}
		}

		/**
		 * Include methods
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes() {
			new Assets();
			new Recommended_Plugins();
			new Pro_Upgrade();
			new Notifications();
			new Featured();
			new Feedback();
		}


		/**
		 * Initialization
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function rolemaster_suite_init() {
			$this->rolemaster_suite_load_textdomain();
		}


		/**
		 * Text Domain
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function rolemaster_suite_load_textdomain() {
			$domain = 'rolemaster-suite';
			$locale = apply_filters( 'rolemaster_suite_plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( ROLEMASTER_BASE ) . '/languages/' );
		}

		/**
		* Deactivate Pro Plugin if it's not already active
		*
		* @author Jewel Theme <support@jeweltheme.com>
		*/
		public static function rolemaster_suite_activation_hook() {
			if ( rolemaster_suite_license_client()->is_free_plan() ) {
				$plugin = 'rolemaster-suite-pro/rolemaster-suite.php';
			} else {
				$plugin = 'rolemaster-suite/rolemaster-suite.php';
			}
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
			}
		}


		/**
		 * Returns the singleton instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Rolemaster_Suite ) ) {
				self::$instance = new Rolemaster_Suite();
				self::$instance->rolemaster_suite_init();
			}

			return self::$instance;
		}
	}

	// Get Instant of Rolemaster_Suite Class .
	Rolemaster_Suite::get_instance();
}

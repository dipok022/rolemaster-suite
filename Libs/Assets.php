<?php
		namespace ROLEMASTER\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Assets' ) ) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.0.1
	 */
	class Assets {

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'rolemaster_suite_enqueue_scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'rolemaster_suite_admin_enqueue_scripts' ), 100 );
		}


		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode() {
			return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
		}

		/**
		 * Enqueue Scripts
		 *
		 * @method wp_enqueue_scripts()
		 */
		public function rolemaster_suite_enqueue_scripts() {

			// CSS Files .
			wp_enqueue_style( 'rolemaster-suite-frontend', ROLEMASTER_ASSETS . 'css/rolemaster-suite-frontend.css', ROLEMASTER_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'rolemaster-suite-frontend', ROLEMASTER_ASSETS . 'js/role-master-suite-frontend.js', array( 'jquery' ), ROLEMASTER_VER, true );
		}


		/**
		 * Enqueue Scripts
		 *
		 * @method admin_enqueue_scripts()
		 */
		public function rolemaster_suite_admin_enqueue_scripts() {
			// CSS Files .
			wp_enqueue_style( 'rolemaster-suite-admin', ROLEMASTER_ASSETS . 'css/role-master-suite-admin.css', array( 'dashicons' ), ROLEMASTER_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'rolemaster-suite-admin', ROLEMASTER_ASSETS . 'js/role-master-suite-admin.js', array( 'jquery' ), ROLEMASTER_VER, true );
			wp_localize_script(
				'rolemaster-suite-admin',
				'ROLEMASTERCORE',
				array(
					'admin_ajax'        => admin_url( 'admin-ajax.php' ),
					'recommended_nonce' => wp_create_nonce( 'rolemaster_suite_recommended_nonce' ),
					'is_premium'        => rolemaster_suite_license_client()->is_premium() ? true : false,
					'is_agency'         => rolemaster_suite_license_client()->is_plan( 'agency' ) ? true : false,
				)
			);
		}
	}
}
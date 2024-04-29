<?php

if ( ! function_exists( 'rolemaster_suite_license_client' ) ) {
	/**
	 * License Client function
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	function rolemaster_suite_license_client() {
		global $rolemaster_suite_license_client;

		if ( ! isset( $rolemaster_suite_license_client ) ) {
			// Include SDK.
			require_once ROLEMASTER_LIBS . '/License/Loader.php';

			$rolemaster_suite_license_client = new \ROLEMASTER\Libs\License\Loader(
				array(
					'plugin_root'      => ROLEMASTER_FILE,
					'software_version' => ROLEMASTER_VER,
					'software_title'   => 'RoleMaster Suite',
					'product_id'       => '',
					'redirect_url'     => admin_url( 'admin.php?page=' . ROLEMASTER_SLUG . '-license-activation' ),
					'software_type'    => 'plugin', // theme/plugin .
					'api_end_point'    => \ROLEMASTER\Libs\Helper::api_endpoint(),
					'text_domain'      => 'rolemaster-suite',
					'license_menu'     => array(
						'icon_url'    => 'dashicons-image-filter',
						'position'    => 40,
						'menu_type'   => 'add_submenu_page', // 'add_submenu_page',
                        'parent_slug' => '-settings',
						'menu_title'  => esc_html__( 'License', 'rolemaster-suite' ),
						'page_title'  => esc_html__( 'License Activation', 'rolemaster-suite' ),
					),
				)
			);
		}

		return $rolemaster_suite_license_client;
	}

	// Init Rolemaster_Suite_Wc_Client.
	rolemaster_suite_license_client();

	// Signal that Rolemaster_Suite_Wc_Client was initiated.
	do_action( 'rolemaster_suite_license_client_loaded' );
}
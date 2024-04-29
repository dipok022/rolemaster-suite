<?php

// Test Code
// if ( rolemaster_suite_license_client()->is_plan( 'business' ) ) {
//     pretty_log('Yeah plan is business');
// } else {
//     pretty_log('not business');
// }

// if ( rolemaster_suite_license_client()->is_free_plan() ) {
//     pretty_log('Yeah Free Plan');
// } else {
//     pretty_log('not a free plan');
// }

// if ( rolemaster_suite_license_client()->is_premium() ) {
//     pretty_log('Yeah Premium Plan');
// } else {
//     pretty_log('not a pro plan');
// }

// if ( rolemaster_suite_license_client()->can_use_premium_code() ) {
//     pretty_log('Yeah Premium Code Can Use');
// } else {
//     pretty_log('Cant use');
// }





// if ( ! function_exists( 'product_one_client' ) ) {
//     function product_one_client() {
//         global $product_one_client;
//         if ( ! isset( $product_one_client ) ) {
//             // Include SDK.
//             require_once 'class-plugin-loader.php';
//             $product_one_client = new Loader([
//                 'plugin_root' => ROLEMASTER_FILE,
//                 'software_version' => ROLEMASTER_VER,
//                 'software_title' => 'RoleMaster Suite',
//                 'product_id' => 82,
//                 'redirect_url' => home_url('/'),
//                 'api_end_point' => 'https://bo.jeweltheme.com/',
//             ]);

//         }

//         return $product_one_client;
//     }

//     // Init Rolemaster_Suite_Client.
//     product_one_client();

//     // Signal that Rolemaster_Suite_Client was initiated.
//     do_action( 'product_one_client_loaded' );

// }

// if ( product_one_client()->is_premium() ) {
//     pretty_log('Yeah Premium Plan');
// } else {
//     pretty_log('not a pro plan');
// }





//$wcam_lib = new WC_AM_Client_2_9( __FILE__, 32960, '1.2', 'plugin', 'http://wc/', 'WooCommerce API Manager PHP Library for Plugins and Themes' );
// $wcam_lib = new WC_AM_Client_2_9(
// 	__FILE__,
// 	'',
// 	'1.2',
// 	'plugin',
// 	'http://wc/',
// 	'WooCommerce API Manager PHP Library for Plugins and Themes',
// 	'wc-am-text'
// );

/**
 * Custom men Plugin example.
 *
 * Last argument to the WC_AM_Client_2_9 class is to prevent the not activate yet admin message from being displayed, which may not be necessary with a custom menu.
 *
 * Example using add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null );
 *
 * Arguments:
 *
 * $borderless_license_menu = array( 'menu_type' => 'add_submenu_page', 'parent_slug' => 'borderless.php', 'page_title' => '', 'menu_title' => '', 'capability' => '', 'menu_slug' => '', 'menu_slug' => '', 'callback' => '', 'position' => '' );
 * Only arguments with values need to be provided.
 *
 * Custom menus allowed:
 *
 * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null );
 * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null );
 * add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon_url = '', $position = null );
 *
 */
// $license_menu = array( 'menu_type' => 'add_submenu_page', 'parent_slug' => 'borderless.php', 'page_title' => 'License Activation', 'menu_title' => 'License' );

// $license = new WC_AM_Client_2_9( __FILE__, 32960, '1.2', 'plugin', 'http://wc', 'WooCommerce API Manager PHP Library for Plugins and Themes', 'wc-am-text', $license_menu, false );
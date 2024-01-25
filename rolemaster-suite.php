<?php
/**
 * Plugin Name: RoleMaster Suite
 * Plugin URI:  https://jeweltheme.com/rolemaster-suite
 * Description: User Roles & Capability Plugin
 * Version:     1.0.0
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com
 * Text Domain: rolemaster-suite
 * Domain Path: /languages/
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package RoleMaster Suite
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Tweaks init
 */
add_action('init', 'rolemaster_suite_init');
function rolemaster_suite_init(){
    rolemaster_suite_hide_frontend_admin_bar();
}

/**
 * Hide Frontend Admin Bar
 *
 * @return void
 */
function rolemaster_suite_hide_frontend_admin_bar(){

    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the current user's roles
        $user = wp_get_current_user();
        $roles = (array) $user->roles;

        // Define the roles for which the admin bar should be hidden
        $roles_to_hide_admin_bar = array('subscriber', 'contributor');

        // Check if the user has one of the specified roles
        if (array_intersect($roles, $roles_to_hide_admin_bar)) {
            // Hide the admin bar for users with specified roles
            add_filter('show_admin_bar', '__return_false');
        }
    }    
}
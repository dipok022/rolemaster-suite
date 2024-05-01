<?php

namespace ROLEMASTER\Inc\Classes;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WP Adminify
 * @package WP Adminify: Admin Bar Editor
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class UserRoleEditorAssets extends UserRoleEditorModel
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'user_role_editor_enqueue_scripts'], 100);
    }

    public function user_role_editor_enqueue_scripts()
    {
        global $pagenow;
        if (('admin.php' === $pagenow) && ('rolemaster_suite_editor-settings' === $_GET['page'])) {

            wp_register_style('rolemaster-suite-user-role-editor', ROLEMASTER_ASSETS . 'css/rolemaster-suite-user-role-editor.css', false, ROLEMASTER_VER);
            wp_register_script('rolemaster-suite-user-role-editor', ROLEMASTER_ASSETS . 'js/rolemaster-suite-user-role-editor.js', ['wp-element'], ROLEMASTER_VER, true);

            // Enqueue Styles
            wp_enqueue_style('rolemaster-suite-user-role-editor');

            // Enqueue Scripts
            wp_enqueue_script('rolemaster-suite-user-role-editor');
        }

        // Localize Scripts
        $localize_user_role_data = array(
            'rest_urls'                => [
                'baseUrl'                   => UserRoleEditorApiEndPoints::get_rest_url(''),
                'getUserRoleCapabilities'   => UserRoleEditorApiEndPoints::get_rest_url('/get-user-role-capabilities/')
            ],
            'ajax_url'                 => admin_url('admin-ajax.php'),
            'image_path'               => ROLEMASTER_IMAGES,
            'nonce'                    => wp_create_nonce( 'wp_rest' )
        );
        wp_localize_script('rolemaster-suite-user-role-editor', 'WPAdminifyUserRoleEditor', $localize_user_role_data);
    }

}

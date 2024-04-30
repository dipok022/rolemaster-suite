<?php

namespace ROLEMASTER\Inc\Classes;

use WPAdminify\Inc\Utils;
use WPAdminify\Inc\Admin\AdminSettings;
use WPAdminify\Inc\Admin\AdminSettingsModel;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WP Adminify
 * @package WP Adminify: Admin Bar Editor
 *
 * @author WP Adminify <support@wpadminify.com>
 */

class UserRoleEditorAssets extends AdminSettingsModel
{
    public function __construct()
    {
        $this->options = (array) AdminSettings::get_instance()->get();
        add_action('admin_enqueue_scripts', [$this, 'user_role_editor_enqueue_scripts'], 100);
    }

    public function user_role_editor_enqueue_scripts()
    {
        global $pagenow;
        if (('admin.php' === $pagenow) && ('adminify-user-role-editor' === $_GET['page'])) {
            // Enqueue Styles
            wp_enqueue_style('wp-adminify-user-role-editor');

            // Enqueue Scripts
            wp_enqueue_script('wp-adminify-user-role-editor');
        }

        // De-register and Dequeue Scripts/Styles
        if (!empty($this->options['adminify_assets'])) {
            foreach ($this->options['adminify_assets'] as $value) {
                wp_dequeue_style($value);
                wp_deregister_style($value);
            }
        }

        // Localize Scripts
        $localize_user_role_data = array(
            'rest_urls'                => [
                'baseUrl'                   => UserRoleEditorApiEndPoints::get_rest_url(''),
                'getUserRoleCapabilities'   => UserRoleEditorApiEndPoints::get_rest_url('/get-user-role-capabilities/')
            ],
            'ajax_url'                 => admin_url('admin-ajax.php'),
            'image_path'               => WP_ADMINIFY_ASSETS_IMAGE,
            'nonce'                    => wp_create_nonce( 'wp_rest' )
        );
        wp_localize_script('wp-adminify-user-role-editor', 'WPAdminifyUserRoleEditor', $localize_user_role_data);
    }

}

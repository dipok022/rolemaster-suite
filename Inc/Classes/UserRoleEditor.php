<?php

namespace ROLEMASTER\Inc\Classes;

use ROLEMASTER\Libs\Helper;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

/**
 * WP Adminify
 * @package WP Adminify: User Role Editor
 *
 * @author WP Adminify <support@wpadminify.com>
 */

if (!class_exists('UserRoleEditor')) {
    class UserRoleEditor extends UserRoleEditorModel
    {
        public $adminify_ui;

        public function __construct()
        {
            add_action('init', [$this, 'get_custom_post_types_list'], 9999);
            add_action('admin_menu', [$this, 'rolemaster_suite_user_role_editor_submenu'], 60);
            add_filter('admin_body_class', [$this, 'rolemaster_suite_user_role_editor_body_class']);
            add_filter('admin_init', [$this, 'rolemaster_suite_create_user_role']);
            add_filter( 'register_post_type_args', [$this, 'custom_post_types_capability'], 10, 2 );

            new UserRoleEditorApiEndPoints();
            new UserRoleEditorAssets();
        }

        public function get_custom_post_types_list()
        {
            $args = array(
                'public'   => true,
                '_builtin' => false,
            );
            $post_types          = get_post_types( $args, 'objects' );
            unset($post_types['adminify_admin_page']);
            update_option('_wp_adminify_all_cpts',array_keys($post_types));
        }

        // Admin Bar Editor Body Class
        public function rolemaster_suite_user_role_editor_body_class($classes)
        {
            $classes .= ' adminify_user_role_editor ';
            return $classes;
        }

        /**
         * Admin Bar Editor Menu
         */
        public function rolemaster_suite_user_role_editor_submenu()
        {
            // If WP Adminify Plugin Installed then show on Sub Menu
            if (Helper::is_plugin_active('adminify/adminify.php') || Helper::is_plugin_active('adminify-pro/adminify.php')) {
                add_submenu_page(
                    'wp-adminify-settings',
                    esc_html__('User Role Editor by WP Adminify', 'rolemaster-suite'),
                    esc_html__('User Role Editor', 'rolemaster-suite'),
                    apply_filters('rolemaster_suite_capability', 'manage_options'),
                    'rolemaster-suite-editor', // Page slug, will be displayed in URL
                    [$this, 'rolemaster_suite_user_role_editor_contents']
                );
            } else {
                // If WP Adminify Plugin not Installed then show on Main Menu
                add_menu_page(
                    __('Rolemaster Suite', 'rolemaster-suite'),
                    __('Rolemaster Suite', 'rolemaster-suite'),
                    'manage_options',
                    'rolemaster_suite_editor' . '-settings',
                    array($this, 'rolemaster_suite_user_role_editor_contents'),
                    ROLEMASTER_IMAGES . 'menu-icon.svg',
                    40
                );

                add_submenu_page(
                    'rolemaster_suite_editor' . '-settings',
                    __('Rolemaster Suite Settings', 'rolemaster-suite'),
                    __('Settings', 'rolemaster-suite'),
                    'manage_options',
                    'rolemaster_suite_editor' . '-settings',
                    array($this, 'rolemaster_suite_user_role_editor_contents'),
                    10
                );
            }
        }

        public function rolemaster_suite_create_user_role()
        {
            // Gets the simple_role role object.
            $role_to_administrator = get_role( 'administrator' );
            $role_to_editor        = get_role( 'editor' );

            $granted_users = [
                $role_to_administrator,
                $role_to_editor
            ];

            // Add a new capability to all granted users.
            foreach ($granted_users as $users) {
                $users->add_cap( 'create_posts', true );
                $users->add_cap( 'create_pages', true );
            }

            $all_cpts = get_option('_wp_adminify_all_cpts',null);
            if($all_cpts != null){
                foreach ($all_cpts as $cpt) {
                    foreach ($granted_users as $users) {
                        $existing_caps = array_keys($users->capabilities);

                        $new_caps = [
                            'edit_'.$cpt.'s',
                            'create_'.$cpt.'s',
                            'edit_others_'.$cpt.'s',
                            'publish_'.$cpt.'s',
                            'edit_published_'.$cpt.'s',
                            'edit_private_'.$cpt.'s',
                            'delete_'.$cpt.'s',
                            'delete_others_'.$cpt.'s',
                            'delete_published_'.$cpt.'s',
                            'delete_private_'.$cpt.'s',
                            'read_private_'.$cpt.'s'
                        ];

                        foreach ($new_caps as $key => $cap) {
                            if(!in_array($cap,$existing_caps)){
                                $users->add_cap( $cap, true );
                            }
                        }
                    }
                }
            }
        }

        public function custom_post_types_capability( $args, $post_type )
        {

            $all_cpts = get_option('_wp_adminify_all_cpts',null);

            if ( ($all_cpts != null) && is_array($all_cpts) ){
                if ( in_array($post_type, $all_cpts)){
                    $args['capability_type'] = [
                        $post_type,
                        $post_type.'s'
                    ];
                    $args['map_meta_cap'] = true;
                }
            }

            return $args;
        }

        public static function add_new_capability($role_id, $cap_id)
        {
            global $wpdb;

            $result['status'] = false;
            $result['saved_cap'] = [];
            $result['message'] = '';


            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);
            $capabilities = $get_all_roles[$role_id]['capabilities'];

            if(! in_array($cap_id, array_keys($capabilities))) {
                $admin = get_role( 'administrator' );
                $admin->add_cap( $cap_id, true );

                $role = get_role( $role_id );
                $role->add_cap( $cap_id, true );

                $result['status']     = true;
                $result['message']    = sprintf(__('"%s" created successfully.','rolemaster-suite'),$cap_id);
                $result['saved_cap']  = $cap_id;
            }else if(in_array($cap_id, array_keys($capabilities))){
                $result['message'] = sprintf(__('"%s" already exists.','rolemaster-suite'),$cap_id);
            }else{
                $result['message'] = __('Something went wrong, please try again.','rolemaster-suite');
            }
            return $result;
        }

        public static function add_new_role($role_id, $role_name, $capabilities = [])
        {
            global $wpdb;

            $result['status'] = false;
            $result['saved_role'] = [];
            $result['message'] = '';
            $capabilities = empty($capabilities) ? ['read'=>1] : $capabilities;

            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);
            if(! in_array($role_id, array_keys($get_all_roles))) {
                add_role(
                    'adminify_'.$role_id,
                    $role_name,
                    $capabilities,
                );
                $result['status']     = true;
                $result['message']    = sprintf(__('Role "%s" created successfully.','rolemaster-suite'),$role_name);
                $result['saved_role'] = ['adminify_'.$role_id => ['name' => $role_name, 'capabilities' => $capabilities]];
            }else if(in_array($role_id, array_keys($get_all_roles))){
                $result['message'] = sprintf(__('Role "%s" already exists.','rolemaster-suite'),$role_name);
            }else{
                $result['message'] = __('Something went wrong, please try again.','rolemaster-suite');
            }
            return $result;
        }

        public static function delete_role($role_id, $role_name)
        {
            global $wpdb;

            $result['status'] = false;
            $result['deleted_role'] = [];
            $result['message'] = '';

            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);

            if(in_array($role_id, array_keys($get_all_roles))) {
                remove_role( $role_id );
                $result['status']     = true;
                $result['message']    = sprintf(__('Role "%s" deleted successfully.','rolemaster-suite'),$role_name);
                $result['deleted_role'] = $role_id;
            }else if( !in_array($role_id, array_keys($get_all_roles))){
                $result['message'] = sprintf(__('Role "%s" doesn\'t exists.','rolemaster-suite'),$role_name);
            }else{
                $result['message'] = __('Something went wrong, please try again.','rolemaster-suite');
            }
            return $result;
        }

        public static function update_capabilities($role,$caps)
        {
            global $wpdb,$wp_roles;

            $result['status'] = false;
            $result['message'] = '';

            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);

            $format_cap = [];
            foreach ($caps as $cap) {
                $format_cap[$cap] = 1;
            }

            $get_all_roles[$role]['capabilities'] = $format_cap;
            update_option($option_name,$get_all_roles);

            $updated_caps = $get_all_roles[$role]['capabilities'];
            if($updated_caps == $format_cap) {
                $result['status']     = true;
                $result['message']    = __('Saved capabilities successfully.','rolemaster-suite');
            }else{
                $result['message'] = __('Something went wrong, please try again.','rolemaster-suite');
            }
            return $result;
        }

        public static function delete_capabilities($delete_caps)
        {
            global $wpdb,$wp_roles;

            $temp_array = $delete_caps;

            $result['status'] = false;
            $result['message'] = '';

            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);

            foreach ($delete_caps as $cap) {
                foreach (array_keys($wp_roles->roles) as $key => $role) {
                    $wp_roles->remove_cap($role, $cap);
                    unset($temp_array[$key]);
                }
            }

            if(empty($temp_array)) {
                $result['status']     = true;
                $result['message']    = __('Selected capabilities deleted successfully.','rolemaster-suite');
            }else if( !empty($temp_array) ){
                $result['message'] = __('Selected capabilities doesn\'t exists.','rolemaster-suite');
            }else{
                $result['message'] = __('Something went wrong, please try again.','rolemaster-suite');
            }
            return $result;
        }

        public static function rename_role($role_id, $role_name)
        {
            global $wpdb;

            $result['status'] = false;
            $result['message'] = '';
            $result['renamed_to'] = '';

            $option_name = $wpdb->prefix . 'user_roles';
            $get_all_roles = get_option($option_name);
            $get_all_roles[$role_id]['name'] = $role_name;
            if( !empty($role_name) && is_array($get_all_roles[$role_id]) && array_key_exists('name',$get_all_roles[$role_id])){
                update_option($option_name,$get_all_roles);
                $result['status']     = true;
                $result['renamed_to'] = $role_name;
                $result['message']    = sprintf(__('Role "%s" updated successfully.','rolemaster-suite'),$role_name);
            }
            return $result;
        }

        public function rolemaster_suite_user_role_editor_contents()
        {   ?>
            <div id="rolemaster-suite-user--role--editor-root" class="wrap">
            </div>
            <?php
        }

    }
}

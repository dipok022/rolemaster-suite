<?php

namespace ROLEMASTER\Inc\Classes;

// no direct access allowed
if (!defined('ABSPATH'))  exit;

class UserRoleEditorApiEndPoints extends UserRoleEditorModel
{

	private $namespace = 'rolemaster-suite-editor-api/';
	private $version = 'v1';

    public function __construct()
    {
        add_action( 'rest_api_init', [$this, 'add_endpoints'] );
    }

    /**
     * Generate API Namespace
     */
	public function api_namespace() {
		return $this->namespace . $this->version;
	}

    /**
     * Register Routes
     */
    public function add_endpoints() {
        register_rest_route(
            $this->api_namespace(),
            '/get-user-role-capabilities',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            =>  [$this, 'get_user_role_capabilities'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/save-user-role/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            =>  [$this, 'save_user_role'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/rename-user-role/',
            [
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            =>  [$this, 'rename_user_role'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/delete-user-role/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            =>  [$this, 'delete_user_role'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/update-capabilities/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            =>  [$this, 'update_capabilities'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/save-new-capability/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            =>  [$this, 'save_new_capability'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/delete-capabilities/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            =>  [$this, 'delete_capabilities'],
                'permission_callback' =>  [$this, 'check_permission']
            ]
        );
    }

    /**
     * Get admin bar items
     */
    public function get_user_role_capabilities($request)
    {
        global $wp_roles;
        $all_roles = $wp_roles->roles;

        return [
            'user_role_capabilities' => $all_roles,
            'get_post_types'         => $this->get_registered_post_types()
        ];
    }

    public function save_user_role($request)
    {
        $role_id       = $request['role_id'];
        $role_name     = $request['role_name'];
        $capabilities  = $request['capabilities'];

        $result = UserRoleEditor::add_new_role($role_id,$role_name,$capabilities);

        wp_send_json(json_encode($result));
    }

    public function save_new_capability($request)
    {
        $role_id  = $request['role'];
        $cap_id   = $request['capId'];

        $result = UserRoleEditor::add_new_capability($role_id,$cap_id);

        wp_send_json(json_encode($result));
    }

    public function update_capabilities($request)
    {
        $role = $request['role'];
        $caps = $request['caps'];

        $result = UserRoleEditor::update_capabilities($role,$caps);

        wp_send_json(json_encode($result));
    }

    public function rename_user_role($request)
    {
        $role_id       = $request['role_id'];
        $role_name     = $request['role_name'];

        $result = UserRoleEditor::rename_role($role_id,$role_name);

        wp_send_json(json_encode($result));
    }

    public function delete_capabilities($request)
    {
        $caps     = $request['delete_caps'];
        $result = UserRoleEditor::delete_capabilities($caps);

        wp_send_json(json_encode($result));
    }

    public function delete_user_role($request)
    {
        $role_id     = $request['value'];
        $role_name   = $request['label'];
        $result = UserRoleEditor::delete_role($role_id, $role_name);

        wp_send_json(json_encode($result));
    }

    /**
     * Make sure that user has administrative permission
     */
    public function check_permission() {
        return current_user_can( 'manage_options' );
	}

    public function get_registered_post_types() {
        global $wp_post_types;
        $args = array(
            'public'   => true,
            '_builtin' => false,
        );

        $post_types_default['posts'] = 'posts';
        $post_types_default['pages'] = 'pages';

        $output              = 'names'; // names or objects, note names is the default
        $operator            = 'and'; // 'and' or 'or'
        $post_types_custom          = get_post_types( $args, $output, $operator );
        unset($post_types_custom['adminify_admin_page']);
        $post_types = $post_types_default + $post_types_custom;
        return $post_types;

    }

	/**
	 * Returns the full rest url of a given endpoint.
	 *
	 */
	public static function get_rest_url( $endpoint ) {
        $instance = new self();
		return \rest_url($instance->api_namespace() . $endpoint);
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*
 * @version       1.0.0
 * @package       Rolemaster_Suite
 * @license       Copyright Rolemaster_Suite
 */

if ( ! function_exists( 'rolemaster_suite_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name rolemaster_suite_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function rolemaster_suite_option( $section = 'rolemaster_suite_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

if ( ! function_exists( 'rolemaster_suite_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function rolemaster_suite_exclude_pages() {
		return rolemaster_suite_option( 'rolemaster_suite_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'rolemaster_suite_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function rolemaster_suite_exclude_pages_except() {
		return rolemaster_suite_option( 'rolemaster_suite_triggers', 'exclude_pages_except', array() );
	}
}





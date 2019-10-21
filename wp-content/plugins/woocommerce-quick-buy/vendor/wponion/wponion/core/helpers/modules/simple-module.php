<?php

use WPOnion\Modules\Util\Endpoint;
use WPOnion\Modules\Util\Page_Actions;
use WPOnion\Modules\Util\Plugin_Links;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( 'wponion_plugin_links' ) ) {
	/**
	 * @param $plugin_file
	 *
	 * @return \WPOnion\Modules\Util\Plugin_Links
	 */
	function wponion_plugin_links( $plugin_file ) {
		return new Plugin_Links( $plugin_file );
	}
}

if ( ! function_exists( 'wponion_endpoint' ) ) {
	/**
	 * @param $slug
	 *
	 * @return \WPOnion\Modules\Util\Endpoint
	 */
	function wponion_endpoint( $slug ) {
		return new Endpoint( $slug );
	}
}

if ( ! function_exists( 'wponion_page_actions' ) ) {
	/**
	 * @param       $post_type
	 * @param array $buttons
	 */
	function wponion_page_actions( $post_type, $buttons = array() ) {
		Page_Actions::add( $post_type, $buttons );
	}
}

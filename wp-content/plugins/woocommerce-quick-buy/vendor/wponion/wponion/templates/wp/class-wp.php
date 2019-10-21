<?php

namespace WPOnion\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Theme\Global_Theme' ) ) {
	require_once wponion()->tpl( 'global/class-global-theme.php' );
}

if ( ! class_exists( '\WPOnion\Theme\WP' ) ) {
	/**
	 * Class WP
	 *
	 * @package WPOnion\Theme
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class WP extends Global_Theme {
		/**
		 * WP constructor.
		 *
		 * @param $data
		 */
		public function __construct( $data ) {
			parent::__construct( $data, __FILE__, 'wp' );
		}

		/**
		 * Registers Assets With WP.
		 */
		public function register_assets() {
		}
	}
}

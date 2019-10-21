<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPOnion_Loader' ) ) {
	/**
	 * Class WPOnion_Loader
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	final class WPOnion_Loader {
		/**
		 * Stores Loader Instance.
		 *
		 * @var \WPOnion_Loader
		 * @static
		 */
		public static $_instance = null;

		/**
		 * Stores Framework Informations.
		 *
		 * @var array
		 * @static
		 */
		public static $_loaded = array();

		/**
		 * Stores Data.
		 *
		 * @var array
		 * @static
		 */
		public static $data = array();

		/**
		 * WPOnion_Loader constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', [ &$this, 'load_framework' ], -1 );
		}

		/**
		 * Loads Framework From A Plugin which has the latest version
		 */
		public function load_framework() {
			$latest_version = max( array_keys( self::$data ) );
			$info           = ( isset( self::$data[ $latest_version ] ) ) ? self::$data[ $latest_version ] : [];

			if ( empty( $info ) ) {
				$ms = __( 'Unable To Load WPOnion Framework. Please Contact The Author', 'wponion' );
				wp_die( $ms . '<p style="word-break: break-all;"> <strong>' . __( 'ERROR ID : ', 'wponion' ) . '</strong>' . base64_encode( wp_json_encode( self::$data ) ) . '</p>' );
			}

			if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
				// translators: 1. Added PHP Version
				$msg = sprintf( __( 'WPOnion incompatible with PHP Version %2$s. Please Install/Upgrade PHP To %1$s or Higher ', 'wponion' ), '<strong>5.6</strong>', '<code>' . PHP_VERSION . '</code>' );
				wp_die( $msg );
			}

			self::$_loaded = array(
				'path'    => $info,
				'version' => $latest_version,
			);
			require $info . 'wponion-init.php';
		}

		/**
		 * Creates A Static Instances
		 *
		 * @return \WPOnion_Loader
		 */
		public static function instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Stores Framework Version & its details
		 *
		 * @param string      $data other information.
		 * @param string|bool $version framework version.
		 *
		 * @return $this
		 */
		public function add( $data = '', $version = false ) {
			if ( file_exists( trailingslashit( $data ) . 'index.php' ) ) {
				if ( false === $version ) {
					$args    = get_file_data( trailingslashit( $data ) . 'index.php', array( 'version' => 'Version' ) );
					$version = ( isset( $args['version'] ) && ! empty( $args['version'] ) ) ? $args['version'] : $version;
				}
				self::$data[ $version ] = trailingslashit( $data );
			}
			return $this;
		}
	}
}

if ( ! function_exists( 'wponion_load' ) ) {
	/**
	 * Adds Passed Plugin path to the list array which later used to compare and
	 * load the framework from a plugin which has the latest version of framework
	 *
	 * @param string $framework_path
	 * @param bool   $version
	 */
	function wponion_load( $framework_path = __DIR__, $version = false ) {
		WPOnion_Loader::instance()
			->add( $framework_path, $version );
	}
}

if ( function_exists( 'wponion_load' ) ) {
	wponion_load( __DIR__ );
}

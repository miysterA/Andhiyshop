<?php

namespace WPOnion\Registry;

use WPOnion\Bridge;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Registry\Core' ) ) {
	/**
	 * Class Core
	 *
	 * @package WPOnion\Registry
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Core implements Common {
		/**
		 * Stores All Instances
		 *
		 * @var array
		 */
		protected $registry = array();

		/**
		 * Adds An Instance To Array.
		 *
		 * @param string          $type
		 * @param \WPOnion\Bridge $instance
		 *
		 * @return mixed|void
		 */
		public function add( $type, Bridge &$instance ) {
			if ( ! isset( $this->registry[ $type ] ) ) {
				$this->registry[ $type ] = array();
			}

			$key = get_class( $instance );

			if ( ! isset( $registry[ $type ][ $key ] ) ) {
				$this->registry[ $type ][ $key ] = $instance;
			}
		}

		/**
		 * Returns An Instance.
		 *
		 * @param string $type
		 * @param        $key
		 *
		 * @return object|bool
		 * @static
		 */
		public function get( $type, $key ) {
			return ( isset( $this->registry[ $type ][ $key ] ) ) ? $this->registry[ $type ][ $key ] : false;
		}
	}
}

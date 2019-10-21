<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPOnion' ) ) {
	/**
	 * Class WPOnion
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	final class WPOnion {
		use WPOnion\Traits\Self_Instance;

		/**
		 * @param $path
		 * @param $is_url
		 *
		 * @return string
		 */
		protected function url_path( $path, $is_url ) {
			return ( $is_url ) ? $this->url( $path ) : $this->path( $path );
		}

		/**
		 * Returns Current WPOnion Path.
		 *
		 * @param string $extra
		 *
		 * @return string
		 */
		public function path( $extra = '' ) {
			return WPONION_PATH . $extra;
		}

		/**
		 * Returns Current WPOnion URL.
		 *
		 * @param string $extra
		 *
		 * @return string
		 */
		public function url( $extra = '' ) {
			return WPONION_URL . $extra;
		}

		/**
		 * Returns Current Assets Path As URL.
		 *
		 * @param string $extra
		 * @param bool   $is_url
		 *
		 * @return string
		 */
		public function assets( $extra = '', $is_url = true ) {
			return $this->url_path( 'assets/' . $extra, $is_url );
		}

		/**
		 * Returns Templates Path.
		 *
		 * @param string $extra
		 * @param bool   $is_url
		 *
		 * @return string
		 */
		public function tpl( $extra = '', $is_url = false ) {
			return $this->url_path( 'templates/' . $extra, $is_url );
		}

		/**
		 * @param string $extra
		 * @param bool   $is_url
		 *
		 * @return string
		 */
		public function data( $extra = '', $is_url = false ) {
			return $this->url_path( 'data/' . $extra, $is_url );
		}
	}
}

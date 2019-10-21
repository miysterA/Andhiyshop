<?php

namespace WPOnion\Registry;

use WPOnion\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Registry\Field_Error' ) ) {
	/**
	 * Class Field_Error
	 *
	 * @package WPOnion\Registry
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Field_Error {
		/**
		 * Stores ALL Erros Instance.
		 *
		 * @var array
		 */
		private $errors = array();

		/**
		 * @param string $error_id
		 *
		 * @return bool|string|array
		 */
		public function get( $error_id = 'settings' ) {
			return Helper::array_key_get( $error_id, $this->errors, false );
		}

		/**
		 * Stores an given data to array.
		 *
		 * @param array $errors
		 */
		public function set( $errors = array() ) {
			$this->errors = $errors;
		}
	}
}

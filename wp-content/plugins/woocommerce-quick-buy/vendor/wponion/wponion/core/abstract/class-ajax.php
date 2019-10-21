<?php

namespace WPOnion\Bridge;

use Exception;
use WPOnion\Helper as Helper;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Bridge\Ajax' ) ) {
	/**
	 * Class Ajax
	 *
	 * @package WPOnion\Bridge
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	abstract class Ajax {
		/**
		 * @var bool
		 * @access
		 */
		protected $validate_module = true;

		/**
		 * @var bool
		 * @access
		 */
		protected $validate_field_path = true;

		/**
		 * Stores Module.
		 *
		 * @var bool
		 * @access
		 */
		protected $module = false;

		/**
		 * @var bool
		 * @access
		 */
		protected $add_assets = false;

		/**
		 * Ajax constructor.
		 */
		public function __construct() {

			if ( $this->validate_module ) {
				$this->validate( 'module', __( 'Unable To Find The Module', 'wponion' ), false, 'REQUEST' );
			}

			if ( $this->validate_field_path ) {
				$this->validate( 'field_path', __( 'Unable To Find The Field', 'wponion' ), false, 'REQUEST' );
			}
			$this->run();
			$this->json_error();
		}

		/**
		 * This function is used to run the ajax.
		 */
		abstract function run();

		/**
		 * Returns give key's value from $_GET
		 *
		 * @param string $key
		 * @param bool   $default
		 *
		 * @return bool|mixed
		 */
		protected function get( $key = '', $default = false ) {
			return $this->get_post_request( $key, $default, 'GET' );
		}

		/**
		 * Checks for the given key in the given method and returns it.
		 *
		 * @param string $key
		 * @param mixed  $default
		 * @param string $type
		 *
		 * @return mixed
		 */
		private function get_post_request( $key, $default = false, $type = 'GET' ) {
			$return = $default;
			if ( false !== $this->has( $key, $type ) ) {
				switch ( $type ) {
					case 'GET':
						$return = Helper::array_key_get( $key, $_GET, $default );
						break;
					case 'POST':
						$return = Helper::array_key_get( $key, $_POST, $default );
						break;
					case 'REQUEST':
						$return = Helper::array_key_get( $key, $_REQUEST, $default );
						break;
					default:
						$return = $default;
						break;
				}
			}
			return $return;
		}

		/**
		 * Checks if given key exists in given request global array ($_GET/$_POST/$_REQUEST)
		 *
		 * @param string $key
		 * @param string $type
		 *
		 * @return bool
		 */
		protected function has( $key = '', $type = 'GET' ) {
			switch ( $type ) {
				case 'GET':
					$has = Helper::array_key_isset( $key, $_GET );
					break;
				case 'POST':
					$has = Helper::array_key_isset( $key, $_POST );
					break;
				default:
					$has = Helper::array_key_isset( $key, $_REQUEST );
					break;
			}
			return $has;
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function has_get( $key = '' ) {
			return $this->has( $key, 'GET' );
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function has_post( $key = '' ) {
			return $this->has( $key, 'POST' );
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function has_request( $key = '' ) {
			return $this->has( $key, 'REQUEST' );
		}

		/**
		 * Returns give key's value from $_GET
		 *
		 * @param string $key
		 * @param bool   $default
		 *
		 * @return bool|mixed
		 */
		protected function post( $key = '', $default = false ) {
			return $this->get_post_request( $key, $default, 'POST' );
		}

		/**
		 * Returns give key's value from $_REQUEST
		 *
		 * @param string $key
		 * @param bool   $default
		 *
		 * @return bool|mixed
		 */
		protected function request( $key = '', $default = false ) {
			return $this->get_post_request( $key, $default, 'REQUEST' );
		}

		/**
		 * Renders WPO Core Data.
		 *
		 * @param array $data
		 *
		 * @return array
		 */
		protected function wpo_json_data( $data = array() ) {
			if ( ! is_array( $data ) ) {
				return $data;
			}
			return wp_parse_args( wponion_ajax_args( $this->add_assets ), $data );
		}

		/**
		 * @param mixed $data
		 * @param null  $status_code
		 */
		protected function json_error( $data = null, $status_code = null ) {
			do_action( 'wponion_ajax_shutdown_error' );
			do_action( 'wponion_ajax_shutdown' );
			wp_send_json_error( $this->wpo_json_data( $data ), $status_code );
		}

		/**
		 * @param mixed $data
		 * @param null  $status_code
		 */
		protected function json_success( $data = null, $status_code = null ) {
			do_action( 'wponion_ajax_shutdown_success' );
			do_action( 'wponion_ajax_shutdown' );
			wp_send_json_success( $this->wpo_json_data( $data ), $status_code );
		}

		/**
		 * @param bool|string $error_title
		 * @param bool|string $error_message
		 *
		 * @return array
		 */
		protected function error_message( $error_title = false, $error_message = false ) {
			return array(
				'title'   => $error_title,
				'message' => $error_message,
			);
		}

		/**
		 * @param bool|string $success_title
		 * @param bool|string $success_message
		 *
		 * @return array
		 */
		protected function success_message( $success_title = false, $success_message = false ) {
			return array(
				'title'   => $success_title,
				'message' => $success_message,
			);
		}

		/**
		 * @param string      $key
		 * @param string|bool $error_title
		 * @param string|bool $error_message
		 * @param string      $type
		 *
		 * @return bool|mixed
		 */
		protected function validate( $key, $error_title = false, $error_message = false, $type = 'GET' ) {
			if ( ( false === $key || false === $this->has( $key, $type ) ) || ( true === $this->has( $key, $type ) && empty( $this->get_post_request( $key, false, $type ) ) ) ) {
				$this->json_error( $this->error_message( $error_title, $error_message ) );
				return false;
			}
			return $this->get_post_request( $key, false, $type );
		}

		/**
		 * Sends WP Error.
		 *
		 * @param string|bool $error_title
		 * @param string|bool $error_message
		 * @param array       $args
		 */
		protected function error( $error_title = false, $error_message = false, $args = array() ) {
			$this->json_error( wp_parse_args( $args, $this->error_message( $error_title, $error_message ) ) );
		}

		/**
		 * @param bool|string $success_title
		 * @param bool|string $success_message
		 * @param array       $args
		 */
		protected function success( $success_title = false, $success_message = false, $args = array() ) {
			$this->json_success( wp_parse_args( $args, $this->success_message( $success_title, $success_message ) ) );
		}

		/**
		 * @param string      $key
		 * @param string|bool $error_title
		 * @param string|bool $error_message
		 *
		 * @return bool|mixed
		 */
		protected function validate_post( $key, $error_title = false, $error_message = false ) {
			return $this->validate( $key, $error_title, $error_message, 'POST' );
		}

		/**
		 * @param string      $key
		 * @param string|bool $error_title
		 * @param string|bool $error_message
		 *
		 * @return bool|mixed
		 */
		protected function validate_get( $key, $error_title = false, $error_message = false ) {
			return $this->validate( $key, $error_title, $error_message, 'GET' );
		}

		/**
		 * Returns Field Path.
		 *
		 * @param bool $base
		 *
		 * @return mixed|string
		 */
		protected function field_path( $base = false ) {
			$field_path = explode( '/', $this->post( 'field_path' ) );
			if ( true === $base ) {
				return array_shift( $field_path );
			}
			array_shift( $field_path );
			return implode( '/', $field_path );
		}

		/**
		 * Fetchs Builder Path.
		 *
		 * @param string|bool $type
		 *
		 * @return array|bool
		 */
		protected function builder_path( $type = false ) {
			$builder_path = explode( '/', $this->post( 'builder_path' ) );
			$builder_path = array_filter( array(
				'container_id'     => isset( $builder_path[0] ) ? $builder_path[0] : false,
				'sub_container_id' => isset( $builder_path[1] ) ? $builder_path[1] : false,
			) );
			if ( false === $type ) {
				return $builder_path;
			}
			return ( isset( $builder_path[ $type ] ) ) ? $builder_path[ $type ] : false;
		}

		/**
		 * Validates & Returns Proper Value function
		 *
		 * @param $module
		 *
		 * @return string
		 */
		protected function get_value_function_name( $module ) {
			$return = 'wpo_' . $module;
			switch ( $module ) {
				case 'taxonomy':
					$return = 'wpo_term_meta';
					break;
				case 'metabox':
				case 'nav_menu':
				case 'media_fields':
				case 'wc_product':
				case 'bulk_edit':
				case 'quick_edit':
					$return = 'wpo_post_meta';
					break;
				case 'user_profile':
					$return = 'wpo_user_meta';
					break;
				case 'customizer':
				case 'wc_settings':
				case 'dashboard_widgets':
				case 'widget':
					$return = 'wpo_settings';
					break;
			}
			return $return;
		}

		/**
		 * Fetches And Returns the module.
		 *
		 * @return bool|mixed|\WPOnion\Bridge\Module
		 */
		protected function get_module() {
			if ( false !== $this->module ) {
				return $this->module;
			}

			$module   = $this->post( 'module', false );
			$function = 'wponion_' . $module;

			if ( ! function_exists( $function ) || ! function_exists( $this->get_value_function_name( $module ) ) ) {
				$this->error( __( 'Module Not Found', 'wponion' ), __( 'Module Callback / Registry Function Not Found', 'wponion' ) );
			}

			try {
				$this->module = $function( $this->field_path( true ) );
				if ( ! is_object( $this->module ) ) {
					throw new Exception();
				}

				return $this->module;
			} catch ( Exception $exception ) {
				$this->module = false;
				$this->error( __( 'Module Instance Not Found', 'wponion' ), __( 'Module Callback / Registry Function Not Found', 'wponion' ) );
			}
			return false;
		}

		/**
		 * Fetchs The Field.
		 *
		 * @return array|bool|\WPO\Container|\WPO\Field
		 */
		protected function get_field() {
			$module = $this->get_module();
			if ( method_exists( $module, 'fields' ) ) {
				$fields = $module->fields();
				unset( $module );
				if ( method_exists( $fields, 'get' ) ) {
					$field = $fields->get( $this->field_path() );
					if ( ! empty( $field ) ) {
						return $field;
					};
					$this->error( __( 'Field Not Found', 'wponion' ) );
				}
				$this->error( __( 'Unable To Fetch The Field', 'wponion' ) );
			}
			$this->error( __( 'Unable To Find The Field', 'wponion' ) );
			return false;
		}

		/**
		 * Catches Ajax Localizer Output.
		 *
		 * @return array
		 */
		protected function localizer() {
			return wponion_localize()->as_array();
		}
	}
}

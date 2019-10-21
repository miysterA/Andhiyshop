<?php

namespace WPOnion;

use WPOnion\Registry\Field_Types;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Field' ) ) {
	/**
	 * Class Field
	 *
	 * @package WPOnion\Bridge
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	abstract class Field extends Bridge {

		/**
		 * Total Fields.
		 *
		 * @var int
		 */
		public static $total_fields = 0;

		/**
		 * orginal_field
		 *
		 * @var array
		 */
		protected $orginal_field = array();

		/**
		 * orginal_unique
		 *
		 * @var array
		 */
		protected $orginal_unique = array();

		/**
		 * orginal_value
		 *
		 * @var array
		 */
		protected $orginal_value = array();

		/**
		 * Database ID
		 *
		 * @var string
		 */
		protected $unique = '';

		/**
		 * Database ID
		 *
		 * @var string
		 */
		protected $base_unique = '';

		/**
		 * field
		 *
		 * @var array
		 */
		protected $field = array();

		/**
		 * value
		 *
		 * @var array|string
		 */
		protected $value = array();

		/**
		 * Stores Field Errors.
		 *
		 * @var null
		 */
		protected $errors = null;

		/**
		 * Stores Debug Data.
		 *
		 * @var array
		 */
		protected $debug_data = array();

		/**
		 * select_framework
		 *
		 * @var bool
		 */
		protected $select_framework = false;

		/**
		 * WPOnion_Field constructor.
		 *
		 * @param array        $field
		 * @param array        $value
		 * @param string|array $unique
		 */
		public function __construct( $field = array(), $value = array(), $unique = array() ) {
			self::$total_fields++;
			$this->orginal_field  = $field;
			$this->orginal_unique = $unique;
			$this->orginal_value  = $value;
			$this->field          = $this->_handle_field_args( $this->set_args( $field ) );
			$this->value          = $value;

			if ( ! wponion_is_array( $unique ) ) {
				$this->unique      = $unique;
				$this->base_unique = $unique;
				$this->module      = false;
			} else {
				$this->unique      = ( isset( $unique['unique'] ) ) ? $unique['unique'] : false;
				$this->base_unique = ( isset( $unique['base'] ) ) ? $unique['base'] : false;
				$this->module      = ( isset( $unique['module'] ) ) ? $unique['module'] : false;
			}

			$this->get_errors();

			$is_did_action = ( did_action( 'wponion_ajax_enqueue_scripts' ) || did_action( 'admin_enqueue_scripts' ) || did_action( 'customize_controls_enqueue_scripts' ) || did_action( 'wp_enqueue_scripts' ) || did_action( 'customize_controls_print_scripts' ) || did_action( 'customize_controls_print_footer_scripts' ) || did_action( 'customize_controls_print_styles' ) );

			if ( defined( 'WPONION_FIELD_ASSETS' ) && true === WPONION_FIELD_ASSETS || true === $is_did_action ) {
				$this->field_assets();
			} else {
				$this->add_action( 'admin_enqueue_scripts', 'field_assets', 1 );
				$this->add_action( 'customize_controls_enqueue_scripts', 'field_assets', 99999 );
				$this->add_action( 'wp_enqueue_scripts', 'field_assets', 1 );
				$this->add_action( 'wponion_ajax_enqueue_scripts', 'field_assets', 10 );
			}

			$this->init_subfields();
		}

		/**
		 * Handles Defaults Field Args.
		 *
		 * @param $data
		 *
		 * @return array
		 */
		public function _handle_field_args( $data ) {
			if ( isset( $data['class'] ) ) {
				$data['attributes']          = ( isset( $data['attributes'] ) ) ? $data['attributes'] : array();
				$data['attributes']['class'] = isset( $data['attributes']['class'] ) ? $data['attributes']['class'] : array();
				$data['attributes']['class'] = wponion_html_class( $data['attributes']['class'], $data['class'], false );
			}
			return $this->handle_field_args( $data );
		}

		/**
		 * This function Returns Global Default Field Args.
		 *
		 * @return array
		 */
		protected function defaults() {
			return $this->parse_args( $this->field_default(), wponion_field_defaults() );
		}

		/**
		 * Check / Returns an element from $this->field.
		 *
		 * @param string $key
		 * @param null   $value
		 *
		 * @return bool|mixed
		 */
		public function data( $key = '', $value = null ) {
			if ( isset( $this->field[ $key ] ) ) {
				if ( null === $value ) {
					return $this->field[ $key ];
				} else {
					return ( $value === $this->field[ $key ] ) ? true : false;
				}
			}
			return false;
		}

		/**
		 * Generates Final HTML output of the current field.
		 */
		public function final_output() {
			$only_field = ( $this->has( 'only_field' ) && true === $this->data( 'only_field' ) ) ? true : false;
			if ( false !== $this->data( 'before_render' ) && wponion_is_callable( $this->data( 'before_render' ) ) ) {
				wponion_callback( $this->data( 'before_render' ), array( &$this, $only_field ) );
			}

			if ( $only_field ) {
				$this->output();
			} else {
				$this->wrapper();
			}

			$this->debug( __( 'Raw Field Args', 'wponion' ), $this->orginal_field );
			$this->debug( __( 'Field Args', 'wponion' ), $this->field );
			$this->debug( __( 'Field Value', 'wponion' ), $this->value );
			$this->debug( __( 'Unique', 'wponion' ), $this->unique() );
			$this->debug( __( 'Module', 'wponion' ), $this->module() );
			$this->wp_pointer();
			$this->localize_field();

			if ( false !== $this->data( 'after_render' ) && wponion_is_callable( $this->data( 'after_render' ) ) ) {
				wponion_callback( $this->data( 'after_render' ), array( &$this, $this->js_field_id(), $only_field ) );
			}
		}

		/**
		 * Checks If current elements key exists.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function has( $key = '' ) {
			return ( false === $this->data( $key ) || null === $this->data( $key ) || empty( $this->data( $key ) ) ) ? false : true;
		}

		/**
		 * Returns Default HTML Class.
		 *
		 * @param array $extra_class
		 *
		 * @return array
		 */
		protected function default_wrap_class( $extra_class = array() ) {
			$type      = $this->data( 'type' );
			$is_nested = ( isset( $this->field['fields'] ) && ! empty( $this->field['fields'] ) ) ? true : false;
			$has_error = ( $this->has_errors() ) ? ' wponion-element-has-error ' : '';
			return wponion_html_class( array(
				'wponion-element',
				'wponion-element-' . $type,
				'wponion-field-' . $type,
				'wponion-element-type-' . $type,
				'wponion-field-type-' . $type,
				( $is_nested ) ? 'wponion-has-nested-fields' : '',
				$has_error,
			), $extra_class, false );
		}

		/**
		 * Handles Field Dependency
		 */
		protected function handle_dependency() {
			$dependency = $this->data( 'dependency' );
			$save       = array();
			if ( wponion_is_array( $dependency ) && ! empty( array_filter( $dependency ) ) ) {
				foreach ( $dependency as $dep ) {
					$parent = false;
					if ( 0 === strpos( $dep['controller'], '.' ) || 0 === strpos( $dep['controller'], '<' ) ) {
						$dep['controller'] = trim( trim( $dep['controller'], '.' ), '<' );
						$parent            = true;
					} elseif ( ! empty( $this->data( 'sub' ) ) ) {
						$dep['controller'] = $this->data( 'sub' ) . '_' . $dep['controller'];
						$parent            = false;
					}
					$dep['parent'] = $parent;
					$save[]        = $dep;
				}
				if ( ! empty( $save ) ) {
					wponion_localize()->add( $this->js_field_id(), array( 'dependency' => $save ), true, false );
				}
			}
		}

		/**
		 * Generates Elements Wrapper.
		 */
		protected function wrapper() {
			if ( wponion_is_debug() ) {
				wponion_timer( $this->unique() );
			}
			$_wrap_attr                            = $this->data( 'wrap_attributes' );
			$has_title                             = ( false === $this->has( 'title' ) ) ? 'wponion-element-no-title wponion-field-no-title' : '';
			$is_pseudo                             = ( true === $this->data( 'pseudo' ) ) ? ' wponion-pseudo-field ' : '';
			$has_dep                               = false;
			$is_debug                              = ( $this->has( 'debug' ) ) ? 'wponion-field-debug' : '';
			$is_js_validate                        = ( $this->has( 'js_validate' ) ) ? 'wponion-js-validate' : '';
			$_wrap_attr['data-wponion-jsid']       = $this->js_field_id();
			$_wrap_attr['data-wponion-field-type'] = $this->data( 'type' );
			if ( $this->has( 'dependency' ) ) {
				$has_dep = 'wponion-has-dependency';
				$this->handle_dependency();
			}

			$_wrap_attr['class'] = wponion_html_class( $this->data( 'wrap_class' ), $this->default_wrap_class( array(
				$is_pseudo,
				$has_title,
				$has_dep,
				$is_debug,
				$is_js_validate,
				( false !== $this->data( 'badge' ) ) ? 'wponion-has-badge' : '',
				wponion_html_class( $this->field_wrap_class() ),
			) ) );

			if ( false === self::has_column_css( $_wrap_attr['class'] ) ) {
				$_wrap_attr['class'] .= ' col-xs-12 ';
			}

			if ( $this->has( 'horizontal' ) && true === $this->data( 'horizontal' ) ) {
				$_wrap_attr['class'] .= ' horizontal ';
			}

			if ( Field_Types::design_exists( $this->element_type() ) ) {
				$_wrap_attr['class'] .= ' ui-field wponion-ui-field ';
			}

			if ( false !== $this->data( 'wrap_tooltip' ) ) {
				$_wrap_attr['class'] = $_wrap_attr['class'] . ' wponion-has-wrap-tooltip wponion-wrap-tooltip';
				$tooltip             = $this->tooltip_data( $this->data( 'wrap_tooltip' ), array(), 'wrap_tooltip' );
				$_wrap_attr          = $this->parse_args( $tooltip['attr'], $_wrap_attr );
			}

			$_wrap_attr['id'] = $this->wrap_id();
			$_wrap_attr       = wponion_array_to_html_attributes( $_wrap_attr );
			$this->_handle_column_data();

			echo '<div ' . $_wrap_attr . '>';
			echo $this->badge();
			echo '<div class="row">';
			echo $this->title();
			echo $this->field_wrapper( true );
			echo $this->output();
			echo $this->field_wrapper( false ) . '<div class="clear"></div>';
			if ( $this->has( 'debug' ) ) {
				echo '<div class="wponion-developer-timer">' . __( 'Field Rendered In', 'wponion' ) . ' ' . wponion_timer( $this->unique(), true ) . ' ' . __( 'Seconds', 'wponion' ) . '</div>';
			}
			echo '</div></div>';
		}

		/**
		 * Generates Badge HTML.
		 *
		 * @return string
		 */
		protected function badge() {
			$html = '';
			if ( false !== $this->data( 'badge' ) ) {
				$badges = $this->data( 'badge' );
				if ( is_string( $badges ) || is_array( $badges ) && ! isset( $badges[0] ) ) {
					$badges = array( $badges );
				}
				foreach ( $badges as $badge ) {
					$badge = $this->handle_args( 'content', $badge, array(
						'type'      => 'success',
						'placement' => 'top-left',
						'content'   => null,
						'pointer'   => false,
						'outline'   => false,
					) );

					$container = 'wpo-badge-container wpo-badge-' . $badge['placement'] . ' wpo-badge-type-' . $badge['type'];
					$class     = 'wpo-badge';
					$class     .= ( true === $badge['outline'] ) ? ' wpo-badge-outline wpo-badge-outline-' . $badge['type'] . ' ' : ' wpo-badge-' . $badge['type'] . ' ';
					$class     .= ( true === $badge['pointer'] ) ? ' wpo-badge-pill ' : '';
					$html      .= '<div class="' . $container . '"><div class="' . $class . '" >' . $badge['content'] . '</div></div>';
				}
			}
			return $html;
		}

		/**
		 * Validates if Current Element Has Column Wrap Class.
		 *
		 * @param $class
		 *
		 * @static
		 * @return bool
		 */
		public static function has_column_css( $class ) {
			preg_match_all( '/col\b-(xs|sm|md|lg|xl)?\b-?\b(1[0-2]|[1-9])/', $class, $matches, PREG_SET_ORDER, 0 );
			return ( empty( $matches ) ) ? false : $matches;
		}

		/**
		 * Returns A Valid Wrap ID.
		 *
		 * @return string
		 */
		private function wrap_id() {
			$attrs = $this->data( 'wrap_attributes' );
			if ( isset( $attrs['id'] ) && ! empty( $attrs['id'] ) ) {
				return $attrs['id'];
			} elseif ( ! empty( $this->data( 'wrap_id' ) ) ) {
				return $this->data( 'wrap_id' );
			}
			return $this->js_field_id();
		}

		/**
		 * Stores Debug Info.
		 *
		 * @param string      $key
		 * @param array|mixed $data
		 *
		 * @return array|bool
		 */
		protected function debug( $key = '', $data = array() ) {
			if ( true === $key ) {
				return $this->debug_data;
			} elseif ( $this->has( 'debug' ) && false === isset( $this->debug_data[ $key ] ) ) {
				$this->debug_data[ $key ] = $data;
			}
			return false;
		}

		/**
		 * Renders Field Wrapper.
		 *
		 * @param bool $is_start
		 *
		 * @return string
		 */
		protected function field_wrapper( $is_start = true ) {
			if ( true === $is_start ) {
				$wrap_class = ( ! $this->has( 'title' ) ) ? ' wponion-fieldset wponion-fieldset-notitle ' : ' wponion-fieldset ';
				return '<div class="' . $wrap_class . ' ' . $this->data( 'fieldset_column' ) . '">';
			}
			return '</div>';
		}

		/**
		 * Renders Element Title HTML.
		 *
		 * @return string
		 */
		protected function title() {
			$html = '';
			if ( $this->has( 'title' ) && false === $this->data( 'hide_title' ) ) {
				$html .= '<div class="wponion-field-title wponion-element-title ' . $this->data( 'title_column' ) . '">';
				$html .= $this->title_before_after( false ) . '<h4>' . $this->data( 'title' ) . '</h4>' . $this->title_before_after( true );
				$html .= $this->field_help();
				$html .= $this->title_desc();
				$html .= '</div>';
			}
			return $html;
		}

		/**
		 * Returns Default Column CSS Class based on the modules or it returns defaults.
		 *
		 * @return array
		 * @uses \apply_filters('wponion_field_column_css_class')
		 */
		protected function get_default_column_class() {
			$return             = array();
			$return['title']    = 'col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2';
			$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10';
			switch ( $this->module() ) {
				case 'taxonomy':
					$return['title']    = 'col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3';
					$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xl-9';
					break;
				case 'metabox':
					$screen = get_current_screen();
					if ( $screen ) {
						if ( in_array( $screen->base, array( 'term', 'edit-tags' ), true ) ) {
							$return['title']    = 'col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4';
							$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-8 col-lg-8 col-xl-8';
						}
					}
					break;
				case 'wp_importer':
					$return['title']    = 'col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-3';
					$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-8 col-lg-8 col-xl-9';
					break;
				default:
					$return['title']    = 'col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2';
					$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10';
					break;
			}
			if ( false === $this->has( 'title' ) || true === $this->data( 'hide_title' ) ) {
				$return['fieldset'] = 'col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12';
			}

			return apply_filters( 'wponion_field_column_css_class', $return, $this->module(), $this );
		}

		/**
		 * Validates Column Data.
		 */
		protected function _handle_column_data() {
			$defaults = $this->get_default_column_class();
			$title    = $this->data( 'title_column' );
			$fieldset = $this->data( 'fieldset_column' );
			if ( false === $title && false === $fieldset ) {
				$this->field['title_column']    = $defaults['title'];
				$this->field['fieldset_column'] = $defaults['fieldset'];
			} else {
				if ( false !== $title && false === $fieldset ) {
					$matches   = self::has_column_css( $title );
					$new_class = wponion_get_possible_column_class( $matches );
					if ( ! empty( $new_class ) ) {
						$this->field['fieldset_column'] = $new_class;
					}
				}

				if ( false === $title && false !== $fieldset ) {
					$matches   = self::has_column_css( $fieldset );
					$new_class = wponion_get_possible_column_class( $matches );
					if ( ! empty( $new_class ) ) {
						$this->field['title_column'] = $new_class;
					}
				}
			}
		}

		/**
		 * Renders HTML for ToolTip.
		 *
		 * @return string
		 */
		protected function field_help() {
			$html = '';
			if ( $this->has( 'help' ) ) {
				$data                              = $this->tooltip_data( $this->data( 'help' ), array( 'icon' => ' wpoic-help-circle' ) );
				$data['attr']['data-wponion-jsid'] = $this->js_field_id();
				$span_attr                         = wponion_array_to_html_attributes( $data['attr'] );
				$html                              = '<span ' . $span_attr . '><span class="' . $data['data']['icon'] . '"></span></span>';
			}
			return $html;
		}

		/**
		 * @param array $main_data
		 * @param array $extra_args
		 * @param bool  $localize
		 *
		 * @return array
		 */
		protected function tooltip_data( $main_data = array(), $extra_args = array(), $localize = true ) {
			$data = $this->handle_data( $main_data, $this->parse_args( $extra_args, array(
				'content'     => false,
				'js_field_id' => $this->js_field_id(),
			) ), 'content' );
			return wponion_tooltip( $data, false, false, $localize );
		}

		/**
		 * Returns A Unique ID
		 *
		 * @return string
		 */
		protected function unid() {
			return $this->module() . '_' . $this->field_id();
		}

		/**
		 * Checks and returns title_before and title_after key & values.
		 *
		 * @param bool $is_after
		 *
		 * @return string
		 */
		protected function title_before_after( $is_after = false ) {
			if ( false === $is_after && false !== $this->has( 'title_before' ) ) {
				return $this->data( 'title_after' );
			} elseif ( true === $is_after && false !== $this->has( 'title_after' ) ) {
				return $this->data( 'title_after' );
			}
			return '';
		}

		/**
		 * Generates Multiple Description.
		 *
		 * @param $desc
		 * @param $css_class
		 *
		 * @return string
		 */
		protected function description_render( $desc, $css_class ) {
			$desc   = ( ! is_array( $desc ) ) ? array( $desc ) : $desc;
			$return = '';
			foreach ( array_filter( $desc ) as $c ) {
				$return .= '<p class="wponion-desc ' . $css_class . '">' . wponion_markdown()->line( $c ) . '</p>';
			}
			return $return;
		}

		/**
		 * Generates Title Description HTML.
		 *
		 * @return string
		 */
		protected function title_desc() {
			return ( $this->has( 'desc' ) ) ? $this->description_render( $this->data( 'desc' ), 'wponion-title-desc' ) : '';
		}

		/**
		 * Generates Field Description HTML.
		 *
		 * @return string
		 */
		protected function field_desc() {
			return ( $this->has( 'desc_field' ) ) ? $this->description_render( $this->data( 'desc_field' ), 'wponion-field-desc' ) : '';
		}

		/**
		 * Returns Element Before Data.
		 *
		 * @return bool|mixed|string
		 */
		protected function before() {
			return ( false !== $this->has( 'before' ) && false === $this->has( 'only_field' ) ) ? wponion_markdown()->line( $this->data( 'before' ) ) : '';
		}

		/**
		 * Returns Elements After Data.
		 *
		 * @return string
		 */
		protected function after() {
			if ( false === $this->has( 'only_field' ) ) {
				$data = ( false !== $this->has( 'after' ) ) ? wponion_markdown()->line( $this->data( 'after' ) ) : '';
				$data = $data . $this->field_desc();
				$data = $data . $this->field_error();
				return $data;
			}
			return '';
		}

		/**
		 * Returns Field Errors.
		 *
		 * @return array|false
		 */
		protected function get_errors() {
			if ( null === $this->errors ) {
				$this->errors   = false;
				$error_instance = wponion_registry( sanitize_title( $this->module() . '_' . $this->base_unique() . '_errors' ), '\WPOnion\Registry\Field_Error' );
				if ( $error_instance ) {
					$id           = str_replace( array( '[', ']' ), array( '/', '' ), $this->name() );
					$this->errors = $error_instance->get( $id );
					$this->debug( __( 'Field Errors', 'wponion' ), $this->errors );
				}
			}
			return $this->errors;
		}

		/**
		 * Returns True if has errors.
		 *
		 * @return bool
		 */
		protected function has_errors() {
			return ( wponion_is_array( $this->get_errors() ) );
		}

		/**
		 * Renders Field Errors.
		 *
		 * @return string
		 */
		protected function field_error() {
			$errors = $this->get_errors();
			if ( false !== $errors && isset( $errors['message'] ) ) {
				$html = '<div class="wponion-field-errors invalid-feedback"><ul>';
				foreach ( $errors['message'] as $message ) {
					$html .= '<li>' . $message . '</li>';
				}
				$html .= '</ul></div>';
				return $html;
			}
			return '';
		}

		/**
		 * Returns Element Type.
		 *
		 * @return bool|mixed
		 */
		protected function element_type() {
			if ( false !== $this->has( 'text_type' ) ) {
				return $this->data( 'text_type' );
			} elseif ( false !== $this->has( 'attributes' ) ) {
				$data = $this->data( 'attributes' );
				if ( isset( $data['type'] ) ) {
					return $data['type'];
				}
			}
			return $this->data( 'type' );
		}

		/**
		 * Returns Fields ID.
		 *
		 * @return bool|mixed
		 */
		protected function field_id() {
			return ( false !== $this->has( 'id' ) ) ? $this->data( 'id' ) : false;
		}

		/**
		 * Generates Field Attributes HTML.
		 *
		 * @param array $field_attributes
		 *
		 * @return string
		 */
		protected function attributes( $field_attributes = array() ) {
			$user_attrs = ( false !== $this->has( 'attributes' ) ) ? $this->data( 'attributes' ) : array();

			if ( false !== $this->has( 'style' ) ) {
				$user_attrs['style'] = $this->data( 'style' );
			}

			if ( true === $this->has( 'disabled' ) ) {
				$user_attrs['disabled'] = 'disabled';
			}

			if ( false !== $this->has( 'placeholder' ) ) {
				$user_attrs['placeholder'] = $this->data( 'placeholder' );
			}

			$user_attrs['data-depend-id'] = $this->depend_id();
			$user_attrs                   = $this->parse_args( $user_attrs, $field_attributes );
			$user_attrs['class']          = wponion_html_class( $user_attrs['class'], isset( $field_attributes['class'] ) ? $field_attributes['class'] : array() );

			if ( ! isset( $user_attrs['data-wponion-jsid'] ) ) {
				$user_attrs['data-wponion-jsid'] = $this->js_field_id();
			}

			if ( ! isset( $user_attrs['data-depend-id'] ) ) {
				$user_attrs['data-depend-id'] = $this->field_id();
			}

			return wponion_array_to_html_attributes( $user_attrs );
		}

		/**
		 * Returns An Actual Depend ID
		 *
		 * @return bool|mixed|string
		 */
		protected function depend_id() {
			$key = ( ! empty( $this->field_id() ) ) ? $this->field_id() : $this->js_field_id();
			$key = ( ! empty( $this->data( 'sub' ) ) ) ? $this->data( 'sub' ) . '_' . $key : $key;
			return ( empty( $key ) ) ? $this->field_id() : $key;
		}

		/**
		 * Returns Fields Class.
		 *
		 * @param string $field_class
		 *
		 * @return string
		 */
		protected function element_class( $field_class = '' ) {
			return apply_filters( 'wponion_' . $this->module() . '_field_html_class', wponion_html_class( $this->data( 'class' ), $field_class, false ) );
		}

		/**
		 * Returns Current Elements Value.
		 *
		 * @param string $key
		 *
		 * @return array|string|mixed
		 */
		protected function value( $key = '' ) {
			return ( ! empty( $key ) ) ? $this->get_value( $key ) : $this->value;
		}

		/**
		 * Checks if array key exists in $this->value
		 *
		 * @param string $key
		 *
		 * @return bool|mixed
		 */
		protected function get_value( $key = '' ) {
			return ( isset( $this->value[ $key ] ) ) ? $this->value[ $key ] : false;
		}

		/**
		 * Returns Elements Name.
		 *
		 * @param string $extra_name
		 *
		 * @return string
		 */
		protected function raw_name( $extra_name = '' ) {
			if ( false !== $this->has( 'name' ) ) {
				return $this->data( 'name' ) . $extra_name;
			} elseif ( false !== $this->has( 'un_array' ) && true === $this->data( 'un_array' ) ) {
				return implode( '/', array_filter( array( $this->unique(), $extra_name ) ) );
			} else {
				return implode( '/', array_filter( array( $this->unique(), $this->field_id(), $extra_name ) ) );
			}
		}

		/**
		 * @param string|bool $extra_name
		 *
		 * @return string
		 */
		public function name( $extra_name = '' ) {
			if ( isset( $this->field['fields'] ) && ! empty( $this->field['fields'] ) || method_exists( $this->field, 'containers' ) ) {
				return $this->raw_name( $extra_name );
			}
			return wponion_get_field_unique_html( $this->raw_name( $extra_name ) );
		}

		/**
		 * @param string $extra
		 * @param bool   $unique
		 *
		 * @return string
		 */
		public function unique( $extra = '', $unique = false ) {
			$unique = ( false === $unique ) ? $this->unique : $unique;
			return ( ! empty( $extra ) ) ? $unique . '/' . $extra : $unique;
		}

		/**
		 * Returns Base Unqiue Matchs.
		 *
		 * @return mixed
		 */
		public function base_unique() {
			return $this->base_unique;
		}

		/**
		 * Generates A New JS Field ID.
		 */
		protected function js_field_id() {
			if ( ! isset( $this->js_field_id ) ) {
				$key               = wponion_js_obj_name( 'wponion', 'field', $this->unid() . '_' . $this->unique() . '_' . uniqid( time() ) );
				$this->js_field_id = sanitize_key( str_replace( array( '-', '_' ), '', $key ) );
			}
			return $this->js_field_id;
		}

		/**
		 * Handles JS Values For A Element.
		 *
		 * @param null $data
		 * @param bool $js_convert
		 */
		public function localize_field( $data = null, $js_convert = true ) {
			if ( null === $data ) {
				$data = $this->js_field_args();
				if ( ! empty( $data ) ) {
					wponion_localize()->add( $this->js_field_id(), $data );
				}

				if ( $this->has( 'debug' ) ) {
					wponion_localize()->add( $this->js_field_id(), array( 'debug_info' => $this->debug( true ) ), true, false );
				}

				if ( $this->has( 'js_validate' ) ) {
					wponion_localize()->add( $this->js_field_id(), array( 'js_validate' => $this->data( 'js_validate' ) ) );
				}

				$path     = explode( '/', $this->unique( $this->field_id() ) );
				$new_path = array();
				if ( ! empty( $path ) ) {
					$current = current( $path );
					foreach ( $path as $id ) {
						$new_path[] = $id;
						if ( $current === $id ) {
							$new_path[] = $this->data( 'builder_path' );
						}
					}
				}

				$data       = array(
					'field_id'     => $this->field_id(),
					'module'       => $this->module(),
					'unique'       => $this->unique(),
					'field_path'   => implode( '/', array_filter( $new_path ) ),
					'builder_path' => $this->data( 'builder_path' ),
				);
				$js_convert = false;
			}

			wponion_localize()->add( $this->js_field_id(), $data, true, $js_convert );
		}

		/**
		 * Returns A Valid WP Pointer Instance.
		 *
		 * @param bool $pointer_id
		 *
		 * @return \WPOnion\Modules\Util\WP_Pointers
		 */
		private function wp_pointer_instance( $pointer_id = false ) {
			$pointer_id = ( empty( $pointer_id ) ) ? sanitize_title( $this->unique() ) : $pointer_id;
			$instance   = wponion_wp_pointers( $pointer_id );
			return ( false === $instance ) ? $this->wp_pointer_instance( false ) : $instance;
		}

		/**
		 * Handles WP Pointer.
		 */
		private function wp_pointer() {
			$pointer = $this->data( 'wp_pointer' );

			if ( is_string( $pointer ) ) {
				$this->wp_pointer_instance()
					->add( '#' . $this->wrap_id(), $pointer, array(
						'align' => 'right',
						'edge'  => 'right',
					) );
			} elseif ( wponion_is_array( $pointer ) ) {
				if ( isset( $pointer[0] ) ) {
					$title       = false;
					$instance_id = false;
					$text        = false;
					if ( 1 === count( $pointer ) ) {
						$title = $pointer;
					} elseif ( 2 === count( $pointer ) ) {
						$title = $pointer[0];
						$text  = $pointer[1];
					} elseif ( 3 === count( $pointer ) ) {
						$instance_id = $pointer[0];
						$title       = $pointer[1];
						$text        = $pointer[2];
					}

					$this->wp_pointer_instance( $instance_id )
						->add( '#' . $this->wrap_id(), $title, $text, array(
							'align' => 'right',
							'edge'  => 'right',
						) );
				} elseif ( isset( $pointer['title'] ) || isset( $pointer['pointer_id'] ) || isset( $pointer['id'] ) ) {
					$pointer_id = false;

					if ( isset( $pointer['pointer_id'] ) ) {
						$pointer_id = $pointer['pointer_id'];
					} elseif ( isset( $pointer['id'] ) ) {
						$pointer_id = $pointer['id'];
					}

					unset( $pointer['id'] );
					unset( $pointer['pointer_id'] );
					$this->wp_pointer_instance( $pointer_id )
						->add( '#' . $this->wrap_id() . ' > .wponion-field-title', $pointer );
				}
			}
		}

		/**
		 * @param       $key
		 * @param       $value
		 * @param array $defaults
		 * @param array $force_defaults
		 *
		 * @return array
		 */
		protected function handle_args( $key, $value, $defaults = array(), $force_defaults = array() ) {
			return wponion_handle_string_args_with_defaults( $key, $value, $defaults, $force_defaults );
		}

		/**
		 * Handles Options Value.
		 *
		 * @param       $key
		 * @param       $value
		 * @param array $more_defaults
		 *
		 * @return array
		 */
		protected function handle_options( $key, $value, $more_defaults = array() ) {
			$defaults = $this->set_args( $more_defaults, array(
				'label'        => '',
				'key'          => '',
				'attributes'   => array(),
				'disabled'     => false,
				'tooltip'      => false,
				'custom_input' => false,
			) );

			if ( ! wponion_is_array( $value ) ) {
				$defaults['key']   = $key;
				$defaults['label'] = $value;
				$value             = $defaults;
			} else {
				$value = $this->parse_args( $value, $defaults );
				if ( false !== $value['tooltip'] ) {
					$value['tooltip'] = ( true === $value['tooltip'] ) ? $value['label'] : $value['tooltip'];
					$value['tooltip'] = $this->tooltip_data( $value['tooltip'], array( 'placement' => 'right' ), false );
				}

				if ( true === $value['disabled'] ) {
					$value['attributes']['disabled'] = 'disabled';
				}

				if ( '' === $value['key'] ) {
					$value['key'] = $key;
				}
			}

			if ( true === $value['label'] ) {
				if ( false === $value['custom_input'] ) {
					$value['custom_input'] = true;
				}
			}

			return $value;
		}

		/**
		 * Handles Fields Sub Field instances.
		 *
		 * @param      $field
		 * @param      $value
		 * @param      $unqiue
		 * @param bool $is_init
		 *
		 * @return mixed
		 * @uses wponion_add_element|wponion_field
		 *
		 */
		protected function sub_field( $field, $value, $unqiue, $is_init = false ) {
			$func                  = ( false === $is_init ) ? 'wponion_add_element' : 'wponion_field';
			$field['sub']          = $this->field_id();
			$field['builder_path'] = $this->data( 'builder_path' );
			$_instance             = $func( $field, $value, array(
				'unique' => $unqiue,
				'base'   => $this->base_unique,
				'module' => $this->module(),
			) );

			if ( true === $is_init && ( ! isset( $field['__no_instance'] ) || isset( $field['__no_instance'] ) && false === $field['__no_instance'] ) ) {
				$field['__instance'] = $_instance;
				return $field;
			}
			return $_instance;
		}

		/**
		 * Check if value is === to given value and returns an html output.
		 *
		 * @param string|array $helper
		 * @param string       $current
		 * @param string       $type
		 * @param bool         $echo
		 *
		 * @return string
		 */
		public function checked( $helper = '', $current = '', $type = 'checked', $echo = false ) {
			if ( wponion_is_array( $helper ) && in_array( $current, $helper, false ) ) {
				$result = ' ' . $type . '="' . $type . '"';
			} elseif ( wponion_validate_bool_val( $helper ) === wponion_validate_bool_val( $current ) ) {
				$result = ' ' . $type . '="' . $type . '"';
			} else {
				$result = '';
			}
			if ( $echo ) {
				echo $result;
			}
			return $result;
		}

		/**
		 * @param string $type
		 *
		 * @return array
		 */
		public function element_data( $type = '' ) {
			$is_ajax    = ( isset( $this->field['ajax'] ) && true === $this->field['ajax'] );
			$query_args = array();

			if ( $is_ajax && empty( $this->value ) ) {
				return array();
			}

			if ( isset( $this->field['query_args'] ) && wponion_is_array( $this->field['query_args'] ) && ! empty( $this->field['query_args'] ) ) {
				$query_args = $this->field['query_args'];
			}

			if ( $is_ajax ) {
				$query_args['post__in'] = ( ! wponion_is_array( $this->value ) ) ? explode( ',', $this->value ) : $this->value;
			}

			$data = wponion_query()->query( $type, $query_args, '' );
			return ( ! is_array( $data ) ) ? array() : $data;
		}

		/**
		 * @param bool $module_instance
		 *
		 * @return bool|string|\WPOnion\Bridge\Module
		 */
		public function module( $module_instance = false ) {
			if ( true === $module_instance ) {
				$module   = parent::module();
				$function = 'wponion_' . $module;
				return wponion_callback( $function, array( $this->base_unique() ) );
			}
			return parent::module();
		}

		/**
		 * This function is used to set any args that requires in javascript for the current field.
		 *
		 * @return array
		 */
		protected function js_field_args() {
			return array();
		}

		/**
		 * Fired after __constructor fired for the current plugin to handle subplugins.
		 */
		protected function init_subfields() {
		}

		/**
		 * This function is called after array merge with default is done.
		 *
		 * @param array $data
		 *
		 * @return array
		 */
		public function handle_field_args( $data = array() ) {
			return $data;
		}

		/**
		 * Custom Hookable Function to provide custom wrap class.
		 */
		protected function field_wrap_class() {
			return '';
		}

		/**
		 * Function Required To Register / Load current field's assets.
		 *
		 * @return mixed
		 */
		abstract public function field_assets();

		/**
		 * Custom Function To Return Current Fields Default Args.
		 *
		 * @return mixed
		 */
		abstract protected function field_default();

		/**
		 * Function Where all field can output their html.
		 *
		 * @return mixed
		 */
		abstract protected function output();
	}
}

<?php

namespace WPOnion\Field;

use WPOnion\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! class_exists( '\WPOnion\Field\Select' ) ) {
	/**
	 * Class Select
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Select extends Field {
		/**
		 * select_framework
		 *
		 * @var null
		 */
		protected $select_framework = null;

		/**
		 * Final HTML Output
		 */
		protected function output() {
			echo $this->before();
			$options = $this->data( 'options' );

			if ( isset( $this->field['ajax'] ) && true === $this->field['ajax'] && ! empty( $this->value ) && wponion_is_callable( $options ) ) {
				$options = wponion_callback( $options, array( $this ) );
			} elseif ( ( ! isset( $this->field['ajax'] ) || isset( $this->field['ajax'] ) && false === $this->field['ajax'] ) && wponion_is_callable( $options ) ) {
				$options = wponion_callback( $options, array( $this ) );
			}

			$options = ( wponion_is_array( $options ) ) ? $options : array_filter( $this->element_data( $options ) );
			$attr    = $this->attributes( array(
				'name'  => ( true === $this->has( 'multiple' ) ) ? $this->name( '[]' ) : $this->name(),
				'class' => array( 'form-control' ),
			) );

			$element = '<select ' . $attr . '>';
			if ( $this->has( 'options_html' ) && ! empty( $this->data( 'options_html' ) ) ) {
				$element .= $this->data( 'options_html' );
			} else {
				if ( true === $this->data( 'empty_option' ) ) {
					$element .= '<option value=""></option>';
				}

				foreach ( $options as $key => $option ) {
					if ( wponion_is_array( $option ) && isset( $option['label'] ) ) {
						$element .= $this->sel_option( $this->handle_options( $key, $option ) );
					} elseif ( wponion_is_array( $option ) && ! isset( $option['label'] ) ) {
						$element .= '<optgroup label="' . $key . '">';
						foreach ( $option as $k => $v ) {
							$element .= $this->sel_option( $this->handle_options( $k, $v ) );
						}
						$element .= '</optgroup>';
					} else {
						$element .= $this->sel_option( $this->handle_options( $key, $option ) );
					}
				}
			}
			$element .= '</select>';

			echo wponion_input_group_html( $this->data( 'prefix' ), $this->data( 'surfix' ), $element );

			if ( false === $this->select_framework && true === $this->data( 'ajax' ) ) {
				echo wponion_add_element( array(
					'type'    => 'wp_notice_error',
					'before'  => '<br/>',
					'large'   => true,
					'alt'     => true,
					'content' => __( 'Ajax Search Will Not Work In Select Field If Not Javascript Select Framework Used Such As <code>Select2</code> / <code>Chosen</code> / <code>Selectize</code>', 'wponion' ),
				) );
			}

			echo $this->after();
		}

		/**
		 * Handles Option array.
		 *
		 * @param $data
		 *
		 * @return string
		 */
		protected function sel_option( $data ) {
			$elem_id = sanitize_title( $this->name() . '_' . $data['key'] );
			if ( isset( $data['tooltip'] ) && wponion_is_array( $data['tooltip'] ) ) {
				$data['attributes']['title']             = $data['tooltip']['attr']['title'];
				$data['attributes']['data-wponion-jsid'] = $this->js_field_id();
				$data['attributes']['data-field-jsid']   = $elem_id;
				$data['attributes']['class']             = ' wponion-field-tooltip ';
				wponion_localize()->add( $this->js_field_id(), array( $elem_id . 'tooltip' => $data['tooltip']['data'] ) );
			}

			$data['attributes']['value'] = $data['key'];
			return '<option ' . wponion_array_to_html_attributes( $data['attributes'] ) . $this->checked( $this->value(), $data['key'], 'selected' ) . ' > ' . $data['label'] . ' </option > ';
		}

		/**
		 * checks and updated fields args based on field config.
		 *
		 * @param array $data
		 *
		 * @return array
		 */
		public function handle_field_args( $data = array() ) {
			if ( true === $data['multiple'] ) {
				$data['attributes']['multiple'] = 'multiple';
			}

			if ( ! isset( $data['attributes']['class'] ) ) {
				$data['attributes']['class'] = array();
			}

			$this->select_framework      = wponion_validate_select_framework( $data );
			$select_class                = wponion_select_classes( $this->select_framework );
			$data['attributes']['class'] = wponion_html_class( $data['attributes']['class'], $select_class, false );
			wponion_localize()->add( $this->js_field_id(), array(
				$this->select_framework => ( isset( $data[ $this->select_framework ] ) && wponion_is_array( $data[ $this->select_framework ] ) ) ? $data[ $this->select_framework ] : array(),
			) );
			return $data;
		}

		/**
		 * Returns all fields default.
		 *
		 * @return array|mixed
		 */
		protected function field_default() {
			return array(
				'options'      => array(),
				'multiple'     => false,
				'empty_option' => false,
				'ajax'         => false,
				'prefix'       => '',
				'surfix'       => '',
			);
		}

		/**
		 * @return array
		 */
		protected function js_field_args() {
			return array(
				'ajax' => ( true === $this->data( 'ajax' ) ) ? array(
					'query_args'  => ( is_array( $this->data( 'query_args' ) ) && ! empty( $this->data( 'query_args' ) ) ) ? $this->data( 'query_args' ) : array(),
					'option_type' => ( ! empty( $this->data( 'options' ) ) ) ? $this->data( 'options' ) : false,
				) : false,
			);
		}

		/**
		 * Loads the required plugins assets.
		 *
		 * @return mixed|void
		 */
		public function field_assets() {
			wponion_load_asset( $this->select_framework );
		}
	}
}

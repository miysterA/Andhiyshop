<?php

namespace WPOnion\Field;

use WPOnion\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Field\Fieldset' ) ) {
	/**
	 * Class Fieldset
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Fieldset extends Field {

		/**
		 * Final HTML Output
		 */
		protected function output() {
			echo $this->before();
			echo ( true !== $this->data( 'only_field' ) ) ? '<div class="wponion-fieldset-wrap row">' : '';
			if ( $this->has( 'fields' ) ) {
				if ( $this->has( 'heading' ) ) {
					echo $this->sub_field( array(
						'type'    => 'subheading',
						'content' => $this->data( 'heading' ),
					), $this->data( 'heading' ), $this->unique() );
				}

				foreach ( $this->data( 'fields' ) as $field ) {
					echo $this->sub_field( $field, wponion_get_field_value( $field, $this->value() ), $this->name(), false );
				}
			}

			echo ( true !== $this->data( 'only_field' ) ) ? '</div>' : '';
			echo $this->after();
		}

		/**
		 * Returns all fields default.
		 *
		 * @return array|mixed
		 */
		protected function field_default() {
			return array(
				'fields'   => array(),
				'heading'  => null,
				'un_array' => false,
			);
		}

		/**
		 * Loads the required plugins assets.
		 *
		 * @return mixed|void
		 */
		public function field_assets() {
		}
	}
}


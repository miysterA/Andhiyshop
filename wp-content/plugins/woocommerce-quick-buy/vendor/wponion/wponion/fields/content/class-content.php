<?php

namespace WPOnion\Field;

use WPOnion\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Field\Content' ) ) {
	/**
	 * Class Content
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Content extends Field {
		protected function output() {
			echo $this->before();
			$content = $this->data( 'content' );

			if ( ! empty( $this->data( 'content' ) ) ) {
				if ( wponion_is_callable( $this->data( 'content' ) ) ) {
					$this->catch_output( 'start' );
					echo wponion_callback( $this->data( 'content' ) );
					$content = $this->catch_output( 'end' );
				} elseif ( file_exists( $this->data( 'content' ) ) ) {
					$this->catch_output( 'start' );
					include $this->data( 'content' );
					$content = $this->catch_output( 'end' );
				}
			}

			if ( $this->has( 'markdown' ) && true === $this->has( 'markdown' ) && ! empty( $content ) ) {
				$content = '<div class="wponion-markdown-output">' . wponion_markdown( $content ) . '</div>';
			}

			echo do_shortcode( $content );

			echo $this->after();
		}

		/**
		 * Fields Default.
		 *
		 * @return array|mixed
		 */
		protected function field_default() {
			return array(
				'content'  => false,
				'markdown' => false,
			);
		}

		public function field_assets() {
		}
	}
}

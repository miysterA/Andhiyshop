<?php

namespace WPO\Fields;

use WPO\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPO\Fields\Background' ) ) {
	/**
	 * Class Background
	 *
	 * @package WPO\Fields
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Background extends Field {
		/**
		 * Background constructor.
		 *
		 * @param bool  $id
		 * @param bool  $title
		 * @param array $args
		 */
		public function __construct( $id = false, $title = false, $args = array() ) {
			parent::__construct( 'background', $id, $title, $args );
		}

		/**
		 * @param bool $show_preview
		 *
		 * @return $this
		 */
		public function preview( $show_preview = true ) {
			return $this->_set( 'preview', $show_preview );
		}

		/**
		 * @return \WPO\Fields\Background
		 */
		public function show_preview() {
			return $this->preview( true );
		}

		/**
		 * @return \WPO\Fields\Background
		 */
		public function hide_preview() {
			return $this->preview( false );
		}

		/**
		 * @param string $height
		 *
		 * @return $this
		 */
		public function preview_height( $height = '200px' ) {
			return $this->_set( 'height', $height );
		}

		/**
		 * @param bool|\WPO\Field $background_repeat
		 *
		 * @return $this
		 */
		public function background_repeat( $background_repeat = true ) {
			return $this->_set( 'background-repeat', $background_repeat );
		}

		/**
		 * @param bool|\WPO\Field $background_attachment
		 *
		 * @return $this
		 */
		public function background_attachment( $background_attachment = true ) {
			return $this->_set( 'background-attachment', $background_attachment );
		}

		/**
		 * @param bool|\WPO\Field $background_position
		 *
		 * @return $this
		 */
		public function background_position( $background_position = true ) {
			return $this->_set( 'background-position', $background_position );
		}

		/**
		 * @param bool|\WPO\Field $background_clip
		 *
		 * @return $this
		 */
		public function background_clip( $background_clip = true ) {
			return $this->_set( 'background-clip', $background_clip );
		}

		/**
		 * @param bool|\WPO\Field $background_origin
		 *
		 * @return $this
		 */
		public function background_origin( $background_origin = true ) {
			return $this->_set( 'background-origin', $background_origin );
		}

		/**
		 * @param bool|\WPO\Field $background_size
		 *
		 * @return $this
		 */
		public function background_size( $background_size = true ) {
			return $this->_set( 'background-size', $background_size );
		}

		/**
		 * @param bool|\WPO\Field $background_color
		 *
		 * @return $this
		 */
		public function background_color( $background_color = true ) {
			return $this->_set( 'background-color', $background_color );
		}

		/**
		 * @param bool|\WPO\Field $background_image
		 *
		 * @return $this
		 */
		public function background_image( $background_image = true ) {
			return $this->_set( 'background-image', $background_image );
		}
	}
}

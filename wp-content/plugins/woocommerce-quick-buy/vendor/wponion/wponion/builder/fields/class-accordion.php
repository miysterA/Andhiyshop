<?php

namespace WPO\Fields;

use WPO\Helper\Field\Nested_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPO\Fields\Accordion' ) ) {
	/**
	 * Class Accordion
	 *
	 * @package WPO\Fields
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 *
	 * @method get_is_open()
	 */
	class Accordion extends Nested_Fields {
		/**
		 * Accordion constructor.
		 *
		 * @param bool  $id
		 * @param bool  $title
		 * @param array $args
		 */
		public function __construct( $id = false, $title = false, $args = array() ) {
			parent::__construct( 'accordion', $id, $title, $args );
		}

		/**
		 * @param bool $is_open
		 *
		 * @return $this
		 */
		public function is_open( $is_open = false ) {
			return $this->_set( 'is_open', $is_open );
		}

		/**
		 * @return $this
		 */
		public function open() {
			return $this->is_open( true );
		}

		/**
		 * @return $this
		 */
		public function close() {
			return $this->is_open( false );
		}
	}
}

<?php

namespace WPOnion\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! trait_exists( '\WPOnion\Traits\Serializable' ) ) {
	/**
	 * Trait Serializable
	 *
	 * @package WPOnion\Traits
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	trait Serializable {
		/**
		 * serialize the data in $this->variable
		 *
		 * @return string
		 */
		public function serialize() {
			return serialize( $this->get() );
		}

		/**
		 * unserialize and stores the data into $this->variable.
		 *
		 * @param string $content
		 */
		public function unserialize( $content ) {
			$this->set( unserialize( $content ) );
		}
	}
}

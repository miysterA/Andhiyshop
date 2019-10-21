<?php

namespace WPO\Fields;

use WPO\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPO\Fields\Font_Picker' ) ) {
	/**
	 * Class Font_Picker
	 *
	 * @package WPO\Fields
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Font_Picker extends Field {
		/**
		 * Color_Group constructor.
		 *
		 * @param bool  $id
		 * @param bool  $title
		 * @param array $args
		 */
		public function __construct( $id = false, $title = false, $args = array() ) {
			parent::__construct( 'font_picker', $id, $title, $args );
		}

		/**
		 * Accecpts Only True / False.
		 *
		 * @param bool $show_google_fonts
		 *
		 * @return $this
		 */
		public function google_fonts( $show_google_fonts = true ) {
			$this['google_fonts'] = $show_google_fonts;
			return $this;
		}

		/**
		 * Accecpts Only True / False.
		 *
		 * @param bool $show_websafe_fonts
		 *
		 * @return $this
		 */
		public function websafe_fonts( $show_websafe_fonts = true ) {
			$this['websafe_fonts'] = $show_websafe_fonts;
			return $this;
		}

		/**
		 * Accecpts Only True / False.
		 *
		 * @param bool $show_in_group
		 *
		 * @return $this
		 */
		public function group( $show_in_group = true ) {
			$this['group'] = $show_in_group;
			return $this;
		}
	}
}

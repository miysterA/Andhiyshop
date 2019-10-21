<?php

namespace WPO\Fields;

use WPO\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPO\Fields\Spacing' ) ) {
	/**
	 * Class Spacing
	 *
	 * @package WPO\Fields
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Spacing extends Field {
		/**
		 * Spacing constructor.
		 *
		 * @param bool  $id
		 * @param bool  $title
		 * @param array $args
		 */
		public function __construct( $id = false, $title = false, $args = array() ) {
			parent::__construct( 'spacing', $id, $title, $args );
		}

		/**
		 * @param $top
		 *
		 * @return $this
		 */
		public function top( $top ) {
			$this['top'] = $top;
			return $this;
		}

		/**
		 * @param $bottom
		 *
		 * @return $this
		 */
		public function bottom( $bottom ) {
			$this['bottom'] = $bottom;
			return $this;
		}

		/**
		 * @param $left
		 *
		 * @return $this
		 */
		public function left( $left ) {
			$this['left'] = $left;
			return $this;
		}

		/**
		 * @param $right
		 *
		 * @return $this
		 */
		public function right( $right ) {
			$this['right'] = $right;
			return $this;
		}

		/**
		 * @param bool $all
		 *
		 * @return $this
		 */
		public function all( $all = true ) {
			$this['all'] = $all;
			return $this;
		}

		/**
		 * @param $unit
		 *
		 * @return $this
		 */
		public function unit( $unit ) {
			$this['unit'] = $unit;
			return $this;
		}

		/**
		 * Default Options Are :
		 * array(
		 *    'px' => 'px',
		 *    '%'  => '%',
		 *    'em' => 'em',
		 * )
		 *
		 * @param $options
		 *
		 * @return $this
		 */
		public function unit_options( $options ) {
			$this['unit_options'] = $options;
			return $this;
		}


		/**
		 * Default Icons Are :
		 * array(
		 *    'top'    => '<i class="wpoic-up"></i>',
		 *    'bottom' => '<i class="wpoic-down"></i>',
		 *    'left'   => '<i class="wpoic-left"></i>',
		 *    'right'  => '<i class="wpoic-right"></i>',
		 *    'all'    => '<i class="wpoic-move"></i>',
		 * )
		 *
		 * @param $icons
		 *
		 * @return $this
		 */
		public function icons( $icons ) {
			$this['icons'] = $icons;
			return $this;
		}
	}
}

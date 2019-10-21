<?php

namespace WPOnion;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WPOnion\Util' ) ) {
	/**
	 * Class Util
	 *
	 * @package WPOnion\JS
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Util {
		/**
		 * Stores Fields JS id.
		 *
		 * @var bool
		 * @access
		 */
		protected $js_id = false;

		/**
		 * Stores Element HTML.
		 *
		 * @var null
		 * @access
		 */
		protected $element = null;

		/**
		 * Util constructor.
		 *
		 * @param bool|string $element
		 * @param bool|string $js_id
		 */
		public function __construct( $element = false, $js_id = false ) {
			$this->element = $element;
			$this->js_id   = $js_id;
		}

		/**
		 * @return bool|string
		 */
		public function element() {
			return $this->element;
		}

		/**
		 * Handles Fields JS ID.
		 *
		 * @param $element
		 * @param $js_id
		 */
		protected function handle_element( $element, $js_id = false ) {
			$js_id = ( $js_id === $this->js_id ) ? false : $js_id;

			if ( $element instanceof Util ) {
				$this->element = $element->element();
				$this->js_id   = $element->js_id();
			} elseif ( is_string( $element ) ) {
				$this->element = $element;
				$re            = '/(?:data-wponion-jsid)="(.+?)"/m';
				preg_match( $re, $element, $matches, PREG_OFFSET_CAPTURE, 0 );
				if ( isset( $matches[1][0] ) && ! empty( $matches[1][0] ) ) {
					$this->js_id = trim( $matches[1][0] );
				}
			}

			if ( ! empty( $js_id ) ) {
				$this->js_id = $js_id;
			}
		}

		/**
		 * Returns Element String.
		 *
		 * @return bool|string|null
		 */
		public function __toString() {
			return ( ! empty( $this->element ) ) ? $this->element : '';
		}

		/**
		 * Returns JS Id.
		 *
		 * @return bool|string
		 */
		public function js_id() {
			if ( empty( $this->js_id ) ) {
				$this->js_id = 'wpo' . wponion_hash_string( $this->element . microtime( true ) );
			}
			return $this->js_id;
		}

		/**
		 * @param string $content
		 * @param array  $args
		 * @param bool   $localize
		 *
		 * @return array|mixed
		 */
		public function tooltip( $content, $args = array(), $localize = true ) {
			$args = wp_parse_args( $args, array(
				'content'     => $content,
				'image'       => false,
				'arrow'       => true,
				'arrowType'   => 'round',
				'js_field_id' => $this->js_id(),
				'element'     => $this->element,
				'placement'   => 'top',
			) );

			$this->js_id   = $args['js_field_id'];
			$this->element = $args['element'];
			unset( $args['js_field_id'] );
			unset( $args['element'] );

			if ( false === $args['image'] && true === wponion_is_url( $args['content'] ) ) {
				$args['image']   = $args['content'];
				$args['content'] = false;
			}

			$attr = array( 'wponion-help' => 'wponion-help' );

			if ( false !== $localize ) {
				$localize                        = ( true === $localize ) ? 'wponion-help' : $localize;
				$localize                        = ( false !== $this->element ) ? md5( $this->element ) : $localize;
				$attr['data-wponion-tooltip-id'] = $localize;
				wponion_localize()->add( $this->js_id, array( $localize => $args ) );
			} else {
				$attr['data-tippy'] = $args['content'];
			}

			if ( empty( $this->element ) ) {
				return array(
					'attr' => $attr,
					'data' => $args,
				);
			}

			$attr['data-wponion-jsid'] = $this->js_id;
			preg_match_all( '/^(.?)<[a-z][a-z0-9]*?\b/s', $this->element, $matches, PREG_SET_ORDER, 0 );
			if ( isset( $matches[0][0] ) ) {
				$this->element = str_replace( $matches[0][0], $matches[0][0] . ' ' . wponion_array_to_html_attributes( $attr ) . ' ', $this->element );
			}
			return $this;
		}

		/**
		 * @param $args
		 *
		 * @return $this|bool|string
		 */
		public function inline_ajax( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'method'      => 'post',
				'url'         => admin_url( 'admin-ajax.php' ),
				'part_url'    => false,
				'data'        => array(),
				'success'     => false,
				'error'       => false,
				'always'      => false,
				'js_field_id' => $this->js_id(),
				'element'     => $this->element(),
			) );

			$this->handle_element( $args['element'], $args['js_field_id'] );

			unset( $args['button'] );
			unset( $args['js_field_id'] );
			wponion_localize()->add( $this->js_id(), array( 'inline_ajax' => $args ) );
			if ( ! empty( $this->element ) ) {
				$this->element = preg_replace( '/(<[a-zA-Z0-9]* )(.*)/', '$1 data-wponion-inline-ajax="' . $this->js_id() . '" $2', $this->element );
				return $this;
			}
			return $this->js_id();
		}

		/**
		 * @param string|bool $image_src
		 * @param string|bool $full_size
		 * @param bool        $element
		 *
		 * @return $this
		 */
		public function image_popup( $image_src = false, $full_size = false, $element = false ) {
			if ( false === $element && true !== wponion_is_url( $image_src ) ) {
				$element   = $image_src;
				$image_src = false;
			}

			$full_size = ( ! empty( $full_size ) ) ? "data-fullsize='" . $full_size . "' " : ' ';
			$full_size .= '  wponion-img-popup="wponion-img-popup" ';

			$this->handle_element( $element );

			if ( ! empty( $this->element ) ) {
				$this->element = preg_replace( '/(<[a-zA-Z0-9]* )(.*)/', '$1 ' . $full_size . '  $2', $this->element );
			} else {
				$this->element = '<img src="' . $image_src . '" ' . $full_size . ' />';
			}
			return $this;
		}
	}
}

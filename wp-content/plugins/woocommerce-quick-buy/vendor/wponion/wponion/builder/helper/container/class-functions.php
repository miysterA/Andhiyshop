<?php

namespace WPO\Helper\Container;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use WPO\Container;

if ( ! trait_exists( '\WPO\Helper\Container\Functions' ) ) {
	/**
	 * Trait Functions
	 *
	 * @package WPO\Helper\Container
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	trait Functions {
		/**
		 * Returns Sub Containers.
		 *
		 * @param bool|string $key
		 *
		 * @return array|mixed|\WPO\Container
		 */
		public function containers( $key = false ) {
			if ( $this->has_containers() ) {
				if ( empty( $key ) ) {
					return $this->containers;
				}
				$key       = array_filter( explode( '/', $key ) );
				$_key      = array_shift( $key );
				$container = $this->container_exists( $_key );
				return ( method_exists( $container, 'get' ) ) ? $container->get( implode( '/', $key ) ) : $container;

			}
			return ( $this->has_containers() ) ? $this->containers : array();
		}

		/**
		 * Checks If Current Instance Has Container.
		 *
		 * @return bool
		 */
		public function has_containers() {
			return ( false !== $this->containers && wponion_is_array( $this->containers ) && ! empty( $this->containers ) );
		}

		/**
		 * Returns First Container Instance.
		 *
		 * @return false|\WPO\Container
		 */
		public function first_container() {
			/* @var \WPO\Container $container */
			foreach ( $this->containers() as $key => $container ) {
				if ( wpo_is_container( $container ) && false === $container->is_disabled() ) {
					return $container;
				}
			}
			return false;
		}

		/**
		 * Checks If Conntainer Exists
		 * and if exists then returns the container instance.
		 *
		 * @param $container_id
		 *
		 * @return \WPO\Container|false
		 */
		public function container_exists( $container_id ) {
			if ( $this->has_containers() ) {
				return ( isset( $this->containers[ $container_id ] ) ) ? $this->containers[ $container_id ] : false;
			}
			return false;
		}

		/**
		 * Checks If Conntainer Exists and removes it.
		 *
		 * @param $container_id
		 *
		 * @return bool
		 */
		public function remove_container( $container_id ) {
			if ( $this->has_containers() ) {
				unset( $this->containers[ $container_id ] );
				return true;
			}
			return false;
		}

		/**
		 * @param bool $instance_or_slug
		 * @param bool $title
		 * @param bool $icon
		 *
		 * @return $this|bool|false|\WPO\Container
		 */
		public function separator( $instance_or_slug = false, $title = false, $icon = false ) {
			$_container = $this->container( $instance_or_slug, $title, $icon );
			$_container->is_separator( true );
			return $_container;
		}

		/**
		 * @param bool|\WPO\Container|string $container_slug_or_instance
		 * @param bool                       $title
		 * @param bool                       $icon
		 *
		 * @return $this|bool|false|\WPO\Container
		 */
		public function container( $container_slug_or_instance = false, $title = false, $icon = false ) {
			if ( $this->has_fields() && $this->has_containers() ) {
				wp_die( __( 'A Container Cannot Have Both Field & Containers', 'wponion' ) );
			}
			if ( wpo_is_container( $container_slug_or_instance ) ) {
				$this->containers[ $container_slug_or_instance->name() ] = $container_slug_or_instance;
				return $container_slug_or_instance;
			}

			$return = false;

			if ( is_string( $container_slug_or_instance ) && false === $title && false === $icon ) {
				$return = $this->container_exists( $container_slug_or_instance );
			}

			if ( false === $return ) {
				$return                              = Container::create( $container_slug_or_instance, $title, $icon );
				$this->containers[ $return->name() ] = $return;
			}
			return $return;
		}

		/**
		 * @param                $before_container_id
		 * @param \WPO\Container $new_container
		 *
		 * @return $this
		 */
		public function container_before( $before_container_id, Container $new_container ) {
			if ( $this->has_containers() ) {
				$this->containers = \WPOnion\Helper::array_insert_before( $before_container_id, $this->containers, $new_container->name(), $new_container );
			}
			return $this;
		}

		/**
		 * @param                $after_container_id
		 * @param \WPO\Container $new_container
		 *
		 * @return $this
		 */
		public function container_after( $after_container_id, Container $new_container ) {
			if ( $this->has_containers() ) {
				$this->containers = \WPOnion\Helper::array_insert_after( $after_container_id, $this->containers, $new_container->name(), $new_container );
			}
			return $this;
		}

		/**
		 * @param $name
		 * @param $value
		 *
		 * @return $this
		 */
		public function set_var( $name, $value ) {
			$this->custom_data[ $name ] = $value;
			return $this;
		}

		/**
		 * @param $name
		 *
		 * @return bool
		 */
		public function get_var( $name ) {
			return ( isset( $this->custom_data[ $name ] ) ) ? $this->custom_data[ $name ] : false;
		}

		/**
		 * @param $name
		 *
		 * @return bool
		 */
		public function isset_var( $name ) {
			return ( isset( $this->custom_data[ $name ] ) );
		}

		/**
		 * @param $name
		 */
		public function remove_var( $name ) {
			unset( $this->custom_data[ $name ] );
		}

		/**
		 * @param array $containers
		 *
		 * @return $this
		 */
		public function set_containers( $containers = array() ) {
			$this->containers = $containers;
			return $this;
		}

		/**
		 * @param string     $type
		 * @param bool|array $data
		 *
		 * @return array|bool
		 */
		protected function json_serialize( $type, $data = false ) {
			switch ( $type ) {
				case 'get':
					if ( $this->has_fields() ) {
						return array( 'fields' => $this->fields() );
					}
					if ( $this->has_containers() ) {
						return array( 'containers' => $this->containers() );
					}
					break;
				case 'set':
					if ( isset( $data['fields'] ) ) {
						$this->set_fields( $data['fields'] );
					}

					if ( isset( $data['containers'] ) ) {
						$this->set_containers( $data['containers'] );
					}
					break;
			}
			return array();
		}

		/**
		 * JSON Encodes. Data
		 *
		 * @return array
		 */
		public function jsonSerialize() {
			return $this->json_serialize( 'get' );
		}
	}
}

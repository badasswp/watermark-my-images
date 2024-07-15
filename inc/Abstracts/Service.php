<?php
/**
 * Service Abstraction.
 *
 * Establish the base class for all services
 * here and define shared logic.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Abstracts;

use WatermarkMyImages\Interfaces\Registrable;

abstract class Service implements Registrable {
	/**
	 * Concrete Classes.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static array $instances;

	/**
	 * Establish Singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public static function get_instance(): static {
		$instance = get_called_class();

		if ( ! isset( static::$instances[ $instance ] ) ) {
			static::$instances[ $instance ] = new static();
		}

		return static::$instances[ $instance ];
	}

	/**
	 * Register to WP.
	 *
	 * Bind all logic from child concrete classes to
	 * the plugin at this point.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function register();
}

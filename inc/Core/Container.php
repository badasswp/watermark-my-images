<?php
/**
 * Container Class.
 *
 * This class acts as a Factory class for registering
 * all Plugin services.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Core;

use WatermarkMyImages\Services\Boot;
use WatermarkMyImages\Services\Admin;
use WatermarkMyImages\Services\Logger;
use WatermarkMyImages\Services\MetaData;
use WatermarkMyImages\Services\PageLoad;
use WatermarkMyImages\Services\Attachment;
use WatermarkMyImages\Services\WooCommerce;

use WatermarkMyImages\Interfaces\Registrable;

class Container implements Registrable {
	/**
	 * Services.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public static array $services;

	/**
	 * Set up Services.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::$services = [
			Admin::class,
			Attachment::class,
			Boot::class,
			Logger::class,
			MetaData::class,
			PageLoad::class,
			WooCommerce::class,
		];
	}

	/**
	 * Register Services.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( static::$services as $service ) {
			( $service::get_instance() )->register();
		}
	}
}

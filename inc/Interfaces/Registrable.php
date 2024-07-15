<?php
/**
 * Registrable Interface.
 *
 * Define contract methods to be adopted globally
 * by classes across the plugin.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Interfaces;

interface Registrable {
	/**
	 * Register class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void;
}

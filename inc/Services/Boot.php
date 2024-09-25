<?php
/**
 * Boot Service.
 *
 * Handle all setup logic before plugin is
 * fully capable.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Boot extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_translation' ] );
	}

	/**
	 * Register Text Domain.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_translation(): void {
		load_plugin_textdomain(
			'watermark-my-images',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../../languages'
		);
	}
}

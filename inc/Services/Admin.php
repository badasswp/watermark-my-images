<?php
/**
 * Admin Class.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Admin extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
	}
}

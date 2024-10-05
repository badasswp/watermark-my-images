<?php
/**
 * Logger Class.
 *
 * This class handles the logging of failed
 * Watermark additions.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Logger extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'watermark_my_images_on_add_image', [ $this, 'log_watermark_errors' ], 10, 3 );
		add_action( 'watermark_my_images_on_page_load', [ $this, 'log_watermark_errors' ], 10, 3 );
		add_action( 'watermark_my_images_on_woo_product_get_image', [ $this, 'log_watermark_errors' ], 10, 3 );
	}

	/**
	 * Log Watermark Errors.
	 *
	 * This is responsible for logging errors from
	 * Watermark operations.
	 *
	 * @since 1.0.2
	 *
	 * @param string|\WP_Error $url       Image URL or WP_Error.
	 * @param string[]         $watermark Watermark paths.
	 * @param int              $id        Image ID.
	 *
	 * @return void
	 */
	public function log_watermark_errors( $url, $watermark, $id ): void {
		if ( ! wmig_set_settings( 'logs' ) ) {
			return;
		}

		if ( is_wp_error( $url ) ) {
			wp_insert_post(
				[
					'post_type'    => 'wmi_error',
					'post_title'   => 'Watermark error log, ID - ' . $id,
					'post_content' => (string) $url->get_error_message(),
					'post_status'  => 'publish',
				]
			);
		}
	}
}

<?php
/**
 * Attachment Class.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

class Attachment extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'watermark_my_images_completed', [ $this, 'add_watermark_metadata' ], 10, 3 );
	}

	/**
	 * Add Watermark Meta.
	 *
	 * This method is responsible for capturing meta-data
	 * if watermarking was successful.
	 *
	 * @param string|\WP_Error $html      Image HTML or WP_Error.
	 * @param string[]         $watermark Watermark paths.
	 * @param int              $id        Image ID.
	 *
	 * @return void
	 */
	public function add_watermark_metadata( $html, $watermark, $id ): void {
		if ( ! is_wp_error( $html ) ) {
			update_post_meta(
				$id,
				'watermark_my_images',
				[
					'abs' => $watermark['abs'] ?? '',
					'rel' => $watermark['rel'] ?? '',
				]
			);
		}
	}
}

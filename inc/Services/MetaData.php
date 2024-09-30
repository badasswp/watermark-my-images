<?php
/**
 * MetaData Class.
 *
 * This class is responsible for loading MetaData specific
 * logic for plugin use.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class MetaData extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'watermark_my_images_on_add_image', [ $this, 'add_watermark_metadata' ], 10, 3 );
		add_action( 'watermark_my_images_on_page_load', [ $this, 'add_watermark_metadata' ], 10, 3 );
		add_action( 'watermark_my_images_on_woo_product_get_image', [ $this, 'add_watermark_metadata' ], 10, 3 );
	}

	/**
	 * Add Watermark Meta.
	 *
	 * This method is responsible for capturing meta-data
	 * if watermarking was successful.
	 *
	 * @since 1.0.0
	 *
	 * @param string|\WP_Error $url       Image URL or WP_Error.
	 * @param string[]         $watermark Watermark paths.
	 * @param int              $id        Image ID.
	 *
	 * @return void
	 */
	public function add_watermark_metadata( $url, $watermark, $id ): void {
		// Bail out early, if \WP_Error.
		if ( is_wp_error( $url ) ) {
			return;
		}

		// Save only if Watermark post meta doesn't exist.
		$watermark_post_meta = get_post_meta( $id, 'watermark_my_images', true );
		if ( empty( $watermark_post_meta['abs'] ) ) {
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

<?php
/**
 * Attachment Class.
 *
 * This class is responsible for loading Attachment specific
 * logic for plugin use.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Attachment extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'add_attachment', [ $this, 'add_watermark_on_add_attachment' ], 10, 1 );
	}

	/**
	 * Get Watermark Image.
	 *
	 * This method uses the Image ID to retrieve
	 * the image to be watermarked.
	 *
	 * @since 1.0.0
	 *
	 * @param int $image_id Image ID.
	 *
	 * @return void
	 */
	public function add_watermark_on_add_attachment( $image_id ): void {
		$image_html     = wp_get_attachment_url( $image_id );
		$this->image_id = $image_id;

		$image_watermark = get_post_meta( $this->image_id, 'watermark_my_images', true );

		// Bail out, if it exist.
		if ( file_exists( $image_watermark['abs'] ?? '' ) ) {
			return;
		}

		try {
			$watermark = $this->watermarker->get_watermark();
			$response  = $watermark['rel'] ?? '';
		} catch ( \Exception $e ) {
			$response = new \WP_Error(
				'watermark-log-error',
				sprintf(
					'Fatal Error: %s',
					$e->getMessage()
				)
			);
		}

		/**
		 * Fire after Watermark is completed.
		 *
		 * @since 1.0.0
		 *
		 * @param string|\WP_Error $response  Image HTML or WP Error.
		 * @param string[]         $watermark Array containing abs and rel paths to new images.
		 * @param int              $id        Image ID.
		 *
		 * @return void
		 */
		do_action( 'watermark_my_images_on_add_attachment', $response, $watermark, $id = $this->image_id );
	}
}

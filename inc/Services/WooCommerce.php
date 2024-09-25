<?php
/**
 * WooCommerce Class.
 *
 * This class is responsible for loading WooCommerce specific
 * logic for plugin use.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use DOMDocument;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class WooCommerce extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'woocommerce_product_get_image', [ $this, 'add_watermark_on_get_image' ], 10, 5 );
	}

	/**
	 * Get Watermark Image.
	 *
	 * This method uses the Image ID to retrieve
	 * the image to be watermarked.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $image_html  Image HTML.
	 * @param \WC_Product $product     Product.
	 * @param string      $size        Size (by default, this is `woocommerce_thumbnail`).
	 * @param mixed[]     $attr        Image Attributes.
	 * @param bool        $placeholder TRUE or FALSE to return placeholder.
	 *
	 * @return string
	 */
	public function add_watermark_on_get_image( $image_html, $product, $size, $attr, $placeholder ): string {
		$this->image_id  = $product->get_image_id();
		$image_watermark = get_post_meta( $this->image_id, 'watermark_my_images', true );

		// Bail out, if it exist.
		if ( file_exists( $image_watermark['abs'] ?? '' ) ) {
			return $this->get_image_html( $image_html, $image_watermark['rel'] );
		}

		try {
			$watermark  = $this->watermarker->get_watermark();
			$response   = $watermark['rel'] ?? '';
			$image_html = $this->get_image_html( $image_html, $watermark['rel'] );
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
		 * @param string|\WP_Error $response  Image URL or WP Error.
		 * @param string[]         $watermark Array containing abs and rel paths to new images.
		 * @param int              $id        Image ID.
		 *
		 * @return void
		 */
		do_action( 'watermark_my_images_on_woo_product_get_image', $response, $watermark ?? [], $id = $this->image_id );

		return $image_html;
	}

	/**
	 * Get HTML Image.
	 *
	 * Reusable utility function for replacing the Image
	 * HTML with a new watermarked image.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html      Image HTML.
	 * @param string $new_image New Image URL.
	 *
	 * @return string
	 */
	public function get_image_html( $html, $new_image ): string {
		$dom = new DOMDocument();
		$dom->loadHTML( $html, LIBXML_NOERROR );

		$image  = $dom->getElementsByTagName( 'img' )[0];
		$source = $image->getAttribute( 'src' );

		return str_replace( $source, $new_image, $html );
	}
}

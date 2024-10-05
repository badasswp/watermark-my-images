<?php
/**
 * PageLoad Class.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use DOMDocument;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class PageLoad extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'wp_get_attachment_image', [ $this, 'register_wp_get_attachment_image' ], 10, 5 );
	}

	/**
	 * Generate Watermark on wp_get_attachment_image.
	 *
	 * Filter WP image on the fly for image display used in
	 * posts, pages and so on.
	 *
	 * @since 1.0.1
	 *
	 * @param string       $html          HTML img element or empty string on failure.
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|int[] $size          Requested image size.
	 * @param bool         $icon          Whether the image should be treated as an icon.
	 * @param string[]     $attr          Array of attribute values for the image markup, keyed by attribute name.
	 *
	 * @return string
	 */
	public function register_wp_get_attachment_image( $html, $attachment_id, $size, $icon, $attr ): string {
		// Bail out, if empty.
		if ( empty( $html ) ) {
			return $html;
		}

		// Bail out, if not enabled in Options.
		if ( ! wmig_set_settings( 'page_load' ) ) {
			return $html;
		}

		$html = $this->get_watermark_html( $html, $attachment_id );

		/**
		 * Filter Watermark Image HTML.
		 *
		 * @since 1.0.1
		 *
		 * @param string $html          Attachment HTML.
		 * @param int    $attachment_id Image ID.
		 *
		 * @return string
		 */
		return (string) apply_filters( 'watermark_my_images_attachment_html', $html, $attachment_id );
	}

	/**
	 * Get Watermark Image HTML.
	 *
	 * This generic method uses the original image HTML to generate
	 * a Watermark-Image HTML. This is useful for images that pre-date
	 * the installation of the plugin on a WP Instance.
	 *
	 * @since 1.0.1
	 *
	 * @param string $html Image HTML.
	 * @param int    $id   Image Attachment ID.
	 *
	 * @return string
	 */
	protected function get_watermark_html( $html, $id = 0 ): string {
		// Bail out, if empty or NOT image.
		if ( empty( $html ) || ! preg_match( '/<img.*>/', $html, $image ) ) {
			return $html;
		}

		// Get DOM object.
		$dom = new DOMDocument();
		$dom->loadHTML( $html, LIBXML_NOERROR );

		// Generate Watermark images.
		foreach ( $dom->getElementsByTagName( 'img' ) as $image ) {
			// For the src image.
			$src = $image->getAttribute( 'src' );

			if ( empty( $src ) ) {
				return $html;
			}

			// For the srcset images.
			$srcset = $image->getAttribute( 'srcset' );

			if ( empty( $srcset ) ) {
				return $html;
			}

			preg_match_all( '/http\S+\b/', $srcset, $image_urls );

			foreach ( $image_urls[0] as $img_url ) {
				$html = $this->_get_webp_html( $img_url, $html, $id );
			}
		}

		return $html;
	}

	/**
	 * Reusable method for obtaining new Image HTML string.
	 *
	 * @since 1.0.1
	 *
	 * @param string $img_url  Relative path to Image - 'https://example.com/wp-content/uploads/2024/01/wm.png'.
	 * @param string $img_html The Image HTML - '<img src="sample.png"/>'.
	 * @param int    $image_id Image Attachment ID.
	 *
	 * @return string
	 */
	protected function _get_webp_html( $img_url, $img_html, $image_id ): string {
		// Bail out, if it is not an image.
		if ( ! wp_attachment_is_image( $image_id ) ) {
			return $img_html;
		}

		// Get the Main image & directory.
		$attachment = get_attached_file( $image_id );
		$img_prefix = pathinfo( $attachment, PATHINFO_DIRNAME );

		// Get absolute path of Image metadata.
		$img_metadata = trailingslashit( $img_prefix ) . pathinfo( $img_url, PATHINFO_BASENAME );

		// Get Watermark.
		try {
			$watermark = $this->watermarker->get_watermark( $img_metadata );
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
		 * @param string|\WP_Error $response  Image URL or WP Error.
		 * @param string[]         $watermark Array containing abs and rel paths to new images.
		 * @param int              $id        Image ID.
		 *
		 * @return void
		 */
		do_action( 'watermark_my_images_on_page_load', $response, $watermark ?? [], $image_id );

		// Replace image metadata with watermark.
		if ( ! is_wp_error( $response ) && file_exists( $watermark['abs'] ?? '' ) ) {
			return str_replace( $img_url, $response, $img_html );
		}

		return $img_html;
	}
}

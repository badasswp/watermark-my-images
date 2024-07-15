<?php
/**
 * Plugin Class.
 *
 * Register Plugin actions and filters within this
 * class for plugin use.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages;

use DOMDocument;
use Imagine\Gd\Imagine;
use WatermarkMyImages\Text;

class Plugin {
	/**
	 * Plugin Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Plugin
	 */
	protected static $instance;

	/**
	 * Set up Instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Run Plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		add_filter( 'woocommerce_product_get_image', [ $this, 'get_watermark_image' ], 10, 5 );
		add_action( 'watermark_my_images_completed', [ $this, 'add_watermark_metadata' ], 10, 3 );
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
	public function get_watermark_image( $image_html, $product, $size, $attr, $placeholder ): string {
		$this->image_id  = $product->get_image_id();
		$image_watermark = get_post_meta( $this->image_id, 'watermark_my_images', true );

		// Bail out, if it exist.
		if ( file_exists( $image_watermark['abs'] ?? '' ) ) {
			return $this->get_image_html( $image_html, $image_watermark['rel'] );
		}

		try {
			$watermark  = $this->get_watermark();
			$image_html = $this->get_image_html( $image_html, $watermark['rel'] );
		} catch ( \Exception $e ) {
			$image_html = new \WP_Error(
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
		 * @param string|\WP_Error $image_html Image HTML or WP Error.
		 * @param string[]         $watermark  Array containing abs and rel paths to new images.
		 * @param int              $id         Image ID.
		 *
		 * @return void
		 */
		do_action( 'watermark_my_images_completed', $image_html, $watermark, $id = $this->image_id );

		return $image_html;
	}

	/**
	 * Get Watermark.
	 *
	 * This method is responsible for handling custom
	 * watermarking operations.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception $e When unable to datect Image ID.
	 * @throws \Exception $e When unable to create Text Drawer object.
	 * @throws \Exception $e When unable to open Image resource.
	 * @throws \Exception $e When unable to paste Text on Image resource.
	 * @throws \Exception $e When unable to save Watermark Image.
	 *
	 * @return string[]
	 */
	public function get_watermark(): array {
		$img_absolute_path = get_attached_file( $this->image_id );

		if ( ! file_exists( $img_absolute_path ) ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to create Image watermark, file does not exist for Image ID: %d.', 'watermark-my-images' ),
					$this->image_id
				)
			);
		}

		try {
			$text = ( new Text() )->get_text();
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to create Text object, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		try {
			$image = ( new Imagine() )->open( $img_absolute_path );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to open Image Resource, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		try {
			$image->paste( $text, new Point( 0, 0 ) );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to paste Text on Image resource, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		try {
			$image->save( $this->get_watermark_abs_path() );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to save to Watermark image, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		return [
			'abs' => $this->get_watermark_abs_path(),
			'rel' => $this->get_watermark_rel_path(),
		];
	}

	/**
	 * Get Watermark absolute path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_watermark_abs_path(): string {
		return str_replace(
			pathinfo( get_attached_file( $this->image_id ), PATHINFO_BASENAME ),
			'watermark-my-images-' . $this->image_id . '.jpg',
			$img_absolute_path
		);
	}

	/**
	 * Get Watermark relative path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_watermark_rel_path(): string {
		$url = wp_get_attachment_url( $this->id );

		return str_replace(
			substr( $url, strrpos( $url, '/' ) ),
			'watermark-my-images-' . $this->image_id . '.jpg',
			$url
		);
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

<?php
/**
 * Watermarker Class.
 *
 * This is responsible for all the chocolatey goodness
 * happening behind the scenes.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Core;

use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use WatermarkMyImages\Abstracts\Service;

class Watermarker {
	/**
	 * Set up.
	 *
	 * @param Service $service
	 */
	public function __construct( Service $service ) {
		$this->service = $service;
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
		$img_absolute_path = get_attached_file( $this->service->image_id );

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
		$img_absolute_path = get_attached_file( $this->service->image_id );

		return str_replace(
			pathinfo( $img_absolute_path, PATHINFO_BASENAME ),
			'watermark-my-images-' . $this->service->image_id . '.jpg',
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
		$url = wp_get_attachment_url( $this->service->image_id );

		return str_replace(
			substr( $url, strrpos( $url, '/' ) + 1 ),
			'watermark-my-images-' . $this->service->image_id . '.jpg',
			$url
		);
	}
}

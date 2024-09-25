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
use Imagine\Gd\Image as Text_Object;
use Imagine\Image\ImageInterface as Image_Object;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Exceptions\TextException;

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
	 * @throws \Exception $e When unable to detect Image ID.
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
			throw new TextException(
				sprintf(
					esc_html__( 'Unable to create Text object, %s', 'watermark-my-images' ),
					$e->getMessage()
				),
				500,
				'Text Object',
				$e
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
			$image->paste( $text, $this->get_text_position( $image, $text ) );
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
	private function get_watermark_abs_path(): string {
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
	private function get_watermark_rel_path(): string {
		$url = wp_get_attachment_url( $this->service->image_id );

		return str_replace(
			substr( $url, strrpos( $url, '/' ) ),
			'/watermark-my-images-' . $this->service->image_id . '.jpg',
			$url
		);
	}

	/**
	 * Get Text Position.
	 *
	 * This method gets the position for the Text
	 * that will be pasted on the Image.
	 *
	 * @since 1.0.0
	 *
	 * @param Image_Object $image Image Object.
	 * @param Text_Object  $text  Text Object.
	 *
	 * @return Point
	 */
	private function get_text_position( $image, $text ): Point {
		// Get Sizes.
		$text_size  = $text->getSize();
		$image_size = $image->getSize();

		// Get Positions.
		$posx = ( $image_size->getWidth() - $text_size->getWidth() ) / 2;
		$posy = ( $image_size->getHeight() - $text_size->getHeight() ) / 2;

		/**
		 * Filter Text Position.
		 *
		 * This filter is responsible for manipulating the text
		 * text position before it is pasted.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $position Text Position (x, y).
		 * @return mixed[]
		 */
		list( $posx, $posy ) = (array) apply_filters( 'watermark_my_images_text_position', [ $posx, $posy ] );

		return new Point( (int) $posx, (int) $posy );
	}
}

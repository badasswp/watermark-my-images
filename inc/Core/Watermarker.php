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
	 * Image absolute file path.
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public static string $file;

	/**
	 * Get Watermark.
	 *
	 * This method is responsible for handling custom
	 * watermarking operations.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $file Absolute path to Image file.
	 *
	 * @throws \Exception $e When unable to detect Image ID.
	 * @throws \Exception $e When unable to create Text Drawer object.
	 * @throws \Exception $e When unable to open Image resource.
	 * @throws \Exception $e When unable to paste Text on Image resource.
	 * @throws \Exception $e When unable to save Watermark Image.
	 *
	 * @return string[]
	 */
	public function get_watermark( $file = null ): array {
		static::$file = is_null( $file ) ? get_attached_file( $this->service->image_id ) : $file;

		if ( ! file_exists( static::$file ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					/* translators: Image ID. */
					esc_html__( 'Unable to create Image watermark, file does not exist for Image ID: %d.', 'watermark-my-images' ),
					esc_html( $this->image_id )
				)
			);
		}

		try {
			$image = ( new Image() )->get_image();
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to open Image Resource, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		try {
			$text = ( new Text() )->get_text();
		} catch ( \Exception $e ) {
			throw new TextException(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to create Text object, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				),
				500,
				'Text Object',
			);
		}

		try {
			$image->paste( $text, $this->get_text_position( $image, $text ) );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to paste Text on Image resource, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		try {
			$image->save( $this->get_watermark_abs_path() );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to save to Watermark image, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
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
		$base_name = pathinfo( static::$file, PATHINFO_BASENAME );
		$file_name = pathinfo( static::$file, PATHINFO_FILENAME );

		return str_replace(
			$base_name,
			sprintf( '%s-watermark-my-images.jpg', $file_name ),
			static::$file
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
		$url      = wp_get_attachment_url( $this->service->image_id );
		$basename = pathinfo( $url, PATHINFO_BASENAME );

		return str_replace(
			$basename,
			sprintf( '%s-watermark-my-images.jpg', $basename ),
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

<?php
/**
 * Watermarker Class.
 *
 * This is responsible for all the chocolatey goodness
 * happening behind the scenes.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Engine;

use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Gd\Image as Text_Object;
use Imagine\Image\ImageInterface as Image_Object;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Exceptions\SaveException;
use WatermarkMyImages\Exceptions\TextException;
use WatermarkMyImages\Exceptions\ImageException;
use WatermarkMyImages\Exceptions\PasteException;

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
	 * @throws \Exception     $e When unable to detect Image ID.
	 * @throws ImageException $e When unable to create Image object.
	 * @throws TextException  $e When unable to create Text Drawer object.
	 * @throws PasteException $e When unable to paste Text on Image resource.
	 * @throws SaveException  $e When unable to save Watermark Image.
	 *
	 * @return string[]
	 */
	public function get_watermark( $file = null ): array {
		static::$file = is_null( $file ) ? get_attached_file( $this->service->image_id ) : $file;

		if ( ! file_exists( static::$file ) ) {
			throw new \Exception(
				sprintf(
					/* translators: Image ID. */
					esc_html__( 'Unable to create Image watermark, file does not exist for Image ID: %d.', 'watermark-my-images' ),
					esc_html( $this->service->image_id )
				)
			);
		}

		try {
			$image = ( new Image() )->get_image();
		} catch ( \Exception $e ) {
			throw new ImageException(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to create Image object, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				),
				500,
				'Image Object',
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
			$image->paste( $text, $this->get_position( $image, $text ) );
		} catch ( \Exception $e ) {
			throw new PasteException(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to paste Text on Image resource, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				),
				500,
				'Paste Activity',
			);
		}

		try {
			$image->save( $this->get_watermark_abs_path() );
		} catch ( \Exception $e ) {
			throw new SaveException(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to save to Watermark image, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				),
				500,
				'Save Activity',
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
	protected function get_watermark_abs_path(): string {
		return wmig_set_equivalent( static::$file );
	}

	/**
	 * Get Watermark relative path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_watermark_rel_path(): string {
		if ( ! $this->service->image_id ) {
			return '';
		}

		$url = wp_get_attachment_url( $this->service->image_id );

		return wmig_set_equivalent( $url );
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
	protected function get_position( $image, $text ): Point {
		$width = ( $image->getSize()->getWidth() - $text->getSize()->getWidth() );
		$width = $width > 0 ? $width : 0;

		$height = ( $image->getSize()->getHeight() - $text->getSize()->getHeight() );
		$height = $height > 0 ? $height : 0;

		$position = [ $width / 2, $height / 2 ];

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
		list( $posx, $posy ) = (array) apply_filters( 'watermark_my_images_text_position', $position );

		return new Point( (int) $posx, (int) $posy );
	}
}

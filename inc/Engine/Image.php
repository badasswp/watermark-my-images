<?php
/**
 * Image Class.
 *
 * This class handles the creation of image resouce
 * in GD Image format.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Engine;

use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface as Image_Object;

class Image {
	/**
	 * Imagine object.
	 *
	 * This object is an instance of the
	 * Imagine class.
	 *
	 * @since 1.0.5
	 *
	 * @var Imagine
	 */
	protected $imagine;

	/**
	 * Set up.
	 *
	 * @since 1.0.5
	 */
	public function __construct() {
		$this->imagine = new Imagine();
	}

	/**
	 * Get Image Resource.
	 *
	 * @since 1.0.1
	 *
	 * @return Image_Object
	 */
	public function get_image(): Image_Object {
		try {
			return $this->imagine->open( Watermarker::$file );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to open Image Resource, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}
	}
}

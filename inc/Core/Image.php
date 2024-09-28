<?php
/**
 * Image Class.
 *
 * This class handles the creation of image resouce
 * in GD Image format.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Core;

use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface as Image_Object;

use WatermarkMyImages\Core\Watermarker;

class Image {
	/**
	 * Get Image Resource.
	 *
	 * @since 1.0.1
	 *
	 * @return Image_Object
	 */
	public function get_image(): Image_Object {
		try {
			return ( new Imagine() )->open( Watermarker::$file );
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

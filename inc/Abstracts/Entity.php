<?php
/**
 * Entity Abstraction.
 *
 * Establish base methods for different entities
 * such as Image, Text and so on.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Abstracts;

use Imagine\Gd\Imagine;
use Imagine\Image\Palette\RGB;

abstract class Entity {
	/**
	 * Get RGB.
	 *
	 * @since TBD
	 *
	 * @param RGB $rgb
	 * @return RGB
	 */
	protected function get_rgb( RGB $rgb ): RGB {
		return $rgb;
	}

	/**
	 * Get Imagine.
	 *
	 * @since TBD
	 *
	 * @param Imagine $imagine
	 * @return Imagine
	 */
	protected function get_imagine( Imagine $imagine ): Imagine {
		return $imagine;
	}
}

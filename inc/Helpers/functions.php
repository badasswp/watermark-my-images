<?php
/**
 * Functions.
 *
 * This class holds reusable utility functions that can be
 * accessed across the plugin.
 *
 * @package WatermarkMyImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Plugin Options.
 *
 * @since 1.0.1
 *
 * @param string $option   Plugin option to be retrieved.
 * @param string $fallback Default return value.
 *
 * @return mixed
 */
function wmig_set_settings( $option, $fallback = '' ) {
	return get_option( 'watermark_my_images', [] )[ $option ] ?? $fallback;
}

/**
 * Get Watermark Equivalent.
 *
 * For e.g. if you pass in /var/www/image-1.png
 * you should receive /var/www/image-1-watermark-my-images.jpg.
 *
 * @since 1.0.1
 *
 * @param string $url Passed in URL.
 * @return string
 */
function wmig_set_equivalent( $url ): string {
	$base_name = pathinfo( $url, PATHINFO_BASENAME );
	$file_name = pathinfo( $url, PATHINFO_FILENAME );

	return str_replace(
		$base_name,
		sprintf( '%s-watermark-my-images.jpg', $file_name ),
		$url
	);
}

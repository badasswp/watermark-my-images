<?php
/**
 * Plugin Name: Watermark My Images
 * Plugin URI:  https://github.com/badasswp/watermark-my-images
 * Description: Insert Watermarks into your WP images.
 * Version:     1.0.5
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: watermark-my-images
 * Domain Path: /languages
 *
 * @package WatermarkMyImages
 */

namespace badasswp\WatermarkMyImages;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';

// Bail out, if Composer is NOT installed.
if ( ! file_exists( $autoload ) ) {
	add_action(
		'admin_notices',
		function () {
			printf(
				esc_html__( 'Fatal Error: Composer is NOT installed!', 'watermark-my-images' ),
			);
		}
	);

	return;
}

// Autoload classes.
require_once $autoload;

// Get instance and Run plugin.
require_once __DIR__ . '/inc/Helpers/functions.php';
( \WatermarkMyImages\Plugin::get_instance() )->run();

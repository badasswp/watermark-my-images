<?php

namespace WatermarkMyImages\Tests\Helpers;

use WP_Mock\Tools\TestCase;

require_once __DIR__ . '/../../inc/Helpers/functions.php';

/**
 * @covers wmig_set_settings
 * @covers wmig_set_equivalent
 */
class FunctionsTest extends TestCase {
	public function test_wmig_set_settings() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true,
				]
			);

		$is_watermark_added_on_upload = wmig_set_settings( 'upload', [] );

		$this->assertTrue( $is_watermark_added_on_upload );
	}

	public function test_wmig_set_equivalent() {
		$watermark_abs_url = wmig_set_equivalent( '/var/www/wp-content/uploads/2024/09/sample-1.png' );
		$watermark_rel_url = wmig_set_equivalent( 'https://example.com/wp-content/uploads/2024/09/sample-2.png' );

		$this->assertSame( $watermark_abs_url, '/var/www/wp-content/uploads/2024/09/sample-1-watermark-my-images.jpg' );
		$this->assertSame( $watermark_rel_url, 'https://example.com/wp-content/uploads/2024/09/sample-2-watermark-my-images.jpg' );
	}
}

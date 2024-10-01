<?php

namespace WatermarkMyImages\Tests\Helpers;

use WP_Mock\Tools\TestCase;

require_once __DIR__ . '/../../inc/Helpers/functions.php';

/**
 * @covers wmi_get_settings
 */
class FunctionsTest extends TestCase {
	public function test_wmi_get_settings() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true,
				]
			);

		$is_watermark_added_on_upload = wmi_get_settings( 'upload', [] );

		$this->assertTrue( $is_watermark_added_on_upload );
	}
}

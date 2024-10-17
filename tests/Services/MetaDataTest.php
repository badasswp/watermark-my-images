<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\MetaData;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\MetaData::__construct
 * @covers \WatermarkMyImages\Services\MetaData::register
 */
class MetaDataTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->metadata = new MetaData();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_add_image', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_page_load', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_woo_product_get_image', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );

		$this->metadata->register();

		$this->assertConditionsMet();
	}
}

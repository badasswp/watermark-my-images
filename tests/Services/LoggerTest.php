<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\Logger;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\Logger::__construct
 * @covers \WatermarkMyImages\Services\Logger::__register
 */
class LoggerTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->logger = new Logger();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_add_image', [ $this->logger, 'log_watermark_errors' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_page_load', [ $this->logger, 'log_watermark_errors' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_woo_product_get_image', [ $this->logger, 'log_watermark_errors' ], 10, 3 );

		$this->logger->register();

		$this->assertConditionsMet();
	}
}

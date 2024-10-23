<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\WooCommerce;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\WooCommerce::__construct
 * @covers \WatermarkMyImages\Services\WooCommerce::register
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 */
class WooCommerceTest extends TestCase {
	public WooCommerce $woocommerce;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->woocommerce = new WooCommerce();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_image', [ $this->woocommerce, 'add_watermark_on_get_image' ], 10, 5 );

		$this->woocommerce->register();

		$this->assertConditionsMet();
	}
}

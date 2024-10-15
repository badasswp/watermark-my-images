<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Engine\Watermarker;

/**
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers \WatermarkMyImages\Engine\Watermarker::get_watermark_abs_path
 */
class WatermarkerTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		Watermarker::$file = '/var/www/wp-content/uploads/2024/10/sample.png';
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_watermark_abs_path() {
		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();

		$abs_path = $watermarker->get_watermark_abs_path();

		$this->assertSame( $abs_path, '/var/www/wp-content/uploads/2024/10/sample-watermark-my-images.jpg' );
		$this->assertConditionsMet();
	}

	public function test_get_watermark_rel_path_returns_empty_string() {
		$service = Mockery::mock( Service::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();
		$service->id = null;

		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();
		$watermarker->service = $service;

		$rel_path = $watermarker->get_watermark_rel_path();

		$this->assertSame( $rel_path, '' );
		$this->assertConditionsMet();
	}

	public function test_get_watermark_rel_path_passes() {
		$service = Mockery::mock( Service::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();
		$service->image_id = 1;

		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();
		$watermarker->service = $service;

		\WP_Mock::userFunction( 'wp_get_attachment_url' )
			->with( 1 )
			->andReturn( 'https://example.com/wp-content/uploads/2024/10/sample.png' );

		$rel_path = $watermarker->get_watermark_rel_path();

		$this->assertSame( $rel_path, 'https://example.com/wp-content/uploads/2024/10/sample-watermark-my-images.jpg' );
		$this->assertConditionsMet();
	}
}

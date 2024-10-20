<?php

namespace WatermarkMyImages\Tests\Exceptions;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Exceptions\ImageException;

/**
 * @covers \WatermarkMyImages\Exceptions\ImageException::__construct
 * @covers \WatermarkMyImages\Exceptions\ImageException::getContext
 */
class ImageExceptionTest extends TestCase {
	public ImageException $image_exception;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->image_exception = new ImageException(
			'Unable to create Image Object',
			500,
			'Image Object'
		);
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_instance_is_an_exception() {
		$this->assertInstanceOf( \Exception::class, $this->image_exception );
	}

	public function test_context_is_set() {
		$this->assertSame( 'Image Object', $this->image_exception->getContext() );
	}
}

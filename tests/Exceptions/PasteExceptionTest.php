<?php

namespace WatermarkMyImages\Tests\Exceptions;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Exceptions\PasteException;

/**
 * @covers \WatermarkMyImages\Exceptions\PasteException::__construct
 * @covers \WatermarkMyImages\Exceptions\PasteException::getContext
 */
class PasteExceptionTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->paste_exception = new PasteException(
			'Unable to paste Text on Image Resource',
			500,
			'Paste Activity'
		);
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_instance_is_an_exception() {
		$this->assertInstanceOf( \Exception::class, $this->paste_exception );
	}

	public function test_context_is_set() {
		$this->assertSame( 'Paste Activity', $this->paste_exception->getContext() );
	}
}

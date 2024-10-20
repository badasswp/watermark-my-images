<?php

namespace WatermarkMyImages\Tests\Exceptions;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Exceptions\SaveException;

/**
 * @covers \WatermarkMyImages\Exceptions\SaveException::__construct
 * @covers \WatermarkMyImages\Exceptions\SaveException::getContext
 */
class SaveExceptionTest extends TestCase {
	public SaveException $save_exception;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->save_exception = new SaveException(
			'Unable to save Text to Image Resource',
			500,
			'Save Activity'
		);
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_instance_is_an_exception() {
		$this->assertInstanceOf( \Exception::class, $this->save_exception );
	}

	public function test_context_is_set() {
		$this->assertSame( 'Save Activity', $this->save_exception->getContext() );
	}
}

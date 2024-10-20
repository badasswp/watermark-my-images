<?php

namespace WatermarkMyImages\Tests\Exceptions;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Exceptions\TextException;

/**
 * @covers \WatermarkMyImages\Exceptions\TextException::__construct
 * @covers \WatermarkMyImages\Exceptions\TextException::getContext
 */
class TextExceptionTest extends TestCase {
	public TextException $text_exception;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->text_exception = new TextException(
			'Unable to create Text Object',
			500,
			'Text Object'
		);
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_instance_is_an_exception() {
		$this->assertInstanceOf( \Exception::class, $this->text_exception );
	}

	public function test_context_is_set() {
		$this->assertSame( 'Text Object', $this->text_exception->getContext() );
	}
}

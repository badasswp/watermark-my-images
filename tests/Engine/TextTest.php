<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Engine\Text;
use WatermarkMyImages\Engine\Watermarker;

/**
 * @covers \WatermarkMyImages\Engine\Text::__construct
 */
class TextTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->text        = new Text();
		Watermarker::$file = __DIR__ . '/sample.png';
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}
}

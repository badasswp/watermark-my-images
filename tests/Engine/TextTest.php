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

	public function test_args_is_set() {
		$this->assertSame(
			$this->text->args,
			[
				'size'       => 60,
				'tx_color'   => '#000',
				'bg_color'   => '#FFF',
				'font'       => 'Arial',
				'label'      => 'WATERMARK',
				'tx_opacity' => 100,
				'bg_opacity' => 0,
			]
		);
	}
}

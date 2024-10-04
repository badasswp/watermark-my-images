<?php

namespace WatermarkMyImages\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Options;

/**
 * @covers \WatermarkMyImages\Admin\Options::__construct
 * @covers \WatermarkMyImages\Admin\Options::get_form_page
 */
class OptionsTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_form_page() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		$form_page = Options::get_form_page();

		$this->assertSame(
			$form_page,
			[
				'title'   => 'Watermark My Images',
				'summary' => 'Insert Watermarks into your WP images.',
				'slug'    => 'watermark-my-images',
				'option'  => 'watermark_my_images',
			]
		);
	}
}

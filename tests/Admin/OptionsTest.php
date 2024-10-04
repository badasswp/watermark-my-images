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
				'times' => 2,
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

	public function test_get_form_submit() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times' => 2,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		$form_submit = Options::get_form_submit();

		$this->assertSame(
			$form_submit,
			[
				'heading' => 'Actions',
				'button'  => [
					'name'  => 'watermark_my_images_save_settings',
					'label' => 'Save Changes',
				],
				'nonce'   => [
					'name'   => 'watermark_my_images_settings_nonce',
					'action' => 'watermark_my_images_settings_action',
				],
			]
		);
	}

	public function test_get_form_notice() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times' => 1,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		$form_notice = Options::get_form_notice();

		$this->assertSame(
			$form_notice,
			[
				'label' => 'Settings Saved.',
			]
		);
	}
}

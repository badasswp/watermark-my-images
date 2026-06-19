<?php

namespace WatermarkMyImages\Tests\Admin;

use WatermarkMyImages\Admin\Options;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \WatermarkMyImages\Admin\Options::get_form_page
 * @covers \WatermarkMyImages\Admin\Options::get_form_submit
 * @covers \WatermarkMyImages\Admin\Options::get_form_notice
 * @covers \WatermarkMyImages\Admin\Options::get_form_fields
 */
class OptionsTest extends WPMockTestCase {
	public function setUp(): void {
		parent::setUp();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_get_form_page() {
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

	public function test_get_form_fields() {
		$form_fields = Options::get_form_fields();

		$this->assertSame(
			$form_fields,
			[
				'text_options'  => [
					'heading'  => 'Text Options',
					'controls' => [
						'label'      => [
							'control'     => 'text',
							'placeholder' => 'WATERMARK',
							'label'       => 'Text Label',
							'summary'     => 'e.g. WATERMARK',
						],
						'size'       => [
							'control'     => 'text',
							'placeholder' => '60',
							'label'       => 'Text Size',
							'summary'     => 'e.g. 60',
						],
						'tx_color'   => [
							'control'     => 'text',
							'placeholder' => '#000',
							'label'       => 'Text Color',
							'summary'     => 'e.g. #000',
						],
						'bg_color'   => [
							'control'     => 'text',
							'placeholder' => '#FFF',
							'label'       => 'Background Color',
							'summary'     => 'e.g. #FFF',
						],
						'tx_opacity' => [
							'control'     => 'text',
							'placeholder' => '100',
							'label'       => 'Text Opacity (%)',
							'summary'     => 'e.g. 100',
						],
						'bg_opacity' => [
							'control'     => 'text',
							'placeholder' => '0',
							'label'       => 'Background Opacity (%)',
							'summary'     => 'e.g. 0',
						],
					],
				],
				'image_options' => [
					'heading'  => 'Image Options',
					'controls' => [
						'upload'      => [
							'control' => 'checkbox',
							'label'   => 'Add Watermark on Image Upload',
							'summary' => 'This is useful for new images.',
						],
						'page_load'   => [
							'control' => 'checkbox',
							'label'   => 'Add Watermark on Page Load',
							'summary' => 'This is useful for existing images.',
						],
						'woocommerce' => [
							'control' => 'checkbox',
							'label'   => 'Enable WooCommerce Watermarks',
							'summary' => 'Allow WooCommerce serve watermark images.',
						],
						'logs'        => [
							'control' => 'checkbox',
							'label'   => 'Log errors for Failed Watermarks',
							'summary' => 'Enable this option to log errors.',
						],
					],
				],
			]
		);
	}

	public function test_get_form_notice() {
		$form_notice = Options::get_form_notice();

		$this->assertSame(
			$form_notice,
			[
				'label' => 'Settings Saved.',
			]
		);
	}
}

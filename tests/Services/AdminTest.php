<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Form;
use WatermarkMyImages\Admin\Options;
use WatermarkMyImages\Services\Admin;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\Admin::__construct
 * @covers \WatermarkMyImages\Services\Admin::register
 * @covers \WatermarkMyImages\Services\Admin::register_options_menu
 * @covers \WatermarkMyImages\Services\Admin::register_options_init
 * @covers \WatermarkMyImages\Services\Admin::register_options_styles
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers \WatermarkMyImages\Admin\Options::__callStatic
 * @covers \WatermarkMyImages\Admin\Options::get_form_fields
 * @covers \WatermarkMyImages\Admin\Options::get_form_notice
 * @covers \WatermarkMyImages\Admin\Options::get_form_page
 * @covers \WatermarkMyImages\Admin\Options::get_form_submit
 * @covers \WatermarkMyImages\Admin\Options::init
 */
class AdminTest extends TestCase {
	public Admin $admin;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->admin = new Admin();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$_POST = [];
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'admin_init', [ $this->admin, 'register_options_init' ] );
		\WP_Mock::expectActionAdded( 'admin_menu', [ $this->admin, 'register_options_menu' ] );
		\WP_Mock::expectActionAdded( 'admin_enqueue_scripts', [ $this->admin, 'register_options_styles' ] );

		$this->admin->register();

		$this->assertConditionsMet();
	}

	public function test_register_options_menu() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 75,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 27,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 18,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'add_menu_page' )
			->with(
				'Watermark My Images',
				'Watermark My Images',
				'manage_options',
				'watermark-my-images',
				[ $this->admin, 'register_options_page' ],
				'dashicons-format-image',
				100
			);

		$this->admin->register_options_menu();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_on_POST() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 75,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 27,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 18,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	public function test_register_options_init_bails_on_NONCE() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 75,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 27,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 18,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		$_POST = [
			'watermark_my_images_save_settings'  => true,
			'watermark_my_images_settings_nonce' => 'a8jfkgw2h7i',
		];

		\WP_Mock::userFunction( 'wp_unslash' )
			->with( 'a8jfkgw2h7i' )
			->andReturn( 'a8jfkgw2h7i' );

		\WP_Mock::userFunction( 'sanitize_text_field' )
			->with( 'a8jfkgw2h7i' )
			->andReturn( 'a8jfkgw2h7i' );

		\WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8jfkgw2h7i', 'watermark_my_images_settings_action' )
			->andReturn( false );

		$this->admin->register_options_init();

		$this->assertConditionsMet();
	}

	public function test_register_options_styles() {
		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 25,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr',
			[
				'times'  => 9,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_attr__',
			[
				'times'  => 6,
				'return' => function ( $text ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction( 'plugins_url' )
			->with( 'watermark-my-images/styles.css' )
			->andReturn( 'https://example.com/wp-content/plugins/watermark-my-images/styles.css' );

		\WP_Mock::userFunction( 'wp_enqueue_style' )
			->with(
				'watermark-my-images',
				'https://example.com/wp-content/plugins/watermark-my-images/styles.css',
				[],
				true,
				'all'
			)
			->andReturn( null );

		$this->admin->register_options_styles();

		$this->assertConditionsMet();
	}
}

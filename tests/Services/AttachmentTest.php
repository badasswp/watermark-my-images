<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Services\Attachment;

/**
 * @covers \WatermarkMyImages\Services\Attachment::__construct
 * @covers \WatermarkMyImages\Services\Attachment::register
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers wmig_set_settings
 */
class AttachmentTest extends TestCase {
	public Attachment $attachment;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->attachment = new Attachment();

		$this->create_mock_image( __DIR__ . '/sample.png' );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'add_attachment', [ $this->attachment, 'add_watermark_on_add_attachment' ], 10, 1 );
		\WP_Mock::expectActionAdded( 'delete_attachment', [ $this->attachment, 'remove_watermark_on_attachment_delete' ], 10, 1 );
		\WP_Mock::expectFilterAdded( 'attachment_fields_to_edit', [ $this->attachment, 'add_watermark_attachment_fields' ], 10, 2 );
		\WP_Mock::expectFilterAdded( 'wp_generate_attachment_metadata', [ $this->attachment, 'add_watermark_to_metadata' ], 10, 3 );
		\WP_Mock::expectFilterAdded( 'wp_prepare_attachment_for_js', [ $this->attachment, 'show_watermark_images_on_wp_media_modal' ], 10, 3 );

		$this->attachment->register();

		$this->assertConditionsMet();
	}

	public function test_add_watermark_on_add_attachment_bails_on_upload_NOT_set() {
		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn( [] );

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => false
				]
			);

		$this->attachment->add_watermark_on_add_attachment( 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_on_add_attachment_bails_if_attachment_is_NOT_image() {
		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn( [] );

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true
				]
			);

		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( false );

		$this->attachment->add_watermark_on_add_attachment( 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_on_add_attachment_bails_if_watermark_already_exists() {
		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => __DIR__ . '/sample.png',
					'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg'
				]
			);

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true
				]
			);

		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		$this->attachment->add_watermark_on_add_attachment( 1 );

		$this->assertConditionsMet();
	}
}

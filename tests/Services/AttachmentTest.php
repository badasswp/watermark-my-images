<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Error;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Engine\Watermarker;
use WatermarkMyImages\Services\Attachment;

/**
 * @covers \WatermarkMyImages\Services\Attachment::__construct
 * @covers \WatermarkMyImages\Services\Attachment::register
 * @covers \WatermarkMyImages\Services\Attachment::add_watermark_on_add_attachment
 * @covers \WatermarkMyImages\Services\Attachment::add_watermark_to_metadata
 * @covers \WatermarkMyImages\Services\Attachment::remove_watermark_on_attachment_delete
 * @covers \WatermarkMyImages\Services\Attachment::add_watermark_attachment_fields
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers wmig_get_settings
 * @covers wmig_get_equivalent
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
					'upload' => false,
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
					'upload' => true,
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
					'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
				]
			);

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true,
				]
			);

		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		$this->attachment->add_watermark_on_add_attachment( 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_on_add_attachment_passes() {
		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();

		$watermarker->shouldReceive( 'get_watermark' )
			->with()
			->andReturn(
				[
					'abs' => __DIR__ . '/sample.png',
					'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
				]
			);

		$this->attachment->watermarker = $watermarker;

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn( '' );

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'upload' => true,
				]
			);

		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		\WP_Mock::expectAction(
			'watermark_my_images_on_add_image',
			'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
			[
				'abs' => __DIR__ . '/sample.png',
				'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
			],
			1
		);

		$this->attachment->add_watermark_on_add_attachment( 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_to_attachement() {
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->once()
			->with( 1 )
			->andReturn( false );

		$this->attachment->add_watermark_to_metadata( [], 1, 'create' );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_to_metadata() {
		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_attached_file' )
			->with( 1 )
			->andReturn( '/var/www/html/wp-content/uploads/2024/10/sample-watermark-my-images.jpg' );

		\WP_Mock::userFunction(
			'trailingslashit',
			[
				'times'  => 1,
				'return' => function ( $url ) {
					return rtrim( $url, '/' ) . '/';
				},
			]
		);

		\WP_Mock::expectAction(
			'watermark_my_images_on_add_image_crops',
			'https://example.com/wp-content/uploads/2024/10/full-watermark-my-images.jpg',
			[
				'abs' => '/var/www/html/wp-content/uploads/2024/10/full-watermark-my-images.jpg',
				'rel' => 'https://example.com/wp-content/uploads/2024/10/full-watermark-my-images.jpg',
			],
			1,
			'/var/www/html/wp-content/uploads/2024/10/full.png'
		);

		$watermarker->shouldReceive( 'get_watermark' )
			->with( '/var/www/html/wp-content/uploads/2024/10/full.png' )
			->andReturn(
				[
					'abs' => '/var/www/html/wp-content/uploads/2024/10/full-watermark-my-images.jpg',
					'rel' => 'https://example.com/wp-content/uploads/2024/10/full-watermark-my-images.jpg',
				]
			);

		$this->attachment->watermarker = $watermarker;

		$this->attachment->add_watermark_to_metadata(
			[
				'sizes' => [
					[
						'file' => 'full.png',
					],
				],
			],
			1,
			'create'
		);

		$this->assertConditionsMet();
	}

	public function test_remove_watermark_on_attachment_delete_bails_if_NOT_image() {
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( false );

		$this->attachment->remove_watermark_on_attachment_delete( 1 );

		$this->assertConditionsMet();
	}

	public function test_remove_watermark_on_attachment_delete_bails_if_no_watermark_post_meta() {
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn( '' );

		\WP_Mock::userFunction( 'wp_get_attachment_metadata' )
			->with( 1 )
			->andReturn( [] );

		$this->attachment->remove_watermark_on_attachment_delete( 1 );

		$this->assertConditionsMet();
	}

	public function test_remove_watermark_on_attachment_delete_removes_parent_watermark() {
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => __DIR__ . '/sample.png',
					'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
				]
			);

		\WP_Mock::userFunction( 'wp_delete_file' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( true );

		\WP_Mock::expectAction( 'watermark_my_images_on_delete_image', __DIR__ . '/sample.png', 1 );

		\WP_Mock::userFunction( 'wp_get_attachment_metadata' )
			->with( 1 )
			->andReturn( [] );

		$this->attachment->remove_watermark_on_attachment_delete( 1 );

		$this->assertConditionsMet();
	}

	public function test_remove_watermark_on_attachment_delete_removes_parent_and_child_watermarks() {
		\WP_Mock::userFunction( 'wp_attachment_is_image' )
			->with( 1 )
			->andReturn( true );

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => __DIR__ . '/sample.png',
					'rel' => 'https://example.com/wp-content/2024/10/sample-watermark-my-images.jpg',
				]
			);

		\WP_Mock::userFunction( 'wp_delete_file' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( true );

		\WP_Mock::expectAction( 'watermark_my_images_on_delete_image', __DIR__ . '/sample.png', 1 );

		$this->create_mock_image( __DIR__ . '/thumbnail-watermark-my-images.jpg' );

		\WP_Mock::userFunction(
			'trailingslashit',
			[
				'return' => function ( $url ) {
					return rtrim( $url, '/' ) . '/';
				},
			]
		);

		\WP_Mock::userFunction( 'wp_get_attachment_metadata' )
			->with( 1 )
			->andReturn(
				[
					'sizes' => [
						[
							'file' => 'thumbnail.png',
						],
					],
				]
			);

		\WP_Mock::userFunction( 'wp_delete_file' )
			->with( __DIR__ . '/thumbnail-watermark-my-images.jpg' )
			->andReturn( true );

		\WP_Mock::expectAction( 'watermark_my_images_on_delete_image_crops', __DIR__ . '/thumbnail-watermark-my-images.jpg', 1 );

		$this->attachment->remove_watermark_on_attachment_delete( 1 );

		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/thumbnail-watermark-my-images.jpg' );
	}

	public function test_add_watermark_attachment_fields() {
		$post = Mockery::mock( \WP_Post::class )->makePartial();
		$post->shouldAllowMockingProtectedMethods();
		$post->ID = 1;

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => '/var/www/html/wp-content/uploads/2024/11/sample-watermark-my-images.jpg',
					'rel' => 'https://example.com/wp-content/uploads/2024/11/sample-watermark-my-images.jpg',
				]
			);

		$fields = $this->attachment->add_watermark_attachment_fields( [], $post );

		$this->assertSame(
			[
				'watermark_my_images' => [
					'label' => 'Watermarked Image',
					'input' => 'text',
					'value' => 'https://example.com/wp-content/uploads/2024/11/sample-watermark-my-images.jpg',
					'helps' => 'Watermarked Image generated by Watermark my Images.',
				],
			],
			$fields
		);

		$this->assertConditionsMet();
	}

	public function create_mock_image( $image_file_name ) {
		// Create a blank image.
		$width  = 400;
		$height = 200;
		$image  = imagecreatetruecolor( $width, $height );

		// Set background color.
		$bg_color = imagecolorallocate( $image, 255, 255, 255 );
		imagefill( $image, 0, 0, $bg_color );
		imagejpeg( $image, $image_file_name );
	}

	public function destroy_mock_image( $image_file_name ) {
		if ( file_exists( $image_file_name ) ) {
			unlink( $image_file_name );
		}
	}
}

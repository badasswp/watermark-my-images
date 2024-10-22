<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\MetaData;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\MetaData::__construct
 * @covers \WatermarkMyImages\Services\MetaData::register
 * @covers \WatermarkMyImages\Services\MetaData::add_watermark_metadata
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 */
class MetaDataTest extends TestCase {
	public MetaData $metadata;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->metadata = new MetaData();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_add_image', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_page_load', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_woo_product_get_image', [ $this->metadata, 'add_watermark_metadata' ], 10, 3 );

		$this->metadata->register();

		$this->assertConditionsMet();
	}

	public function test_add_watermark_metadata_fails_on_is_wp_error() {
		$error = Mockery::mock( \WP_Error::class )->makePartial();
		$error->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'times'  => 1,
				'return' => function ( $error ) {
					return $error instanceof \WP_Error;
				},
			]
		);

		$this->metadata->add_watermark_metadata( $error, [], 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_metadata_bails_out_on_get_post_meta() {
		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'times'  => 1,
				'return' => function ( $error ) {
					return $error instanceof \WP_Error;
				},
			]
		);

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => '/var/www/html/wp-content/uploads/2024/10/img-watermark-my-images.jpg',
					'rel' => 'https://www.example.com/wp-content/uploads/2024/10/img-watermark-my-images.jpg',
				]
			);

		$this->metadata->add_watermark_metadata( '', [], 1 );

		$this->assertConditionsMet();
	}

	public function test_add_watermark_metadata_passes() {
		$watermark = [
			'abs' => '/var/www/html/wp-content/uploads/2024/10/img-watermark-my-images.jpg',
			'rel' => 'https://www.example.com/wp-content/uploads/2024/10/img-watermark-my-images.jpg',
		];

		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'times'  => 1,
				'return' => function ( $error ) {
					return $error instanceof \WP_Error;
				},
			]
		);

		\WP_Mock::userFunction( 'get_post_meta' )
			->with( 1, 'watermark_my_images', true )
			->andReturn( '' );

		\WP_Mock::userFunction( 'update_post_meta' )
			->with( 1, 'watermark_my_images', $watermark )
			->andReturn( true );

		$this->metadata->add_watermark_metadata( $watermark['abs'], $watermark, 1 );

		$this->assertConditionsMet();
	}
}

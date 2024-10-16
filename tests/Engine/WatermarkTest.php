<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Engine\Watermarker;

use Imagine\Gd\Image as Text;
use Imagine\Image\ImageInterface as Image;
use Imagine\Image\BoxInterface as Box;

use Resource;
use Imagine\Image\Palette\PaletteInterface as Palette;
use Imagine\Image\Metadata\MetadataBag as MetaData;
use Imagine\Image\Point;

/**
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers \WatermarkMyImages\Engine\Watermarker::get_watermark_abs_path
 * @covers \WatermarkMyImages\Engine\Watermarker::get_watermark_rel_path
 */
class WatermarkerTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		Watermarker::$file = '/var/www/wp-content/uploads/2024/10/sample.png';
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_watermark_abs_path() {
		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();

		$abs_path = $watermarker->get_watermark_abs_path();

		$this->assertSame( $abs_path, '/var/www/wp-content/uploads/2024/10/sample-watermark-my-images.jpg' );
		$this->assertConditionsMet();
	}

	public function test_get_watermark_rel_path_returns_empty_string() {
		$service = Mockery::mock( Service::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();

		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();
		$watermarker->service = $service;

		$rel_path = $watermarker->get_watermark_rel_path();

		$this->assertSame( $rel_path, '' );
		$this->assertConditionsMet();
	}

	public function test_get_watermark_rel_path_passes() {
		$service = Mockery::mock( Service::class )->makePartial();
		$service->shouldAllowMockingProtectedMethods();
		$service->image_id = 1;

		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();
		$watermarker->service = $service;

		\WP_Mock::userFunction( 'wp_get_attachment_url' )
			->with( 1 )
			->andReturn( 'https://example.com/wp-content/uploads/2024/10/sample.png' );

		$rel_path = $watermarker->get_watermark_rel_path();

		$this->assertSame( $rel_path, 'https://example.com/wp-content/uploads/2024/10/sample-watermark-my-images.jpg' );
		$this->assertConditionsMet();
	}

	public function test_get_position() {
		$watermarker = Mockery::mock( Watermarker::class )->makePartial();
		$watermarker->shouldAllowMockingProtectedMethods();

		$resource = Mockery::mock( Resource::class )->makePartial();
		$resource->shouldAllowMockingProtectedMethods();

		$palette = Mockery::mock( Palette::class )->makePartial();
		$palette->shouldAllowMockingProtectedMethods();

		$metadata = Mockery::mock( Metadata::class )->makePartial();
		$metadata->shouldAllowMockingProtectedMethods();

		$text = Mockery::mock( new Text( $resource, $palette, $metadata ) )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		$text_size_object = Mockery::mock( Box::class )->makePartial();
		$text_size_object->shouldAllowMockingProtectedMethods();

		$image_size_object = Mockery::mock( Box::class )->makePartial();
		$image_size_object->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'getSize' )
			->with()
			->andReturn( $text_size_object );

		$image->shouldReceive( 'getSize' )
			->with()
			->andReturn( $image_size_object );

		$text_size_object->shouldReceive( 'getWidth' )
			->with()
			->andReturn( 50 );

		$image_size_object->shouldReceive( 'getWidth' )
			->with()
			->andReturn( 100 );

		$text_size_object->shouldReceive( 'getHeight' )
			->with()
			->andReturn( 50 );

		$image_size_object->shouldReceive( 'getHeight' )
			->with()
			->andReturn( 100 );

		\WP_Mock::expectFilter( 'watermark_my_images_text_position', [ 25, 25 ] );

		$position = $watermarker->get_position( $image, $text );

		$this->assertInstanceOf( Point::class, $position );
		$this->assertConditionsMet();
	}
}

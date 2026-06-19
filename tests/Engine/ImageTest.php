<?php

namespace WatermarkMyImages\Tests\Engine;

use WP_Mock;
use Mockery;
use Exception;
use Badasswp\WPMockTC\WPMockTestCase;

use WatermarkMyImages\Engine\Image;
use WatermarkMyImages\Engine\Watermarker;

use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface as Image_Object;

/**
 * @covers \WatermarkMyImages\Engine\Image::get_image
 * @covers \WatermarkMyImages\Engine\Image::get_imagine
 */
class ImageTest extends WPMockTestCase {
	public Image $image;

	public function setUp(): void {
		parent::setUp();

		Watermarker::$file = __DIR__ . '/sample.png';
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_get_image_passes_and_returns_image_object() {
		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		$imagine = Mockery::mock( Imagine::class )->makePartial();
		$imagine->shouldAllowMockingProtectedMethods();

		$image_object = Mockery::mock( Image_Object::class )->makePartial();
		$image_object->shouldAllowMockingProtectedMethods();

		$imagine->shouldReceive( 'open' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( $image_object );

		$image->shouldReceive( 'get_imagine' )
			->with( Mockery::type( Imagine::class ) )
			->andReturn( $imagine );

		$response = $image->get_image();

		$this->assertInstanceOf( Image_Object::class, $response );
		$this->assertConditionsMet();
	}

	public function test_get_image_catches_and_then_throws_exception() {
		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		$imagine = Mockery::mock( Imagine::class )->makePartial();
		$imagine->shouldAllowMockingProtectedMethods();

		$imagine->shouldReceive( 'open' )
			->with( __DIR__ . '/sample.png' )
			->andThrow(
				new Exception( 'File not found' )
			);

		$image->shouldReceive( 'get_imagine' )
			->with( Mockery::type( Imagine::class ) )
			->andReturn( $imagine );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to open Image Resource, File not found' );

		$response = $image->get_image();

		$this->assertInstanceOf( Image_Object::class, $response );
		$this->assertConditionsMet();
	}

	public function test_get_imagine_returns_imagine_instance() {
		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		$imagine = Mockery::mock( Imagine::class )->makePartial();
		$imagine->shouldAllowMockingProtectedMethods();

		$response = $image->get_imagine( new Imagine() );

		$this->assertInstanceOf( Imagine::class, $response );
		$this->assertConditionsMet();
	}
}

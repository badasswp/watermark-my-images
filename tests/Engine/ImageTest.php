<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Engine\Image;
use WatermarkMyImages\Engine\Watermarker;

use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface as Image_Object;

/**
 * @covers \WatermarkMyImages\Engine\Image::__construct
 * @covers \WatermarkMyImages\Engine\Image::get_image
 */
class ImageTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->image       = new Image();
		Watermarker::$file = __DIR__ . '/sample.png';

		$this->create_mock_image( __DIR__ . '/sample.png' );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_image() {
		$imagine      = Mockery::mock( Imagine::class )->makePartial();
		$image_object = Mockery::mock( Image_Object::class )->makePartial();

		$imagine->shouldReceive( 'open' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( $image_object );

		$image_resource = $this->image->get_image();

		$this->assertInstanceOf( Image_Object::class, $image_resource );
		$this->assertConditionsMet();
	}

	public function test_get_image_throws_exception() {
		$imagine_mock = $this->createMock( Imagine::class );
		$imagine_mock->method( 'open' )
			->will( $this->throwException( new Exception( 'File not found' ) ) );

		\WP_Mock::userFunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userFunction(
			'esc_html',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		$reflection = new \ReflectionClass( $this->image );
		$property   = $reflection->getProperty( 'imagine' );

		$property->setAccessible( true );
		$property->setValue( $this->image, $imagine_mock );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to open Image Resource, File not found' );

		$this->image->get_image();
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

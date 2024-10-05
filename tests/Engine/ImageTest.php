<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
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

		$this->image = new Image();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_image() {
		$imagine      = Mockery::mock( Imagine::class )->makePartial();
		$image_object = Mockery::mock( Image_Object::class )->makePartial();

		$this->create_mock_image( __DIR__ . '/sample.png' );
		Watermarker::$file = __DIR__ . '/sample.png';

		$imagine->shouldReceive( 'open' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( $image_object );

		$image_resource = $this->image->get_image();

		$this->assertInstanceOf( Image_Object::class, $image_resource );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	/*public function test_get_image_throws_exception() {
		$imagine      = Mockery::mock( Imagine::class )->makePartial();
		$image_object = Mockery::mock( Image_Object::class )->makePartial();

		$this->create_mock_image( __DIR__ . '/sample.png' );
		Watermarker::$file = __DIR__ . '/sample.png';

		$imagine->shouldReceive( 'open' )
			->with( __DIR__ . '/sample.png' )
			->andReturn( $image_object );

		\WP_Mock::userfunction(
			'esc_html__',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		\WP_Mock::userfunction(
			'esc_html',
			[
				'times'  => 1,
				'return' => function ( $text, $domain = 'watermark-my-images' ) {
					return $text;
				},
			]
		);

		$image_resource = $this->image->get_image();

		$this->assertInstanceOf( Image_Object::class, $image_resource );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}*/

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

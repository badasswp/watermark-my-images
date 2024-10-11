<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use ReflectionClass;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Engine\Text;
use WatermarkMyImages\Engine\Watermarker;

use Imagine\Gd\Font;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Palette\Color\RGB as TextColor;

/**
 * @covers \WatermarkMyImages\Engine\Text::__construct
 */
class TextTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->text        = new Text();
		Watermarker::$file = __DIR__ . '/sample.png';
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_args_is_set() {
		$this->assertSame(
			$this->text->args,
			[
				'size'       => 60,
				'tx_color'   => '#000',
				'bg_color'   => '#FFF',
				'font'       => 'Arial',
				'label'      => 'WATERMARK',
				'tx_opacity' => 100,
				'bg_opacity' => 0,
			]
		);
	}

	public function test_get_option() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_options' )
			->with()
			->andReturn(
				[
					'size'       => 60,
					'tx_color'   => '#000',
					'bg_color'   => '#FFF',
					'font'       => 'Arial',
					'label'      => 'WATERMARK',
					'tx_opacity' => 100,
					'bg_opacity' => 0,
				]
			);

		$this->assertSame( '60', $text->get_option( 'size' ) );
		$this->assertConditionsMet();
	}

	public function test_get_options_passes() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();
		$text->args = [];

		$options = [
			'size'       => 60,
			'tx_color'   => '#000',
			'bg_color'   => '#FFF',
			'font'       => 'Arial',
			'label'      => 'WATERMARK',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn( $options );

		\WP_Mock::userFunction(
			'wp_parse_args',
			[
				'times'  => 1,
				'return' => function ( $args, $default_args ) {
					return array_merge( $default_args, $args );
				},
			]
		);

		$text->shouldReceive( 'get_size' )
			->with( $options )
			->andReturn( 60 );

		\WP_Mock::expectFilter( 'watermark_my_images_text', $options );

		$options = $text->get_options();

		$this->assertSame(
			$options,
			[
				'size'       => 60,
				'tx_color'   => '#000',
				'bg_color'   => '#FFF',
				'font'       => 'Arial',
				'label'      => 'WATERMARK',
				'tx_opacity' => 100,
				'bg_opacity' => 0,
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_options_passes_with_size_updated() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();
		$text->args = [];

		$options = [
			'size'       => 60,
			'tx_color'   => '#000',
			'bg_color'   => '#FFF',
			'font'       => 'Arial',
			'label'      => 'WATERMARK',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn( $options );

		\WP_Mock::userFunction(
			'wp_parse_args',
			[
				'times'  => 1,
				'return' => function ( $args, $default_args ) {
					return array_merge( $default_args, $args );
				},
			]
		);

		$text->shouldReceive( 'get_size' )
			->with( $options )
			->andReturn( 100 );

		$options_with_size_updated = [
			'size'       => 100,
			'tx_color'   => '#000',
			'bg_color'   => '#FFF',
			'font'       => 'Arial',
			'label'      => 'WATERMARK',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];

		\WP_Mock::expectFilter( 'watermark_my_images_text', $options );

		$options = $text->get_options();

		$this->assertSame(
			$options,
			[
				'size'       => 100,
				'tx_color'   => '#000',
				'bg_color'   => '#FFF',
				'font'       => 'Arial',
				'label'      => 'WATERMARK',
				'tx_opacity' => 100,
				'bg_opacity' => 0,
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_options_passes_with_filter_applied() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();
		$text->args = [];

		$options = [
			'size'       => 60,
			'tx_color'   => '#000',
			'bg_color'   => '#FFF',
			'font'       => 'Arial',
			'label'      => 'WATERMARK',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];

		$filtered_options = [
			'size'       => 75,
			'tx_color'   => '#FFF',
			'bg_color'   => '#F00',
			'font'       => 'Arial',
			'label'      => 'Copyright',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];

		\WP_Mock::userFunction( 'get_option' )
			->with( 'watermark_my_images', [] )
			->andReturn( $options );

		\WP_Mock::userFunction(
			'wp_parse_args',
			[
				'times'  => 1,
				'return' => function ( $args, $default_args ) {
					return array_merge( $default_args, $args );
				},
			]
		);

		\WP_Mock::onFilter( 'watermark_my_images_text' )
			->with( $options )
			->reply( $filtered_options );

		$text->shouldReceive( 'get_size' )
			->with( $filtered_options )
			->andReturn( 60 );

		$options = $text->get_options();

		$this->assertSame(
			$options,
			[
				'size'       => 60,
				'tx_color'   => '#FFF',
				'bg_color'   => '#F00',
				'font'       => 'Arial',
				'label'      => 'Copyright',
				'tx_opacity' => 100,
				'bg_opacity' => 0,
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_font_passes() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$tx_color_obj = new TextColor( $rgb, [ 255, 255, 255 ], 100 );
		$tx_color     = Mockery::mock( $tx_color_obj )->makePartial();

		$text->shouldReceive( 'get_option' )
			->with( 'tx_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_opacity' )
			->andReturn( '100' );

		$rgb->shouldReceive( 'color' )
			->with( 'tx_color', 100 )
			->andReturn( $tx_color );

		$text->shouldReceive( 'get_option' )
			->with( 'size' )
			->andReturn( 60 );

		$text->shouldReceive( 'get_font_url' )
			->with()
			->andReturn( '/var/www/wp-content/plugins/watermark-my-images/fonts/Arial.otf' );

		$tx_font = $text->get_font();

		$this->assertInstanceOf( Font::class, $tx_font );
		$this->assertConditionsMet();
	}

	public function test_get_font_url() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$reflect = new ReflectionClass( $this->text );

		$text->shouldReceive( 'get_option' )
			->with( 'font' )
			->andReturn( 'Arial' );

		\WP_Mock::userFunction( 'plugin_dir_path' )
			->with( pathinfo( $reflect->getFileName(), PATHINFO_DIRNAME ) )
			->andReturn( '/var/www/wp-content/uploads/watermark-my-images/inc/Engine' );

		$font = $text->get_font_url();

		$this->assertSame( $font, '/var/www/wp-content/uploads/watermark-my-images/inc/Engine/../fonts/Arial.otf' );
		$this->assertConditionsMet();
	}

	/*public function test_get_font_throws_exception() {
		$rgb_mock = $this->createMock( RGB::class );
		$rgb_mock->method( 'color' )
			->will( $this->throwException( new Exception( 'Invalid RGB Color value' ) ) );

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

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_option' )
			->with( 'tx_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_opacity' )
			->andReturn( '100' );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to create Text color, Invalid RGB Color value' );

		$text->get_font();
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

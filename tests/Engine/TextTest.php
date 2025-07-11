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
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\BoxInterface;
use Imagine\Image\FontInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\PointInterface;
use Imagine\Draw\DrawerInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * @covers \WatermarkMyImages\Engine\Text::__construct
 * @covers \WatermarkMyImages\Engine\Text::get_option
 * @covers \WatermarkMyImages\Engine\Text::get_options
 * @covers \WatermarkMyImages\Engine\Text::get_font
 * @covers \WatermarkMyImages\Engine\Text::get_font_url
 * @covers \WatermarkMyImages\Engine\Text::get_size
 * @covers \WatermarkMyImages\Engine\Text::get_text_length
 * @covers \WatermarkMyImages\Engine\Text::get_text
 */
class TextTest extends TestCase {
	public Text $text;

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
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_font_passes() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$color = Mockery::mock( ColorInterface::class )->makePartial();
		$color->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_rgb' )
			->with( Mockery::type( RGB::class ) )
			->andReturn( $rgb );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_opacity' )
			->andReturn( '100' );

		$rgb->shouldReceive( 'color' )
			->with( '#FFF', 100 )
			->andReturn( $color );

		$text->shouldReceive( 'get_font_url' )
			->with()
			->andReturn( '/var/www/wp-content/plugins/watermark-my-images/fonts/Arial.otf' );

		$text->shouldReceive( 'get_option' )
			->with( 'size' )
			->andReturn( 60 );

		$tx_font = $text->get_font();

		$this->assertInstanceOf( Font::class, $tx_font );
		$this->assertConditionsMet();
	}

	public function test_get_font_catches_and_then_throws_exception() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_rgb' )
			->with( Mockery::type( RGB::class ) )
			->andReturn( $rgb );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'tx_opacity' )
			->andReturn( '100' );

		$rgb->shouldReceive( 'color' )
			->with( '#FFF', 100 )
			->andThrow(
				new \Exception( 'Error: Unable to parse RGB color' )
			);

		\WP_Mock::userFunction( 'esc_html__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to create Text color, Error: Unable to parse RGB color' );

		$text->get_font();

		$this->assertConditionsMet();
	}

	public function test_get_text_catches_background_exception_and_throws_it() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_rgb' )
			->with( Mockery::type( RGB::class ) )
			->andReturn( $rgb );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_opacity' )
			->andReturn( '100' );

		$rgb->shouldReceive( 'color' )
			->with( '#FFF', 100 )
			->andThrow(
				new \Exception( 'Error: Unable to parse Background color' )
			);

		\WP_Mock::userFunction( 'esc_html__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to create Background color, Error: Unable to parse Background color' );

		$text->get_text();

		$this->assertConditionsMet();
	}

	/*public function test_get_text_catches_textbox_exception_and_throws_it() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$imagine = Mockery::mock( Imagine::class )->makePartial();
		$imagine->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'esc_html__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$bg_color = new TextColor( $rgb, [ 255, 255, 255 ], 100 );

		// Now, 1st stage...
		$text->shouldReceive( 'get_rgb' )
			->with( Mockery::type( RGB::class ) )
			->andReturn( $rgb );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_opacity' )
			->andReturn( 100 );

		$rgb->shouldReceive( 'color' )
			->with( '#FFF', 100 )
			->andReturn( $bg_color );

		// Now, 2nd stage...
		$text->shouldReceive( 'get_imagine' )
			->with( Mockery::type( Imagine::class ) )
			->andReturn( $imagine );

		$box = new Box( 100, 200 );

		$text->shouldReceive( 'get_text_box' )
			->andReturn( $box );

		$imagine->shouldReceive( 'create' )
			->with( $box, $bg_color )
			->andThrow(
				new \Exception( 'Error: Text Box color' )
			);

		$this->expectException( \Error::class );
		$this->expectExceptionMessage( 'Unable to create Text Box, Error: Text Box color' );

		$text->get_text();

		$this->assertConditionsMet();
	}

	public function test_get_text_catches_draw_exception_and_throws_it() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$rgb = Mockery::mock( RGB::class )->makePartial();
		$rgb->shouldAllowMockingProtectedMethods();

		$box = Mockery::mock( Box::class )->makePartial();
		$box->shouldAllowMockingProtectedMethods();

		$imagine = Mockery::mock( Imagine::class )->makePartial();
		$imagine->shouldAllowMockingProtectedMethods();

		$font = Mockery::mock( FontInterface::class )->makePartial();
		$font->shouldAllowMockingProtectedMethods();

		$bg_color = Mockery::mock( ColorInterface::class )->makePartial();
		$bg_color->shouldAllowMockingProtectedMethods();

		$text_box = Mockery::mock( ImageInterface::class )->makePartial();
		$text_box->shouldAllowMockingProtectedMethods();

		$drawer = Mockery::mock( DrawerInterface::class )->makePartial();
		$drawer->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'esc_html__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		// Now, 1st stage...
		$text->shouldReceive( 'get_rgb' )
			->with( Mockery::type( RGB::class ) )
			->andReturn( $rgb );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_color' )
			->andReturn( '#FFF' );

		$text->shouldReceive( 'get_option' )
			->with( 'bg_opacity' )
			->andReturn( '100' );

		$rgb->shouldReceive( 'color' )
			->with( '#FFF', '100' )
			->andReturn( $bg_color );

		// Now, 2nd stage...
		$text->shouldReceive( 'get_imagine' )
			->with( Mockery::type( Imagine::class ) )
			->andReturn( $imagine );

		$text->shouldReceive( 'get_text_length' )
			->andReturn( '200' );

		$text->shouldReceive( 'get_text_box' )
			->andReturn( $box );

		$text->shouldReceive( 'get_option' )
			->with( 'size' )
			->andReturn( '100' );

		$imagine->shouldReceive( 'create' )
			->with( $box, $bg_color )
			->andReturn( $text_box );

		// Now, 3rd stage...
		$text_box->shouldReceive( 'draw' )
			->andReturn( $drawer );

		$text->shouldReceive( 'get_option' )
			->with( 'label' )
			->andReturn( 'WATERMARK' );

		$text->shouldReceive( 'get_font' )
			->andReturn( $font );

		$drawer->shouldReceive( 'text' )
			->with(
				'WATERMARK',
				$font,
				Mockery::type( PointInterface::class )
			)
			->andThrow(
				new \Exception( 'Error: Drawing is disabled..' )
			);

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( '' );

		$text->get_text();

		$this->assertConditionsMet();
	}*/

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

	public function test_get_size_passes() {
		$options = [
			'size' => 50,
		];

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$this->create_mock_image( __DIR__ . '/sample.png' );

		$size = $text->get_size( $options );

		$this->assertSame( $size, 50 );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_size_passes_when_no_size_is_set() {
		$options = '';

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$this->create_mock_image( __DIR__ . '/sample.png' );

		$size = $text->get_size( $options );

		$this->assertSame( $size, 50 );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_size_passes_when_size_is_zero() {
		$options = [
			'size' => 0,
		];

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$this->create_mock_image( __DIR__ . '/sample.png' );

		$size = $text->get_size( $options );

		$this->assertSame( $size, 50 );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_size_passes_when_size_is_non_numeric() {
		$options = [
			'size' => 'john doe',
		];

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$this->create_mock_image( __DIR__ . '/sample.png' );

		$size = $text->get_size( $options );

		$this->assertSame( $size, 50 );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_size_returns_floor() {
		$options = [
			'size' => 40,
		];

		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$this->create_mock_image( __DIR__ . '/sample.png' );

		$size = $text->get_size( $options );

		$this->assertSame( $size, 40 );
		$this->assertConditionsMet();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_get_text_length() {
		$text = Mockery::mock( Text::class )->makePartial();
		$text->shouldAllowMockingProtectedMethods();

		$text->shouldReceive( 'get_option' )
			->with( 'size' )
			->andReturn( 100 );

		$text->shouldReceive( 'get_option' )
			->with( 'label' )
			->andReturn( 'WATERMARK' );

		$text->shouldReceive( 'get_char_ratio' )
			->andReturnUsing(
				function ( $char ) {
					$ratio = 1;

					switch ( $char ) {
						case 'A':
						case 'G':
							$ratio = 0.917;
							break;

						case 'B':
						case 'H':
						case 'N':
						case 'S':
						case 'T':
						case 'U':
						case 'Z':
							$ratio = 0.8;
							break;

						case 'C':
						case 'Y':
							$ratio = 0.9;
							break;

						case 'D':
						case 'R':
						case 'X':
							$ratio = 0.833;
							break;

						case 'E':
							$ratio = 0.7;
							break;

						case 'F':
						case 'L':
							$ratio = 0.667;
							break;

						case 'J':
							$ratio = 0.6;
							break;

						case 'K':
							$ratio = 0.817;
							break;

						case 'O':
						case 'Q':
							$ratio = 0.967;
							break;

						case 'P':
							$ratio = 0.75;
							break;

						case 'V':
							$ratio = 0.867;
							break;

						case 'W':
							$ratio = 1.283;
							break;

						case 'I':
							$ratio = 0.133;
							break;

						default:
							$ratio = 1;
							break;
					}

					return $ratio;
				}
			);

		$length = $text->get_text_length();

		$this->assertSame( $length, 890.0 );
		$this->assertConditionsMet();
	}

	public function create_mock_image( $image_file_name ) {
		// Create a blank image.
		$width  = 850;
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

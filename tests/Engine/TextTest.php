<?php

namespace WatermarkMyImages\Tests\Engine;

use Mockery;
use Exception;
use ReflectionClass;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Engine\Text;
use WatermarkMyImages\Engine\Watermarker;

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
				'times' => 1,
				'return' => function( $args, $default ) {
					return array_merge( $default, $args );
				}
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
				'times' => 1,
				'return' => function( $args, $default ) {
					return array_merge( $default, $args );
				}
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

		\WP_Mock::expectFilter(
			'watermark_my_images_text',
			$options_with_size_updated
		);

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
}

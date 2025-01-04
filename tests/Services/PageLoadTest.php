<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use DOMDocument;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Engine\Watermarker;
use WatermarkMyImages\Services\PageLoad;

/**
 * @covers \WatermarkMyImages\Services\PageLoad::__construct
 * @covers \WatermarkMyImages\Services\PageLoad::register
 * @covers \WatermarkMyImages\Services\PageLoad::register_wp_get_attachment_image
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers wmig_get_settings
 */
class PageLoadTest extends TestCase {
	public PageLoad $page_load;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->page_load = new PageLoad();

		$this->create_mock_image( __DIR__ . '/sample.png' );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_register() {
		\WP_Mock::expectFilterAdded( 'wp_get_attachment_image', [ $this->page_load, 'register_wp_get_attachment_image' ], 10, 5 );

		$this->page_load->register();

		$this->assertConditionsMet();
	}

	public function test_register_wp_get_attachment_image_bails_out_on_empty() {
		$html = $this->page_load->register_wp_get_attachment_image(
			'',
			1,
			[],
			false,
			[]
		);

		$this->assertSame( $html, '' );
		$this->assertConditionsMet();
	}

	public function test_register_wp_get_attachment_image_bails_out_if_options_is_not_enabled() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'page_load' => false,
				]
			);

		$html = $this->page_load->register_wp_get_attachment_image(
			'<p><img src="sample.jpeg"/></p>',
			1,
			[],
			false,
			[]
		);

		$this->assertSame( $html, '<p><img src="sample.jpeg"/></p>' );
		$this->assertConditionsMet();
	}

	public function test_register_wp_get_attachment_image_passes_correctly() {
		$page_load = Mockery::mock( PageLoad::class )->makePartial();
		$page_load->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn(
				[
					'page_load' => true,
				]
			);

		$page_load->shouldReceive( 'get_watermark_html' )
			->with( '<p><img src="https://example.com/wp-content/uploads/sample.jpeg"/></p>', 1 )
			->andReturnUsing(
				function ( $arg ) {
					return str_replace( '.jpeg', '-watermark.jpeg', $arg );
				}
			);

		\WP_Mock::expectFilter(
			'watermark_my_images_attachment_html',
			'<p><img src="https://example.com/wp-content/uploads/sample-watermark.jpeg"/></p>',
			1
		);

		$html = $page_load->register_wp_get_attachment_image(
			'<p><img src="https://example.com/wp-content/uploads/sample.jpeg"/></p>',
			1,
			[],
			false,
			[]
		);

		$this->assertConditionsMet();
		$this->assertSame(
			'<p><img src="https://example.com/wp-content/uploads/sample-watermark.jpeg"/></p>',
			$html
		);
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

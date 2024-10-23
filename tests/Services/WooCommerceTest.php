<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\WooCommerce;
use WatermarkMyImages\Abstracts\Service;

/**
 * @covers \WatermarkMyImages\Services\WooCommerce::__construct
 * @covers \WatermarkMyImages\Services\WooCommerce::register
 * @covers \WatermarkMyImages\Services\WooCommerce::add_watermark_on_get_image
 * @covers \WatermarkMyImages\Services\WooCommerce::get_image_html
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 */
class WooCommerceTest extends TestCase {
	public WooCommerce $woocommerce;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->woocommerce = new WooCommerce();

		$this->create_mock_image( __DIR__ . '/sample.png' );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();

		$this->destroy_mock_image( __DIR__ . '/sample.png' );
	}

	public function test_register() {
		\WP_Mock::expectFilterAdded( 'woocommerce_product_get_image', [ $this->woocommerce, 'add_watermark_on_get_image' ], 10, 5 );

		$this->woocommerce->register();

		$this->assertConditionsMet();
	}

	public function test_add_watermark_on_get_image_bails_on_image_existence() {
		$product = Mockery::mock( \WC_Product::class )->makePartial();
		$product->shouldAllowMockingProtectedMethods();

		$product->shouldReceive( 'get_image_id' )
			->andReturn( 1 );

		\WP_Mock::userFunction( 'get_post_meta' )
			->once()
			->with( 1, 'watermark_my_images', true )
			->andReturn(
				[
					'abs' => __DIR__ . '/sample.png',
					'rel' => 'https://example.com/wp-content/uploads/2024/10/sample-1-watermark-my-images.jpg',
				]
			);

		$image = $this->woocommerce->add_watermark_on_get_image(
			'<img src="">',
			$product,
			'woocommerce_thumbnail',
			[],
			true
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

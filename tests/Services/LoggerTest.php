<?php

namespace WatermarkMyImages\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Services\Logger;
use WatermarkMyImages\Abstracts\Service;

require_once __DIR__ . '/../../inc/Helpers/functions.php';

/**
 * @covers \WatermarkMyImages\Services\Logger::__construct
 * @covers \WatermarkMyImages\Services\Logger::register
 * @covers \WatermarkMyImages\Services\Logger::log_watermark_errors
 * @covers wmig_set_settings
 */
class LoggerTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->logger = new Logger();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_add_image', [ $this->logger, 'log_watermark_errors' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_page_load', [ $this->logger, 'log_watermark_errors' ], 10, 3 );
		\WP_Mock::expectActionAdded( 'watermark_my_images_on_woo_product_get_image', [ $this->logger, 'log_watermark_errors' ], 10, 3 );

		$this->logger->register();

		$this->assertConditionsMet();
	}

	public function test_log_watermark_errors_bails_out_early() {
		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn( [ 'logs' => false ] );

		$url = 'image-1-watermark-my-images.jpg';

		$this->logger->log_watermark_errors( $url, [], 1 );

		$this->assertConditionsMet();
	}

	public function test_log_watermark_errors_bails_out_if_it_is_not_wp_error() {
		$url = 'image-1-watermark-my-images.jpg';

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn( [ 'logs' => true ] );

		\WP_Mock::userFunction( 'is_wp_error' )
			->once()
			->with( 'image-1-watermark-my-images.jpg' )
			->andReturn( false );

		$this->logger->log_watermark_errors( $url, [], 1 );

		$this->assertConditionsMet();
	}

	public function test_log_watermark_errors_passes() {
		$url = Mockery::mock( '\WP_Error' )->makePartial();
		$url->shouldAllowMockingProtectedMethods();

		$url->shouldReceive( 'get_error_message' )
			->andReturn( 'Unable to create Image Object.' );

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'watermark_my_images', [] )
			->andReturn( [ 'logs' => true ] );

		\WP_Mock::userFunction(
			'is_wp_error',
			[
				'times'  => 1,
				'return' => function ( $url ) {
					return $url instanceof \WP_Error;
				},
			]
		);

		\WP_Mock::userFunction( 'wp_insert_post' )
			->once()
			->with(
				[
					'post_type'    => 'wmi_error',
					'post_title'   => 'Watermark error log, ID - 1',
					'post_content' => 'Unable to create Image Object.',
					'post_status'  => 'publish',
				]
			)
			->andReturn( 100 );

		$this->logger->log_watermark_errors( $url, [], 1 );

		$this->assertConditionsMet();
	}
}

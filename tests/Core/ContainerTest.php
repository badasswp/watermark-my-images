<?php

namespace WatermarkMyImages\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Core\Container;
use WatermarkMyImages\Abstracts\Service;

use WatermarkMyImages\Services\Admin;
use WatermarkMyImages\Services\Attachment;
use WatermarkMyImages\Services\Boot;
use WatermarkMyImages\Services\Logger;
use WatermarkMyImages\Services\MetaData;
use WatermarkMyImages\Services\PageLoad;
use WatermarkMyImages\Services\WooCommerce;

/**
 * @covers \WatermarkMyImages\Core\Container::__construct
 * @covers \WatermarkMyImages\Core\Container::register
 * @covers \WatermarkMyImages\Abstracts\Service::__construct
 * @covers \WatermarkMyImages\Abstracts\Service::get_instance
 * @covers \WatermarkMyImages\Engine\Watermarker::__construct
 * @covers \WatermarkMyImages\Services\Admin::register
 * @covers \WatermarkMyImages\Services\Attachment::register
 * @covers \WatermarkMyImages\Services\Boot::register
 * @covers \WatermarkMyImages\Services\Logger::register
 * @covers \WatermarkMyImages\Services\MetaData::register
 * @covers \WatermarkMyImages\Services\PageLoad::register
 * @covers \WatermarkMyImages\Services\WooCommerce::register
 */
class ContainerTest extends TestCase {
	public Container $container;

	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_container_contains_required_services() {
		$this->container = new Container();

		$this->assertTrue( in_array( Admin::class, Container::$services, true ) );
		$this->assertTrue( in_array( Attachment::class, Container::$services, true ) );
		$this->assertTrue( in_array( Boot::class, Container::$services, true ) );
		$this->assertTrue( in_array( Logger::class, Container::$services, true ) );
		$this->assertTrue( in_array( MetaData::class, Container::$services, true ) );
		$this->assertTrue( in_array( PageLoad::class, Container::$services, true ) );
		$this->assertTrue( in_array( WooCommerce::class, Container::$services, true ) );
	}

	public function test_register() {
		$container = new Container();

		/**
		 * Hack around unset Service::$instances.
		 *
		 * We create instances of services so we can
		 * have a populated version of the Service abstraction's instances.
		 */
		foreach ( Container::$services as $service ) {
			$service::get_instance();
		}

		\WP_Mock::expectActionAdded(
			'admin_init',
			[
				Service::$instances[ Admin::class ],
				'register_options_init',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_menu',
			[
				Service::$instances[ Admin::class ],
				'register_options_menu',
			]
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			[
				Service::$instances[ Admin::class ],
				'register_options_styles',
			]
		);

		\WP_Mock::expectActionAdded(
			'add_attachment',
			[
				Service::$instances[ Attachment::class ],
				'add_watermark_on_add_attachment',
			],
			10,
			1
		);

		\WP_Mock::expectActionAdded(
			'delete_attachment',
			[
				Service::$instances[ Attachment::class ],
				'remove_watermark_on_attachment_delete',
			],
			10,
			1
		);

		\WP_Mock::expectFilterAdded(
			'attachment_fields_to_edit',
			[
				Service::$instances[ Attachment::class ],
				'add_watermark_attachment_fields',
			],
			10,
			2
		);

		\WP_Mock::expectFilterAdded(
			'wp_generate_attachment_metadata',
			[
				Service::$instances[ Attachment::class ],
				'add_watermark_to_metadata',
			],
			10,
			3
		);

		\WP_Mock::expectFilterAdded(
			'wp_prepare_attachment_for_js',
			[
				Service::$instances[ Attachment::class ],
				'show_watermark_images_on_wp_media_modal',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'init',
			[
				Service::$instances[ Boot::class ],
				'register_translation',
			],
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_add_image',
			[
				Service::$instances[ Logger::class ],
				'log_watermark_errors',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_page_load',
			[
				Service::$instances[ Logger::class ],
				'log_watermark_errors',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_woo_product_get_image',
			[
				Service::$instances[ Logger::class ],
				'log_watermark_errors',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_add_image',
			[
				Service::$instances[ MetaData::class ],
				'add_watermark_metadata',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_page_load',
			[
				Service::$instances[ MetaData::class ],
				'add_watermark_metadata',
			],
			10,
			3
		);

		\WP_Mock::expectActionAdded(
			'watermark_my_images_on_woo_product_get_image',
			[
				Service::$instances[ MetaData::class ],
				'add_watermark_metadata',
			],
			10,
			3
		);

		\WP_Mock::expectFilterAdded(
			'wp_get_attachment_image',
			[
				Service::$instances[ PageLoad::class ],
				'register_wp_get_attachment_image',
			],
			10,
			5
		);

		\WP_Mock::expectFilterAdded(
			'woocommerce_product_get_image',
			[
				Service::$instances[ WooCommerce::class ],
				'add_watermark_on_get_image',
			],
			10,
			5
		);

		\WP_Mock::expectFilterAdded(
			'woocommerce_single_product_image_thumbnail_html',
			[
				Service::$instances[ WooCommerce::class ],
				'add_watermark_to_product_gallery_image',
			],
			10,
			2
		);

		$container->register();

		$this->assertConditionsMet();
	}
}

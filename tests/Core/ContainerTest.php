<?php

namespace WatermarkMyImages\Tests\Core;

use Mockery;
use WP_Mock\Tools\TestCase;

use WatermarkMyImages\Core\Container;
use WatermarkMyImages\Services\Admin;
use WatermarkMyImages\Services\Attachment;
use WatermarkMyImages\Services\Boot;
use WatermarkMyImages\Services\Logger;
use WatermarkMyImages\Services\MetaData;
use WatermarkMyImages\Services\PageLoad;
use WatermarkMyImages\Services\WooCommerce;

/**
 * @covers \WatermarkMyImages\Core\Container::__construct
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
}

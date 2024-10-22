<?php

namespace WatermarkMyImages\Tests\Interfaces;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Interfaces\Registrable;

/**
 * @covers \WatermarkMyImages\Interfaces\Registrable::register
 */
class RegistrableTest extends TestCase {
	public Registrable $registrable;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->registrable = $this->getMockForAbstractClass( Registrable::class );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		$this->registrable->expects( $this->once() )
			->method( 'register' );

		$this->registrable->register();

		$this->assertConditionsMet();
	}
}

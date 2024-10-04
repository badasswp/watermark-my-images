<?php

namespace WatermarkMyImages\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Form;

/**
 * @covers \WatermarkMyImages\Admin\Form::__construct
 * @covers \WatermarkMyImages\Admin\Form::get_options
 */
class FormTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->form = Mockery::mock( Form::class )->makePartial();
		$this->form->shouldAllowMockingProtectedMethods();

		/*$this->form = new Form(
			[
				'page' => [
					'title'   => 'WordPress Plugin',
					'summary' => 'Lorem ipsum dolor sit amet...',
				],
			]
		);*/
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_options() {
		$this->form->shouldReceive( 'get_form' )
			->andReturn( '' );

		$options = $this->form->get_options();

		$this->assertSame(
			$options,
			[
				'title'   => '',
				'summary' => '',
				'form'    => '',
			],
		);
	}
}

<?php

namespace WatermarkMyImages\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Form;

/**
 * @covers \WatermarkMyImages\Admin\Form::get_options
 */
class FormTest extends TestCase {
	public Form $form;

	public function setUp(): void {
		\WP_Mock::setUp();

		$options = [
			'page' => [
				'title'   => 'Plugin Title',
				'summary' => 'Plugin Summary',
			],
		];

		$this->form = $this->getMockBuilder( Form::class )
			->setConstructorArgs( [ $options ] )
			->onlyMethods(
				[
					'get_form',
					'get_form_action',
					'get_form_notice',
					'get_form_main',
					'get_form_submit',
				]
			)
			->getMock();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_options() {
		$this->form->expects( $this->once() )
			->method( 'get_form' )
			->willReturn( 'Plugin Form' );

		$this->assertSame(
			$this->form->get_options(),
			[
				'title'   => 'Plugin Title',
				'summary' => 'Plugin Summary',
				'form'    => 'Plugin Form',
			]
		);
	}
}

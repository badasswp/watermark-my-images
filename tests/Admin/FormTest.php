<?php

namespace WatermarkMyImages\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Form;

/**
 * @covers \WatermarkMyImages\Admin\Form::get_options
 * @covers \WatermarkMyImages\Admin\Form::get_form
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

	public function test_get_form() {
		$form = Mockery::mock( Form::class )->makePartial();
		$form->shouldAllowMockingProtectedMethods();

		$form->shouldReceive( 'get_form_action' )
			->andReturn( 'https://example.com' );

		$form->shouldReceive( 'get_form_notice' )
			->andReturn( 'Form Notice' );

		$form->shouldReceive( 'get_form_main' )
			->andReturn( 'Form Main' );

		$form->shouldReceive( 'get_form_submit' )
			->andReturn( 'Form Submit' );

		$plugin_form = $form->get_form();

		$this->assertSame(
			'<form class="badasswp-form" method="POST" action="https://example.com">
				Form Notice
				<div class="badasswp-form-main">Form Main</div>
				<div class="badasswp-form-submit">Form Submit</div>
			</form>',
			$plugin_form
		);
	}
}

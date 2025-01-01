<?php

namespace WatermarkMyImages\Tests\Admin;

use Mockery;
use WP_Mock\Tools\TestCase;
use WatermarkMyImages\Admin\Form;

/**
 * @covers \WatermarkMyImages\Admin\Form::__construct
 * @covers \WatermarkMyImages\Admin\Form::get_options
 * @covers \WatermarkMyImages\Admin\Form::get_form
 * @covers \WatermarkMyImages\Admin\Form::get_form_action
 * @covers \WatermarkMyImages\Admin\Form::get_form_main
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

	public function test_get_form_action() {
		$form = Mockery::mock( Form::class )->makePartial();
		$form->shouldAllowMockingProtectedMethods();

		$_SERVER['REQUEST_URI'] = 'https://example.com/\/';

		\WP_Mock::userFunction( 'esc_url' )
			->andReturnUsing(
				function ( $arg ) {
					return rtrim( filter_var( $arg, FILTER_SANITIZE_URL ), '/' );
				}
			);

		\WP_Mock::userFunction( 'sanitize_text_field' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'wp_unslash' )
			->andReturnUsing(
				function ( $arg ) {
					return stripslashes( $arg );
				}
			);

		$form_action = $form->get_form_action();

		$this->assertSame( 'https://example.com', $form_action );
	}

	public function test_get_form_main() {
		$form = Mockery::mock( Form::class )->makePartial();
		$form->shouldAllowMockingProtectedMethods();

		$reflection = new \ReflectionClass( $form );
		$property   = $reflection->getProperty( 'options' );
		$property->setAccessible( true );
		$property->setValue(
			$form,
			[
				'fields' => [
					'form_group_1',
					'form_group_2',
					'form_group_3',
				],
			]
		);

		\WP_Mock::expectFilter(
			'watermark_my_images_form_fields',
			[
				'form_group_1',
				'form_group_2',
				'form_group_3',
			]
		);

		$form->shouldReceive( 'get_form_group' )
			->times( 3 )
			->andReturnUsing(
				function ( $arg ) {
					return sprintf(
						'<section>%s</section>',
						$arg
					);
				}
			);

		$form_main = $form->get_form_main();

		$this->assertSame(
			'<section>form_group_1</section><section>form_group_2</section><section>form_group_3</section>',
			$form_main
		);
	}
}

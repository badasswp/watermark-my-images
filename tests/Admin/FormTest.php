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
 * @covers \WatermarkMyImages\Admin\Form::get_form_group
 */
class FormTest extends TestCase {
	public Form $form;

	public function setUp(): void {
		\WP_Mock::setUp();

		$this->form = Mockery::mock( Form::class )->makePartial();
		$this->form->shouldAllowMockingProtectedMethods();

		$reflection = new \ReflectionClass( $this->form );
		$property   = $reflection->getProperty( 'options' );
		$property->setAccessible( true );
		$property->setValue(
			$this->form,
			[
				'page'   => [
					'title'   => 'Plugin Title',
					'summary' => 'Plugin Summary',
				],
				'fields' => [
					'form_group_1',
					'form_group_2',
					'form_group_3',
				],
			]
		);
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_get_options() {
		$this->form->shouldReceive( 'get_form' )
			->andReturn( 'Plugin Form' );

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
		$this->form->shouldReceive( 'get_form_action' )
			->andReturn( 'https://example.com' );

		$this->form->shouldReceive( 'get_form_notice' )
			->andReturn( 'Form Notice' );

		$this->form->shouldReceive( 'get_form_main' )
			->andReturn( 'Form Main' );

		$this->form->shouldReceive( 'get_form_submit' )
			->andReturn( 'Form Submit' );

		$plugin_form = $this->form->get_form();

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

		$form_action = $this->form->get_form_action();

		$this->assertSame( 'https://example.com', $form_action );
	}

	public function test_get_form_main() {
		\WP_Mock::expectFilter(
			'watermark_my_images_form_fields',
			[
				'form_group_1',
				'form_group_2',
				'form_group_3',
			]
		);

		$this->form->shouldReceive( 'get_form_group' )
			->times( 3 )
			->andReturnUsing(
				function ( $arg ) {
					return sprintf(
						'<section>%s</section>',
						$arg
					);
				}
			);

		$form_main = $this->form->get_form_main();

		$this->assertSame(
			'<section>form_group_1</section><section>form_group_2</section><section>form_group_3</section>',
			$form_main
		);
	}

	public function test_get_form_group() {
		$this->form->shouldReceive( 'get_form_group_body' )
			->once()
			->andReturn( 'Form Group Body' );

		$form_group = $this->form->get_form_group(
			[
				'heading'  => 'Form Heading',
				'controls' => [],
			]
		);

		$this->assertSame(
			'<div class="badasswp-form-group"><div class="badasswp-form-group-heading">Form Heading</div><div class="badasswp-form-group-body">Form Group Body</div></div>',
			$form_group
		);
	}
}

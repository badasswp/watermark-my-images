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
 * @covers \WatermarkMyImages\Admin\Form::get_form_group_body
 * @covers \WatermarkMyImages\Admin\Form::get_setting
 * @covers \WatermarkMyImages\Admin\Form::get_form_control
 * @covers \WatermarkMyImages\Admin\Form::get_text_control
 * @covers \WatermarkMyImages\Admin\Form::get_checkbox_control
 * @covers \WatermarkMyImages\Admin\Form::get_select_control
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
					'slug'    => 'plugin-slug',
					'option'  => 'plugin_option',
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

	public function test_get_form_group_body() {
		$this->form->shouldReceive( 'get_form_control' )
			->times( 1 )
			->with(
				[
					'control'     => 'text',
					'placeholder' => 'Placeholder',
					'label'       => 'Label',
					'summary'     => 'Summary',
				],
				'name'
			)
			->andReturn( 'Form Control' );

		$form_group_body = $this->form->get_form_group_body(
			[
				'name' => [
					'control'     => 'text',
					'placeholder' => 'Placeholder',
					'label'       => 'Label',
					'summary'     => 'Summary',
				],
			]
		);

		$this->assertSame(
			'<p class="badasswp-form-group-block">
					<label>Label</label>
					Form Control
					<em>Summary</em>
				</p>',
			$form_group_body
		);
	}

	public function test_get_setting() {
		\WP_Mock::userFunction( 'get_option' )
			->with( 'plugin_option', [] )
			->andReturn(
				[
					'option_1' => 'Option 1',
					'option_2' => 'Option 2',
					'option_3' => 'Option 3',
				]
			);

		$this->assertSame( 'Option 1', $this->form->get_setting( 'option_1' ) );
		$this->assertSame( 'Option 2', $this->form->get_setting( 'option_2' ) );
		$this->assertSame( 'Option 3', $this->form->get_setting( 'option_3' ) );
	}

	public function test_get_form_control_returns_text_control() {
		$this->form->shouldReceive( 'get_text_control' )
			->once()
			->with(
				[
					'control'     => 'text',
					'placeholder' => 'Text Placeholder',
					'label'       => 'Text Label',
					'summary'     => 'Text Summary',
				],
				'text_name'
			)
			->andReturn( 'Text Control' );

		$control = $this->form->get_form_control(
			[
				'control'     => 'text',
				'placeholder' => 'Text Placeholder',
				'label'       => 'Text Label',
				'summary'     => 'Text Summary',
			],
			'text_name'
		);

		$this->assertSame( 'Text Control', $control );
	}

	public function test_get_form_control_returns_select_control() {
		$this->form->shouldReceive( 'get_select_control' )
			->once()
			->with(
				[
					'control'     => 'select',
					'placeholder' => 'Select Placeholder',
					'label'       => 'Select Label',
					'summary'     => 'Select Summary',
				],
				'select_name'
			)
			->andReturn( 'Select Control' );

		$control = $this->form->get_form_control(
			[
				'control'     => 'select',
				'placeholder' => 'Select Placeholder',
				'label'       => 'Select Label',
				'summary'     => 'Select Summary',
			],
			'select_name'
		);

		$this->assertSame( 'Select Control', $control );
	}

	public function test_get_form_control_returns_checkbox_control() {
		$this->form->shouldReceive( 'get_checkbox_control' )
			->once()
			->with(
				[
					'control'     => 'checkbox',
					'placeholder' => 'Checkbox Placeholder',
					'label'       => 'Checkbox Label',
					'summary'     => 'Checkbox Summary',
				],
				'checkbox_name'
			)
			->andReturn( 'Checkbox Control' );

		$control = $this->form->get_form_control(
			[
				'control'     => 'checkbox',
				'placeholder' => 'Checkbox Placeholder',
				'label'       => 'Checkbox Label',
				'summary'     => 'Checkbox Summary',
			],
			'checkbox_name'
		);

		$this->assertSame( 'Checkbox Control', $control );
	}

	public function test_get_text_control() {
		$this->form->shouldReceive( 'get_setting' )
			->times( 1 )->with( 'text_name' )->andReturn( 'Text Name' );

		$control = $this->form->get_text_control(
			[
				'control'     => 'text',
				'placeholder' => 'Text Placeholder',
				'label'       => 'Text Label',
				'summary'     => 'Text Summary',
			],
			'text_name'
		);

		$this->assertSame(
			'<input type="text" placeholder="Text Placeholder" value="Text Name" name="text_name"/>',
			$control
		);
	}

	public function test_get_checkbox_control() {
		$this->form->shouldReceive( 'get_setting' )
			->times( 1 )->with( 'checkbox_name' )->andReturn( 'on' );

		$control = $this->form->get_checkbox_control(
			[
				'control'     => 'checkbox',
				'placeholder' => 'Checkbox Placeholder',
				'label'       => 'Checkbox Label',
				'summary'     => 'Checkbox Summary',
			],
			'checkbox_name'
		);

		$this->assertSame(
			'<input
				name="checkbox_name"
				type="checkbox"
				checked
			/>',
			$control
		);
	}

	public function test_get_select_control() {
		$this->form->shouldReceive( 'get_setting' )
			->times( 4 )->with( 'select_name' )->andReturn( 'selected_option' );

		$control = $this->form->get_select_control(
			[
				'control'     => 'select',
				'placeholder' => 'Select Placeholder',
				'label'       => 'Select Label',
				'summary'     => 'Select Summary',
				'options'     => [
					'not_selected_option_1' => 'Not Selected Option 1',
					'not_selected_option_2' => 'Not Selected Option 2',
					'selected_option'       => 'Selected Option',
					'not_selected_option_3' => 'Not Selected Option 3',
				]
			],
			'select_name'
		);

		$this->assertSame(
			'<select name="select_name">
				<option value="not_selected_option_1" >Not Selected Option 1</option><option value="not_selected_option_2" >Not Selected Option 2</option><option value="selected_option" selected>Selected Option</option><option value="not_selected_option_3" >Not Selected Option 3</option>
			</select>',
			$control
		);
	}
}

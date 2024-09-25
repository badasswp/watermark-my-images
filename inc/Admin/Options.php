<?php
/**
 * Options Class.
 *
 * This class is responsible for holding the Admin
 * page options.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Admin;

class Options {
	/**
	 * The Form.
	 *
	 * This array defines every single aspect of the
	 * Form displayed on the Admin options page.
	 *
	 * @since 1.0.0
	 */
	public static array $form;

	/**
	 * Define custom static method for calling
	 * dynamic methods for e.g. Options::get_page_title().
	 *
	 * @since 1.0.0
	 *
	 * @param string  $method Method name.
	 * @param mixed[] $args   Method args.
	 *
	 * @return string|mixed[]
	 */
	public static function __callStatic( $method, $args ) {
		static::init();

		$keys = substr( $method, strpos( $method, '_' ) + 1 );
		$keys = explode( '_', $keys );

		$value = '';

		foreach ( $keys as $key ) {
			$value = empty( $value ) ? ( static::$form[ $key ] ?? '' ) : ( $value[ $key ] ?? '' );
		}

		return $value;
	}

	/**
	 * Set up Form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init(): void {
		static::$form = [
			'page'   => static::get_form_page(),
			'notice' => static::get_form_notice(),
			'fields' => static::get_form_fields(),
			'submit' => static::get_form_submit(),
		];
	}

	/**
	 * Form Page.
	 *
	 * The Form page items containg the Page title,
	 * summary, slug and option name.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_page(): array {
		return [
			'title'   => esc_html__(
				'Watermark My Images',
				'watermark-my-images'
			),
			'summary' => esc_html__(
				'Insert Watermarks into your WP images.',
				'watermark-my-images'
			),
			'slug'    => 'watermark-my-images',
			'option'  => 'watermark_my_images',
		];
	}

	/**
	 * Form Submit.
	 *
	 * The Form submit items containing the heading,
	 * button name & label and nonce params.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_submit(): array {
		return [
			'heading' => esc_html__( 'Actions', 'watermark-my-images' ),
			'button'  => [
				'name'  => 'watermark_my_images_save_settings',
				'label' => esc_html__( 'Save Changes', 'watermark-my-images' ),
			],
			'nonce'   => [
				'name'   => 'watermark_my_images_settings_nonce',
				'action' => 'watermark_my_images_settings_action',
			],
		];
	}

	/**
	 * Form Fields.
	 *
	 * The Form field items containing the heading for
	 * each group block and controls.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_fields() {
		return [
			'text_options'  => [
				'heading'  => esc_html__( 'Text Options', 'watermark-my-images' ),
				'controls' => [
					'label'      => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( 'WATERMARK', 'watermark-my-images' ),
						'label'       => esc_html__( 'Text Label', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. WATERMARK', 'watermark-my-images' ),
					],
					'size'       => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( '60', 'watermark-my-images' ),
						'label'       => esc_html__( 'Text Size', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. 60', 'watermark-my-images' ),
					],
					'tx_color'   => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( '#000', 'watermark-my-images' ),
						'label'       => esc_html__( 'Text Color', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. #000', 'watermark-my-images' ),
					],
					'bg_color'   => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( '#FFF', 'watermark-my-images' ),
						'label'       => esc_html__( 'Background Color', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. #FFF', 'watermark-my-images' ),
					],
					'tx_opacity' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( '100', 'watermark-my-images' ),
						'label'       => esc_html__( 'Text Opacity (%)', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. 100', 'watermark-my-images' ),
					],
					'bg_opacity' => [
						'control'     => esc_attr( 'text' ),
						'placeholder' => esc_attr__( '0', 'watermark-my-images' ),
						'label'       => esc_html__( 'Background Opacity (%)', 'watermark-my-images' ),
						'summary'     => esc_html__( 'e.g. 0', 'watermark-my-images' ),
					],
				],
			],
			'image_options' => [
				'heading'  => esc_html__( 'Image Options', 'watermark-my-images' ),
				'controls' => [
					'upload'    => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Add Watermark on Image Upload', 'watermark-my-images' ),
						'summary' => esc_html__( 'This is useful for new images.', 'watermark-my-images' ),
					],
					'page_load' => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Add Watermark on Page Load', 'watermark-my-images' ),
						'summary' => esc_html__( 'This is useful for existing images.', 'watermark-my-images' ),
					],
					'logs'      => [
						'control' => esc_attr( 'checkbox' ),
						'label'   => esc_html__( 'Log errors for Failed Watermarks', 'watermark-my-images' ),
						'summary' => esc_html__( 'Enable this option to log errors.', 'watermark-my-images' ),
					],
				],
			],
		];
	}

	/**
	 * Form Notice.
	 *
	 * The Form notice containing the notice
	 * text displayed on save.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public static function get_form_notice() {
		return [
			'label' => esc_html__( 'Settings Saved.', 'watermark-my-images' ),
		];
	}
}

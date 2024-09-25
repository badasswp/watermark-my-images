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
		$keys = substr( $method, strpos( $method, '_' ) + 1 );
		$keys = explode( '_', $keys );

		$value = '';

		foreach ( $keys as $key ) {
			$value = empty( $value ) ? ( self::FORM[ $key ] ?? '' ) : ( $value[ $key ] ?? '' );
		}

		return $value;
	}

	/**
	 * The Form.
	 *
	 * This array defines every single aspect of the
	 * Form displayed on the Admin options page.
	 *
	 * @since 1.0.0
	 */
	public const FORM = [
		'page'   => self::FORM_PAGE,
		'notice' => self::FORM_NOTICE,
		'fields' => self::FORM_FIELDS,
		'submit' => self::FORM_SUBMIT,
	];

	/**
	 * Form Page.
	 *
	 * The Form page items containg the Page title,
	 * summary, slug and option name.
	 *
	 * @since 1.0.0
	 */
	public const FORM_PAGE = [
		'title'   => 'Watermark My Images',
		'summary' => 'Insert Watermarks into your WP images.',
		'slug'    => 'watermark-my-images',
		'option'  => 'watermark_my_images',
	];

	/**
	 * Form Submit.
	 *
	 * The Form submit items containing the heading,
	 * button name & label and nonce params.
	 *
	 * @since 1.0.0
	 */
	public const FORM_SUBMIT = [
		'heading' => 'Actions',
		'button'  => [
			'name'  => 'watermark_my_images_save_settings',
			'label' => 'Save Changes',
		],
		'nonce'   => [
			'name'   => 'watermark_my_images_settings_nonce',
			'action' => 'watermark_my_images_settings_action',
		],
	];

	/**
	 * Form Fields.
	 *
	 * The Form field items containing the heading for
	 * each group block and controls.
	 *
	 * @since 1.0.0
	 */
	public const FORM_FIELDS = [
		'text_options' => [
			'heading'  => 'Text Options',
			'controls' => [
				'label'   => [
					'control'     => 'text',
					'placeholder' => 'WATERMARK',
					'label'       => 'Text Label',
					'summary'     => 'e.g. WATERMARK',
				],
				'size'   => [
					'control'     => 'text',
					'placeholder' => '60',
					'label'       => 'Text Size',
					'summary'     => 'e.g. 60',
				],
				'tx_color'   => [
					'control'     => 'text',
					'placeholder' => '#000',
					'label'       => 'Text Color',
					'summary'     => 'e.g. #000',
				],
				'bg_color'   => [
					'control'     => 'text',
					'placeholder' => '#FFF',
					'label'       => 'Background Color',
					'summary'     => 'e.g. #FFF',
				],
				'tx_opacity'   => [
					'control'     => 'text',
					'placeholder' => '100',
					'label'       => 'Text Opacity (%)',
					'summary'     => 'e.g. 100',
				],
				'bg_opacity'   => [
					'control'     => 'text',
					'placeholder' => '0',
					'label'       => 'Background Opacity (%)',
					'summary'     => 'e.g. 0',
				],
			],
		],
		'image_options'  => [
			'heading'  => 'Image Options',
			'controls' => [
				'upload'    => [
					'control' => 'checkbox',
					'label'   => 'Add Watermark on Image Upload',
					'summary' => 'This is useful for new images.',
				],
				'page_load' => [
					'control' => 'checkbox',
					'label'   => 'Add Watermark on Page Load',
					'summary' => 'This is useful for existing images.',
				],
				'logs' => [
					'control' => 'checkbox',
					'label'   => 'Log errors for Failed Watermarks',
					'summary' => 'Enable this option to log errors.',
				],
			],
		],
	];

	/**
	 * Form Notice.
	 *
	 * The Form notice containing the notice
	 * text displayed on save.
	 *
	 * @since 1.0.0
	 */
	public const FORM_NOTICE = [
		'label' => 'Settings Saved.',
	];
}

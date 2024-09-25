<?php
/**
 * Admin Class.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Admin\Form;
use WatermarkMyImages\Admin\Options;
use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Admin extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', [ $this, 'register_options_init' ] );
		add_action( 'admin_menu', [ $this, 'register_options_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_options_styles' ] );
	}

	/**
	 * Register Options Menu.
	 *
	 * This controls the menu display for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_menu(): void {
		add_menu_page(
			__( Options::get_page_title(), Options::get_page_slug() ),
			__( Options::get_page_title(), Options::get_page_slug() ),
			'manage_options',
			Options::get_page_slug(),
			[ $this, 'register_options_page' ],
			'dashicons-format-image',
			10
		);
	}

	/**
	 * Register Options Page.
	 *
	 * This controls the display of the menu page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_page(): void {
		vprintf(
			'<section class="wrap">
				<h1>%s</h1>
				<p>%s</p>
				%s
			</section>',
			( new Form( Options::FORM ) )->get_options()
		);
	}

	/**
	 * Register Settings.
	 *
	 * This method handles all save actions for the fields
	 * on the Plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_init(): void {
		$form_fields          = [];
		$form_button_name     = Options::get_submit_button_name();
		$form_settings_nonce  = Options::get_submit_nonce_name();
		$form_settings_action = Options::get_submit_nonce_action();

		if ( ! isset( $_POST[ $form_button_name ] ) || ! isset( $_POST[ $form_settings_nonce ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $form_settings_nonce ] ) ), $form_settings_action ) ) {
			return;
		}

		foreach ( Options::get_fields() as $field ) {
			$form_fields = array_merge(
				array_keys( $field['controls'] ?? [] ),
				$form_fields
			);
		}

		$options = array_combine(
			$form_fields,
			array_map(
				function ( $field ) {
					return sanitize_text_field( $_POST[ $field ] ?? '' );
				},
				$form_fields
			)
		);

		update_option( Options::get_page_option(), $options );
	}

	/**
	 * Register Styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_styles(): void {
		wp_enqueue_style(
			Options::get_page_slug(),
			plugins_url( 'watermark-my-images/styles.css' ),
			[],
			true,
			'all'
		);
	}
}

<?php
/**
 * Text Class.
 *
 * This class handles the creation of text to be
 * embossed on images.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Core;

use Imagine\Gd\Font;
use Imagine\Gd\Image;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;

class Text {
	/**
	 * Text Args.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed[]
	 */
	public array $args;

	/**
	 * Set up.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->args = [
			'size'       => 60,
			'tx_color'   => '#000',
			'bg_color'   => '#FFF',
			'font'       => 'Arial',
			'label'      => 'WATERMARK',
			'tx_opacity' => 100,
			'bg_opacity' => 0,
		];
	}

	/**
	 * Get Watermark Text Option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option e.g. 'size'.
	 * @return string
	 */
	private function get_option( $option ): string {
		return $this->get_options()[ $option ] ?? '';
	}

	/**
	 * Get Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	private function get_options(): array {
		$options = wp_parse_args(
			get_option( 'watermark_my_images', [] ) ?? [],
			$this->args
		);

		/**
		 * Filter Text Options.
		 *
		 * This filter is responsible for manipulating the text
		 * options before it is passed on.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $options Text Options.
		 * @return mixed[]
		 */
		return (array) apply_filters( 'watermark_my_images_text', $options );
	}

	/**
	 * Get the Font.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception $e When RGB Palette is unable to create Text color.
	 *
	 * @return Font
	 */
	private function get_font(): Font {
		try {
			$tx_color = ( new RGB() )->color( $this->get_option( 'tx_color' ), (int) $this->get_option( 'tx_opacity' ) );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to create Text color, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		return new Font(
			$this->get_font_url(),
			$this->get_option( 'size' ),
			$tx_color
		);
	}

	/**
	 * Get Text.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception $e When RGB Palette is unable to create Text color.
	 * @throws \Exception $e When Imagine object is unable to create Text Box.
	 * @throws \Exception $e When Drawer is unable to draw Text on Text Box.
	 *
	 * @return Image
	 */
	public function get_text(): Image {
		try {
			$bg_color = ( new RGB() )->color( $this->get_option( 'bg_color' ), (int) $this->get_option( 'bg_opacity' ) );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to create Background color, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		try {
			$text_box = ( new Imagine() )->create(
				new Box(
					$this->get_option( 'size' ) * ( strlen( $this->get_option( 'label' ) ) - 0.5 ),
					$this->get_option( 'size' )
				),
				$bg_color
			);
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to create Text Box, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		try {
			$text_box->draw()->text(
				$this->get_option( 'label' ),
				$this->get_font(),
				new Point( 0, 0 )
			);
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					/* translators: Exception error message. */
					esc_html__( 'Unable to draw Text, %s', 'watermark-my-images' ),
					esc_html( $e->getMessage() )
				)
			);
		}

		return $text_box;
	}

	/**
	 * Get the Font URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_font_url(): string {
		return sprintf(
			'%s/../fonts/%s.otf',
			plugin_dir_path( __DIR__ ),
			$this->get_option( 'font' ),
		);
	}
}

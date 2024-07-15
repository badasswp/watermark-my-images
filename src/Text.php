<?php
/**
 * Text Class.
 *
 * This class handles the creation of text to be
 * embossed on images.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages;

use Imagine\Gd\Font;
use Imagine\Image\Box;
use Imagine\Gd\Drawer;
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
			'tx_color' => '#FFF',
			'bg_color' => '#BBB',
			'font'     => 'Arial',
			'size'     => 12,
			'position' => [ 0, 0 ],
			'label'    => 'WATERMARK',
		];
	}

	/**
	 * Get Watermark Text Options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option e.g. 'size'.
	 * @return string
	 */
	public function get_watermark( $option ): string {
		return $this->get_options()[ $option ] ?? '';
	}

	/**
	 * Get Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	public function get_options(): array {
		$options = wp_parse_args(
			get_option( 'watermark_my_images', [] )['text'] ?? [],
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
	public function get_font(): Font {
		try {
			$tx_color = ( new RGB() )->color( $this->get_watermark( 'tx_color' ), 100 );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to create Text color, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		return new Font(
			$this->get_font_url(),
			$this->get_watermark( 'size' ),
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
	 * @return Drawer
	 */
	public function get_text(): Drawer {
		try {
			$bg_color = ( new RGB() )->color( $this->get_watermark( 'bg_color' ), 100 );
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to create Background color, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		try {
			$text_box = ( new Imagine() )->create(
				new Box( 85, 35 ),
				$bg_color
			);
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to create Text Box, %s', 'watermark-my-images' ),
					$e->getMessage()
				)
			);
		}

		try {
			$text_box->draw()->text(
				$this->get_watermark( 'label' ),
				$this->get_font(),
				new Point( 0, 0 )
			);
		} catch ( \Exception $e ) {
			throw new \Exception(
				sprintf(
					esc_html__( 'Unable to draw Text, %s', 'watermark-my-images' ),
					$e->getMessage()
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
	public function get_font_url(): string {
		return sprintf(
			'%s/fonts/%s.otf',
			plugin_dir_path( __DIR__ ),
			$this->get_watermark( 'font' ),
		);
	}
}

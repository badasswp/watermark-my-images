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
			get_option( 'watermark_my_image', [] )['text'] ?? [],
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
		return (array) apply_filters( 'watermark_my_image_text', $options );
	}

	/**
	 * Get the Font.
	 *
	 * @since 1.0.0
	 *
	 * @return Font
	 */
	public function get_font(): Font {
		$font = new Font(
			$this->get_font_url(),
			$this->get_watermark( 'size' ),
			( new RGB() )->color( $this->get_watermark( 'tx_color' ), 100 )
		);

		return $font;
	}

	/**
	 * Get the Font URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_font_url(): string {
		return trailingslashit( plugin_dir_path( __FILE__ ) . '/fonts' ) . $this->get_watermark( 'font' ) . '.otf';
	}

	/**
	 * Get Text.
	 *
	 * @since 1.0.0
	 *
	 * @return Imagine
	 */
	public function get_text(): Imagine {
		$imagine_box = ( new Imagine() )->create(
			new Box( 85, 35 ),
			( new RGB() )->color( $this->get_watermark( 'bg_color' ), 100 )
		);

		$imagine = $imagine_box->draw();

		$imagine->text(
			$this->get_watermark( 'label' ),
			$this->get_font(),
			new Point( 0, 0 )
		);

		return $imagine;
	}
}
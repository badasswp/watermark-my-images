<?php
/**
 * Text Class.
 *
 * This class handles the creation of text to be
 * embossed on images.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Engine;

use Imagine\Gd\Font;
use Imagine\Gd\Image;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;

use WatermarkMyImages\Abstracts\Entity;

class Text extends Entity {
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
	protected function get_option( $option ): string {
		return $this->get_options()[ $option ] ?? '';
	}

	/**
	 * Get Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_options(): array {
		$options = get_option( 'watermark_my_images', [] );

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
		$filtered_options = (array) apply_filters( 'watermark_my_images_text', $options );

		// Adjust the text height based on Image width.
		$filtered_options['size'] = $this->get_size( $filtered_options );

		// Remove empty plugin options.
		$filtered_options = array_filter(
			$filtered_options,
			function ( $value ) {
				return ! empty( $value );
			}
		);

		return wp_parse_args( $filtered_options, $this->args );
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
	protected function get_font(): Font {
		try {
			$tx_color = $this->get_rgb( new RGB() )->color( $this->get_option( 'tx_color' ), (int) $this->get_option( 'tx_opacity' ) );
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
			$bg_color = $this->get_rgb( new RGB() )->color( $this->get_option( 'bg_color' ), (int) $this->get_option( 'bg_opacity' ) );
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
			$text_box = $this->get_imagine( new Imagine() )->create(
				$this->get_text_box(),
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
	 * Get Text Box.
	 *
	 * @since TBD
	 *
	 * @return Box
	 */
	protected function get_text_box(): Box {
		return new Box(
			$this->get_text_length(),
			$this->get_option( 'size' )
		);
	}

	/**
	 * Get the Font URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_font_url(): string {
		return sprintf(
			'%s/../fonts/%s.otf',
			plugin_dir_path( __DIR__ ),
			$this->get_option( 'font' ),
		);
	}

	/**
	 * Get Size (Text Height).
	 *
	 * This refers to the height of the Text character.
	 * It uses the % of the image width.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $options Text Options.
	 * @return integer
	 */
	protected function get_size( $options ): int {
		$default_size = 50;

		// Ensure Size is set.
		$size = (int) ( $options['size'] ?? '' );
		$size = empty( $size ) ? $default_size : $size;

		// Get Ratio & Image Size.
		$ratio      = 100 / $size;
		$image_size = getimagesize( Watermarker::$file )[0] ?? 0;

		return floor( $image_size / ( $ratio * 8.5 ) );
	}

	/**
	 * Get Text Length.
	 *
	 * This method is responsible for getting the precise
	 * character length.
	 *
	 * @since 1.0.1
	 *
	 * @return float
	 */
	protected function get_text_length(): float {
		$length = array_reduce(
			str_split( strtoupper( $this->get_option( 'label' ) ) ),
			function ( $carry, $char ) {
				return $carry + $this->get_char_ratio( $char ) * $this->get_option( 'size' );
			},
			0
		);

		return $length + ( ( strlen( $this->get_option( 'label' ) ) - 1 ) * ( $this->get_option( 'size' ) * 0.1 ) );
	}

	/**
	 * Get Character Ratio.
	 *
	 * This method is responsible for getting the precise
	 * character widths.
	 *
	 * @since 1.0.1
	 *
	 * @param string $char
	 * @return float
	 */
	protected function get_char_ratio( $char ): float {
		$ratio = 1;

		switch ( $char ) {
			case 'A':
			case 'G':
				$ratio = 0.917;
				break;

			case 'B':
			case 'H':
			case 'N':
			case 'S':
			case 'T':
			case 'U':
			case 'Z':
				$ratio = 0.8;
				break;

			case 'C':
			case 'Y':
				$ratio = 0.9;
				break;

			case 'D':
			case 'R':
			case 'X':
				$ratio = 0.833;
				break;

			case 'E':
				$ratio = 0.7;
				break;

			case 'F':
			case 'L':
				$ratio = 0.667;
				break;

			case 'J':
				$ratio = 0.6;
				break;

			case 'K':
				$ratio = 0.817;
				break;

			case 'O':
			case 'Q':
				$ratio = 0.967;
				break;

			case 'P':
				$ratio = 0.75;
				break;

			case 'V':
				$ratio = 0.867;
				break;

			case 'W':
				$ratio = 1.283;
				break;

			case 'I':
				$ratio = 0.133;
				break;

			default:
				$ratio = 1;
				break;
		}

		return $ratio;
	}
}

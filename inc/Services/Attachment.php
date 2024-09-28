<?php
/**
 * Attachment Class.
 *
 * This class is responsible for loading Attachment specific
 * logic for plugin use.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Services;

use WatermarkMyImages\Abstracts\Service;
use WatermarkMyImages\Interfaces\Registrable;

class Attachment extends Service implements Registrable {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'add_attachment', [ $this, 'add_watermark_on_add_attachment' ], 10, 1 );
		add_action( 'delete_attachment', [ $this, 'remove_watermark_on_delete_attachment' ], 10, 1 );
		add_filter( 'attachment_fields_to_edit', [ $this, 'add_watermark_attachment_fields' ], 10, 2 );
		add_filter( 'wp_generate_attachment_metadata', [ $this, 'add_watermark_to_metadata' ], 10, 3 );
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'show_watermark_images_on_wp_media_modal' ], 10, 3 );
	}

	/**
	 * Get Watermark Image.
	 *
	 * This method uses the Image ID to retrieve
	 * the image to be watermarked.
	 *
	 * @since 1.0.0
	 *
	 * @param int $image_id Image ID.
	 *
	 * @return void
	 */
	public function add_watermark_on_add_attachment( $image_id ): void {
		$image_watermark = get_post_meta( $image_id, 'watermark_my_images', true );

		// Bail out, if it is not an image.
		if ( ! wp_attachment_is_image( $image_id ) ) {
			return;
		}

		// Bail out, if watermark image exist.
		if ( file_exists( $image_watermark['abs'] ?? '' ) ) {
			return;
		}

		$this->image_id = $image_id;

		try {
			$watermark = $this->watermarker->get_watermark();
			$response  = $watermark['rel'] ?? '';
		} catch ( \Exception $e ) {
			$response = new \WP_Error(
				'watermark-log-error',
				sprintf(
					'Fatal Error: %s',
					$e->getMessage()
				)
			);
		}

		/**
		 * Fire after Watermark is completed.
		 *
		 * @since 1.0.0
		 *
		 * @param string|\WP_Error $response  Image URL or WP Error.
		 * @param string[]         $watermark Array containing abs and rel paths to new images.
		 * @param int              $id        Image ID.
		 *
		 * @return void
		 */
		do_action( 'watermark_my_images_on_add_attachment', $response, $watermark ?? [], $id = $this->image_id );
	}

	/**
	 * Remove Watermark Image.
	 *
	 * This method removes the Watermark images
	 * associated with an Image.
	 *
	 * @since 1.0.0
	 *
	 * @param int $image_id Image ID.
	 * @return void
	 */
	public function remove_watermark_on_delete_attachment( $image_id ): void {
		// Bail out, if it is not an image.
		if ( ! wp_attachment_is_image( $image_id ) ) {
			return;
		}

		// Get absolute path for main image.
		$main_image = get_post_meta( $image_id, 'watermark_my_images', true );

		if ( file_exists( $main_image['abs'] ?? '' ) ) {
			wp_delete_file( $main_image['abs'] );

			/**
			 * Fires after Watermark Image has been deleted.
			 *
			 * @since 1.0.0
			 *
			 * @param string $main_image['abs'] Absolute path to Watermark image.
			 * @param int    $image_id          Image ID.
			 *
			 * @return void
			 */
			do_action( 'watermark_my_images_on_delete_attachment', $main_image['abs'], $image_id );
		}
	}

	/**
	 * Add attachment fields for Watermarked image.
	 *
	 * As the name implies, this logic creates a Watermarked field label
	 * in the WP attachment modal so users can see the path of the image's
	 * generated Watermarked version.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $fields Fields Array.
	 * @param \WP_Post $post   WP Post.
	 *
	 * @return mixed[]
	 */
	public function add_watermark_attachment_fields( $fields, $post ): array {
		$image_watermark = get_post_meta( $post->ID, 'watermark_my_images', true );

		$fields['watermark_my_images'] = [
			'label' => 'Watermarked Image',
			'input' => 'text',
			'value' => (string) ( $image_watermark['rel'] ?? '' ),
			'helps' => 'Watermarked Image generated by Watermark my Images.',
		];

		return $fields;
	}

	/**
	 * Show Watermark Images.
	 *
	 * This displays the Watermarked images on the WP
	 * Medial modal window.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]     $metadata   Image Attachment data to be sent to JS.
	 * @param \WP_Post    $attachment Attachment ID or object.
	 * @param array|false $meta       Array of attachment meta data, or false if there is none.
	 *
	 * @return void
	 */
	public function show_watermark_images_on_wp_media_modal( $metadata, $attachment, $meta ) {
		$image_watermark = get_post_meta( $attachment->ID, 'watermark_my_images', true );

		// Bail out, if it is not an image.
		if ( ! wp_attachment_is_image( $attachment->ID ) ) {
			return $metadata;
		}

		// Bail out, if watermark image does NOT exist.
		if ( ! file_exists( $image_watermark['abs'] ?? '' ) ) {
			return $metadata;
		}

		$metadata['sizes']['full']['url'] = $image_watermark['rel'] ?? '';

		return $this->get_watermark_metadata( $metadata );
	}

	/**
	 * Generate Watermark images for metadata.
	 *
	 * Get Watermark images for the various sizes generated by WP
	 * when the user adds a new image to the WP media.
	 *
	 * @since 1.0.1
	 *
	 * @param mixed[] $metadata      An array of attachment meta data.
	 * @param int     $attachment_id Attachment ID.
	 * @param string  $context       Additional context. Can be 'create' or 'update'.
	 *
	 * @return mixed[]
	 */
	public function add_watermark_to_metadata( $metadata, $attachment_id, $context ): array {
		// Get parent image URL.
		$abs_url = get_attached_file( $attachment_id );
		$img_url = (string) substr( $abs_url, 0, strrpos( $abs_url, '/' ) );

		// Convert srcset images.
		foreach ( $metadata['sizes'] ?? [] as $img ) {
			$img_file = trailingslashit( $img_url ) . ( $img['file'] ?? '' );

			try {
				$watermark = $this->watermarker->get_watermark( $img_file );
			} catch ( \Exception $e ) {
				$response = new \WP_Error(
					'watermark-log-error',
					sprintf(
						'Fatal Error: %s',
						$e->getMessage()
					)
				);
			}
		}

		return $metadata;
	}

	/**
	 * Get Watermark Metadata.
	 *
	 * Mutate Meta data array and get the Watermarked Images
	 * for all Image meta data.
	 *
	 * @since 1.0.1
	 *
	 * @param mixed[] $metadata Meta data array.
	 * @return mixed[]
	 */
	private function get_watermark_metadata( $metadata ): array {
		return wp_parse_args(
			[
				'sizes' => wp_parse_args(
					[
						'thumbnail' => wp_parse_args(
							[
								'url' => $this->get_meta_watermark_image( $metadata['sizes']['thumbnail']['url'] ?? '' )
							],
							$metadata['sizes']['thumbnail'] ?? [],
						),
						'medium' => wp_parse_args(
							[
								'url' => $this->get_meta_watermark_image( $metadata['sizes']['medium']['url'] ?? '' )
							],
							$metadata['sizes']['medium'] ?? [],
						),
						'large' => wp_parse_args(
							[
								'url' => $this->get_meta_watermark_image( $metadata['sizes']['large']['url'] ?? '' )
							],
							$metadata['sizes']['large'] ?? [],
						),
					],
					$metadata['sizes'] ?? []
				)
			],
			$metadata
		);
	}
}

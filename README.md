# watermark-my-images
Insert Watermarks into your WP images.

[![Coverage Status](https://coveralls.io/repos/github/badasswp/watermark-my-images/badge.svg?branch=master)](https://coveralls.io/github/badasswp/watermark-my-images?branch=master)

<img width="1018" alt="screenshot-1" src="https://github.com/user-attachments/assets/2f8218c9-2dd1-4d6f-9998-f7c84c5b070a">

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/watermark-my-images/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/watermark-my-images/releases).

## Why Watermark My Images?

In this age of social media, it is easy for your intellectual assets to get hijacked and you never get the credit you deserve for your work. This plugin prevents that by helping you insert watermarks on your site images, article thumbnails, and your product photos, so you don't have to worry about anyone stealing your image assets ever again!

### Hooks

#### `watermark_my_images_on_add_image`

This custom hook (action) fires after the watermark is added to the uploaded image. For e.g. to log errors, you could do:

```php
add_action( 'watermark_my_images_on_add_image', [ $this, 'log_errors' ], 10, 3 );

public function log_errors( $response, $watermark, $id ): void {
    if ( is_wp_error( $response ) ) {
        error_log(
            sprintf(
                'Fatal Error: Failure adding Watermark Image, %s. Image ID: %d',
                $response->get_error_message(),
                $id
            )
        )
    }
}
```

**Parameters**

- response _`{string|\WP_Error}`_ By default this will be the image URL of the watermarked image or WP Error object.
- watermark _`{string[]}`_ By default this will be a string array, containing the absolute and relative paths of the watermarked image.
- id _`{int}`_ By default this will be the Image ID.
<br/>

#### `watermark_my_images_on_add_image_crops`

This custom hook (action) fires after the watermark is added to one of the crops of the uploaded image. For e.g. to log errors, you could do:

```php
add_action( 'watermark_my_images_on_add_image_crops', [ $this, 'log_errors' ], 10, 4 );

public function log_errors( $response, $watermark, $id, $crop ): void {
    if ( is_wp_error( $response ) ) {
        error_log(
            sprintf(
                'Fatal Error: Failure adding Watermark Image, %s. Image Crop: %s',
                $response->get_error_message(),
                $crop
            )
        )
    }
}
```

**Parameters**

- response _`{string|\WP_Error}`_ By default this will be the image URL of the watermarked image or WP Error object.
- watermark _`{string[]}`_ By default this will be a string array, containing the absolute and relative paths of the watermarked image.
- id _`{int}`_ By default this will be the Image ID.
- crop _`{string}`_ By default this will be a crop of the Image for e.g. `150 x 150`, `300 x 300` and so on.
<br/>

#### `watermark_my_images_on_delete_image`

This custom hook (action) fires immediately after a Watermarked image is deleteed.

```php
add_action( 'watermark_my_images_on_delete_image', [ $this, 'delete_wm_image' ], 10, 2 );

public function delete_wm_image( $watermarked_image, $attachment_id ): void {
    if ( file_exists( $watermarked_image ) ) {
        wp_delete_file( $watermarked_image );
    }
}
```

**Parameters**

- watermarked image _`{string}`_ By default this will be the absolute path of the Watermarked metadata image.
- attachment_id _`{int}`_ By default this is the Image ID.
<br/>

#### `watermark_my_images_on_delete_image_crops`

This custom hook (action) fires immediately after a watermarked metadata image is deleteed.

```php
add_action( 'watermark_my_images_on_delete_image_crops', [ $this, 'delete_wm_image' ], 10, 2 );

public function delete_wm_image( $watermarked_image, $attachment_id ): void {
    if ( file_exists( $watermarked_image ) ) {
        wp_delete_file( $watermarked_image );
    }
}
```

**Parameters**

- watermarked image _`{string}`_ By default this will be the absolute path of the Watermarked metadata image.
- attachment_id _`{int}`_ By default this is the Image ID.
<br/>

#### `watermark_my_images_on_page_load`

This custom hook (action) fires after the watermark is added to an image during page load. For e.g. to log errors, you could do:

```php
add_action( 'watermark_my_images_on_page_load', [ $this, 'log_errors' ], 10, 3 );

public function log_errors( $response, $watermark, $id ): void {
    if ( is_wp_error( $response ) ) {
        error_log(
            sprintf(
                'Fatal Error: Failure adding Watermark Image, %s. Image ID: %d',
                $response->get_error_message(),
                $id
            )
        )
    }
}
```

**Parameters**

- response _`{string|\WP_Error}`_ By default this will be the image URL of the watermarked image or WP Error object.
- watermark _`{string[]}`_ By default this will be a string array, containing the absolute and relative paths of the watermarked image.
- id _`{int}`_ By default this will be the Image ID.
<br/>

#### `watermark_my_images_on_woo_product_get_image`

This custom hook (action) fires when the watermark to a WooCommerce image. For e.g:

```php
add_action( 'watermark_my_images_on_woo_product_get_image', [ $this, 'log_errors' ], 10, 3 );

public function log_errors( $response, $watermark, $id ): void {
    if ( is_wp_error( $response ) ) {
        error_log(
            sprintf(
                'Fatal Error: Failure adding Watermark Image, %s. Image ID: %d',
                $response->get_error_message(),
                $id
            )
        )
    }
}
```

**Parameters**

- response _`{string|\WP_Error}`_ By default this will be the image URL of the watermarked image or WP Error object.
- watermark _`{string[]}`_ By default this will be a string array, containing the absolute and relative paths of the watermarked image.
- id _`{int}`_ By default this will be the Image ID.
<br/>

#### `watermark_my_images_text`

This custom hook (filter) helps you filter the text options for your watermark. For e.g. if you want a white text on a black background, you could pass like so:

```php
add_filter( 'watermark_my_images_text', [ $this, 'text_options' ], 10, 1 );

public function text_options( $options ): array {
    $options = wp_parse_args(
        [
            'tx_color' => '#FFF',
            'bg_color' => '#000',
        ],
        $options
    );

    return $options;
}
```

**Parameters**

- options _`{mixed[]}`_ By default this will be an array.
<br/>

#### `watermark_my_images_text_position`

This custom hook (filter) helps you filter the text position of the watermark like so:

```php
add_filter( 'watermark_my_images_text_position', [ $this, 'text_position' ], 10, 1 );

public function text_position( $position ): array {
    return [0, 0];
}
```

**Parameters**

- position _`{integer[]}`_ By default this will be an array containing x and y positions.
<br/>

#### `watermark_my_images_form_fields`

This custom hook (filter) provides the ability to add custom fields to the Admin options page like so:

```php
add_filter( 'watermark_my_images_form_fields', [ $this, 'custom_form_fields' ] );

public function custom_form_fields( $fields ): array {
    $fields = wp_parse_args(
        [
            'custom_group'  => [
                'label'    => 'Custom Heading',
                'controls' => [
                    'custom_option_1' => [
                        'control' => 'text',
                        'label'   => 'My Custom Option 1',
                        'summary' => 'Enable this option to save my custom option 1.',
                    ],
                    'custom_option_2' => [
                        'control' => 'select',
                        'label'   => 'My Custom Option 2',
                        'summary' => 'Enable this option to save my custom option 2.',
                        'options' => [],
                    ],
                    'custom_option_3' => [
                        'control' => 'checkbox',
                        'label'   => 'My Custom Option 3',
                        'summary' => 'Enable this option to save my custom option 3.',
                    ],
                ],
            ],
        ],
        $fields
    );

    return (array) $fields;
}
```

**Parameters**

- fields _`{array}`_ By default this will be an associative array containing key, value options of each field option.
<br/>

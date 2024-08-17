# watermark-my-images
Insert Watermarks into your WP images.

<img width="1018" alt="screenshot-1" src="https://github.com/user-attachments/assets/2f8218c9-2dd1-4d6f-9998-f7c84c5b070a">

## Why Watermark My Images?

In this age of social media, it is easy for your intellectual assets to get hijacked and you never get the credit you deserve for your work. This plugin prevents that by helping you insert watermarks on your site images, article thumbnails, and your product photos, so you don't have to worry about anyone stealing your image assets ever again!

### Hooks

#### `watermark_my_images_on_add_attachment`

This custom hook (action) fires when the watermark is added on attachment upload. For e.g. to log errors, you could do:

```php
add_action( 'watermark_my_images_on_add_attachment', [ $this, 'log_errors' ], 10, 3 );

public function log_errors( $response, $watermark, $id ): void {
    if ( is_wp_error( $response ) ) {
        error_log(
            sprintf(
                'Fatal Error: Failure converting Watermark Image, %s. Image ID: %d',
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

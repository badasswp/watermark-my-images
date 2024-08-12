# watermark-my-images
Insert Watermarks into your WP images.

<img width="1018" alt="screenshot-1" src="https://github.com/user-attachments/assets/996fd0c6-c654-46a8-a277-09d0810ad88c">

## Why Watermark My Images?

In this day & age of social media and fast sharing of information, it's easy for your intellectual assets to get hijacked and you never get the credit you deserve for your work. This plugin helps you insert watermarks on your site images, article thumbnails, and your product photos, so you don't have to worry about anyone stealing your image assets ever again!

### Hooks

#### `watermark_my_images_text`

This custom hook (filter) helps you filter the text options for your watermark. For e.g. if you want a white text on a black background, you could pass like so:

```php
add_filter( 'watermark_my_images_text', [ $this, 'text_options' ], 10, 1 );

public function text_options( $options ) {
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

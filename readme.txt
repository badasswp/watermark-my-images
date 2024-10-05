=== Watermark My Images ===
Contributors: badasswp
Tags: image, watermark, copyright, intellectual, media.
Requires at least: 4.0
Tested up to: 6.6.2
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Insert Watermarks into your WP images.

== Installation ==

1. Go to 'Plugins > Add New' on your WordPress admin dashboard.
2. Search for 'Watermark My Images' plugin from the official WordPress plugin repository.
3. Click 'Install Now' and then 'Activate'.
4. Go to the Watermark My Images options page and set your Text and Image options.
5. Proceed to upload any image of your choice and you should see your Watermark now attached to it.
6. Happy Watermarking!

== Description ==

In this age of social media, it is easy for your intellectual assets to get hijacked and you never get the credit you deserve for your work. This plugin prevents that by helping you insert watermarks on your site images, article thumbnails, and your product photos, so you don't have to worry about anyone stealing your image assets ever again!

== Screenshots ==

1. WooCommerce Watermarked Images - Watermark your WooCommerce Product Images.
2. Watermarked Images - Watermark your images by simply uploading them.
3. Watermark Text Options - Configure your plugin text options here.
4. Watermark Image Options - Configure your plugin image options here.

== Changelog ==

= 1.0.5 =
* Change function name `wmi_get_settings` to `wmig_get_settings`.
* Change function name `wmi_get_equivalent` to `wmig_get_equivalent`.
* Added more Unit tests.
* Tested up to WP 6.6.2.

= 1.0.4 =
* Prevent direct access that can lead to Security issues.
* Tested up to WP 6.6.2.

= 1.0.3 =
* Remove `icc` files (non-GPL files).
* Added more Unit tests.
* Fix bugs & linting issues.
* Tested up to WP 6.6.2.

= 1.0.2 =
* Ignore PHPCS warning for Admin options page.
* Implement Image, Paste & Save Exception classes.
* Tie Plugin options to Attachment & PageLoad service.
* Added Unit tests coverage.
* Fix bugs & linting issues.
* Tested up to WP 6.6.2.

= 1.0.1 =
* Fix Text height relative to Image width.
* Implement New Image & Text class methods.
* Implement Watermark addition & deletion for Image Metadata.
* Custom Hooks - `watermark_my_images_on_delete_image_crops`, `watermark_my_images_on_page_load`, `watermark_my_images_on_add_image_crops`.
* Tested up to WP 6.6.2.

= 1.0.0 =
* Initial release
* Add Watermarks to Images.
* Custom Hooks - `watermark_my_images_on_add_image`, `watermark_my_images_on_woo_product_get_image`, `watermark_my_images_text`, `watermark_my_images_text_position`, `watermark_my_images_form_fields`, `watermark_my_images_on_delete_image`.
* Custom plugin options page.
* Tested up to WP 6.6.2.

== Contribute ==

If you'd like to contribute to the development of this plugin, you can find it on [GitHub](https://github.com/badasswp/watermark-my-images).

# Changelog

## 1.1.0
* Feat: Add WooCommerce watermark option in plugin settings page.
* Test: Updated unit test cases.
* Fix: Issue with WooCommerce watermarked images not showing.
* Refactor of Service Instances to use Dependency Injection.
* Tested up to WP 6.8.

## 1.0.6
* Fix bug related to empty plugin options.
* Fix failing tests.
* Tested up to WP 6.7.0.

## 1.0.5
* Change function name `wmi_get_settings` to `wmig_get_settings`.
* Change function name `wmi_get_equivalent` to `wmig_get_equivalent`.
* Added more Unit tests.
* Tested up to WP 6.6.2.

## 1.0.4
* Prevent direct access that can lead to Security issues.
* Tested up to WP 6.6.2.

## 1.0.3
* Remove `icc` files (non-GPL files).
* Added more Unit tests.
* Fix bugs & linting issues.
* Tested up to WP 6.6.2.

## 1.0.2
* Ignore PHPCS warning for Admin options page.
* Implement Image, Paste & Save Exception classes.
* Tie Plugin options to Attachment & PageLoad service.
* Added Unit tests coverage.
* Fix bugs & linting issues.
* Tested up to WP 6.6.2.

## 1.0.1
* Fix Text height relative to Image width.
* Implement New Image & Text class methods.
* Implement Watermark addition & deletion for Image Metadata.
* Custom Hooks - `watermark_my_images_on_delete_image_crops`, `watermark_my_images_on_page_load`, `watermark_my_images_on_add_image_crops`.
* Tested up to WP 6.6.2.

## 1.0.0
* Initial release
* Add Watermarks to Images.
* Custom Hooks - `watermark_my_images_on_add_image`, `watermark_my_images_on_woo_product_get_image`, `watermark_my_images_text`, `watermark_my_images_text_position`, `watermark_my_images_form_fields`, `watermark_my_images_on_delete_image`.
* Custom plugin options page.
* Tested up to WP 6.6.2.

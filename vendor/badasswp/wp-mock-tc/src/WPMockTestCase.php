<?php
/**
 * WPMockTestCase Class.
 *
 * Base class for defining popular WP functions
 * we intend to mock.
 *
 * @package WPMockTC
 */

namespace Badasswp\WPMockTC;

use WP_Mock;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Response;
use WP_Mock\Tools\TestCase;
use Mockery;

class WPMockTestCase extends TestCase {
	/**
	 * List of overriden functions.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	public static array $overrides = array();

	/**
	 * Setup Method.
	 *
	 * Define basic WP mocks for test
	 * cases to inherit.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setUp(): void {
		WP_Mock::setUp();

		static::i18n();
		static::esc();
		static::esc_i18n();
		static::utils();
	}

	/**
	 * TearDown Method.
	 *
	 * Clear all mocks in readiness for
	 * new mock definitions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function tearDown(): void {
		WP_Mock::tearDown();

		// Clear out overrides, after test is done.
		static::$overrides = array();
	}

	/**
	 * Internalization mocks.
	 *
	 * Define all i18n mocks here.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function i18n(): void {
		$pass_through = fn( $arg ) => $arg;

		// Get i18n functions.
		$i18n_funcs = array( '__', '_x' );

		foreach ( $i18n_funcs as $func ) {
			if ( ! in_array( $func, static::$overrides, true ) ) {
				WP_Mock::userFunction( $func )->andReturnUsing( $pass_through );
			}
		}

		if ( ! in_array( '_e', static::$overrides, true ) ) {
			WP_Mock::userFunction( '_e' )
				->andReturnUsing(
					function ( $arg ) {
						echo $arg;
					}
				);
		}

		if ( ! in_array( '_n', static::$overrides, true ) ) {
			WP_Mock::userFunction( '_n' )
				->andReturnUsing(
					function ( $single, $plural, $number ) {
						return 1 === $number ? $single : $plural;
					}
				);
		}
	}

	/**
	 * Escape mocks.
	 *
	 * Define all escape mocks here.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function esc(): void {
		$pass_through = fn( $arg ) => $arg;

		// Get escape functions.
		$esc_funcs = array( 'esc_html', 'esc_attr', 'esc_url' );

		foreach ( $esc_funcs as $func ) {
			if ( ! in_array( $func, static::$overrides, true ) ) {
				WP_Mock::userFunction( $func )->andReturnUsing( $pass_through );
			}
		}
	}

	/**
	 * Escape + i18n mocks.
	 *
	 * Define all esc_i18n mocks here.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function esc_i18n(): void {
		$pass_through = fn( $arg1, $arg2 ) => $arg1;

		// Get escape__ functions.
		$esc_i18n_funcs = array( 'esc_html__', 'esc_attr__' );

		foreach ( $esc_i18n_funcs as $func ) {
			if ( ! in_array( $func, static::$overrides, true ) ) {
				WP_Mock::userFunction( $func )->andReturnUsing( $pass_through );
			}
		}

		if ( ! in_array( 'esc_html_e', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'esc_html_e' )
				->andReturnUsing(
					function ( $arg ) {
						echo $arg;
					}
				);
		}
	}

	/**
	 * Utils mocks.
	 *
	 * Define all utils mocks here.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function utils(): void {
		if ( ! in_array( 'absint', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'absint' )
				->andReturnUsing( fn( $arg ) => intval( $arg ) );
		}

		if ( ! in_array( 'is_wp_error', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'is_wp_error' )
				->andReturnUsing( fn( $arg ) => $arg instanceof WP_Error );
		}

		if ( ! in_array( 'wp_json_encode', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'wp_json_encode' )
				->andReturnUsing( fn( $arg ) => json_encode( $arg ) );
		}

		if ( ! in_array( 'wp_parse_args', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'wp_parse_args' )
				->andReturnUsing( fn( $arg1, $arg2 ) => array_merge( $arg2, $arg1 ) );
		}

		if ( ! in_array( 'wp_strip_all_tags', static::$overrides, true ) ) {
			WP_Mock::userFunction( 'wp_strip_all_tags' )
				->andReturnUsing( fn( $arg ) => strip_tags( $arg ) );
		}
	}

	/**
	 * Override mock functions.
	 *
	 * @since 1.1.0
	 *
	 * @param array $overrides List of overrides.
	 * @return void
	 */
	public static function override( $overrides ): void {
		static::$overrides = $overrides;
	}
}

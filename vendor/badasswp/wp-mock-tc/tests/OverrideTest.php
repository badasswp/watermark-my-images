<?php

namespace Badasswp\WPMockTC\Tests;

use WP_Mock;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \Badasswp\WPMockTC\Tests\OverrideClass::get_user_names_appended_to_post_id
 */
class OverrideTest extends WPMockTestCase {
	private $override_class;

	public function setUp(): void {
		// Let's override the absint method.
		parent::override( array( 'absint' ) );
		parent::setUp();

		$this->override_class = new OverrideClass();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_get_user_names_appended_to_post_id() {
		// Now we have to manually mock the absint here.
		WP_Mock::userFunction( 'absint' )
			->andReturnUsing(
				function ( $arg ) {
					return intval( $arg );
				}
			);

		$this->assertSame(
			array(
				'John Doe - 1',
				'Cathryn Washington - 1',
				'Jack Foley - 1',
			),
			$this->override_class->get_user_names_appended_to_post_id( 1 )
		);
	}
}

class OverrideClass {
	public function get_user_names_appended_to_post_id( $post_id ) {
		// Safely type-cast Post ID.
		$id = absint( $post_id );

		// Get list of users.
		$users = array( 'John Doe', 'Cathryn Washington', 'Jack Foley' );

		// Get user names appended to Post ID.
		return array_map(
			function ( $arg ) use ( $id ) {
				return sprintf( '%s - %d', esc_html( $arg ), $id );
			},
			$users
		);
	}
}

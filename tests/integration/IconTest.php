<?php
namespace BP\UI\Tests;

use BP\UI\Icon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IconTest extends TestCase {

	public function test_the_full_lucide_set_is_there_aliases_included(): void {
		$this->assertSame( '1.52.0', Icon::version() );
		foreach ( [ 'lock', 'filter', 'users', 'circle-check', 'triangle-alert', 'info', 'circle-x' ] as $name ) {
			$this->assertTrue( Icon::exists( $name ), $name );
		}
	}

	public function test_a_decorative_icon_is_hidden_from_screen_readers(): void {
		$svg = Icon::svg( 'lock' );

		$this->assertStringStartsWith( '<svg class="bp-icon bp-icon-lock"', $svg );
		$this->assertStringContainsString( 'aria-hidden="true"', $svg );
		$this->assertStringContainsString( 'stroke="currentColor"', $svg );
		$this->assertStringContainsString( '<path d="M7 11V7a5 5 0 0 1 10 0v4"/>', $svg );
	}

	public function test_a_labelled_icon_is_an_image_with_an_escaped_name(): void {
		$svg = Icon::svg( 'lock', 'Verrouillé "x"', 16, 'menu-lock' );

		$this->assertStringContainsString( 'role="img" aria-label="Verrouillé &quot;x&quot;"', $svg );
		$this->assertStringContainsString( 'class="bp-icon bp-icon-lock menu-lock"', $svg );
		$this->assertStringContainsString( 'width="16" height="16"', $svg );
	}

	public function test_an_unknown_icon_throws(): void {
		$this->expectException( InvalidArgumentException::class );

		Icon::svg( 'lokc' );
	}

	public function test_the_menu_data_uri_draws_in_the_given_colour(): void {
		$uri = Icon::data_uri( 'users', '#a7aaad' );

		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $uri );
		$this->assertStringContainsString( 'stroke="#a7aaad"', (string) base64_decode( substr( $uri, 26 ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- reads the data URI back.
	}

	public function test_a_menu_colour_that_is_not_hex_throws(): void {
		$this->expectException( InvalidArgumentException::class );

		Icon::data_uri( 'users', 'red" onload="x' );
	}

	public function test_no_icon_carries_script(): void {
		$data = require dirname( __DIR__, 2 ) . '/data/lucide-icons.php';
		foreach ( $data['icons'] as $name => $inner ) {
			$this->assertDoesNotMatchRegularExpression( '/<script|on[a-z]+=|javascript:/i', $inner, $name );
		}
	}
}

<?php
namespace BP\UI\Tests;

use BP\UI\Plugin;
use PHPUnit\Framework\TestCase;

final class TokensTest extends TestCase {

	public function test_every_token_in_tokens_json_is_a_variable_in_tokens_css(): void {
		$root   = dirname( __DIR__, 2 );
		$tokens = json_decode( (string) file_get_contents( $root . '/tokens.json' ), true );
		$css    = (string) file_get_contents( $root . '/assets/tokens.css' );

		$names = array_column( $tokens['color']['tokens'], 'name' );
		foreach ( [ 'fontSize', 'spacing', 'size', 'radius', 'shadow', 'duration', 'easing' ] as $family ) {
			$names = array_merge( $names, array_column( $tokens[ $family ]['tokens'], 'name' ) );
		}
		foreach ( $names as $name ) {
			$this->assertStringContainsString( "--{$name}: ", $css, $name );
		}
		$this->assertStringContainsString( '--bp-font-sans: ', $css );
	}

	public function test_in_wp_admin_the_action_colour_is_the_users_scheme(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/assets/tokens.css' );

		$this->assertMatchesRegularExpression( '/body\.wp-admin \{[^}]*--bp-color-action: var\(--wp-admin-theme-color, #3858e9\);/s', $css );
	}

	public function test_reduced_motion_drops_every_duration(): void {
		$css = (string) file_get_contents( dirname( __DIR__, 2 ) . '/assets/tokens.css' );

		$this->assertMatchesRegularExpression( '/prefers-reduced-motion: reduce\) \{\s*:root \{\s*--bp-duration-fast: 0s;/s', $css );
	}

	public function test_the_tokens_are_registered_as_a_stylesheet(): void {
		Plugin::register_assets();

		$this->assertStringEndsWith( 'assets/tokens.css', wp_styles()->registered[ Plugin::TOKENS ]->src );
	}
}

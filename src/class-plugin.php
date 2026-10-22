<?php
namespace BP\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The shared UI layer, booted by the project's must-use loader. Registers Alpine (CSP build) and
 * its focus plugin under fixed handles, and the design tokens; screens enqueue `bp-alpine`, and
 * stylesheets depend on `bp-tokens`.
 */
final class Plugin {

	public const ALPINE = 'bp-alpine';

	public const ALPINE_FOCUS = 'bp-alpine-focus';

	/** The design tokens as CSS variables (assets/tokens.css): a stylesheet depends on this handle. */
	public const TOKENS = 'bp-tokens';

	public const ALPINE_VERSION = '3.17.4';

	public static function boot(): void {
		foreach ( [ 'wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts' ] as $hook ) {
			add_action( $hook, [ self::class, 'register_assets' ], 1, 0 );
		}
	}

	/**
	 * Both deferred: a component script, printed in the footer without `defer`, runs first and
	 * registers its `Alpine.data()` on `alpine:init` before Alpine starts. Plugins load before
	 * Alpine, as Alpine requires.
	 */
	public static function register_assets(): void {
		$args = [
			'in_footer' => true,
			'strategy'  => 'defer',
		];
		wp_register_script( self::ALPINE_FOCUS, self::url( 'alpine-focus.' . self::ALPINE_VERSION . '.min.js' ), [], self::ALPINE_VERSION, $args );
		wp_register_script( self::ALPINE, self::url( 'alpine-csp.' . self::ALPINE_VERSION . '.min.js' ), [ self::ALPINE_FOCUS ], self::ALPINE_VERSION, $args );
		wp_register_style( self::TOKENS, plugins_url( 'assets/tokens.css', dirname( __DIR__ ) . '/composer.json' ), [], self::version() );
	}

	/** A short hash of the tokens file: browsers reload it when, and only when, the tokens change. */
	private static function version(): string {
		return substr( (string) md5_file( dirname( __DIR__ ) . '/assets/tokens.css' ), 0, 8 );
	}

	private static function url( string $file ): string {
		return plugins_url( 'assets/lib/' . $file, dirname( __DIR__ ) . '/composer.json' );
	}
}

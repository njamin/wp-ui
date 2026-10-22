<?php
namespace BP\UI;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lucide icons, inlined as SVG: a page carries only the icons it shows, styled with
 * `currentColor`, so an icon takes the colour of its text.
 */
final class Icon {

	/** @var array{version: string, icons: array<string, string>}|null */
	private static ?array $data = null;

	/**
	 * An icon's SVG. Decorative by default (hidden from screen readers); a `$label` makes it an
	 * image with that accessible name, for an icon that stands alone.
	 *
	 * @throws InvalidArgumentException When Lucide has no icon of that name: a typo must show.
	 */
	public static function svg( string $name, string $label = '', int $size = 20, string $extra_class = '' ): string {
		$inner = self::data()['icons'][ $name ] ?? throw new InvalidArgumentException( 'No Lucide icon named ' . esc_html( $name ) );
		$a11y  = '' === $label ? 'aria-hidden="true" focusable="false"' : 'role="img" aria-label="' . esc_attr( $label ) . '"';

		return sprintf(
			'<svg class="bp-icon bp-icon-%1$s%2$s" xmlns="http://www.w3.org/2000/svg" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" %4$s>%5$s</svg>',
			esc_attr( $name ),
			'' === $extra_class ? '' : ' ' . esc_attr( $extra_class ),
			$size,
			$a11y,
			$inner
		);
	}

	public static function exists( string $name ): bool {
		return isset( self::data()['icons'][ $name ] );
	}

	/**
	 * The icon as a data URI drawn in `$color`, for `add_menu_page()`: WordPress shows a menu
	 * icon as a background image, where `currentColor` has no text to follow.
	 *
	 * @throws InvalidArgumentException When the icon is unknown or `$color` is not a hex colour.
	 */
	public static function data_uri( string $name, string $color = '#a7aaad' ): string {
		if ( 1 !== preg_match( '/^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color ) ) {
			throw new InvalidArgumentException( 'The menu icon colour is a hex colour' );
		}
		$svg = str_replace( 'stroke="currentColor"', 'stroke="' . $color . '"', self::svg( $name ) );

		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- a data URI, not obfuscation.
	}

	/** The Lucide release the icons come from. */
	public static function version(): string {
		return self::data()['version'];
	}

	/** @return array{version: string, icons: array<string, string>} */
	private static function data(): array {
		return self::$data ??= require dirname( __DIR__ ) . '/data/lucide-icons.php';
	}
}

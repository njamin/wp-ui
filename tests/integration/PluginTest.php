<?php
namespace BP\UI\Tests;

use BP\UI\Plugin;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase {

	public function test_alpine_is_registered_deferred_after_its_focus_plugin(): void {
		Plugin::register_assets();

		$alpine = wp_scripts()->registered[ Plugin::ALPINE ];
		$this->assertSame( [ Plugin::ALPINE_FOCUS ], $alpine->deps );
		$this->assertStringEndsWith( 'assets/lib/alpine-csp.3.17.4.min.js', $alpine->src );
		$this->assertSame( 'defer', $alpine->extra['strategy'] ?? '' );
		$this->assertStringEndsWith( 'assets/lib/alpine-focus.3.17.4.min.js', wp_scripts()->registered[ Plugin::ALPINE_FOCUS ]->src );
	}

	public function test_the_vendored_files_exist(): void {
		foreach ( [ 'alpine-csp', 'alpine-focus' ] as $file ) {
			$this->assertFileExists( dirname( __DIR__, 2 ) . '/assets/lib/' . $file . '.' . Plugin::ALPINE_VERSION . '.min.js' );
		}
	}
}

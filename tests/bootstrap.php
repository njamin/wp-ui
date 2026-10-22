<?php
/**
 * Loads the sandbox site (BP_WP_LOAD overrides the path).
 */

require dirname( __DIR__ ) . '/vendor/autoload.php';

$_SERVER['HTTP_HOST']      = 'bp-sandbox.ddev.site';
$_SERVER['SERVER_NAME']    = 'bp-sandbox.ddev.site';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTPS']          = 'on';

$wp_load = getenv( 'BP_WP_LOAD' ) ?: '/var/www/html/sandbox/web/wp/wp-load.php';
if ( ! file_exists( $wp_load ) ) {
	fwrite( STDERR, "wp-load.php not found at {$wp_load}. Run the suite inside DDEV, or set BP_WP_LOAD.\n" );
	exit( 1 );
}

require $wp_load;

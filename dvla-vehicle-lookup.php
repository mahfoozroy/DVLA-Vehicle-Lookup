<?php
/**
* Plugin Name: DVLA Vehicle Lookup
* Description: Vehicle registration lookup form using DVLA API.
* Version: 1.0
* Author: Roy Mahfooz
*/

defined( 'ABSPATH' ) || exit;

// Define API key if not already defined in wp-config.php.
if ( ! defined( 'DVLA_API_KEY' ) ) {
   define( 'DVLA_API_KEY', '' );
}
if ( ! defined( 'DVLA_PATH' ) ) {
	define( 'DVLA_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'DVLA_URL' ) ) {
	define( 'DVLA_URL', plugin_dir_url(__FILE__) );
}

// Autoload plugin classes.
spl_autoload_register( function ( $class ) {
   if ( strpos( $class, 'DVLA_' ) === 0 ) {
	   $file = DVLA_PATH . 'inc/class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
	   if ( file_exists( $file ) ) {
		   require_once $file;
	   }
   }
} );

// Initialize plugin base
add_action( 'plugins_loaded', function() {
   new DVLA_Plugin();
});

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

// Include core classes.
require_once plugin_dir_path( __FILE__ ) . 'class-dvla-vehicle-lookup.php';
require_once plugin_dir_path( __FILE__ ) . 'class-vehicle-lookup-form.php';

/**
 * Initialize plugin functionality after plugins are loaded.
 */
add_action( 'plugins_loaded', function() {
	new VehicleLookupForm();
} );

/**
 * Schedule daily cleanup of vehicle transients on activation.
 */
register_activation_hook( __FILE__, function() {
	if ( ! wp_next_scheduled( 'daily_vehicle_lookup_cleanup' ) ) {
		wp_schedule_event( time(), 'daily', 'daily_vehicle_lookup_cleanup' );
	}
} );

/**
 * Clear scheduled hook on deactivation.
 */
register_deactivation_hook( __FILE__, function() {
	wp_clear_scheduled_hook( 'daily_vehicle_lookup_cleanup' );
} );

/**
 * Perform cleanup of expired vehicle lookup transients.
 */
add_action( 'daily_vehicle_lookup_cleanup', 'cleanup_vehicle_lookup_transients' );

/**
 * Cleans up expired vehicle lookup transients from the database.
 */
function cleanup_vehicle_lookup_transients() {
	global $wpdb;

	$transient_prefix  = '_transient_vehicle_lookup_';
	$option_name_like  = esc_sql( $transient_prefix ) . '%';
	$results           = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$option_name_like
		)
	);

	if ( is_array( $results ) && ! empty( $results ) ) {
		foreach ( $results as $row ) {
			if ( ! empty( $row->option_name ) ) {
				$transient_name = str_replace( '_transient_', '', $row->option_name );
				get_transient( $transient_name ); // Auto-deletes if expired.
			}
		}
	}
}

/**
 * Shortcode to display vehicle data from a transient based on lookup_key in the URL.
 *
 * @return string HTML output of vehicle details.
 */
function display_vehicle_details_shortcode() {
	$message     = '';
	$vehicle_data = [];

	if ( empty( $_GET['lookup_key'] ) ) {
		$message = '<p>Please perform a vehicle lookup first.</p>';
	} else {
		$lookup_key  = sanitize_text_field( wp_unslash( $_GET['lookup_key'] ) );
		$vehicle_data = get_transient( $lookup_key );

		if ( ! $vehicle_data || ! is_array( $vehicle_data ) ) {
			$message = '<p>Vehicle data not available, please add manually on checkout.</p>';
		}
	}

	ob_start();
	?>
	<div class="vehicle-details-wrapper">
		<h2><?php esc_html_e( 'Vehicle Details', 'dvla-lookup' ); ?></h2>
		<?php echo wp_kses_post( $message ); ?>
		<?php if ( ! empty( $vehicle_data ) && is_array( $vehicle_data ) ) : ?>
			<table class="vehicle-details-table">
				<tbody>
					<tr><th><?php esc_html_e( 'Registration Number', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['registrationNumber'] ?? 'N/A' ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Make', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['make'] ?? 'N/A' ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Fuel Type', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['fuelType'] ?? 'N/A' ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Colour', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['colour'] ?? 'N/A' ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Engine Capacity', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['engineCapacity'] ?? 'N/A' ); ?> cc</td></tr>
					<tr><th><?php esc_html_e( 'Wheelplan', 'dvla-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['wheelplan'] ?? 'N/A' ); ?></td></tr>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'display_vehicle_details', 'display_vehicle_details_shortcode' );

/**
 * Register DVLA Lookup Block.
 */
add_action('init', function() {
    wp_register_script(
        'dvla-lookup-block',
        plugins_url( 'src/index.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor'],
        filemtime(plugin_dir_path(__FILE__) . 'blocks/src/index.js')
    );

    register_block_type('dvla-lookup/display-vehicle-details', [
        'editor_script'   => 'dvla-lookup-block',
        'render_callback' => 'dvla_lookup_render_vehicle_details',
    ]);
});

/**
 * Callback to render the block output.
 */
function dvla_lookup_render_vehicle_details() {
    return do_shortcode( '[display_vehicle_details]' );
}

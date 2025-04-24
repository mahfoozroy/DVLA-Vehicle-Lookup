<?php
/**
 * Plugin Name: DVLA Vehicle Lookup
 * Description: Vehicle registration lookup form using DVLA API.
 * Version: 1.0
 * Author: Roy Mahfooz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'DVLA_API_KEY' ) ) {
    define( 'DVLA_API_KEY', '' );
}

require_once plugin_dir_path(__FILE__) . 'class-dvla-vehicle-lookup.php';
require_once plugin_dir_path(__FILE__) . 'class-vehicle-lookup-form.php';

add_action( 'plugins_loaded', function() {
    new VehicleLookupForm();
} );

register_activation_hook( __FILE__, function() {
    if ( ! wp_next_scheduled( 'daily_vehicle_lookup_cleanup' ) ) {
        wp_schedule_event( time(), 'daily', 'daily_vehicle_lookup_cleanup' );
    }
} );

register_deactivation_hook( __FILE__, function() {
    wp_clear_scheduled_hook('daily_vehicle_lookup_cleanup');
} );

add_action( 'daily_vehicle_lookup_cleanup', 'cleanup_vehicle_lookup_transients' );

function cleanup_vehicle_lookup_transients() {
    global $wpdb;

    $transient_prefix = '_transient_vehicle_lookup_';
    $option_name_like = esc_sql( $transient_prefix ) . '%';

    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
        $option_name_like
    ) );

    if ( is_array( $results ) && !empty( $results ) ) {
        foreach ( $results as $row ) {
            if ( ! empty( $row->option_name ) ) {
                $transient_name = str_replace( '_transient_', '', $row->option_name );
                get_transient( $transient_name ); // This auto-cleans expired transient.
            }
        }
    }
}

function display_vehicle_details_shortcode() {
    $message = '';
    if ( empty($_GET['lookup_key'] ) ) {
        $message = '<p>Please perform a vehicle lookup first.</p>';
    }

    $lookup_key = sanitize_text_field($_GET['lookup_key']);
    $vehicle_data = get_transient($lookup_key);

    if ( !$vehicle_data || !is_array( $vehicle_data ) ) {
        $message = '<p>Vehicle data not available, please add manually on checkout.</p>';
    }

    ob_start();
    ?>
    <div class="vehicle-details-wrapper">
        <h2>Vehicle Details</h2>
        <?php echo $message; ?>
        <table class="vehicle-details-table">
            <tbody>
                <tr><th>Registration Number</th><td><?php echo esc_html($vehicle_data['registrationNumber'] ?? 'N/A'); ?></td></tr>
                <tr><th>Make</th><td><?php echo esc_html($vehicle_data['make'] ?? 'N/A'); ?></td></tr>
                <tr><th>Fuel Type</th><td><?php echo esc_html($vehicle_data['fuelType'] ?? 'N/A'); ?></td></tr>
                <tr><th>Colour</th><td><?php echo esc_html($vehicle_data['colour'] ?? 'N/A'); ?></td></tr>
                <tr><th>Engine Capacity</th><td><?php echo esc_html($vehicle_data['engineCapacity'] ?? 'N/A'); ?> cc</td></tr>
                <tr><th>Wheelplan</th><td><?php echo esc_html($vehicle_data['wheelplan'] ?? 'N/A'); ?></td></tr>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('display_vehicle_details', 'display_vehicle_details_shortcode');

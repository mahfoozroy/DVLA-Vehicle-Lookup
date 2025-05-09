<?php
/**
 * Core plugin loader and bootstrap class.
 */
class DVLA_Plugin {

	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize plugin features.
	 */
	private function init() {
		new DVLA_Lookup_Form();
		new DVLA_Blocks();
		new DVLA_Shortcodes();
		$this->setup_cron();
	}

	/**
	 * Setup cron to clear trasients if missed somehow to avoid piling up DB.
	 */
	private function setup_cron() {
		add_action( 'daily_vehicle_lookup_cleanup', [ $this, 'cleanup_transients' ] );

		register_activation_hook( __FILE__, function() {
			if ( ! wp_next_scheduled( 'daily_vehicle_lookup_cleanup' ) ) {
				wp_schedule_event( time(), 'daily', 'daily_vehicle_lookup_cleanup' );
			}
		} );

		register_deactivation_hook( __FILE__, function() {
			wp_clear_scheduled_hook( 'daily_vehicle_lookup_cleanup' );
		} );
	}

	/**
	 * Cleanup expired transients.
	 */
	public function cleanup_transients() {
		global $wpdb;
		$prefix = '_transient_vehicle_lookup_';
		$like   = esc_sql( $prefix ) . '%';
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$like
		) );
		if ( is_array( $results ) && ! empty( $results ) ) {
			foreach ( $results as $row ) {
				if ( ! empty( $row->option_name ) ) {
					$transient = str_replace( '_transient_', '', $row->option_name );
					get_transient( $transient );
				}
			}
		}
	}
}

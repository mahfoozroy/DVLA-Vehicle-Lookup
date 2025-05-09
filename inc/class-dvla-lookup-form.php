<?php
/**
 * Vehicle Lookup Form Class
 *
 * Renders the shortcode form, handles AJAX calls, and manages DVLA lookup logic.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class VehicleLookupForm
 */
class DVLA_Lookup_Form {

    /**
     * DVLA API key.
     *
     * @var string
     */
    private $api_key;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->api_key = defined( 'DVLA_API_KEY' ) ? DVLA_API_KEY : '';
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_vehicle_lookup', [ $this, 'handle_ajax' ] );
        add_action( 'wp_ajax_nopriv_vehicle_lookup', [ $this, 'handle_ajax' ] );
    }

    /**
     * Enqueue front-end styles and scripts.
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'vehicle-lookup-style',
            DVLA_URL . 'assets/vehicle-lookup.css',
            [],
            '1.0'
        );

        wp_enqueue_script(
            'vehicle-lookup',
            DVLA_URL . 'assets/vehicle-lookup.js',
            [ 'jquery' ],
            '1.0',
            true
        );

        wp_localize_script(
            'vehicle-lookup',
            'VehicleLookup',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'vehicle_lookup_nonce' ),
            ]
        );
    }

    /**
     * Handle AJAX request for vehicle lookup.
     */
    public function handle_ajax() {
        check_ajax_referer( 'vehicle_lookup_nonce', 'nonce' );

        if ( empty( $_POST['registration'] ) ) {
            wp_send_json_error( [ 'message' => 'Please provide a registration number' ] );
        }

        $registration = sanitize_text_field( wp_unslash( $_POST['registration'] ) );
        $lookup       = new DVLA_Vehicle_Lookup( $this->api_key );

        if ( ! $lookup->lookupVehicle( $registration ) ) {
            wp_send_json_error( [ 'message' => $lookup->getLastError() ] );
        }

        $data          = $lookup->getAllData();
        $transient_key = 'vehicle_lookup_' . md5( $registration );

        // Prepare response.
        $response = [
            'registration'   => isset( $data['registrationNumber'] ) ? $data['registrationNumber'] : $registration,
            'make'           => isset( $data['make'] ) ? $data['make'] : '',
            'transient_key'  => $transient_key,
        ];

        // Store transient.
        set_transient( $transient_key, $data, 15 * MINUTE_IN_SECONDS );

        wp_send_json_success( $response );
    }
}

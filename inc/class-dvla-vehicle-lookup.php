<?php
/**
 * DVLA Vehicle Lookup Class
 *
 * Handles communication with the UK DVLA Vehicle Enquiry Service API.
 *
 * @link https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class DVLA_Vehicle_Lookup
 */
class DVLA_Vehicle_Lookup {

    /**
     * DVLA API Key
     *
     * @var string
     */
    private $api_key;

    /**
     * Last error message, if any
     *
     * @var string
     */
    private $last_error = '';

    /**
     * Last vehicle data returned by DVLA
     *
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param string $api_key Your DVLA API key.
     */
    public function __construct( $api_key ) {
        $this->api_key = $api_key;
    }

    /**
     * Perform a vehicle lookup using the DVLA API.
     *
     * @param string $registration Vehicle registration number.
     * @return bool True if successful, false on failure.
     */
    public function lookup_vehicle( $registration ) {
        $url      = 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
        $request  = [
            'headers' => [
                'x-api-key'    => $this->api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode( [ 'registrationNumber' => $registration ] ),
            'timeout' => 10,
        ];
        $response = wp_remote_post( $url, $request );

        if ( is_wp_error( $response ) ) {
            $this->last_error = $response->get_error_message();
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        // Handle API errors.
        if ( isset( $body['httpStatusCode'] ) && $body['httpStatusCode'] >= 400 ) {
            $this->last_error = $body['message'] ?? 'Unknown error occurred';
            return false;
        }

        $this->data = is_array( $body ) ? $body : [];
        return true;
    }

    /**
     * Retrieve all vehicle data returned from the last successful lookup.
     *
     * @return array The vehicle data array.
     */
    public function get_all_data() {
        return $this->data;
    }

    /**
     * Get the last error message (if any).
     *
     * @return string The error message.
     */
    public function get_last_error() {
        return $this->last_error;
    }
}

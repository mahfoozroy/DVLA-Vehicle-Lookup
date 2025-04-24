<?php

class DVLA_Vehicle_Lookup {
    private $api_key;
    private $last_error = '';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function lookupVehicle($registration) {
        $url = 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
        $response = wp_remote_post($url, [
            'headers' => [
                'x-api-key' => $this->api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode(['registrationNumber' => $registration]),
            'timeout' => 10
        ]);

        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['httpStatusCode']) && $body['httpStatusCode'] >= 400) {
            $this->last_error = $body['message'] ?? 'Unknown error occurred';
            return false;
        }

        $this->data = $body;
        return true;
    }

    public function getAllData() {
        return $this->data ?? [];
    }

    public function getLastError() {
        return $this->last_error;
    }
}
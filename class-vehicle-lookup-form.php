<?php

class VehicleLookupForm {

    private $api_key;

    public function __construct() {
        $this->api_key = defined('DVLA_API_KEY') ? DVLA_API_KEY : '';

        add_shortcode('vehicle_lookup_form', [$this, 'renderForm']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_ajax_vehicle_lookup', [$this, 'handleAjax']);
        add_action('wp_ajax_nopriv_vehicle_lookup', [$this, 'handleAjax']);
    }

    public function enqueueScripts() {
        wp_enqueue_style('vehicle-lookup-style', plugins_url('assets/vehicle-lookup.css', __FILE__), 1.0 );
        wp_enqueue_script('vehicle-lookup', plugins_url('assets/vehicle-lookup.js', __FILE__), ['jquery'], 1.0, true);
        wp_localize_script('vehicle-lookup', 'VehicleLookup', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('vehicle_lookup_nonce')
        ]);
    }

    public function renderForm($atts) {
        $atts = shortcode_atts([
            'redirect' => ''
        ], $atts);

        ob_start();
        ?>
        <form id="vehicle-lookup-form">
            <label for="vehicle-registration">Vehicle Registration *</label>
            <div class="vehicle-input-wrapper">
                <i class="awb-form-icon fas fa-car"></i>
                <input type="text" id="vehicle-registration" name="vehicle_registration" placeholder="Registration Number">
            </div>
            <button type="submit">Book Mechanic</button>
            <div id="lookup-message"></div>
        </form>
        <script type="text/javascript">
            window.vehicleLookupRedirectUrl = '<?php echo esc_url($atts['redirect']); ?>';
        </script>

        <div id="lookup-message" style="margin-top: 10px;"></div>
        <?php
        return ob_get_clean();
    }

    public function handleAjax() {
        check_ajax_referer('vehicle_lookup_nonce', 'nonce');

        if (empty($_POST['registration'])) {
            wp_send_json_error(['message' => 'Please provide a registration number']);
        }

        $registration = sanitize_text_field($_POST['registration']);
        $lookup = new DVLA_Vehicle_Lookup($this->api_key);

        if (!$lookup->lookupVehicle($registration)) {
            wp_send_json_error(['message' => $lookup->getLastError()]);
        }

        $transient_key = 'vehicle_lookup_' . md5($registration);
        $data          = $lookup->getAllData();
        $response      = [];
        if ( isset( $data['registrationNumber'] ) ) {
            $response['registration'] = $data['registrationNumber'];
        } else {
            $response['registration'] = $registration;
        }
        if ( isset( $data['make'] ) ) {
            $response['make'] = $data['make'] ? $data['make'] : '';
        } 
        $response['transient_key'] = $transient_key;
        set_transient( $transient_key, $lookup->getAllData(), 15 * MINUTE_IN_SECONDS );

        wp_send_json_success( $response );
    }
}
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
class VehicleLookupForm {

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

        add_shortcode( 'vehicle_lookup_form', [ $this, 'renderForm' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
        add_action( 'wp_ajax_vehicle_lookup', [ $this, 'handleAjax' ] );
        add_action( 'wp_ajax_nopriv_vehicle_lookup', [ $this, 'handleAjax' ] );
    }

    /**
     * Enqueue front-end styles and scripts.
     */
    public function enqueueScripts() {
        wp_enqueue_style(
            'vehicle-lookup-style',
            plugins_url( 'assets/vehicle-lookup.css', __FILE__ ),
            [],
            '1.0'
        );

        wp_enqueue_script(
            'vehicle-lookup',
            plugins_url( 'assets/vehicle-lookup.js', __FILE__ ),
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
     * Render the vehicle lookup form shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function renderForm( $atts ) {
        $atts = shortcode_atts(
            [
                'redirect' => '',
            ],
            $atts,
            'vehicle_lookup_form'
        );

        ob_start();
        ?>
        <form id="vehicle-lookup-form">
            <label for="vehicle-registration"><?php esc_html_e( 'Vehicle Registration *', 'your-text-domain' ); ?></label>
            <div class="vehicle-input-wrapper">
                <i class="awb-form-icon fas fa-car"></i>
                <input type="text" id="vehicle-registration" name="vehicle_registration" placeholder="<?php esc_attr_e( 'Registration Number', 'your-text-domain' ); ?>">
            </div>
            <button type="submit"><?php esc_html_e( 'Book Mechanic', 'your-text-domain' ); ?></button>
            <div id="lookup-message" style="margin-top: 10px;"></div>
        </form>
        <script type="text/javascript">
            window.vehicleLookupRedirectUrl = '<?php echo esc_js( esc_url( $atts['redirect'] ) ); ?>';
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle AJAX request for vehicle lookup.
     */
    public function handleAjax() {
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

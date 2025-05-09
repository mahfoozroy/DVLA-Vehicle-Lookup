<?php
/**
 * Core plugin loader and bootstrap class.
 */
class DVLA_Shortcodes {
	public function __construct() {
		add_shortcode( 'display_vehicle_details', [ $this, 'render_vehicle_details_shortcode' ] );
		add_shortcode( 'vehicle_lookup_form', [ $this, 'renderForm' ] );
	}
	/**
	* Shortcode callback.
	*/
	public function render_vehicle_details_shortcode() {
		$message = '';
		$vehicle_data = [];

		if ( empty( $_GET['lookup_key'] ) ) {
			$message = '<p>Please perform a vehicle lookup first.</p>';
		} else {
			$lookup_key = sanitize_text_field( wp_unslash( $_GET['lookup_key'] ) );
			$vehicle_data = get_transient( $lookup_key );
			if ( ! $vehicle_data || ! is_array( $vehicle_data ) ) {
				$message = '<p>Vehicle data not available, please add manually on checkout.</p>';
			}
		}

		ob_start();
		?>
		<div class="vehicle-details-wrapper">
			<h2><?php esc_html_e( 'Vehicle Details', 'dvla-vehicle-lookup' ); ?></h2>
			<?php echo wp_kses_post( $message ); ?>
			<?php if ( ! empty( $vehicle_data ) && is_array( $vehicle_data ) ) : ?>
				<table class="vehicle-details-table">
					<tbody>
						<tr><th><?php esc_html_e( 'Registration Number', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['registrationNumber'] ?? 'N/A' ); ?></td></tr>
						<tr><th><?php esc_html_e( 'Make', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['make'] ?? 'N/A' ); ?></td></tr>
						<tr><th><?php esc_html_e( 'Fuel Type', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['fuelType'] ?? 'N/A' ); ?></td></tr>
						<tr><th><?php esc_html_e( 'Colour', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['colour'] ?? 'N/A' ); ?></td></tr>
						<tr><th><?php esc_html_e( 'Engine Capacity', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['engineCapacity'] ?? 'N/A' ); ?> cc</td></tr>
						<tr><th><?php esc_html_e( 'Wheelplan', 'dvla-vehicle-lookup' ); ?></th><td><?php echo esc_html( $vehicle_data['wheelplan'] ?? 'N/A' ); ?></td></tr>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
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
            <label for="vehicle-registration"><?php esc_html_e( 'Vehicle Registration *', 'dvla-vehicle-lookup' ); ?></label>
            <div class="vehicle-input-wrapper">
                <i class="awb-form-icon fas fa-car"></i>
                <input type="text" id="vehicle-registration" name="vehicle_registration" placeholder="<?php esc_attr_e( 'Registration Number', 'dvla-vehicle-lookup' ); ?>">
            </div>
            <button type="submit"><?php esc_html_e( 'Book Mechanic', 'dvla-vehicle-lookup' ); ?></button>
            <div id="lookup-message" style="margin-top: 10px;"></div>
        </form>
        <script type="text/javascript">
            window.vehicleLookupRedirectUrl = '<?php echo esc_js( esc_url( $atts['redirect'] ) ); ?>';
        </script>
        <?php
        return ob_get_clean();
    }
}

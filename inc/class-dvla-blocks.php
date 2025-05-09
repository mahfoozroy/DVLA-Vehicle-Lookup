<?php
/**
* Block registration and callbacks.
*/
class DVLA_Blocks {
	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}
	/**
	* Register blocks.
	*/
	public function register_blocks() {
		wp_register_script(
			'dvla-lookup-block',
			DVLA_URL . 'build/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ],
			filemtime( DVLA_PATH . 'src/index.js' )
		);

		register_block_type( 'dvla-lookup/display-vehicle-details', [
			'editor_script'   => 'dvla-lookup-block',
			'render_callback' => [ $this, 'render_vehicle_details' ],
		] );

		register_block_type( 'dvla-lookup/vehicle-lookup-form', [
			'editor_script'   => 'dvla-lookup-block',
			'render_callback' => [ $this, 'render_lookup_form' ],
			'attributes'      => [
				'redirect' => [
					'type'    => 'string',
					'default' => '/booking-page',
				],
			],
		] );
	}

	/**
	* Callbacks to vehicle details block.
	*/
	public function render_vehicle_details() {
		return do_shortcode( '[display_vehicle_details]' );
	}

	/**
	* Callbacks for lookup form.
	*/
	public function render_lookup_form( $attributes ) {
		$redirect = isset( $attributes['redirect'] ) ? esc_url( $attributes['redirect'] ) : '';
		return do_shortcode( '[vehicle_lookup_form redirect="' . $redirect . '"]' );
	}
}

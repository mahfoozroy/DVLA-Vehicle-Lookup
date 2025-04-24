jQuery(function($) {
    $('#vehicle-lookup-form').on('submit', function(e) {
        e.preventDefault();

        let registration = $('#vehicle-registration').val();
        if (!registration) {
            alert('Please enter a registration number');
            return;
        }

        $.post(VehicleLookup.ajax_url, {
            action: 'vehicle_lookup',
            nonce: VehicleLookup.nonce,
            registration: registration
        }).done( function(response ) {
            if ( response.success ) {
                let redirectUrl = window.vehicleLookupRedirectUrl;
                let make = response.data.make ? response.data.make : '';
                redirectUrl += (redirectUrl.includes('?') ? '&' : '?') + 'lookup_key=' + response.data.transient_key + '&text_3U58o=' + response.data.registration + '&text_ss9Hq=' + make;
                window.location.href = redirectUrl;
            } else {
                $('#lookup-message').text(response.data.message || 'An error occurred.');
            }
        }).fail(function() {
            $('#lookup-message').text('Request failed. Please try again.');
        });
    });
});
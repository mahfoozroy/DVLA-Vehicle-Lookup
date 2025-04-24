=== DVLA Vehicle Lookup ===
Contributors: your-name
Tags: dvla, vehicle, registration, uk, lookup, booking, api, mechanic
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress plugin to lookup UK vehicle registration numbers using the DVLA API and redirect users to a booking or information page with results.

== Description ==

**DVLA Vehicle Lookup** lets users enter a UK vehicle registration number and fetch official data from the DVLA (Driver and Vehicle Licensing Agency) API. The plugin is perfect for mechanic booking websites, MOT check tools, or vehicle evaluation sites.

✅ Uses official DVLA Vehicle Enquiry Service API  
✅ Shows a styled input form with vehicle icon  
✅ AJAX lookup with live validation  
✅ Saves vehicle data in a WordPress transient  
✅ Redirects users to a results or booking page  
✅ Shortcode-based, lightweight, and flexible

== Features ==

- Clean input form with Font Awesome icon
- Dynamic redirection after lookup (via shortcode attribute)
- Transient caching to reduce API calls
- Vehicle data display shortcode with formatted table
- Customizable confirmation page
- Graceful error handling and validation
- Daily cleanup of expired transients

== Installation ==

1. Download the plugin ZIP and upload via **Plugins > Add New > Upload Plugin**
2. Activate the plugin
3. Define your DVLA API Key in `wp-config.php`:

```php
define('DVLA_API_KEY', 'your_api_key_here');

[vehicle_lookup_form redirect="/booking-details/"]

This will show a vehicle registration input and redirect to the URL with ?lookup_key=... after lookup.

== Shortcodes ==

[vehicle_lookup_form redirect="/your-page/"]

Shows the form to enter a UK registration number. The redirect attribute specifies where to go after a successful lookup.

[display_vehicle_details]

Displays the vehicle data stored via transient. Must be placed on the page defined in redirect.

== Example Flow ==

User enters vehicle registration number on a lookup page.

Plugin sends request to DVLA API and caches response.

User is redirected to a page like /booking-details/?lookup_key=abc123

[display_vehicle_details] shortcode on that page displays the vehicle data.

== Frequently Asked Questions ==

= Where do I get a DVLA API key? =
Register for a key at: https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service

= What happens if the API fails? =
A friendly error message is shown to the user and data is not stored.

= Does this plugin support caching? =
Yes — vehicle data is cached in WordPress transients for 15 minutes by default.

= Will expired transients pile up in my database? =
No — the plugin includes a daily cleanup cron job that removes expired transients starting with vehicle_lookup_.

== Changelog ==

= 1.1 =

Added styled input form with icon

Added transient-based caching

Added shortcode for displaying results

Added daily cleanup cron

Improved error handling and security checks

== Upgrade Notice ==

= 1.1 = Includes all core features with cleanup and display support. Recommended update.

== License ==

GPLv2 or later
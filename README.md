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

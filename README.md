# DVLA Vehicle Lookup for WordPress

**DVLA Vehicle Lookup** is a lightweight WordPress plugin that lets users enter a UK vehicle registration number and fetch official data from the DVLA (Driver and Vehicle Licensing Agency) API. It is ideal for mechanic booking websites, MOT check tools, or vehicle data portals.

## ✅ Features

- Clean AJAX-based vehicle registration form with Font Awesome icon
- Uses official [DVLA Vehicle Enquiry Service API](https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service)
- Redirect users to any page after lookup with dynamic query key
- Stores vehicle data using WordPress transients (15 min expiry)
- Includes daily cleanup cron job to prevent database bloat
- Shortcode to render vehicle data in a responsive table
- Proper error handling and fallback messaging

---

## 🔧 Installation

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install via WordPress admin.
2. Activate the plugin.
3. Define your DVLA API key in `wp-config.php`:

```php
define('DVLA_API_KEY', 'your_api_key_here');
```

4. Use the lookup shortcode:

```text
[vehicle_lookup_form redirect="/booking-details/"]
```

5. On your target booking/details page, use this shortcode to display results:

```text
[display_vehicle_details]
```

---

## 🔌 Shortcodes

### `[vehicle_lookup_form redirect="/your-page/"]`

- Renders a styled form to input vehicle registration.
- On submit, performs AJAX lookup, saves results to a transient.
- Redirects to the given `redirect` URL with a `lookup_key` in the query string.

### `[display_vehicle_details]`

- Reads the transient based on `lookup_key` from URL.
- Displays a well-formatted table of vehicle data.
- Automatically handles missing fields.

---

## 🚀 Example User Flow

1. User visits `/lookup`, enters vehicle reg number.
2. Plugin sends data to DVLA and saves results in a transient.
3. User is redirected to `/booking-details/?lookup_key=abc123`.
4. That page contains `[display_vehicle_details]` and shows vehicle info.

---

## 🧼 Daily Cleanup Cron

The plugin includes a scheduled daily cron job that scans for and removes expired transients beginning with `vehicle_lookup_`. This prevents your `wp_options` table from bloating over time.

---

## 💡 Frequently Asked Questions

**Where can I get a DVLA API key?**  
You can register at: https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service

**Is the data cached?**  
Yes. Vehicle data is stored in a transient for 15 minutes to reduce API calls.

**What if the API fails?**  
A clean error message is shown to the user. The page does not crash.

**Can I change the redirect page?**  
Yes — you control it via the `redirect` attribute in the shortcode.

**Is it compatible with page builders?**  
Yes — you can add the shortcodes anywhere shortcodes are supported (Gutenberg, Elementor, etc.).

---

## 📄 License

This plugin is open-source and released under the GPL v2 or later license.

---

## 📝 Changelog

### 1.1
- Added styled input form with Font Awesome icon
- Added transient caching for DVLA responses
- Added shortcode to display results
- Implemented daily cron cleanup for expired transients
- Improved error handling and validation

---

## 👨‍💻 Author

Created by: **Your Name**  
Website: [https://roymahfooz.com](https://roymahfooz.com)

Need help or customization? Reach out!
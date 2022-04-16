# Wapuus API (WP Plugin)

For local development, install the [Local WP](https://localwp.com/ "Local WP Homepage") and create a local WordPress website with the following domain: **wapuus-api.local**

After that insert the plugin folder on the */wp-content/plugins/* dir and active it on the WordPress admin dashboard.

In the **wp-config.php** file you can set the following options:

```php
/**
 * Values: true or your local development address as 'http://localhost:3000'
 */
define( 'WAPUUS_API_RESTRICTED_MODE', 'http://localhost:3000' );

/**
 * Prevents the demo user from adding new content or deleting old ones.
 */
define( 'WAPUUS_API_DEMO_USER_RESTRICTED', false );
```

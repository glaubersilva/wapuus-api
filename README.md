# Wapuus API - WordPress Plugin

For local development, install the [Local WP](https://localwp.com/ "Local WP Homepage") and create a local WordPress website with the following domain: 

**wapuus-api.local**

After that insert the plugin folder on the */wp-content/plugins/* dir and active it on the WordPress admin dashboard.

In the **wp-config.php** file you can set the following options:

```php
/**
 * BLock API access from non authorized domains.
 * Acceptable values: true or your local development address as 'http://localhost:3000'
 */
define( 'WAPUUS_API_RESTRICTED_MODE', 'http://localhost:3000' );

/**
 * Prevents the demo user from adding new content or deleting old ones.
 */
define( 'WAPUUS_API_DEMO_USER_RESTRICTED', false );
```

## Testing

Tests use PHPUnit with WordPress installed via Composer.

1. **Install dependencies** (includes WordPress and PHPUnit):

   ```bash
   composer install
   ```

2. **Configure the test environment**  
   If `tests/wp-tests-config.php` does not exist, it is created from `tests/wp-tests-config.dist.php` on `composer install`. Edit `tests/wp-tests-config.php` and set your database credentials (DB_NAME, DB_USER, DB_PASSWORD, DB_HOST).  
   Use **port** (TCP) or **socket** depending on how/where your DB is configured: `host:port` (e.g. `127.0.0.1:3306`) or `localhost:/path/to/socket` (e.g. Local by Flywheel: `localhost:/path/to/Application Support/Local/run/YOUR_SITE/mysql/mysqld.sock`).

3. **Run tests**:

   ```bash
   composer test
   ```

   Or: `composer run test` / `./vendor/bin/phpunit --colors`

   You can run specific tests using `--filter` followed by the class name and/or method:

   ```bash
   composer test -- --filter Wapuus_API_V1_Comments_Tests
   composer test -- --filter test_stats_get
   composer test -- --filter Wapuus_API_V2_Comments_Tests::test_comments_post
   ```

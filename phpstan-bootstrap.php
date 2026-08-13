<?php
/**
 * PHPStan bootstrap.
 *
 * Declares the constants this plugin defines at runtime in bp-job-manager.php
 * so static analysis resolves them without executing the bootstrap file.
 *
 * @package BP_Job_Manager
 */

define( 'BPJM_PLUGIN_VERSION', '3.0.0' );
define( 'BPJM_PLUGIN_PATH', __DIR__ . '/' );
define( 'BPJM_PLUGIN_URL', 'https://example.test/wp-content/plugins/bp-job-manager/' );
define( 'BPJM_PLUGIN_BASENAME', 'bp-job-manager/bp-job-manager.php' );

<?php
/**
 * Plugin Name: SiteCare Maintenance Mode
 * Plugin URI: https://github.com/widumalt/sitecare-maintenance-mode
 * Description: A beginner-friendly maintenance mode plugin skeleton for WordPress. Functional maintenance mode features will be added in a later phase.
 * Version: 1.0.0
 * Author: SiteCare Maintenance Mode Contributors
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sitecare-maintenance-mode
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 */
define( 'SITECARE_MAINTENANCE_VERSION', '1.0.0' );

/**
 * Absolute path to this main plugin file.
 */
define( 'SITECARE_MAINTENANCE_FILE', __FILE__ );

/**
 * Absolute path to the plugin directory.
 */
define( 'SITECARE_MAINTENANCE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * URL to the plugin directory.
 */
define( 'SITECARE_MAINTENANCE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Runs when the plugin is activated.
 *
 * Phase 1 does not create database tables or save settings. This callback is
 * intentionally minimal so activation stays safe while the skeleton is tested.
 *
 * @return void
 */
function sitecare_maintenance_activate() {
	// Phase 1: no setup is required yet.
}

/**
 * Runs when the plugin is deactivated.
 *
 * Phase 1 does not register cron jobs, rewrite rules, or other persistent
 * behavior, so there is nothing to clean up yet.
 *
 * @return void
 */
function sitecare_maintenance_deactivate() {
	// Phase 1: no cleanup is required yet.
}

register_activation_hook( __FILE__, 'sitecare_maintenance_activate' );
register_deactivation_hook( __FILE__, 'sitecare_maintenance_deactivate' );

<?php
/**
 * Uninstall file for SiteCare Maintenance Mode.
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sitecare_maintenance_options' );

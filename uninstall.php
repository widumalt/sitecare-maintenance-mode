<?php
/**
 * Uninstall file for SiteCare Maintenance Mode.
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Phase 2 stores a small settings option for maintenance mode.
 *
 * For now, uninstall cleanup is intentionally conservative and does not delete
 * settings. A later phase can add a clear cleanup policy if the project needs
 * complete data removal on uninstall.
 */

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
 * Phase 1 does not save plugin settings or create database tables.
 *
 * Cleanup logic will be added in a later phase after the plugin starts storing
 * options. Until then, there is nothing to delete on uninstall.
 */

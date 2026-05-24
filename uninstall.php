<?php
/**
 * Uninstall file for SitePause.
 *
 * @package SitePause
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sitecare_maintenance_options' );

<?php
/**
 * Uninstall file for SitePause.
 *
 * @package SitePause
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sitepause_maintenance_options' );

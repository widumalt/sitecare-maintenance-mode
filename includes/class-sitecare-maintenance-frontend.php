<?php
/**
 * Frontend maintenance mode class.
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers frontend hooks for maintenance rendering and preview mode.
 */
class SiteCare_Maintenance_Frontend {

	/**
	 * Registers frontend-related hooks.
	 *
	 * The existing procedural renderer remains in place while this phase starts
	 * a small, beginner-friendly class structure.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'template_redirect', array( $this, 'maybe_render_page' ) );
	}

	/**
	 * Renders maintenance or preview page when needed.
	 *
	 * @return void
	 */
	public function maybe_render_page() {
		sitecare_maintenance_maybe_render_page();
	}
}

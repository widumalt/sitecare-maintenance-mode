<?php
/**
 * Minimal maintenance preset.
 *
 * @package SiteCareMaintenanceMode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php sitecare_maintenance_template_logo( $template_data['logo_url'] ); ?>

<h1><?php echo esc_html( $template_data['title'] ); ?></h1>

<p class="sitecare-maintenance-message"><?php echo esc_html( $template_data['message'] ); ?></p>

<?php
sitecare_maintenance_template_countdown( $template_data['countdown_timestamp'] );
sitecare_maintenance_template_contact( $template_data );
sitecare_maintenance_template_footer( $template_data['options']['footer_text'] );

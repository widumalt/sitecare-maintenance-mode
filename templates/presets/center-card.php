<?php
/**
 * Center Card maintenance preset.
 *
 * @package SitePause
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php sitepause_maintenance_template_logo( $template_data['logo_url'] ); ?>

<h1><?php echo esc_html( $template_data['title'] ); ?></h1>

<p class="sitepause-maintenance-message"><?php echo esc_html( $template_data['message'] ); ?></p>

<?php
sitepause_maintenance_template_countdown( $template_data['countdown_timestamp'] );
sitepause_maintenance_template_contact( $template_data );
sitepause_maintenance_template_footer( $template_data['options']['footer_text'] );

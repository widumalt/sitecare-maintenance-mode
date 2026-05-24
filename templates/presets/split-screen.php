<?php
/**
 * Split Screen maintenance preset.
 *
 * @package SitePause
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sitepause-maintenance-split-primary">
	<?php sitepause_maintenance_template_logo( $template_data['logo_url'] ); ?>

	<h1><?php echo esc_html( $template_data['title'] ); ?></h1>

	<p class="sitepause-maintenance-message"><?php echo esc_html( $template_data['message'] ); ?></p>

	<?php sitepause_maintenance_template_countdown( $template_data['countdown_timestamp'] ); ?>
</div>

<div class="sitepause-maintenance-split-secondary">
	<?php sitepause_maintenance_template_contact( $template_data ); ?>
	<?php sitepause_maintenance_template_footer( $template_data['options']['footer_text'] ); ?>
</div>

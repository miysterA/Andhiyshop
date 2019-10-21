<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * @var $this \WPOnion\Theme\WP_Lite|\WPOnion\Theme\WP
 */
$module = $this->settings();

echo '<div ' . $module->wrap_attributes() . '>';
include wponion()->tpl( 'global/parts/settings/header.php' );
include wponion()->tpl( 'global/parts/settings/content.php' );
include wponion()->tpl( 'global/parts/settings/footer.php' );
echo '</div>'; // div end

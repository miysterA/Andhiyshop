<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * @var \WPOnion\Theme\WP_Modern $this
 * @var \WPO\Builder             $fields
 */
$user_profile = $this->user_profile();
$fields       = $user_profile->fields();
?>

<?php if ( ! empty( $user_profile->option( 'heading' ) ) ) { ?>
	<h2><?php echo $user_profile->option( 'heading' ); ?></h2>
<?php } ?>

<div <?php echo $user_profile->wrap_attributes(); ?>>
	<?php
	foreach ( $fields->get() as $field ) {
		echo $user_profile->render_field( $field );
	}
	?>
</div>

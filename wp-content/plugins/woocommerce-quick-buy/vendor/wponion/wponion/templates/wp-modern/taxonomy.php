<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * @var \WPOnion\Theme\WP_Modern  $this
 * @var \WPOnion\Modules\Taxonomy $taxonomy
 * @var \WPO\Builder              $fields
 */
$taxonomy = $this->taxonomy();
$fields   = $taxonomy->fields();
?>

<div <?php echo $taxonomy->wrap_attributes(); ?>>
	<?php
	foreach ( $fields->get() as $field ) {
		echo $taxonomy->render_field( $field );
	}
	?>
</div>

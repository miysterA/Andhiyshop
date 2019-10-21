<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/* @var $this \WPOnion\Theme\WC */
$module      = $this->settings();
$title       = $module->option( 'framework_title' );
$desc        = $module->option( 'framework_desc' );
$single_page = $module->is_single_page();
$search      = ( true === $module->option( 'search', true ) ) ? __( 'Search Option(s)', 'wponion' ) : $module->option( 'search' );
$is_sticky   = $module->option( 'sticky_header', true );
$is_sticky   = ( true === $is_sticky ) ? 'header-sticky' : '';
?>

<header class="<?php echo $is_sticky; ?>">

	<div class="inner-container row middle-xs">

		<div class="left col-xs-12 col-sm-12 col-md-6">
			<?php
			echo ( ! empty( $title ) ) ? '<h1>' . $title . '</h1>' : '';
			echo ( ! empty( $desc ) ) ? '<p>' . $desc . '</p>' : '';
			?>
		</div>


		<div class="right col-xs-12 col-sm-12 col-md-6">
			<?php if ( false !== $single_page && false !== $search ) : ?>
				<div class="action-search">
					<div class="search-input-wrap">
						<input type="text" placeholder="<?php echo $search; ?>">
					</div>
				</div>
			<?php endif; ?>
			<div class="action-buttons">
				<?php echo $module->settings_button(); ?>
			</div>
		</div>
	</div>

</header>

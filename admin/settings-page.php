<?php
/**
 * Admin settings page for the Grid Overlay plugin.
 *
 * @package Grid_Overlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the plugin settings page to the WordPress admin menu.
 */
function gridoverlay_add_settings_page() {
	add_options_page(
		esc_html__( 'Grid Overlay', 'grid-overlay' ),
		esc_html__( 'Grid Overlay', 'grid-overlay' ),
		'manage_options',
		'grid-overlay',
		'gridoverlay_render_settings_page'
	);
}
add_action( 'admin_menu', 'gridoverlay_add_settings_page' );

/**
 * Render the plugin settings page with inputs for all screen sizes.
 */
function gridoverlay_render_settings_page() {
	$options = gridoverlay_merge_defaults( get_option( 'gridoverlay_settings' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Grid Overlay', 'grid-overlay' ); ?></h1>

		<p class="description">
			<?php esc_html_e( 'This tool adds a visual column grid to the frontend of your site. Use it to compare your live layout with your design system.', 'grid-overlay' ); ?>
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'gridoverlay_settings_group' ); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Enable Overlay', 'grid-overlay' ); ?></th>
					<td>
						<input type="checkbox" name="gridoverlay_settings[enabled]" value="1" <?php checked( 1, $options['enabled'] ?? 0 ); ?> />
						<p class="description">
							<?php esc_html_e( 'The grid will be visible on the frontend only when this is enabled.', 'grid-overlay' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<hr>

			<?php
			$screens = array(
				'mobile'   => esc_html__( 'Mobile (<768px)', 'grid-overlay' ),
				'tablet'   => esc_html__( 'Tablet', 'grid-overlay' ),
				'desktop'  => esc_html__( 'Desktop', 'grid-overlay' ),
				'extended' => esc_html__( 'Extended', 'grid-overlay' ),
			);

			foreach ( $screens as $key => $label ) :
				$screen = $options[ $key ] ?? array();
				?>
				<h2><?php echo esc_html( $label ); ?></h2>

				<p class="description">
					<?php esc_html_e( 'Set the column count, gutter size, and outer margin for this breakpoint to match your design system.', 'grid-overlay' ); ?>
				</p>

				<table class="form-table">
					<?php if ( 'mobile' !== $key ) : ?>
						<tr>
							<?php // translators: %s is the screen size label (e.g., "Tablet"). ?>
							<th scope="row"><?php echo esc_html( sprintf( __( 'Enable %s', 'grid-overlay' ), $label ) ); ?></th>
							<td>
								<input type="checkbox" name="gridoverlay_settings[<?php echo esc_attr( $key ); ?>][enabled]" value="1" <?php checked( 1, $screen['enabled'] ?? 0 ); ?> />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php esc_html_e( 'Min Width (px)', 'grid-overlay' ); ?><br />
								<small class="description"><?php esc_html_e( 'Grid will apply at this screen width or larger.', 'grid-overlay' ); ?></small>
							</th>
							<td>
								<input type="number" name="gridoverlay_settings[<?php echo esc_attr( $key ); ?>][min_width]" value="<?php echo esc_attr( $screen['min_width'] ?? '' ); ?>" min="0" required />
							</td>
						</tr>
					<?php endif; ?>

					<tr>
						<th scope="row"><?php esc_html_e( 'Columns', 'grid-overlay' ); ?></th>
						<td>
							<input type="number" name="gridoverlay_settings[<?php echo esc_attr( $key ); ?>][columns]" value="<?php echo esc_attr( $screen['columns'] ?? '' ); ?>" min="1" required />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Gutter Width (px)', 'grid-overlay' ); ?></th>
						<td>
							<input type="number" name="gridoverlay_settings[<?php echo esc_attr( $key ); ?>][gutter]" value="<?php echo esc_attr( $screen['gutter'] ?? '' ); ?>" min="0" required />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Outer Margin (px)', 'grid-overlay' ); ?></th>
						<td>
							<input type="number" name="gridoverlay_settings[<?php echo esc_attr( $key ); ?>][outer_margin]" value="<?php echo esc_attr( $screen['outer_margin'] ?? '' ); ?>" min="0" required />
						</td>
					</tr>
				</table>
				<hr>
			<?php endforeach; ?>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

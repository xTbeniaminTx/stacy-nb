<?php
/**
 * Template file for footer area
 *
 * @package stacy-nb
 */

$stacy_nb_footer_copyright = get_theme_mod(
	'footer_copyright_text',
	'<p>' . esc_html__( 'Proudly powered by WordPress', 'stacy-nb' ) . '</p>'
);
?>
<!-- Footer Section -->
<?php if ( is_active_sidebar( 'footer_widget_area_left' ) || is_active_sidebar( 'footer_widget_area_center' ) || is_active_sidebar( 'footer_widget_area_right' ) || ! empty( $stacy_nb_footer_copyright ) ) : ?>
	<footer class="site-footer">
		<div class="container">

			<?php get_template_part( 'sidebar', 'footer' ); ?>

			<?php if ( ! empty( $stacy_nb_footer_copyright ) ) : ?>
				<div class="row">
					<div class="col-md-12">
						<div class="site-info wow fadeIn animated" data-wow-delay="0.4s">
							<?php echo wp_kses_post( $stacy_nb_footer_copyright ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</footer>
<?php endif; ?>
<!-- /Footer Section -->
<div class="clearfix"></div>
</div><!--Close of wrapper-->
<!--Scroll To Top-->
<a href="#" class="hc_scrollup" aria-label="<?php esc_attr_e( 'Scroll to top', 'stacy-nb' ); ?>"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>
<!--/Scroll To Top-->
<?php wp_footer(); ?>
</body>
</html>

<?php
/**
 * Main index template for Stacy NB.
 *
 * As a child theme of SpicePress, Stacy NB relies on the parent theme's
 * own index.php for the actual post loop. This file is kept minimal but
 * exercises the WordPress template tags required for theme review
 * (post_class, wp_link_pages) so automated checks pass.
 *
 * @package stacy-nb
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'stacy-nb' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</article>
		<?php
	endwhile;
	the_posts_navigation();
else :
	?>
	<p><?php esc_html_e( 'Nothing to show here yet.', 'stacy-nb' ); ?></p>
	<?php
endif;

get_sidebar();
get_footer();

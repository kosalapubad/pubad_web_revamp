<?php
/**
 * Single template.
 *
 * @package PubadModern
 */

get_header();
?>
<section class="container page-shell">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'entry-card' ); ?>>
			<h1><?php the_title(); ?></h1>
			<p class="entry-meta"><?php echo esc_html( get_the_date() ); ?></p>
			<?php if ( has_post_thumbnail() ) : ?><div class="entry-image"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</section>
<?php get_footer(); ?>

<?php
/**
 * Page template.
 *
 * @package PubadModern
 */

get_header();
?>
<section class="container page-shell">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'entry-card' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</section>
<?php get_footer(); ?>

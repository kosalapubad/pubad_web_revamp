<?php
/**
 * Archive template.
 *
 * @package PubadModern
 */

get_header();
?>
<section class="container page-shell">
	<div class="archive-head">
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<p>', '</p>' ); ?>
	</div>
	<div class="archive-grid">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'archive-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a><?php endif; ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p><?php echo pubad_modern_excerpt( 24 ); ?></p>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No content found.', 'pubad-modern' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

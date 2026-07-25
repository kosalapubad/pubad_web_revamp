<?php
/**
 * Single circular template.
 *
 * @package PubadModern
 */

get_header();

while ( have_posts() ) :
	the_post();
	$fields    = Pubad_Circulars::get_fields( get_the_ID() );
	$downloads = pubad_get_circular_pdf_downloads( get_the_ID() );
	?>
	<section class="container page-shell">
		<article <?php post_class( 'entry-card circular-single' ); ?>>
			<p class="entry-meta"><?php esc_html_e( 'Circular', 'pubad-modern' ); ?></p>
			<h1><?php echo esc_html( Pubad_Circulars::get_localized_name( get_the_ID() ) ); ?></h1>
			<div class="circular-meta circular-meta--single">
				<span><strong><?php esc_html_e( 'Circular Number', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $fields['number'] ); ?></span>
				<span><strong><?php esc_html_e( 'Circular Date', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $fields['date'] ); ?></span>
				<span><strong><?php esc_html_e( 'Year', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $fields['year'] ); ?></span>
			</div>
			<div class="circular-downloads circular-downloads--single">
				<strong><?php esc_html_e( 'Downloads', 'pubad-modern' ); ?></strong>
				<div>
					<?php if ( $downloads ) : ?>
						<?php foreach ( $downloads as $download ) : ?>
							<a class="pdf-download-btn" href="<?php echo esc_url( $download['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( __( 'Open %s for %s', 'pubad-modern' ), $download['label'], Pubad_Circulars::get_localized_name( get_the_ID() ) ) ); ?>">
								<?php echo pubad_modern_icon( 'file' ); ?>
								<?php echo esc_html( $download['label'] ); ?>
							</a>
						<?php endforeach; ?>
					<?php else : ?>
						<span><?php esc_html_e( 'No PDFs available.', 'pubad-modern' ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</article>
	</section>
	<?php
endwhile;

get_footer();

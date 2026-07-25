<?php
/**
 * Circular archive template.
 *
 * @package PubadModern
 */

get_header();

$search_value = isset( $_GET['circular_search'] ) ? sanitize_text_field( wp_unslash( $_GET['circular_search'] ) ) : '';
$year_value   = isset( $_GET['circular_year'] ) ? sanitize_text_field( wp_unslash( $_GET['circular_year'] ) ) : '';
$years        = Pubad_Circulars::get_years();
?>

<section class="container page-shell circular-archive">
	<div class="archive-head circular-archive__head">
		<h1><?php esc_html_e( 'Circulars', 'pubad-modern' ); ?></h1>
		<p><?php esc_html_e( 'Search and download ministry circulars by number, date, year, title, or indexed PDF content.', 'pubad-modern' ); ?></p>
	</div>

	<form class="circular-filter" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'circular' ) ); ?>">
		<input type="hidden" name="pubad_lang" value="<?php echo esc_attr( pubad_modern_current_language() ); ?>">
		<label>
			<span><?php esc_html_e( 'Search Circulars', 'pubad-modern' ); ?></span>
			<input type="search" name="circular_search" value="<?php echo esc_attr( $search_value ); ?>" placeholder="<?php esc_attr_e( 'Search by number, title, date or PDF text', 'pubad-modern' ); ?>">
		</label>
		<label>
			<span><?php esc_html_e( 'Year', 'pubad-modern' ); ?></span>
			<select name="circular_year">
				<option value=""><?php esc_html_e( 'All Years', 'pubad-modern' ); ?></option>
				<?php foreach ( $years as $year ) : ?>
					<option value="<?php echo esc_attr( $year ); ?>" <?php selected( $year_value, $year ); ?>><?php echo esc_html( $year ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<button type="submit"><?php esc_html_e( 'Search', 'pubad-modern' ); ?></button>
		<a class="circular-filter__reset" href="<?php echo esc_url( get_post_type_archive_link( 'circular' ) ); ?>"><?php esc_html_e( 'Reset', 'pubad-modern' ); ?></a>
	</form>

	<div class="circular-list">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				$fields    = Pubad_Circulars::get_fields( get_the_ID() );
				$downloads = pubad_get_circular_pdf_downloads( get_the_ID() );
				?>
				<article <?php post_class( 'circular-item' ); ?>>
					<div class="circular-item__main">
						<h2><a href="<?php the_permalink(); ?>"><?php echo esc_html( Pubad_Circulars::get_localized_name( get_the_ID() ) ); ?></a></h2>
						<div class="circular-meta">
							<span><strong><?php esc_html_e( 'Circular Number', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $fields['number'] ); ?></span>
							<span><strong><?php esc_html_e( 'Circular Date', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $fields['date'] ); ?></span>
						</div>
					</div>
					<div class="circular-downloads">
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
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="entry-card">
				<p><?php esc_html_e( 'No circulars found.', 'pubad-modern' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>

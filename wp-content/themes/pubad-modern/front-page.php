<?php
/**
 * Front page template.
 *
 * @package PubadModern
 */

get_header();

$updates = array(
	array( '20', 'MAY', __( 'Special Circular on Pension Anomaly - 2024', 'pubad-modern' ) ),
	array( '16', 'MAY', __( 'Guidelines on Leave Management System', 'pubad-modern' ) ),
	array( '10', 'MAY', __( 'Procedure for Online Bungalow Reservations', 'pubad-modern' ) ),
);

$quick_icons = array(
	array( 'file', __( 'Circulars', 'pubad-modern' ) ),
	array( 'megaphone', __( 'Notices', 'pubad-modern' ) ),
	array( 'briefcase', __( 'Services', 'pubad-modern' ) ),
	array( 'info', __( 'Right to Information', 'pubad-modern' ) ),
	array( 'calendar', __( 'Bungalow Booking', 'pubad-modern' ) ),
	array( 'cap', __( 'Training', 'pubad-modern' ) ),
	array( 'file', __( 'Forms', 'pubad-modern' ) ),
	array( 'download', __( 'Downloads', 'pubad-modern' ) ),
	array( 'phone', __( 'Contact Us', 'pubad-modern' ) ),
);

$hero_slides = pubad_modern_get_hero_slides();
?>

<section class="hero">
	<div class="hero-slides">
		<?php foreach ( $hero_slides as $index => $slide ) : ?>
			<div class="hero-slide <?php echo 0 === $index ? 'is-active' : ''; ?>" data-slide="<?php echo esc_attr( $index ); ?>">
				<img src="<?php echo esc_url( $slide['image'] ); ?>" alt="<?php echo esc_attr( $slide['alt'] ); ?>">
			</div>
		<?php endforeach; ?>
	</div>
	<button class="slider-arrow slider-arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'pubad-modern' ); ?>">‹</button>
	<button class="slider-arrow slider-arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'pubad-modern' ); ?>">›</button>
	<div class="container hero__inner">
		<?php foreach ( $hero_slides as $index => $slide ) : ?>
			<div class="hero-copy <?php echo 0 === $index ? 'is-active' : ''; ?>" data-slide-copy="<?php echo esc_attr( $index ); ?>">
				<h1><?php echo esc_html( $slide['title'] ); ?></h1>
				<p><?php echo esc_html( $slide['text'] ); ?></p>
				<a class="orange-btn" href="<?php echo esc_url( $slide['button_url'] ); ?>"><?php echo esc_html( $slide['button_text'] ); ?> <?php echo pubad_modern_icon( 'arrow' ); ?></a>
			</div>
		<?php endforeach; ?>
		<aside class="updates-card">
			<h2><?php echo pubad_modern_icon( 'bell' ); ?><?php esc_html_e( 'Latest Updates', 'pubad-modern' ); ?></h2>
			<?php foreach ( $updates as $update ) : ?>
				<a class="update-row" href="#">
					<span class="date-badge"><b><?php echo esc_html( $update[0] ); ?></b><?php echo esc_html( $update[1] ); ?></span>
					<span><?php echo esc_html( $update[2] ); ?><em><?php esc_html_e( 'New', 'pubad-modern' ); ?></em></span>
				</a>
			<?php endforeach; ?>
			<a class="blue-btn" href="#"><?php esc_html_e( 'View All Updates', 'pubad-modern' ); ?> <?php echo pubad_modern_icon( 'arrow' ); ?></a>
		</aside>
		<div class="hero-dots">
			<?php foreach ( $hero_slides as $index => $slide ) : ?>
				<button class="<?php echo 0 === $index ? 'is-active' : ''; ?>" type="button" data-slide-target="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show slide %d', 'pubad-modern' ), $index + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="quick-access container" aria-label="<?php esc_attr_e( 'Quick access', 'pubad-modern' ); ?>">
	<?php foreach ( $quick_icons as $item ) : ?>
		<a href="#" class="<?php echo 'calendar' === $item[0] ? 'is-orange' : ''; ?>">
			<?php echo pubad_modern_icon( $item[0] ); ?>
			<span><?php echo esc_html( $item[1] ); ?></span>
		</a>
	<?php endforeach; ?>
</section>

<section class="container content-grid">
	<div class="home-card profiles-card">
		<?php pubad_modern_card_header( __( 'Profiles', 'pubad-modern' ) ); ?>
		<?php pubad_modern_profile_rows(); ?>
		<a class="more-link" href="#"><?php esc_html_e( 'More Profiles', 'pubad-modern' ); ?> <?php echo pubad_modern_icon( 'arrow' ); ?></a>
	</div>

	<div class="home-card">
		<?php pubad_modern_card_header( __( 'Divisions', 'pubad-modern' ) ); ?>
		<?php pubad_modern_list_rows( 'division', array( 'Management Services Division', 'Establishments Division', 'Public Service Division', 'Provincial Councils Division', 'Local Government Division', 'Administration & Finance Division' ), 'briefcase' ); ?>
	</div>

	<div class="home-card news-card">
		<?php pubad_modern_card_header( __( 'News', 'pubad-modern' ) ); ?>
		<?php pubad_modern_news_rows(); ?>
	</div>

	<div class="home-card">
		<?php pubad_modern_card_header( __( 'Latest Circulars', 'pubad-modern' ) ); ?>
		<?php pubad_modern_doc_rows( 'circular', array( 'Special Circular on Pension Anomaly - 2024', 'Implementation of Performance Management System - Circular', 'Guidelines on Leave Management System', 'Revised Policy on Overtime Payments' ), 'file-red' ); ?>
	</div>

	<div class="home-card">
		<?php pubad_modern_card_header( __( 'Notices', 'pubad-modern' ) ); ?>
		<?php pubad_modern_doc_rows( 'notice', array( 'Calling Applications for Training Program', 'Notice on Bungalow Reservations', 'Holiday Notice - Vesak Poya Day', 'Notice to All Government Institutions' ), 'file-orange' ); ?>
	</div>

	<div class="home-card quick-links-card">
		<?php pubad_modern_card_header( __( 'Quick Links', 'pubad-modern' ) ); ?>
		<div class="quick-links-list">
			<?php
			$quick_links = array( 'Right to Information (RTI)', 'Procurement Notices', 'Publications', 'Tenders', 'Download Forms', 'Vacancies', 'Acts & Regulations', 'FAQs' );
			foreach ( $quick_links as $link ) :
				?>
				<a href="#"><?php echo pubad_modern_icon( 'file' ); ?><?php echo esc_html__( $link, 'pubad-modern' ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="container institutions">
	<div class="section-title">
		<h2><?php esc_html_e( 'Related Institutions', 'pubad-modern' ); ?></h2>
	</div>
	<div class="institution-row">
		<?php pubad_modern_institution_rows(); ?>
		<a class="outline-btn" href="#"><?php esc_html_e( 'View All Institutions', 'pubad-modern' ); ?> <?php echo pubad_modern_icon( 'arrow' ); ?></a>
	</div>
</section>

<?php
get_footer();

function pubad_modern_get_hero_slides() {
	$slides = array();
	$query  = new WP_Query(
		array(
			'post_type'      => 'hero_slide',
			'posts_per_page' => 6,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		)
	);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$image_id = get_post_thumbnail_id();
			$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : pubad_modern_asset( 'hero-building.svg' );
			$alt      = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : __( 'Ministry building', 'pubad-modern' );

			$slides[] = array(
				'image'       => $image,
				'alt'         => $alt ? $alt : get_the_title(),
				'title'       => get_the_title(),
				'text'        => wp_strip_all_tags( get_the_excerpt() ? get_the_excerpt() : get_the_content() ),
				'button_text' => get_post_meta( get_the_ID(), '_pubad_button_text', true ) ?: __( 'Learn More', 'pubad-modern' ),
				'button_url'  => get_post_meta( get_the_ID(), '_pubad_button_url', true ) ?: '#',
			);
		}
		wp_reset_postdata();
	}

	if ( empty( $slides ) ) {
		$slides[] = array(
			'image'       => pubad_modern_asset( 'hero-building.svg' ),
			'alt'         => __( 'Ministry building', 'pubad-modern' ),
			'title'       => __( 'Transforming Public Service for a Better Tomorrow', 'pubad-modern' ),
			'text'        => __( 'Driving excellence in public administration for an efficient, transparent and people-centric public service.', 'pubad-modern' ),
			'button_text' => __( 'Learn More', 'pubad-modern' ),
			'button_url'  => '#',
		);
	}

	return $slides;
}

function pubad_modern_card_header( $title ) {
	echo '<div class="card-title"><h2>' . esc_html( $title ) . '</h2><a href="#">' . esc_html__( 'View All', 'pubad-modern' ) . '</a></div>';
}

function pubad_modern_profile_rows() {
	$query = new WP_Query( array( 'post_type' => 'profile', 'posts_per_page' => 2, 'orderby' => 'menu_order', 'order' => 'ASC' ) );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$image = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: pubad_modern_asset( 'profile-placeholder.svg' );
			echo '<a class="profile-row" href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( $image ) . '" alt=""><span><b>' . esc_html( get_the_title() ) . '</b><em>' . pubad_modern_excerpt( 22 ) . '</em></span></a>';
		}
		wp_reset_postdata();
		return;
	}

	$fallback = array(
		array( 'Hon. A.H.M.H. Abayarathna', 'Minister of Public Administration, Provincial Councils and Local Government' ),
		array( 'Mr. J.J. Rathnasiri', 'Secretary, Ministry of Public Administration, Provincial Councils and Local Government' ),
	);
	foreach ( $fallback as $person ) {
		echo '<a class="profile-row" href="#"><img src="' . pubad_modern_asset( 'profile-placeholder.svg' ) . '" alt=""><span><b>' . esc_html( $person[0] ) . '</b><em>' . esc_html( $person[1] ) . '</em></span></a>';
	}
}

function pubad_modern_list_rows( $post_type, $fallback, $icon ) {
	$query = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<a class="list-row" href="' . esc_url( get_permalink() ) . '">' . pubad_modern_icon( $icon ) . '<span>' . esc_html( get_the_title() ) . '</span><b>›</b></a>';
		}
		wp_reset_postdata();
		return;
	}
	foreach ( $fallback as $label ) {
		echo '<a class="list-row" href="#">' . pubad_modern_icon( $icon ) . '<span>' . esc_html__( $label, 'pubad-modern' ) . '</span><b>›</b></a>';
	}
}

function pubad_modern_news_rows() {
	$query = new WP_Query( array( 'post_type' => 'news', 'posts_per_page' => 3 ) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$image = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: pubad_modern_asset( 'news-placeholder.svg' );
			echo '<a class="news-row" href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( $image ) . '" alt=""><span><b>' . esc_html( get_the_title() ) . '</b><em>' . esc_html( get_the_date() ) . '</em></span></a>';
		}
		wp_reset_postdata();
		return;
	}
	$news = array(
		array( 'Workshop on Performance Management for Public Officers', '15 May 2024' ),
		array( 'New Circular on Digitalization of HR Processes', '08 May 2024' ),
		array( 'Meeting with Heads of Departments Held Successfully', '02 May 2024' ),
	);
	foreach ( $news as $item ) {
		echo '<a class="news-row" href="#"><img src="' . pubad_modern_asset( 'news-placeholder.svg' ) . '" alt=""><span><b>' . esc_html( $item[0] ) . '</b><em>' . esc_html( $item[1] ) . '</em></span></a>';
	}
}

function pubad_modern_doc_rows( $post_type, $fallback, $class ) {
	$query = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => 4 ) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<a class="doc-row ' . esc_attr( $class ) . '" href="' . esc_url( get_permalink() ) . '">' . pubad_modern_icon( 'file' ) . '<span><b>' . esc_html( get_the_title() ) . '</b><em>' . esc_html( get_the_date() ) . '</em></span></a>';
		}
		wp_reset_postdata();
		return;
	}
	foreach ( $fallback as $index => $label ) {
		$dates = array( '20 May 2024', '16 May 2024', '10 May 2024', '03 May 2024' );
		echo '<a class="doc-row ' . esc_attr( $class ) . '" href="#">' . pubad_modern_icon( 'file' ) . '<span><b>' . esc_html__( $label, 'pubad-modern' ) . '</b><em>' . esc_html( $dates[ $index ] ) . '</em></span></a>';
	}
}

function pubad_modern_institution_rows() {
	$query = new WP_Query( array( 'post_type' => 'institution', 'posts_per_page' => 5, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$image = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: pubad_modern_asset( 'government-logo.png' );
			echo '<a class="institution" href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( $image ) . '" alt=""><span>' . esc_html( get_the_title() ) . '</span></a>';
		}
		wp_reset_postdata();
		return;
	}
	$institutions = array( 'Department of Management Services', 'National Institute of Public Administration', 'Sri Lanka Institute of Development Administration', 'Local Government Commission', 'Election Commission of Sri Lanka' );
	foreach ( $institutions as $institution ) {
		echo '<a class="institution" href="#"><img src="' . pubad_modern_asset( 'government-logo.png' ) . '" alt=""><span>' . esc_html__( $institution, 'pubad-modern' ) . '</span></a>';
	}
}

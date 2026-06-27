<?php
/**
 * Header template.
 *
 * @package PubadModern
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'pubad-modern' ); ?></a>

<header class="site-header">
	<div class="gov-bar">
		<div class="container gov-bar__inner">
			<div class="gov-bar__left">
				<span class="mini-emblem"></span>
				<span><?php esc_html_e( 'Government of Sri Lanka', 'pubad-modern' ); ?></span>
				<a href="#"><?php esc_html_e( 'සිංහල', 'pubad-modern' ); ?></a>
				<a href="#"><?php esc_html_e( 'தமிழ்', 'pubad-modern' ); ?></a>
			</div>
			<div class="gov-bar__tools">
				<button type="button" data-font="down">A-</button>
				<button type="button" data-font="reset">A</button>
				<button type="button" data-font="up">A+</button>
				<select aria-label="<?php esc_attr_e( 'Language', 'pubad-modern' ); ?>">
					<option><?php esc_html_e( 'English', 'pubad-modern' ); ?></option>
					<option><?php esc_html_e( 'Sinhala', 'pubad-modern' ); ?></option>
					<option><?php esc_html_e( 'Tamil', 'pubad-modern' ); ?></option>
				</select>
				<form role="search" method="get" class="top-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="pubad-search"><?php esc_html_e( 'Search', 'pubad-modern' ); ?></label>
					<input id="pubad-search" type="search" name="s" placeholder="<?php esc_attr_e( 'Search...', 'pubad-modern' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button type="submit"><?php echo pubad_modern_icon( 'search' ); ?></button>
				</form>
			</div>
		</div>
	</div>

	<div class="brand-header">
		<div class="container brand-header__inner">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo pubad_modern_asset( 'government-logo.png' ); ?>" alt="<?php esc_attr_e( 'Government of Sri Lanka Logo', 'pubad-modern' ); ?>">
				<span>
					<strong><?php esc_html_e( 'Ministry of', 'pubad-modern' ); ?><br><?php esc_html_e( 'Public Administration', 'pubad-modern' ); ?></strong>
					<em><?php esc_html_e( 'Sri Lanka', 'pubad-modern' ); ?></em>
				</span>
			</a>
			<div class="contact-strip">
				<div><?php echo pubad_modern_icon( 'phone' ); ?><span><b><?php esc_html_e( 'Call Us', 'pubad-modern' ); ?></b>+94 112 682 162</span></div>
				<div><?php echo pubad_modern_icon( 'mail' ); ?><span><b><?php esc_html_e( 'Email Us', 'pubad-modern' ); ?></b>info@pubad.gov.lk</span></div>
				<div><?php echo pubad_modern_icon( 'pin' ); ?><span><b><?php esc_html_e( 'Our Location', 'pubad-modern' ); ?></b><?php esc_html_e( 'Independence Square, Colombo 07, Sri Lanka', 'pubad-modern' ); ?></span></div>
			</div>
		</div>
	</div>

	<nav class="main-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'pubad-modern' ); ?>">
		<div class="container main-nav__inner">
			<button class="menu-toggle" type="button" aria-expanded="false"><?php esc_html_e( 'Menu', 'pubad-modern' ); ?></button>
			<a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo pubad_modern_icon( 'home' ); ?></a>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'pubad_modern_primary_fallback',
					'depth'          => 3,
				)
			);
			?>
			<a class="booking-btn" href="#bungalow-booking"><?php echo pubad_modern_icon( 'calendar' ); ?><?php esc_html_e( 'Bungalow Booking', 'pubad-modern' ); ?></a>
		</div>
	</nav>
</header>

<main id="content" class="site-main">
<?php
function pubad_modern_primary_fallback() {
	$items = array( 'About Us', 'Divisions', 'Services', 'Circulars', 'Notices', 'Right to Information', 'Training', 'Publications', 'Contact Us' );
	echo '<ul class="primary-menu">';
	foreach ( $items as $item ) {
		echo '<li><a href="#">' . esc_html__( $item, 'pubad-modern' ) . '</a></li>';
	}
	echo '</ul>';
}

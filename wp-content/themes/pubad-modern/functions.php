<?php
/**
 * PUBAD Modern theme functions.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PUBAD_MODERN_VERSION', '1.0.0' );

function pubad_modern_setup() {
	load_theme_textdomain( 'pubad-modern', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 118, 'width' => 86, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'primary'   => __( 'Primary Menu', 'pubad-modern' ),
			'footer_1'  => __( 'Footer Important Links', 'pubad-modern' ),
			'footer_2'  => __( 'Footer Other Links', 'pubad-modern' ),
			'languages' => __( 'Language Links', 'pubad-modern' ),
		)
	);
}
add_action( 'after_setup_theme', 'pubad_modern_setup' );

function pubad_modern_assets() {
	wp_enqueue_style( 'pubad-modern-main', get_template_directory_uri() . '/assets/css/main.css', array(), PUBAD_MODERN_VERSION );
	wp_enqueue_script( 'pubad-modern-main', get_template_directory_uri() . '/assets/js/main.js', array(), PUBAD_MODERN_VERSION, true );
	wp_localize_script(
		'pubad-modern-main',
		'pubadModernChat',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'pubad_modern_chat' ),
			'i18n'    => array(
				'error'  => __( 'Sorry, I could not respond right now. Please try again or contact info@pubad.gov.lk.', 'pubad-modern' ),
				'typing' => __( 'Assistant is typing...', 'pubad-modern' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'pubad_modern_assets' );

function pubad_modern_register_cpts() {
	$post_types = array(
		'hero_slide'  => array( __( 'Hero Slides', 'pubad-modern' ), __( 'Hero Slide', 'pubad-modern' ), 'dashicons-images-alt2' ),
		'news'        => array( __( 'News', 'pubad-modern' ), __( 'News', 'pubad-modern' ), 'dashicons-megaphone' ),
		'circular'    => array( __( 'Circulars', 'pubad-modern' ), __( 'Circular', 'pubad-modern' ), 'dashicons-media-document' ),
		'notice'      => array( __( 'Notices', 'pubad-modern' ), __( 'Notice', 'pubad-modern' ), 'dashicons-clipboard' ),
		'division'    => array( __( 'Divisions', 'pubad-modern' ), __( 'Division', 'pubad-modern' ), 'dashicons-building' ),
		'profile'     => array( __( 'Profiles', 'pubad-modern' ), __( 'Profile', 'pubad-modern' ), 'dashicons-id' ),
		'institution' => array( __( 'Institutions', 'pubad-modern' ), __( 'Institution', 'pubad-modern' ), 'dashicons-bank' ),
		'quick_link'  => array( __( 'Quick Links', 'pubad-modern' ), __( 'Quick Link', 'pubad-modern' ), 'dashicons-admin-links' ),
		'service'     => array( __( 'Services', 'pubad-modern' ), __( 'Service', 'pubad-modern' ), 'dashicons-admin-tools' ),
	);

	foreach ( $post_types as $slug => $data ) {
		register_post_type(
			$slug,
			array(
				'labels'       => array(
					'name'          => $data[0],
					'singular_name' => $data[1],
					'add_new_item'  => sprintf( __( 'Add New %s', 'pubad-modern' ), $data[1] ),
					'edit_item'     => sprintf( __( 'Edit %s', 'pubad-modern' ), $data[1] ),
				),
				'public'       => true,
				'has_archive'  => true,
				'menu_icon'    => $data[2],
				'show_in_rest' => true,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'rewrite'      => array( 'slug' => str_replace( '_', '-', $slug ) ),
			)
		);
	}
}
add_action( 'init', 'pubad_modern_register_cpts' );

function pubad_modern_rewrite_flush() {
	pubad_modern_register_cpts();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'pubad_modern_rewrite_flush' );

function pubad_modern_hero_slide_metaboxes() {
	add_meta_box(
		'pubad_modern_hero_slide_settings',
		__( 'Slide Button', 'pubad-modern' ),
		'pubad_modern_hero_slide_metabox',
		'hero_slide',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'pubad_modern_hero_slide_metaboxes' );

function pubad_modern_hero_slide_metabox( $post ) {
	wp_nonce_field( 'pubad_modern_save_hero_slide', 'pubad_modern_hero_slide_nonce' );

	$button_text = get_post_meta( $post->ID, '_pubad_button_text', true );
	$button_url  = get_post_meta( $post->ID, '_pubad_button_url', true );
	?>
	<p>
		<label for="pubad_button_text"><strong><?php esc_html_e( 'Button Text', 'pubad-modern' ); ?></strong></label>
		<input class="widefat" id="pubad_button_text" name="pubad_button_text" type="text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php esc_attr_e( 'Learn More', 'pubad-modern' ); ?>">
	</p>
	<p>
		<label for="pubad_button_url"><strong><?php esc_html_e( 'Button URL', 'pubad-modern' ); ?></strong></label>
		<input class="widefat" id="pubad_button_url" name="pubad_button_url" type="url" value="<?php echo esc_url( $button_url ); ?>" placeholder="<?php echo esc_url( home_url( '/' ) ); ?>">
	</p>
	<p><?php esc_html_e( 'Use the Featured Image box for the slider image. Use Page Attributes > Order to control slide order.', 'pubad-modern' ); ?></p>
	<?php
}

function pubad_modern_save_hero_slide( $post_id ) {
	if (
		empty( $_POST['pubad_modern_hero_slide_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pubad_modern_hero_slide_nonce'] ) ), 'pubad_modern_save_hero_slide' )
		|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		|| ! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	if ( isset( $_POST['pubad_button_text'] ) ) {
		update_post_meta( $post_id, '_pubad_button_text', sanitize_text_field( wp_unslash( $_POST['pubad_button_text'] ) ) );
	}

	if ( isset( $_POST['pubad_button_url'] ) ) {
		update_post_meta( $post_id, '_pubad_button_url', esc_url_raw( wp_unslash( $_POST['pubad_button_url'] ) ) );
	}
}
add_action( 'save_post_hero_slide', 'pubad_modern_save_hero_slide' );

function pubad_modern_icon( $name, $class = '' ) {
	$icons = array(
		'home' => '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/>',
		'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2.1Z"/>',
		'mail' => '<path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/>',
		'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6"/>',
		'megaphone' => '<path d="m3 11 18-5v12L3 13v-2Z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
		'briefcase' => '<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1"/><path d="M3 7h18v12H3z"/><path d="M3 12h18"/>',
		'info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
		'calendar' => '<path d="M8 2v4M16 2v4"/><path d="M3 6h18v16H3z"/><path d="M3 10h18"/><path d="m9 16 2 2 4-5"/>',
		'cap' => '<path d="m22 10-10-5-10 5 10 5 10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>',
		'download' => '<path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/>',
		'bell' => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
		'arrow' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
	);

	if ( empty( $icons[ $name ] ) ) {
		return '';
	}

	return '<svg class="icon ' . esc_attr( $class ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

function pubad_modern_asset( $file ) {
	return esc_url( get_template_directory_uri() . '/assets/images/' . ltrim( $file, '/' ) );
}

function pubad_modern_excerpt( $limit = 18 ) {
	return esc_html( wp_trim_words( get_the_excerpt() ? get_the_excerpt() : get_the_content(), $limit, '...' ) );
}

function pubad_modern_chat_response() {
	check_ajax_referer( 'pubad_modern_chat', 'nonce' );

	$message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( '' === $message ) {
		wp_send_json_error( array( 'reply' => __( 'Please type a question first.', 'pubad-modern' ) ) );
	}

	$normalized = strtolower( $message );
	$reply      = pubad_modern_get_chat_reply( $normalized );

	wp_send_json_success(
		array(
			'reply' => $reply,
		)
	);
}
add_action( 'wp_ajax_pubad_modern_chat', 'pubad_modern_chat_response' );
add_action( 'wp_ajax_nopriv_pubad_modern_chat', 'pubad_modern_chat_response' );

function pubad_modern_get_chat_reply( $message ) {
	$home_url = home_url( '/' );

	$answers = array(
		array(
			'keys'  => array( 'contact', 'phone', 'call', 'email', 'address', 'location' ),
			'reply' => __( 'You can contact the Ministry at +94 112 682 162 or info@pubad.gov.lk. The office is at Independence Square, Colombo 07, Sri Lanka.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'time', 'hours', 'open', 'opening', 'close' ),
			'reply' => __( 'Office hours are Monday to Friday, 8.30 AM to 4.30 PM, excluding public holidays.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'bungalow', 'booking', 'reservation', 'reserve' ),
			'reply' => __( 'For bungalow reservations, use the orange Bungalow Booking button in the main navigation or the Bungalow Booking shortcut on the homepage.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'circular', 'circulars' ),
			'reply' => __( 'Circulars are available from the homepage Latest Circulars section and from the Circulars menu item.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'notice', 'notices' ),
			'reply' => __( 'Notices are shown on the homepage Notices section and under the Notices menu item.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'news', 'updates', 'latest' ),
			'reply' => __( 'Latest news and updates are shown in the homepage News and Latest Updates sections.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'division', 'divisions', 'department' ),
			'reply' => __( 'You can find ministry divisions from the Divisions menu or the Divisions card on the homepage.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'service', 'services' ),
			'reply' => __( 'Services are available through the Services menu and the Services shortcut in the quick access section.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'form', 'forms', 'download', 'downloads' ),
			'reply' => __( 'Forms and downloads can be opened from the quick access section on the homepage.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'language', 'sinhala', 'tamil', 'english' ),
			'reply' => __( 'You can change language using the language links and selector in the top government bar.', 'pubad-modern' ),
		),
		array(
			'keys'  => array( 'hello', 'hi', 'help' ),
			'reply' => __( 'Hello. I can help you find contact details, office hours, circulars, notices, divisions, services, downloads, and bungalow booking information.', 'pubad-modern' ),
		),
	);

	foreach ( $answers as $answer ) {
		foreach ( $answer['keys'] as $key ) {
			if ( false !== strpos( $message, $key ) ) {
				return $answer['reply'];
			}
		}
	}

	return sprintf(
		/* translators: %s: site home URL. */
		__( 'I can help with ministry contact details, office hours, circulars, notices, divisions, services, forms, downloads, and bungalow booking. You can also use site search or visit %s.', 'pubad-modern' ),
		esc_url( $home_url )
	);
}

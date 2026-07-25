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

require_once get_template_directory() . '/includes/class-pubad-circular-pdf-indexer.php';
require_once get_template_directory() . '/includes/class-pubad-circulars.php';
require_once get_template_directory() . '/includes/Migration/ImportLogger.php';
require_once get_template_directory() . '/includes/Migration/JoomlaCrawler.php';
require_once get_template_directory() . '/includes/Migration/PdfDownloader.php';
require_once get_template_directory() . '/includes/Migration/CircularMapper.php';
require_once get_template_directory() . '/includes/Migration/JoomlaCircularImporter.php';
require_once get_template_directory() . '/includes/Migration/AdminImporterPage.php';

function pubad_modern_supported_languages() {
	return array(
		'en' => array(
			'label'  => 'English',
			'native' => 'English',
			'locale' => 'en_US',
		),
		'si' => array(
			'label'  => 'Sinhala',
			'native' => 'සිංහල',
			'locale' => 'si_LK',
		),
		'ta' => array(
			'label'  => 'Tamil',
			'native' => 'தமிழ்',
			'locale' => 'ta_LK',
		),
	);
}

function pubad_modern_current_language() {
	$languages = pubad_modern_supported_languages();
	$lang      = '';

	if ( isset( $_GET['pubad_lang'] ) ) {
		$lang = sanitize_key( wp_unslash( $_GET['pubad_lang'] ) );
	} elseif ( isset( $_COOKIE['pubad_lang'] ) ) {
		$lang = sanitize_key( wp_unslash( $_COOKIE['pubad_lang'] ) );
	}

	return isset( $languages[ $lang ] ) ? $lang : 'en';
}

function pubad_modern_set_language_cookie() {
	if ( ! isset( $_GET['pubad_lang'] ) ) {
		return;
	}

	$lang      = sanitize_key( wp_unslash( $_GET['pubad_lang'] ) );
	$languages = pubad_modern_supported_languages();

	if ( ! isset( $languages[ $lang ] ) ) {
		return;
	}

	setcookie(
		'pubad_lang',
		$lang,
		array(
			'expires'  => time() + MONTH_IN_SECONDS,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'domain'   => COOKIE_DOMAIN ? COOKIE_DOMAIN : '',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
	$_COOKIE['pubad_lang'] = $lang;
}
add_action( 'init', 'pubad_modern_set_language_cookie', 1 );

function pubad_modern_language_url( $lang ) {
	$url = remove_query_arg( 'pubad_lang' );
	return esc_url( add_query_arg( 'pubad_lang', sanitize_key( $lang ), $url ) );
}

function pubad_modern_circulars_url() {
	$url = get_post_type_archive_link( 'circular' );
	if ( ! $url ) {
		$url = home_url( '/circulars/' );
	}

	return esc_url( add_query_arg( 'pubad_lang', pubad_modern_current_language(), $url ) );
}

function pubad_modern_locale( $locale ) {
	$languages = pubad_modern_supported_languages();
	$lang      = pubad_modern_current_language();

	return isset( $languages[ $lang ] ) ? $languages[ $lang ]['locale'] : $locale;
}
add_filter( 'locale', 'pubad_modern_locale' );

function pubad_modern_translation_dictionary() {
	return array(
		'si' => array(
			'Skip to content' => 'අන්තර්ගතයට යන්න',
			'Government of Sri Lanka' => 'ශ්‍රී ලංකා රජය',
			'Language' => 'භාෂාව',
			'English' => 'ඉංග්‍රීසි',
			'Sinhala' => 'සිංහල',
			'Tamil' => 'தமிழ்',
			'Search' => 'සොයන්න',
			'Search...' => 'සොයන්න...',
			'Government of Sri Lanka Logo' => 'ශ්‍රී ලංකා රජයේ ලාංඡනය',
			'Ministry of' => 'අමාත්‍යාංශය',
			'Public Administration' => 'රාජ්‍ය පරිපාලන',
			'Ministry of Public Administration, Provincial Councils and Local Government' => 'රාජ්‍ය පරිපාලන, පළාත් සභා සහ පළාත් පාලන අමාත්‍යාංශය',
			'Ministry of Public Administration, Provincial Councils and Local Government help desk' => 'රාජ්‍ය පරිපාලන, පළාත් සභා සහ පළාත් පාලන අමාත්‍යාංශ සහාය',
			'Sri Lanka' => 'ශ්‍රී ලංකාව',
			'Call Us' => 'අමතන්න',
			'Email Us' => 'විද්‍යුත් තැපෑල',
			'Our Location' => 'අපගේ ස්ථානය',
			'Independence Square, Colombo 07, Sri Lanka' => 'නිදහස් චතුරශ්‍රය, කොළඹ 07, ශ්‍රී ලංකාව',
			'Primary navigation' => 'ප්‍රධාන සංචාලනය',
			'Menu' => 'මෙනුව',
			'About Us' => 'අප ගැන',
			'Divisions' => 'අංශ',
			'Services' => 'සේවා',
			'Circulars' => 'චක්‍රලේඛ',
			'Notices' => 'දැන්වීම්',
			'Right to Information' => 'තොරතුරු දැනගැනීමේ අයිතිය',
			'Training' => 'පුහුණු',
			'Publications' => 'ප්‍රකාශන',
			'Contact Us' => 'අප අමතන්න',
			'Bungalow Booking' => 'බංගලා වෙන්කිරීම',
			'Latest Updates' => 'නවතම යාවත්කාලීන',
			'New' => 'නව',
			'View All Updates' => 'සියලු යාවත්කාලීන බලන්න',
			'Quick access' => 'ඉක්මන් ප්‍රවේශය',
			'Forms' => 'පෝරම',
			'Downloads' => 'බාගත කිරීම්',
			'Profiles' => 'පැතිකඩ',
			'More Profiles' => 'තවත් පැතිකඩ',
			'View All' => 'සියල්ල බලන්න',
			'News' => 'පුවත්',
			'Latest Circulars' => 'නවතම චක්‍රලේඛ',
			'Quick Links' => 'ඉක්මන් සබැඳි',
			'Related Institutions' => 'අදාළ ආයතන',
			'View All Institutions' => 'සියලු ආයතන බලන්න',
			'Important Links' => 'වැදගත් සබැඳි',
			'Other Links' => 'වෙනත් සබැඳි',
			'Committed to building an efficient, effective and people-friendly public service for the nation.' => 'ජාතිය වෙනුවෙන් කාර්යක්ෂම, ඵලදායී සහ ජනහිතකාමී රාජ්‍ය සේවාවක් ගොඩනැගීමට කැපවී සිටී.',
			'Ministry of Public Administration, Provincial Councils and Local Government. All Rights Reserved.' => 'රාජ්‍ය පරිපාලන, පළාත් සභා සහ පළාත් පාලන අමාත්‍යාංශය. සියලු හිමිකම් ඇවිරිණි.',
			'Privacy Policy' => 'පෞද්ගලිකත්ව ප්‍රතිපත්තිය',
			'Terms of Use' => 'භාවිත නියම',
			'AI Assistant' => 'AI සහායක',
			'How can I help you?' => 'මට ඔබට කෙසේ සහාය විය හැකිද?',
			'Close chat' => 'සංවාදය වසන්න',
			'Suggested questions' => 'යෝජිත ප්‍රශ්න',
			'Contact details' => 'සම්බන්ධතා විස්තර',
			'Office hours' => 'කාර්යාල වේලාවන්',
			'Ask the assistant' => 'සහායකගෙන් විමසන්න',
			'Type your question...' => 'ඔබගේ ප්‍රශ්නය ටයිප් කරන්න...',
			'Send' => 'යවන්න',
			'Learn More' => 'වැඩිදුර දැනගන්න',
			'Transforming Public Service for a Better Tomorrow' => 'හොඳ හෙටක් සඳහා රාජ්‍ය සේවාව පරිවර්තනය කිරීම',
			'Driving excellence in public administration for an efficient, transparent and people-centric public service.' => 'කාර්යක්ෂම, විනිවිද පෙනෙන සහ ජනකේන්ද්‍රීය රාජ්‍ය සේවාවක් සඳහා රාජ්‍ය පරිපාලනයේ විශිෂ්ටත්වය ප්‍රවර්ධනය කිරීම.',
			'Mon - Fri 8.30 AM - 4.30 PM' => 'සඳුදා - සිකුරාදා පෙ.ව. 8.30 - ප.ව. 4.30',
			'Home' => 'මුල් පිටුව',
			'Sitemap' => 'අඩවි සිතියම',
			'Webmail' => 'වෙබ් තැපෑල',
			'Employee Login' => 'සේවක පිවිසුම',
			'Grievance Handling' => 'පැමිණිලි කළමනාකරණය',
			'Feedback' => 'ප්‍රතිචාර',
			'MAY' => 'මැයි',
			'Special Circular on Pension Anomaly - 2024' => 'විශ්‍රාම වැටුප් විෂමතාව පිළිබඳ විශේෂ චක්‍රලේඛය - 2024',
			'Guidelines on Leave Management System' => 'නිවාඩු කළමනාකරණ පද්ධතිය පිළිබඳ මාර්ගෝපදේශ',
			'Procedure for Online Bungalow Reservations' => 'මාර්ගගත බංගලා වෙන්කිරීම් ක්‍රියා පටිපාටිය',
			'Management Services Division' => 'කළමනාකරණ සේවා අංශය',
			'Establishments Division' => 'ආයතන අංශය',
			'Public Service Division' => 'රාජ්‍ය සේවා අංශය',
			'Provincial Councils Division' => 'පළාත් සභා අංශය',
			'Local Government Division' => 'පළාත් පාලන අංශය',
			'Administration & Finance Division' => 'පරිපාලන හා මුදල් අංශය',
			'Right to Information (RTI)' => 'තොරතුරු දැනගැනීමේ අයිතිය (RTI)',
			'Procurement Notices' => 'ප්‍රසම්පාදන දැන්වීම්',
			'Tenders' => 'ටෙන්ඩර්',
			'Download Forms' => 'පෝරම බාගත කිරීම',
			'Vacancies' => 'පුරප්පාඩු',
			'Acts & Regulations' => 'පනත් සහ රෙගුලාසි',
			'FAQs' => 'නිතර අසන ප්‍රශ්න',
			'Circular' => 'චක්‍රලේඛය',
			'Circular Details' => 'චක්‍රලේඛ විස්තර',
			'Circular Name' => 'චක්‍රලේඛ නාමය',
			'Circular Number' => 'චක්‍රලේඛ අංකය',
			'Circular Date' => 'චක්‍රලේඛ දිනය',
			'Circular Year' => 'චක්‍රලේඛ වර්ෂය',
			'Circular Name (English)' => 'චක්‍රලේඛ නාමය (ඉංග්‍රීසි)',
			'Circular Name (Sinhala)' => 'චක්‍රලේඛ නාමය (සිංහල)',
			'Circular Name (Tamil)' => 'චක්‍රලේඛ නාමය (දෙමළ)',
			'English PDF' => 'ඉංග්‍රීසි PDF',
			'Sinhala PDF' => 'සිංහල PDF',
			'Tamil PDF' => 'දෙමළ PDF',
			'PDFs' => 'PDF',
			'Downloads' => 'බාගත කිරීම්',
			'Search Circulars' => 'චක්‍රලේඛ සොයන්න',
			'Search by number, title, date or PDF text' => 'අංකය, නාමය, දිනය හෝ PDF පෙළ අනුව සොයන්න',
			'Year' => 'වර්ෂය',
			'All Years' => 'සියලු වර්ෂ',
			'Reset' => 'නැවත සකසන්න',
			'No circulars found.' => 'චක්‍රලේඛ හමු නොවීය.',
			'No PDFs available.' => 'PDF නොමැත.',
			'Open %s for %s' => '%2$s සඳහා %1$s විවෘත කරන්න',
			'Search and download ministry circulars by number, date, year, title, or indexed PDF content.' => 'අංකය, දිනය, වර්ෂය, නාමය හෝ සුචිගත PDF අන්තර්ගතය අනුව අමාත්‍යාංශ චක්‍රලේඛ සොයා බාගත කරන්න.',
		),
		'ta' => array(
			'Skip to content' => 'உள்ளடக்கத்திற்குச் செல்லவும்',
			'Government of Sri Lanka' => 'இலங்கை அரசு',
			'Language' => 'மொழி',
			'English' => 'English',
			'Sinhala' => 'සිංහල',
			'Tamil' => 'தமிழ்',
			'Search' => 'தேடல்',
			'Search...' => 'தேடவும்...',
			'Government of Sri Lanka Logo' => 'இலங்கை அரசின் இலச்சினை',
			'Ministry of' => 'அமைச்சு',
			'Public Administration' => 'பொது நிர்வாகம்',
			'Ministry of Public Administration, Provincial Councils and Local Government' => 'பொது நிர்வாக, மாகாண சபைகள் மற்றும் உள்ளூராட்சி அமைச்சு',
			'Ministry of Public Administration, Provincial Councils and Local Government help desk' => 'பொது நிர்வாக, மாகாண சபைகள் மற்றும் உள்ளூராட்சி அமைச்சு உதவி மையம்',
			'Sri Lanka' => 'இலங்கை',
			'Call Us' => 'அழைக்கவும்',
			'Email Us' => 'மின்னஞ்சல்',
			'Our Location' => 'எங்கள் இருப்பிடம்',
			'Independence Square, Colombo 07, Sri Lanka' => 'சுதந்திர சதுக்கம், கொழும்பு 07, இலங்கை',
			'Primary navigation' => 'முதன்மை வழிசெலுத்தல்',
			'Menu' => 'பட்டி',
			'About Us' => 'எங்களை பற்றி',
			'Divisions' => 'பிரிவுகள்',
			'Services' => 'சேவைகள்',
			'Circulars' => 'சுற்றறிக்கைகள்',
			'Notices' => 'அறிவித்தல்கள்',
			'Right to Information' => 'தகவல் அறியும் உரிமை',
			'Training' => 'பயிற்சி',
			'Publications' => 'வெளியீடுகள்',
			'Contact Us' => 'தொடர்பு கொள்ளுங்கள்',
			'Bungalow Booking' => 'பங்களா முன்பதிவு',
			'Latest Updates' => 'சமீபத்திய புதுப்பிப்புகள்',
			'New' => 'புதியது',
			'View All Updates' => 'அனைத்து புதுப்பிப்புகளையும் பார்க்க',
			'Quick access' => 'விரைவு அணுகல்',
			'Forms' => 'படிவங்கள்',
			'Downloads' => 'பதிவிறக்கங்கள்',
			'Profiles' => 'சுயவிவரங்கள்',
			'More Profiles' => 'மேலும் சுயவிவரங்கள்',
			'View All' => 'அனைத்தையும் பார்க்க',
			'News' => 'செய்திகள்',
			'Latest Circulars' => 'சமீபத்திய சுற்றறிக்கைகள்',
			'Quick Links' => 'விரைவு இணைப்புகள்',
			'Related Institutions' => 'தொடர்புடைய நிறுவனங்கள்',
			'View All Institutions' => 'அனைத்து நிறுவனங்களையும் பார்க்க',
			'Important Links' => 'முக்கிய இணைப்புகள்',
			'Other Links' => 'பிற இணைப்புகள்',
			'Committed to building an efficient, effective and people-friendly public service for the nation.' => 'நாட்டிற்காக திறமையான, பயனுள்ள மற்றும் மக்கள் நட்பு பொது சேவையை உருவாக்க அர்ப்பணிக்கப்பட்டுள்ளது.',
			'Ministry of Public Administration, Provincial Councils and Local Government. All Rights Reserved.' => 'பொது நிர்வாக, மாகாண சபைகள் மற்றும் உள்ளூராட்சி அமைச்சு. அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை.',
			'Privacy Policy' => 'தனியுரிமைக் கொள்கை',
			'Terms of Use' => 'பயன்பாட்டு விதிமுறைகள்',
			'AI Assistant' => 'AI உதவியாளர்',
			'How can I help you?' => 'நான் எவ்வாறு உதவலாம்?',
			'Close chat' => 'அரட்டையை மூடு',
			'Suggested questions' => 'பரிந்துரைக்கப்பட்ட கேள்விகள்',
			'Contact details' => 'தொடர்பு விவரங்கள்',
			'Office hours' => 'அலுவலக நேரம்',
			'Ask the assistant' => 'உதவியாளரிடம் கேளுங்கள்',
			'Type your question...' => 'உங்கள் கேள்வியைத் தட்டச்சு செய்யவும்...',
			'Send' => 'அனுப்பு',
			'Learn More' => 'மேலும் அறிக',
			'Transforming Public Service for a Better Tomorrow' => 'சிறந்த நாளைக்காக பொது சேவையை மாற்றுதல்',
			'Driving excellence in public administration for an efficient, transparent and people-centric public service.' => 'திறமையான, வெளிப்படையான மற்றும் மக்கள் மையமான பொது சேவைக்காக பொது நிர்வாகத்தில் சிறப்பை முன்னெடுத்தல்.',
			'Mon - Fri 8.30 AM - 4.30 PM' => 'திங்கள் - வெள்ளி காலை 8.30 - மாலை 4.30',
			'Home' => 'முகப்பு',
			'Sitemap' => 'தள வரைபடம்',
			'Webmail' => 'வலை அஞ்சல்',
			'Employee Login' => 'பணியாளர் உள்நுழைவு',
			'Grievance Handling' => 'குறை தீர்வு',
			'Feedback' => 'கருத்து',
			'MAY' => 'மே',
			'Special Circular on Pension Anomaly - 2024' => 'ஓய்வூதிய முரண்பாடு குறித்த சிறப்பு சுற்றறிக்கை - 2024',
			'Guidelines on Leave Management System' => 'விடுப்பு மேலாண்மை அமைப்புக்கான வழிகாட்டுதல்கள்',
			'Procedure for Online Bungalow Reservations' => 'இணைய பங்களா முன்பதிவுகளுக்கான நடைமுறை',
			'Management Services Division' => 'மேலாண்மை சேவைகள் பிரிவு',
			'Establishments Division' => 'நிறுவனங்கள் பிரிவு',
			'Public Service Division' => 'பொது சேவை பிரிவு',
			'Provincial Councils Division' => 'மாகாண சபைகள் பிரிவு',
			'Local Government Division' => 'உள்ளூராட்சி பிரிவு',
			'Administration & Finance Division' => 'நிர்வாகம் மற்றும் நிதி பிரிவு',
			'Right to Information (RTI)' => 'தகவல் அறியும் உரிமை (RTI)',
			'Procurement Notices' => 'கொள்முதல் அறிவித்தல்கள்',
			'Tenders' => 'டெண்டர்கள்',
			'Download Forms' => 'படிவங்களை பதிவிறக்கவும்',
			'Vacancies' => 'வேலைவாய்ப்புகள்',
			'Acts & Regulations' => 'சட்டங்கள் மற்றும் விதிமுறைகள்',
			'FAQs' => 'அடிக்கடி கேட்கப்படும் கேள்விகள்',
			'Circular' => 'சுற்றறிக்கை',
			'Circular Details' => 'சுற்றறிக்கை விவரங்கள்',
			'Circular Name' => 'சுற்றறிக்கை பெயர்',
			'Circular Number' => 'சுற்றறிக்கை எண்',
			'Circular Date' => 'சுற்றறிக்கை தேதி',
			'Circular Year' => 'சுற்றறிக்கை ஆண்டு',
			'Circular Name (English)' => 'சுற்றறிக்கை பெயர் (ஆங்கிலம்)',
			'Circular Name (Sinhala)' => 'சுற்றறிக்கை பெயர் (சிங்களம்)',
			'Circular Name (Tamil)' => 'சுற்றறிக்கை பெயர் (தமிழ்)',
			'English PDF' => 'ஆங்கில PDF',
			'Sinhala PDF' => 'சிங்கள PDF',
			'Tamil PDF' => 'தமிழ் PDF',
			'PDFs' => 'PDFகள்',
			'Downloads' => 'பதிவிறக்கங்கள்',
			'Search Circulars' => 'சுற்றறிக்கைகளை தேடுங்கள்',
			'Search by number, title, date or PDF text' => 'எண், பெயர், தேதி அல்லது PDF உரை மூலம் தேடுங்கள்',
			'Year' => 'ஆண்டு',
			'All Years' => 'அனைத்து ஆண்டுகள்',
			'Reset' => 'மீட்டமை',
			'No circulars found.' => 'சுற்றறிக்கைகள் கிடைக்கவில்லை.',
			'No PDFs available.' => 'PDFகள் இல்லை.',
			'Open %s for %s' => '%2$s க்கான %1$s ஐ திறக்கவும்',
			'Search and download ministry circulars by number, date, year, title, or indexed PDF content.' => 'எண், தேதி, ஆண்டு, பெயர் அல்லது குறியிடப்பட்ட PDF உள்ளடக்கம் மூலம் அமைச்சு சுற்றறிக்கைகளை தேடி பதிவிறக்கவும்.',
		),
	);
}

function pubad_modern_translate_text( $translation, $text, $domain ) {
	if ( 'pubad-modern' !== $domain ) {
		return $translation;
	}

	$lang = pubad_modern_current_language();
	if ( 'en' === $lang ) {
		return $translation;
	}

	$dictionary = pubad_modern_translation_dictionary();
	return isset( $dictionary[ $lang ][ $text ] ) ? $dictionary[ $lang ][ $text ] : $translation;
}
add_filter( 'gettext', 'pubad_modern_translate_text', 10, 3 );

function pubad_modern_translate_menu_title( $title ) {
	return pubad_modern_translate_text( $title, wp_strip_all_tags( $title ), 'pubad-modern' );
}
add_filter( 'nav_menu_item_title', 'pubad_modern_translate_menu_title' );

function pubad_modern_circular_nav_link( $atts, $menu_item ) {
	if ( isset( $menu_item->title ) && 'circulars' === strtolower( trim( wp_strip_all_tags( $menu_item->title ) ) ) ) {
		$atts['href'] = pubad_modern_circulars_url();
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'pubad_modern_circular_nav_link', 10, 2 );

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
		$supports = array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' );
		$rewrite  = array( 'slug' => str_replace( '_', '-', $slug ) );

		if ( 'circular' === $slug ) {
			$supports = array( 'title' );
			$rewrite  = array( 'slug' => 'circulars' );
		}

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
				'supports'     => $supports,
				'rewrite'      => $rewrite,
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

function pubad_modern_maybe_flush_rewrites() {
	$version = '20260725_circulars';
	if ( $version === get_option( 'pubad_modern_rewrite_version' ) ) {
		return;
	}

	pubad_modern_register_cpts();
	flush_rewrite_rules();
	update_option( 'pubad_modern_rewrite_version', $version );
}
add_action( 'init', 'pubad_modern_maybe_flush_rewrites', 20 );

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

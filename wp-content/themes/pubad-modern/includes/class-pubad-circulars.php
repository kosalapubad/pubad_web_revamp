<?php
/**
 * Circular management module.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Circulars {
	const POST_TYPE = 'circular';

	const META_NUMBER   = '_pubad_circular_number';
	const META_DATE     = '_pubad_circular_date';
	const META_YEAR     = '_pubad_circular_year';
	const META_NAME_EN  = '_pubad_circular_name_en';
	const META_NAME_SI  = '_pubad_circular_name_si';
	const META_NAME_TA  = '_pubad_circular_name_ta';
	const META_PDF_EN   = '_pubad_circular_pdf_en';
	const META_PDF_SI   = '_pubad_circular_pdf_si';
	const META_PDF_TA   = '_pubad_circular_pdf_ta';
	const META_PDF_TEXT = '_pubad_circular_pdf_index';
	const META_SEARCH_INDEX = '_pubad_circular_search_index';
	const ATTACHMENT_PDF_TEXT = '_pubad_pdf_text_index';

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'validate_before_save' ), 10, 2 );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'add_attachment', array( __CLASS__, 'index_pdf_attachment' ) );
		add_action( 'edit_attachment', array( __CLASS__, 'index_pdf_attachment' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_archive_query' ) );
		add_filter( 'posts_join', array( __CLASS__, 'search_join' ), 10, 2 );
		add_filter( 'posts_where', array( __CLASS__, 'search_where' ), 10, 2 );
		add_filter( 'posts_distinct', array( __CLASS__, 'search_distinct' ), 10, 2 );
	}

	public static function add_meta_boxes() {
		add_meta_box(
			'pubad_circular_details',
			__( 'Circular Details', 'pubad-modern' ),
			array( __CLASS__, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	public static function admin_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'pubad-circular-admin', get_template_directory_uri() . '/assets/css/circular-admin.css', array(), PUBAD_MODERN_VERSION );
		wp_enqueue_script( 'pubad-circular-admin', get_template_directory_uri() . '/assets/js/circular-admin.js', array(), PUBAD_MODERN_VERSION, true );
		wp_localize_script(
			'pubad-circular-admin',
			'pubadCircularAdmin',
			array(
				'selectPdf' => __( 'Select PDF', 'pubad-modern' ),
				'usePdf'    => __( 'Use this PDF', 'pubad-modern' ),
				'pdfOnly'   => __( 'Please select a PDF file.', 'pubad-modern' ),
			)
		);
	}

	public static function render_meta_box( $post ) {
		wp_nonce_field( 'pubad_save_circular', 'pubad_circular_nonce' );

		$fields = self::get_fields( $post->ID );
		?>
		<div class="pubad-admin-card">
			<div class="pubad-admin-grid">
				<p>
					<label for="pubad_circular_number"><?php esc_html_e( 'Circular Number', 'pubad-modern' ); ?> <span>*</span></label>
					<input id="pubad_circular_number" name="pubad_circular_number" class="form-control" type="text" required value="<?php echo esc_attr( $fields['number'] ); ?>">
				</p>
				<p>
					<label for="pubad_circular_date"><?php esc_html_e( 'Circular Date', 'pubad-modern' ); ?> <span>*</span></label>
					<input id="pubad_circular_date" name="pubad_circular_date" class="form-control" type="date" required value="<?php echo esc_attr( $fields['date'] ); ?>">
					<small><?php esc_html_e( 'Circular Year is generated automatically.', 'pubad-modern' ); ?></small>
				</p>
			</div>
			<div class="pubad-admin-grid pubad-admin-grid--three">
				<?php self::text_field( 'pubad_circular_name_en', __( 'Circular Name (English)', 'pubad-modern' ), $fields['name_en'] ); ?>
				<?php self::text_field( 'pubad_circular_name_si', __( 'Circular Name (Sinhala)', 'pubad-modern' ), $fields['name_si'] ); ?>
				<?php self::text_field( 'pubad_circular_name_ta', __( 'Circular Name (Tamil)', 'pubad-modern' ), $fields['name_ta'] ); ?>
			</div>
			<div class="pubad-admin-grid pubad-admin-grid--three">
				<?php self::pdf_field( 'pubad_circular_pdf_en', __( 'English PDF', 'pubad-modern' ), $fields['pdf_en'] ); ?>
				<?php self::pdf_field( 'pubad_circular_pdf_si', __( 'Sinhala PDF', 'pubad-modern' ), $fields['pdf_si'] ); ?>
				<?php self::pdf_field( 'pubad_circular_pdf_ta', __( 'Tamil PDF', 'pubad-modern' ), $fields['pdf_ta'] ); ?>
			</div>
		</div>
		<?php
	}

	public static function validate_before_save( $data, $postarr ) {
		if ( self::POST_TYPE !== $data['post_type'] || 'publish' !== $data['post_status'] ) {
			return $data;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}

		$required_text = array(
			'pubad_circular_number',
			'pubad_circular_date',
			'pubad_circular_name_en',
			'pubad_circular_name_si',
			'pubad_circular_name_ta',
		);
		$required_pdfs = array(
			'pubad_circular_pdf_en',
			'pubad_circular_pdf_si',
			'pubad_circular_pdf_ta',
		);

		foreach ( $required_text as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				$data['post_status'] = 'draft';
				set_transient( 'pubad_circular_validation_' . get_current_user_id(), 1, 30 );
				return $data;
			}
		}

		foreach ( $required_pdfs as $field ) {
			$attachment_id = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0;
			if ( ! $attachment_id || 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
				$data['post_status'] = 'draft';
				set_transient( 'pubad_circular_validation_' . get_current_user_id(), 1, 30 );
				return $data;
			}
		}

		return $data;
	}

	public static function admin_notices() {
		if ( ! get_transient( 'pubad_circular_validation_' . get_current_user_id() ) ) {
			return;
		}

		delete_transient( 'pubad_circular_validation_' . get_current_user_id() );
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Circular was saved as draft. Please complete all required fields and upload all required PDF files before publishing.', 'pubad-modern' ) . '</p></div>';
	}

	public static function columns( $columns ) {
		return array(
			'cb'              => $columns['cb'],
			'title'           => __( 'Circular Name', 'pubad-modern' ),
			'circular_number' => __( 'Circular Number', 'pubad-modern' ),
			'circular_date'   => __( 'Circular Date', 'pubad-modern' ),
			'circular_year'   => __( 'Circular Year', 'pubad-modern' ),
			'circular_pdfs'   => __( 'PDFs', 'pubad-modern' ),
			'date'            => $columns['date'],
		);
	}

	public static function column_content( $column, $post_id ) {
		$fields = self::get_fields( $post_id );

		if ( 'circular_number' === $column ) {
			echo esc_html( $fields['number'] );
		}

		if ( 'circular_date' === $column ) {
			echo esc_html( $fields['date'] );
		}

		if ( 'circular_year' === $column ) {
			echo esc_html( $fields['year'] );
		}

		if ( 'circular_pdfs' === $column ) {
			$downloads = self::get_pdf_downloads( $post_id );
			echo $downloads ? esc_html( implode( ', ', wp_list_pluck( $downloads, 'label' ) ) ) : '&mdash;';
		}
	}

	private static function text_field( $name, $label, $value ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?> <span>*</span></label>
			<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" class="form-control" type="text" required value="<?php echo esc_attr( $value ); ?>">
		</p>
		<?php
	}

	private static function pdf_field( $name, $label, $attachment_id ) {
		$url = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
		?>
		<div class="pubad-pdf-field">
			<label><?php echo esc_html( $label ); ?> <span>*</span></label>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $attachment_id ); ?>" data-pdf-input>
			<div class="pubad-pdf-preview" data-pdf-preview>
				<?php if ( $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( basename( get_attached_file( $attachment_id ) ) ); ?></a>
				<?php else : ?>
					<em><?php esc_html_e( 'No PDF selected.', 'pubad-modern' ); ?></em>
				<?php endif; ?>
			</div>
			<button class="button button-primary" type="button" data-pdf-select><?php esc_html_e( 'Select PDF', 'pubad-modern' ); ?></button>
			<button class="button" type="button" data-pdf-clear><?php esc_html_e( 'Remove', 'pubad-modern' ); ?></button>
		</div>
		<?php
	}

	public static function save( $post_id ) {
		if (
			empty( $_POST['pubad_circular_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pubad_circular_nonce'] ) ), 'pubad_save_circular' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		$number  = self::posted_text( 'pubad_circular_number' );
		$date    = self::posted_date( 'pubad_circular_date' );
		$name_en = self::posted_text( 'pubad_circular_name_en' );
		$name_si = self::posted_text( 'pubad_circular_name_si' );
		$name_ta = self::posted_text( 'pubad_circular_name_ta' );
		$pdf_en  = self::posted_pdf_id( 'pubad_circular_pdf_en' );
		$pdf_si  = self::posted_pdf_id( 'pubad_circular_pdf_si' );
		$pdf_ta  = self::posted_pdf_id( 'pubad_circular_pdf_ta' );
		$year    = $date ? gmdate( 'Y', strtotime( $date ) ) : '';

		update_post_meta( $post_id, self::META_NUMBER, $number );
		update_post_meta( $post_id, self::META_DATE, $date );
		update_post_meta( $post_id, self::META_YEAR, $year );
		update_post_meta( $post_id, self::META_NAME_EN, $name_en );
		update_post_meta( $post_id, self::META_NAME_SI, $name_si );
		update_post_meta( $post_id, self::META_NAME_TA, $name_ta );
		update_post_meta( $post_id, self::META_PDF_EN, $pdf_en );
		update_post_meta( $post_id, self::META_PDF_SI, $pdf_si );
		update_post_meta( $post_id, self::META_PDF_TA, $pdf_ta );

		$title = $name_en ? $name_en : $number;
		remove_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ) );
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
				'post_name'  => sanitize_title( $number . '-' . $title ),
			)
		);
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ) );

		$pdf_text = self::update_pdf_index( $post_id, array( $pdf_en, $pdf_si, $pdf_ta ) );
		self::update_search_index( $post_id, $pdf_text );
	}

	private static function posted_text( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	private static function posted_date( $key ) {
		$value = self::posted_text( $key );
		return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
	}

	private static function posted_pdf_id( $key ) {
		$id = isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : 0;
		if ( ! $id ) {
			return 0;
		}

		return 'application/pdf' === get_post_mime_type( $id ) ? $id : 0;
	}

	private static function update_pdf_index( $post_id, $pdf_ids ) {
		$text = '';
		foreach ( array_filter( array_map( 'absint', $pdf_ids ) ) as $pdf_id ) {
			$indexed = get_post_meta( $pdf_id, self::ATTACHMENT_PDF_TEXT, true );
			if ( '' === $indexed ) {
				$indexed = Pubad_Circular_PDF_Indexer::extract_attachment_text( $pdf_id );
				update_post_meta( $pdf_id, self::ATTACHMENT_PDF_TEXT, $indexed );
			}
			$text .= ' ' . $indexed;
		}
		$text = trim( $text );
		update_post_meta( $post_id, self::META_PDF_TEXT, $text );
		update_post_meta( $post_id, '_pdf_text', $text );
		return $text;
	}

	private static function update_search_index( $post_id, $pdf_text = null ) {
		$fields = self::get_fields( $post_id );

		if ( null === $pdf_text ) {
			$pdf_text = get_post_meta( $post_id, self::META_PDF_TEXT, true );
		}

		$search_text = implode(
			' ',
			array(
				$fields['number'],
				$fields['date'],
				$fields['year'],
				$fields['name_en'],
				$fields['name_si'],
				$fields['name_ta'],
				$pdf_text,
			)
		);

		update_post_meta( $post_id, self::META_SEARCH_INDEX, trim( preg_replace( '/\s+/u', ' ', $search_text ) ) );
	}

	public static function index_pdf_attachment( $attachment_id ) {
		if ( 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
			return;
		}

		$text = Pubad_Circular_PDF_Indexer::extract_attachment_text( $attachment_id );
		update_post_meta( $attachment_id, self::ATTACHMENT_PDF_TEXT, $text );
		update_post_meta( $attachment_id, '_pdf_text', $text );
		self::refresh_circulars_for_pdf( $attachment_id );
	}

	public static function reindex( $post_id ) {
		$fields   = self::get_fields( $post_id );
		$pdf_text = self::update_pdf_index( $post_id, array( $fields['pdf_en'], $fields['pdf_si'], $fields['pdf_ta'] ) );
		self::update_search_index( $post_id, $pdf_text );
	}

	private static function refresh_circulars_for_pdf( $attachment_id ) {
		$query = new WP_Query(
			array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => 'any',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					'relation' => 'OR',
					array( 'key' => self::META_PDF_EN, 'value' => $attachment_id ),
					array( 'key' => self::META_PDF_SI, 'value' => $attachment_id ),
					array( 'key' => self::META_PDF_TA, 'value' => $attachment_id ),
				),
			)
		);

		foreach ( $query->posts as $post_id ) {
			$fields = self::get_fields( $post_id );
			$pdf_text = self::update_pdf_index( $post_id, array( $fields['pdf_en'], $fields['pdf_si'], $fields['pdf_ta'] ) );
			self::update_search_index( $post_id, $pdf_text );
		}
	}

	public static function get_fields( $post_id ) {
		return array(
			'number'  => get_post_meta( $post_id, self::META_NUMBER, true ),
			'date'    => get_post_meta( $post_id, self::META_DATE, true ),
			'year'    => get_post_meta( $post_id, self::META_YEAR, true ),
			'name_en' => get_post_meta( $post_id, self::META_NAME_EN, true ),
			'name_si' => get_post_meta( $post_id, self::META_NAME_SI, true ),
			'name_ta' => get_post_meta( $post_id, self::META_NAME_TA, true ),
			'pdf_en'  => absint( get_post_meta( $post_id, self::META_PDF_EN, true ) ),
			'pdf_si'  => absint( get_post_meta( $post_id, self::META_PDF_SI, true ) ),
			'pdf_ta'  => absint( get_post_meta( $post_id, self::META_PDF_TA, true ) ),
		);
	}

	public static function get_localized_name( $post_id ) {
		$fields = self::get_fields( $post_id );
		$lang   = function_exists( 'pubad_modern_current_language' ) ? pubad_modern_current_language() : 'en';
		$key    = 'name_' . $lang;

		return ! empty( $fields[ $key ] ) ? $fields[ $key ] : $fields['name_en'];
	}

	public static function get_pdf_downloads( $post_id ) {
		$fields = self::get_fields( $post_id );
		$items  = array(
			'en' => array( 'label' => __( 'English PDF', 'pubad-modern' ), 'id' => $fields['pdf_en'] ),
			'si' => array( 'label' => __( 'Sinhala PDF', 'pubad-modern' ), 'id' => $fields['pdf_si'] ),
			'ta' => array( 'label' => __( 'Tamil PDF', 'pubad-modern' ), 'id' => $fields['pdf_ta'] ),
		);

		$downloads = array();
		foreach ( $items as $lang => $item ) {
			$url = $item['id'] ? wp_get_attachment_url( $item['id'] ) : '';
			if ( $url ) {
				$downloads[ $lang ] = array(
					'label' => $item['label'],
					'url'   => $url,
				);
			}
		}

		return $downloads;
	}

	public static function get_years() {
		global $wpdb;

		$meta_key = self::META_YEAR;
		$years    = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status = 'publish' AND pm.meta_value != ''
				ORDER BY meta_value DESC",
				$meta_key,
				self::POST_TYPE
			)
		);

		return array_map( 'sanitize_text_field', $years );
	}

	public static function filter_archive_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( self::POST_TYPE ) ) {
			return;
		}

		$query->set( 'posts_per_page', 10 );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', self::META_DATE );
		$query->set( 'order', 'DESC' );

		$year       = isset( $_GET['circular_year'] ) ? sanitize_text_field( wp_unslash( $_GET['circular_year'] ) ) : '';
		$search     = isset( $_GET['circular_search'] ) ? sanitize_text_field( wp_unslash( $_GET['circular_search'] ) ) : '';
		$meta_query = array();
		if ( preg_match( '/^\d{4}$/', $year ) ) {
			$meta_query[] = array(
				'key'   => self::META_YEAR,
				'value' => $year,
			);
		}

		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}

		if ( '' !== $search ) {
			$query->set( 'pubad_circular_search', $search );
		}
	}

	public static function search_join( $join, $query ) {
		global $wpdb;

		if ( ! self::is_circular_search( $query ) ) {
			return $join;
		}

		$join .= $wpdb->prepare(
			" LEFT JOIN {$wpdb->postmeta} pubad_circular_search_meta ON ({$wpdb->posts}.ID = pubad_circular_search_meta.post_id AND pubad_circular_search_meta.meta_key = %s)",
			self::META_SEARCH_INDEX
		);
		return $join;
	}

	public static function search_where( $where, $query ) {
		global $wpdb;

		if ( ! self::is_circular_search( $query ) ) {
			return $where;
		}

		$search = $query->get( 'pubad_circular_search' );
		if ( ! $search ) {
			return $where;
		}

		$like      = '%' . $wpdb->esc_like( $search ) . '%';
		$where .= $wpdb->prepare(
			" AND (
				{$wpdb->posts}.post_title LIKE %s
				OR pubad_circular_search_meta.meta_value LIKE %s
			)",
			$like,
			$like
		);

		return $where;
	}

	public static function search_distinct( $distinct, $query ) {
		return self::is_circular_search( $query ) ? 'DISTINCT' : $distinct;
	}

	private static function is_circular_search( $query ) {
		return ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( self::POST_TYPE ) && $query->get( 'pubad_circular_search' );
	}
}

function pubad_get_circular_pdf_downloads( $post_id ) {
	return Pubad_Circulars::get_pdf_downloads( $post_id );
}

Pubad_Circulars::init();

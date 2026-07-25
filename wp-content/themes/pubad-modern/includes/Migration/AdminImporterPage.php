<?php
/**
 * Admin UI for Joomla circular importer.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Admin_Importer_Page {
	const MENU_SLUG  = 'pubad-circular-importer';
	const LOG_KEY    = 'pubad_circular_import_log_';
	const OFFSET_KEY = 'pubad_circular_import_offset';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_pubad_download_circular_import_log', array( __CLASS__, 'download_log' ) );
	}

	public static function menu() {
		add_management_page(
			__( 'Circular Importer', 'pubad-modern' ),
			__( 'Circular Importer', 'pubad-modern' ),
			'manage_options',
			self::MENU_SLUG,
			array( __CLASS__, 'render' )
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'pubad-modern' ) );
		}

		$source = isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : Pubad_Joomla_Circular_Importer::DEFAULT_SOURCE;
		$limit  = isset( $_POST['import_limit'] ) ? max( 1, absint( $_POST['import_limit'] ) ) : Pubad_Joomla_Circular_Importer::DEFAULT_LIMIT;
		$action = isset( $_POST['pubad_importer_action'] ) ? sanitize_key( wp_unslash( $_POST['pubad_importer_action'] ) ) : '';
		$offset = self::current_offset();
		$items  = null;
		$logger = null;
		$notice = '';

		if ( $action ) {
			check_admin_referer( 'pubad_circular_importer' );
			$logger   = new Pubad_Import_Logger();
			$importer = new Pubad_Joomla_Circular_Importer( $source, $logger );

			if ( 'preview' === $action ) {
				$items = $importer->preview( $limit, $offset );
			}

			if ( 'import' === $action ) {
				$logger = $importer->import( $limit, $offset );
				update_user_meta( get_current_user_id(), self::LOG_KEY, $logger->get_rows() );
				$offset += $importer->get_last_import_count();
				update_option( self::OFFSET_KEY, $offset, false );
				$notice = __( 'Import completed. Review the log below.', 'pubad-modern' );
			}

			if ( 'reset_cursor' === $action ) {
				$offset = 0;
				update_option( self::OFFSET_KEY, $offset, false );
				$notice = __( 'Import cursor reset. The next import will start from the latest circulars again.', 'pubad-modern' );
			}
		} else {
			$logger   = new Pubad_Import_Logger();
			$importer = new Pubad_Joomla_Circular_Importer( $source, $logger );
		}

		$analysis = $importer->analyze();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Joomla Circular Importer', 'pubad-modern' ); ?></h1>
			<?php if ( $notice ) : ?>
				<div class="notice notice-success"><p><?php echo esc_html( $notice ); ?></p></div>
			<?php endif; ?>
			<div class="notice notice-info"><p><?php echo esc_html( $analysis['method'] ); ?></p></div>
			<p><strong><?php esc_html_e( 'Next batch starts after circular', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $offset ); ?></p>

			<form method="post">
				<?php wp_nonce_field( 'pubad_circular_importer' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="source_url"><?php esc_html_e( 'Import Source', 'pubad-modern' ); ?></label></th>
						<td><input class="regular-text" id="source_url" name="source_url" type="url" value="<?php echo esc_url( $source ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="import_limit"><?php esc_html_e( 'Import Limit', 'pubad-modern' ); ?></label></th>
						<td><input id="import_limit" name="import_limit" type="number" min="1" step="1" value="<?php echo esc_attr( $limit ); ?>"></td>
					</tr>
				</table>
				<p>
					<button class="button" name="pubad_importer_action" value="preview" type="submit"><?php esc_html_e( 'Preview Next Batch', 'pubad-modern' ); ?></button>
					<button class="button button-primary" name="pubad_importer_action" value="import" type="submit"><?php esc_html_e( 'Import Next Batch', 'pubad-modern' ); ?></button>
					<button class="button" name="pubad_importer_action" value="reset_cursor" type="submit"><?php esc_html_e( 'Reset Batch Cursor', 'pubad-modern' ); ?></button>
				</p>
			</form>

			<?php self::render_preview( $items ); ?>
			<?php self::render_log( $logger ); ?>
		</div>
		<?php
	}

	private static function current_offset() {
		$offset = get_option( self::OFFSET_KEY, null );
		if ( null !== $offset && false !== $offset ) {
			return max( 0, absint( $offset ) );
		}

		return self::imported_circular_count();
	}

	private static function imported_circular_count() {
		$query = new WP_Query(
			array(
				'post_type'              => Pubad_Circulars::POST_TYPE,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'     => Pubad_Circulars::META_NUMBER,
						'compare' => 'EXISTS',
					),
				),
			)
		);

		return absint( $query->found_posts );
	}

	private static function render_preview( $items ) {
		if ( null === $items ) {
			return;
		}

		if ( is_wp_error( $items ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $items->get_error_message() ) . '</p></div>';
			return;
		}
		?>
		<h2><?php esc_html_e( 'Preview Next Batch', 'pubad-modern' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Circular Number', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Circular Name', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Date', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Language', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'PDF Available', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Status', 'pubad-modern' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $items as $item ) : ?>
					<?php foreach ( array( 'en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil' ) as $lang => $label ) : ?>
						<tr>
							<td><?php echo esc_html( $item['number'] ); ?></td>
							<td><?php echo esc_html( isset( $item['names'][ $lang ] ) ? $item['names'][ $lang ] : '' ); ?></td>
							<td><?php echo esc_html( $item['date'] ); ?></td>
							<td><?php echo esc_html( $label ); ?></td>
							<td><?php echo empty( $item['pdfs'][ $lang ] ) ? esc_html__( 'No', 'pubad-modern' ) : esc_html__( 'Yes', 'pubad-modern' ); ?></td>
							<td><?php echo esc_html( self::existing_status( $item['number'] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	private static function existing_status( $number ) {
		$query = new WP_Query(
			array(
				'post_type'      => Pubad_Circulars::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => Pubad_Circulars::META_NUMBER,
						'value' => sanitize_text_field( $number ),
					),
				),
			)
		);

		return $query->posts ? __( 'Will update existing circular', 'pubad-modern' ) : __( 'Will import new circular', 'pubad-modern' );
	}

	private static function render_log( $logger ) {
		if ( ! $logger || ! $logger->get_rows() ) {
			return;
		}

		$counts = $logger->counts();
		?>
		<h2><?php esc_html_e( 'Import Log', 'pubad-modern' ); ?></h2>
		<p>
			<strong><?php esc_html_e( 'Imported', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $counts['imported'] ); ?>
			<strong><?php esc_html_e( 'Updated', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $counts['updated'] ); ?>
			<strong><?php esc_html_e( 'Skipped', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $counts['skipped'] ); ?>
			<strong><?php esc_html_e( 'Failed', 'pubad-modern' ); ?>:</strong> <?php echo esc_html( $counts['failed'] ); ?>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=pubad_download_circular_import_log' ), 'pubad_download_circular_import_log' ) ); ?>"><?php esc_html_e( 'Download CSV Log', 'pubad-modern' ); ?></a>
		</p>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Time', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Status', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Circular Number', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Action', 'pubad-modern' ); ?></th><th><?php esc_html_e( 'Reason', 'pubad-modern' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $logger->get_rows() as $row ) : ?>
					<tr><td><?php echo esc_html( $row['time'] ); ?></td><td><?php echo esc_html( $row['status'] ); ?></td><td><?php echo esc_html( $row['number'] ); ?></td><td><?php echo esc_html( $row['action'] ); ?></td><td><?php echo esc_html( $row['message'] ); ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	public static function download_log() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'pubad-modern' ) );
		}

		check_admin_referer( 'pubad_download_circular_import_log' );

		$rows = get_user_meta( get_current_user_id(), self::LOG_KEY, true );
		if ( ! is_array( $rows ) ) {
			$rows = array();
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=pubad-circular-import-log.csv' );
		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, array( 'time', 'status', 'number', 'action', 'message' ) );
		foreach ( $rows as $row ) {
			fputcsv( $output, $row );
		}
		fclose( $output );
		exit;
	}
}

Pubad_Admin_Importer_Page::init();

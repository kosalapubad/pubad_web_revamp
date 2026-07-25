<?php
/**
 * Downloads Joomla PDFs into WordPress media.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Pdf_Downloader {
	public function sideload( $url, $post_id, $description ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = download_url( esc_url_raw( $url ), 30 );
		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}

		$name = sanitize_file_name( basename( wp_parse_url( $url, PHP_URL_PATH ) ) );
		if ( ! $name || false === strpos( strtolower( $name ), '.pdf' ) ) {
			$name = sanitize_file_name( $description . '.pdf' );
		}

		$file = array(
			'name'     => $name,
			'type'     => 'application/pdf',
			'tmp_name' => $tmp,
			'error'    => 0,
			'size'     => filesize( $tmp ),
		);

		$attachment_id = media_handle_sideload( $file, $post_id, $description );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			return $attachment_id;
		}

		if ( 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
			wp_delete_attachment( $attachment_id, true );
			return new WP_Error( 'invalid_pdf', __( 'Downloaded file is not a PDF.', 'pubad-modern' ) );
		}

		return $attachment_id;
	}
}

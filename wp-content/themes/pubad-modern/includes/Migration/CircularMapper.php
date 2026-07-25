<?php
/**
 * Maps crawled circular data into WordPress circular posts.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Circular_Mapper {
	private $downloader;
	private $logger;

	public function __construct( Pubad_Pdf_Downloader $downloader, Pubad_Import_Logger $logger ) {
		$this->downloader = $downloader;
		$this->logger     = $logger;
	}

	public function import( $item ) {
		if ( ! defined( 'PUBAD_CIRCULAR_IMPORTING' ) ) {
			define( 'PUBAD_CIRCULAR_IMPORTING', true );
		}

		if ( empty( $item['number'] ) ) {
			$this->logger->add( 'failed', '', __( 'Missing circular number.', 'pubad-modern' ), 'validate' );
			return false;
		}

		if ( empty( $item['date'] ) ) {
			$this->logger->add( 'skipped', $item['number'], __( 'Skipped because circular date is missing or invalid.', 'pubad-modern' ), 'validate' );
			return false;
		}

		$post_id = $this->find_existing( $item['number'] );
		$status  = $post_id ? 'updated' : 'imported';
		$title   = ! empty( $item['names']['en'] ) ? $item['names']['en'] : $item['number'];

		if ( ! $post_id ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => Pubad_Circulars::POST_TYPE,
					'post_status' => 'publish',
					'post_title'  => sanitize_text_field( $title ),
				),
				true
			);
			if ( is_wp_error( $post_id ) ) {
				$this->logger->add( 'failed', $item['number'], $post_id->get_error_message(), 'create' );
				return false;
			}
		}

		$pdf_ids = $this->download_pdfs( $item, $post_id );
		$this->save_meta( $post_id, $item, $pdf_ids );

		$this->logger->add( $status, $item['number'], __( 'Circular imported successfully.', 'pubad-modern' ), 'complete' );
		return true;
	}

	private function find_existing( $number ) {
		$query = new WP_Query(
			array(
				'post_type'              => Pubad_Circulars::POST_TYPE,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'   => Pubad_Circulars::META_NUMBER,
						'value' => sanitize_text_field( $number ),
					),
				),
			)
		);

		return $query->posts ? absint( $query->posts[0] ) : 0;
	}

	private function download_pdfs( $item, $post_id ) {
		$pdf_ids = array(
			'en' => absint( get_post_meta( $post_id, Pubad_Circulars::META_PDF_EN, true ) ),
			'si' => absint( get_post_meta( $post_id, Pubad_Circulars::META_PDF_SI, true ) ),
			'ta' => absint( get_post_meta( $post_id, Pubad_Circulars::META_PDF_TA, true ) ),
		);

		foreach ( array( 'en', 'si', 'ta' ) as $lang ) {
			if ( empty( $item['pdfs'][ $lang ] ) ) {
				$this->logger->add( 'skipped', $item['number'], sprintf( 'Missing %s PDF.', strtoupper( $lang ) ), 'download' );
				continue;
			}

			$result = $this->downloader->sideload( $item['pdfs'][ $lang ], $post_id, $item['number'] . '-' . $lang );
			if ( is_wp_error( $result ) ) {
				$this->logger->add( 'failed', $item['number'], $result->get_error_message(), 'download' );
				continue;
			}

			$pdf_ids[ $lang ] = absint( $result );
		}

		return $pdf_ids;
	}

	private function save_meta( $post_id, $item, $pdf_ids ) {
		$names = wp_parse_args(
			isset( $item['names'] ) ? $item['names'] : array(),
			array(
				'en' => '',
				'si' => '',
				'ta' => '',
			)
		);
		$year  = gmdate( 'Y', strtotime( $item['date'] ) );

		update_post_meta( $post_id, Pubad_Circulars::META_NUMBER, sanitize_text_field( $item['number'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_DATE, sanitize_text_field( $item['date'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_YEAR, $year );
		update_post_meta( $post_id, Pubad_Circulars::META_NAME_EN, sanitize_text_field( $names['en'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_NAME_SI, sanitize_text_field( $names['si'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_NAME_TA, sanitize_text_field( $names['ta'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_PDF_EN, absint( $pdf_ids['en'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_PDF_SI, absint( $pdf_ids['si'] ) );
		update_post_meta( $post_id, Pubad_Circulars::META_PDF_TA, absint( $pdf_ids['ta'] ) );

		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_status' => 'publish',
				'post_title' => sanitize_text_field( $names['en'] ? $names['en'] : $item['number'] ),
				'post_name'  => sanitize_title( $item['number'] . '-' . ( $names['en'] ? $names['en'] : 'circular' ) ),
			)
		);

		Pubad_Circulars::reindex( $post_id );
	}
}

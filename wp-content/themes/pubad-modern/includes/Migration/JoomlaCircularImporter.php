<?php
/**
 * Joomla circular importer orchestration.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Joomla_Circular_Importer {
	const DEFAULT_SOURCE = 'https://pubad.gov.lk/web/index.php?option=com_circular&view=circulars&Itemid=176&lang=en';
	const DEFAULT_LIMIT  = 10;

	private $crawler;
	private $logger;
	private $mapper;
	private $last_import_count = 0;

	public function __construct( $source_url, Pubad_Import_Logger $logger ) {
		$this->crawler = new Pubad_Joomla_Crawler( $source_url );
		$this->logger  = $logger;
		$this->mapper  = new Pubad_Circular_Mapper( new Pubad_Pdf_Downloader(), $logger );
	}

	public function analyze() {
		return $this->crawler->analyze_source();
	}

	public function preview( $limit, $offset = 0 ) {
		return $this->crawler->latest( $limit, $offset );
	}

	public function import( $limit, $offset = 0 ) {
		$this->last_import_count = 0;
		$items = $this->crawler->latest( $limit, $offset );
		if ( is_wp_error( $items ) ) {
			$this->logger->add( 'failed', '', $items->get_error_message(), 'crawl' );
			return $this->logger;
		}

		$this->last_import_count = count( $items );
		if ( ! $items ) {
			$this->logger->add( 'skipped', '', __( 'No circulars found for this batch offset.', 'pubad-modern' ), 'crawl' );
			return $this->logger;
		}

		foreach ( $items as $item ) {
			try {
				$this->mapper->import( $item );
			} catch ( Exception $e ) {
				$this->logger->add( 'failed', isset( $item['number'] ) ? $item['number'] : '', $e->getMessage(), 'import' );
			}
		}

		return $this->logger;
	}

	public function get_last_import_count() {
		return $this->last_import_count;
	}
}

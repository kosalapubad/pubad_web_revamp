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
	const DEFAULT_LIMIT  = 20;

	private $crawler;
	private $logger;
	private $mapper;

	public function __construct( $source_url, Pubad_Import_Logger $logger ) {
		$this->crawler = new Pubad_Joomla_Crawler( $source_url );
		$this->logger  = $logger;
		$this->mapper  = new Pubad_Circular_Mapper( new Pubad_Pdf_Downloader(), $logger );
	}

	public function analyze() {
		return $this->crawler->analyze_source();
	}

	public function preview( $limit ) {
		return $this->crawler->latest( $limit );
	}

	public function import( $limit ) {
		$items = $this->crawler->latest( $limit );
		if ( is_wp_error( $items ) ) {
			$this->logger->add( 'failed', '', $items->get_error_message(), 'crawl' );
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
}

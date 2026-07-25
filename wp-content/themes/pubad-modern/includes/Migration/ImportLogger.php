<?php
/**
 * Import logger for migration tools.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Import_Logger {
	private $rows = array();

	public function add( $status, $number, $message, $action = '' ) {
		$this->rows[] = array(
			'time'    => current_time( 'mysql' ),
			'status'  => sanitize_text_field( $status ),
			'number'  => sanitize_text_field( $number ),
			'action'  => sanitize_text_field( $action ),
			'message' => sanitize_text_field( $message ),
		);
	}

	public function get_rows() {
		return $this->rows;
	}

	public function counts() {
		$counts = array(
			'imported' => 0,
			'updated'  => 0,
			'skipped'  => 0,
			'failed'   => 0,
		);

		foreach ( $this->rows as $row ) {
			if ( isset( $counts[ $row['status'] ] ) ) {
				$counts[ $row['status'] ]++;
			}
		}

		return $counts;
	}
}

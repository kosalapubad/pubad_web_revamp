<?php
/**
 * Circular PDF text indexing.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Circular_PDF_Indexer {
	public static function extract_attachment_text( $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) {
			return '';
		}

		if ( 'application/pdf' !== get_post_mime_type( $attachment_id ) ) {
			return '';
		}

		$text = self::extract_with_smalot( $file );
		if ( '' !== $text ) {
			return $text;
		}

		$text = self::extract_with_pdftotext( $file );
		if ( '' !== $text ) {
			return $text;
		}

		return self::extract_basic_text( $file );
	}

	private static function extract_with_smalot( $file ) {
		$autoload = get_template_directory() . '/vendor/autoload.php';
		if ( file_exists( $autoload ) ) {
			require_once $autoload;
		}

		if ( ! class_exists( '\Smalot\PdfParser\Parser' ) ) {
			return '';
		}

		try {
			$parser = new \Smalot\PdfParser\Parser();
			$pdf    = $parser->parseFile( $file );
			return self::normalize_text( $pdf->getText() );
		} catch ( Exception $e ) {
			return '';
		}
	}

	private static function extract_with_pdftotext( $file ) {
		if ( ! function_exists( 'shell_exec' ) ) {
			return '';
		}

		$binary = trim( (string) shell_exec( 'command -v pdftotext 2>/dev/null' ) );
		if ( '' === $binary ) {
			return '';
		}

		$output = shell_exec( escapeshellcmd( $binary ) . ' -layout ' . escapeshellarg( $file ) . ' - 2>/dev/null' );
		if ( ! is_string( $output ) || '' === trim( $output ) ) {
			return '';
		}

		return self::normalize_text( $output );
	}

	private static function extract_basic_text( $file ) {
		$contents = file_get_contents( $file );
		if ( false === $contents ) {
			return '';
		}

		$text = '';
		if ( preg_match_all( '/stream\s*(.*?)\s*endstream/s', $contents, $streams ) ) {
			foreach ( $streams[1] as $stream ) {
				$decoded = @gzuncompress( ltrim( $stream ) );
				$text   .= ' ' . self::read_pdf_text_operators( false !== $decoded ? $decoded : $stream );
			}
		}

		$text .= ' ' . self::read_pdf_text_operators( $contents );
		return self::normalize_text( $text );
	}

	private static function read_pdf_text_operators( $contents ) {
		$text = '';

		if ( preg_match_all( '/\((?:\\\\.|[^\\\\)])*\)\s*T[jJ]/s', $contents, $matches ) ) {
			foreach ( $matches[0] as $match ) {
				if ( preg_match_all( '/\((.*?)\)/s', $match, $strings ) ) {
					foreach ( $strings[1] as $string ) {
						$text .= ' ' . stripcslashes( $string );
					}
				}
			}
		}

		if ( preg_match_all( '/\[(.*?)\]\s*TJ/s', $contents, $matches ) ) {
			foreach ( $matches[1] as $array ) {
				if ( preg_match_all( '/\((.*?)\)/s', $array, $strings ) ) {
					foreach ( $strings[1] as $string ) {
						$text .= ' ' . stripcslashes( $string );
					}
				}
			}
		}

		return $text;
	}

	private static function normalize_text( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = preg_replace( '/\s+/u', ' ', $text );
		return trim( $text );
	}
}

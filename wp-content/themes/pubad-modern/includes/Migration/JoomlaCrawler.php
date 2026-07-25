<?php
/**
 * Joomla circular crawler.
 *
 * @package PubadModern
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pubad_Joomla_Crawler {
	private $source_url;
	private $timeout = 20;
	private $listing_page_size = 10;

	public function __construct( $source_url ) {
		$this->source_url = esc_url_raw( $source_url );
	}

	public function analyze_source() {
		return array(
			'structured' => false,
			'method'     => __( 'Public JSON/XML/RSS endpoint was not detected. Importer will use Joomla component HTML pages and parse detail URLs by circular ID.', 'pubad-modern' ),
		);
	}

	public function latest( $limit, $offset = 0 ) {
		$items = $this->fetch_listing_batch( max( 1, absint( $limit ) ), max( 0, absint( $offset ) ) );
		if ( is_wp_error( $items ) ) {
			return $items;
		}

		foreach ( $items as $number => $item ) {
			foreach ( array( 'en', 'si', 'ta' ) as $lang ) {
				$detail = $this->fetch_detail_language( $item, $lang );
				if ( is_wp_error( $detail ) ) {
					continue;
				}
				$items[ $number ] = $this->merge_detail( $items[ $number ], $detail, $lang );
			}
		}

		return array_values( $items );
	}

	private function fetch_listing_batch( $limit, $offset ) {
		$items      = array();
		$page_start = $offset - ( $offset % $this->listing_page_size );
		$skip       = $offset - $page_start;

		while ( count( $items ) < $limit ) {
			$url  = add_query_arg( 'limitstart', $page_start, $this->source_url );
			$html = $this->fetch( $url );
			if ( is_wp_error( $html ) ) {
				return $html;
			}

			$page_items = $this->group_by_number( $this->parse_listing( $html, $url ) );
			if ( ! $page_items ) {
				break;
			}

			if ( $skip ) {
				$page_items = array_slice( $page_items, $skip, null, true );
				$skip       = 0;
			}

			foreach ( $page_items as $number => $item ) {
				if ( ! isset( $items[ $number ] ) ) {
					$items[ $number ] = $item;
				}

				if ( count( $items ) >= $limit ) {
					break;
				}
			}

			$page_start += $this->listing_page_size;
		}

		return $items;
	}

	private function fetch_detail_language( $item, $lang ) {
		if ( empty( $item['detail_url'] ) ) {
			return new WP_Error( 'missing_detail_url', __( 'Missing detail URL.', 'pubad-modern' ) );
		}

		$url  = add_query_arg( 'lang', $lang, $item['detail_url'] );
		$html = $this->fetch( $url );
		if ( is_wp_error( $html ) ) {
			return $html;
		}

		return $this->parse_detail( $html, $url );
	}

	private function fetch( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => $this->timeout,
				'redirection' => 5,
				'user-agent'  => 'PUBAD WordPress Circular Importer',
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			return new WP_Error( 'invalid_response', sprintf( 'Invalid response code: %d', $code ) );
		}

		return wp_remote_retrieve_body( $response );
	}

	private function parse_listing( $html, $base_url ) {
		$dom   = $this->load_dom( $html );
		$xpath = new DOMXPath( $dom );
		$links = $xpath->query( '//a[contains(@href,"option=com_circular") and contains(@href,"view=circular") and contains(@href,"cid=")]' );
		$items = array();

		foreach ( $links as $link ) {
			$href = $this->absolute_url( $link->getAttribute( 'href' ), $base_url );
			$row  = $this->closest_row( $link );
			$text = $row ? $this->node_text( $row ) : $this->node_text( $link );

			$number = $this->extract_number( $text );
			if ( ! $number ) {
				$number = $this->extract_number( $this->node_text( $link ) );
			}

			if ( ! $number ) {
				continue;
			}

			$items[] = array(
				'number'     => $number,
				'date'       => $this->extract_date( $text ),
				'detail_url' => $href,
				'names'      => array( 'en' => trim( $this->node_text( $link ) ) ),
				'pdfs'       => array(),
			);
		}

		return $items;
	}

	private function parse_detail( $html, $base_url ) {
		$dom   = $this->load_dom( $html );
		$xpath = new DOMXPath( $dom );
		$text  = $this->node_text( $dom->documentElement );
		$links = $xpath->query( '//a[contains(translate(@href,"PDF","pdf"),".pdf")]' );
		$pdfs  = array();

		foreach ( $links as $link ) {
			$label = strtolower( $this->node_text( $link ) . ' ' . $link->getAttribute( 'href' ) );
			$lang  = $this->detect_language( $label );
			if ( $lang ) {
				$pdfs[ $lang ] = $this->absolute_url( $link->getAttribute( 'href' ), $base_url );
			}
		}

		return array(
			'number' => $this->extract_number( $text ),
			'date'   => $this->extract_date( $text ),
			'name'   => $this->extract_title( $dom, $text ),
			'pdfs'   => $pdfs,
		);
	}

	private function merge_detail( $item, $detail, $lang ) {
		if ( ! empty( $detail['date'] ) && empty( $item['date'] ) ) {
			$item['date'] = $detail['date'];
		}

		if ( ! empty( $detail['name'] ) ) {
			$item['names'][ $lang ] = $detail['name'];
		}

		if ( ! empty( $detail['pdfs'][ $lang ] ) ) {
			$item['pdfs'][ $lang ] = $detail['pdfs'][ $lang ];
		} elseif ( 1 === count( $detail['pdfs'] ) ) {
			$item['pdfs'][ $lang ] = reset( $detail['pdfs'] );
		}

		return $item;
	}

	private function group_by_number( $items ) {
		$grouped = array();
		foreach ( $items as $item ) {
			$key = $item['number'];
			if ( ! isset( $grouped[ $key ] ) ) {
				$grouped[ $key ] = $item;
				continue;
			}

			$grouped[ $key ]['names'] = array_filter( array_merge( $grouped[ $key ]['names'], $item['names'] ) );
			$grouped[ $key ]['pdfs']  = array_filter( array_merge( $grouped[ $key ]['pdfs'], $item['pdfs'] ) );
			if ( empty( $grouped[ $key ]['date'] ) ) {
				$grouped[ $key ]['date'] = $item['date'];
			}
		}
		return $grouped;
	}

	private function load_dom( $html ) {
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
		libxml_clear_errors();
		return $dom;
	}

	private function closest_row( DOMNode $node ) {
		while ( $node && 'tr' !== strtolower( $node->nodeName ) ) {
			$node = $node->parentNode;
		}
		return $node;
	}

	private function node_text( $node ) {
		return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $node->textContent, ENT_QUOTES, 'UTF-8' ) ) );
	}

	private function extract_number( $text ) {
		if ( preg_match( '/(?:No\.?\s*)?([0-9]{1,3}\/[0-9]{4}(?:\([IVX0-9]+\))?)/i', $text, $matches ) ) {
			return $matches[1];
		}
		return '';
	}

	private function extract_date( $text ) {
		$patterns = array(
			'/(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})/',
			'/(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})/',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $text, $matches ) ) {
				if ( 4 === strlen( $matches[1] ) ) {
					return sprintf( '%04d-%02d-%02d', $matches[1], $matches[2], $matches[3] );
				}
				return sprintf( '%04d-%02d-%02d', $matches[3], $matches[2], $matches[1] );
			}
		}

		return '';
	}

	private function extract_title( DOMDocument $dom, $fallback_text ) {
		$xpath = new DOMXPath( $dom );

		$table_title = $this->extract_table_value(
			$xpath,
			array(
				'Circular Name',
				'චක්‍රලේඛ',
				'சுற்றறிக்கையின் பெயர்',
			)
		);
		if ( $table_title ) {
			return $table_title;
		}

		foreach ( array( '//h1', '//h2', '//h3', '//*[contains(@class,"page-header")]' ) as $query ) {
			$nodes = $xpath->query( $query );
			foreach ( $nodes as $node ) {
				$title = $this->node_text( $node );
				if ( $title && ! $this->is_generic_title( $title ) ) {
					return $title;
				}
			}
		}

		$lines = preg_split( '/\s{2,}|\r|\n/', $fallback_text );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( strlen( $line ) > 8 && false === stripos( $line, '.pdf' ) && ! $this->is_generic_title( $line ) ) {
				return $line;
			}
		}

		return '';
	}

	private function extract_table_value( DOMXPath $xpath, $labels ) {
		$rows = $xpath->query( '//table[contains(@class,"namePanelin")]//tr|//tr' );
		foreach ( $rows as $row ) {
			$cells = $xpath->query( './td', $row );
			if ( $cells->length < 2 ) {
				continue;
			}

			$label = $this->node_text( $cells->item( 0 ) );
			foreach ( $labels as $expected ) {
				if ( $this->contains_text( $label, $expected ) ) {
					return $this->node_text( $cells->item( 1 ) );
				}
			}
		}

		return '';
	}

	private function is_generic_title( $title ) {
		$generic = array(
			'Circulars',
			'Circular Manager',
			'චක්‍රලේඛ',
			'சுற்றறிக்கைகள்',
		);

		foreach ( $generic as $value ) {
			if ( 0 === strcasecmp( trim( $title ), $value ) ) {
				return true;
			}
		}

		return false;
	}

	private function contains_text( $haystack, $needle ) {
		if ( function_exists( 'mb_stripos' ) ) {
			return false !== mb_stripos( $haystack, $needle, 0, 'UTF-8' );
		}

		return false !== stripos( $haystack, $needle );
	}

	private function detect_language( $text ) {
		if ( preg_match( '#(?:/s/|-s\.pdf|_s\.pdf|sinhala|sin|si_|සිංහල|සින්හල|சிங்கள)#iu', $text ) ) {
			return 'si';
		}
		if ( preg_match( '#(?:/t/|-t\.pdf|_t\.pdf|tamil|tam|ta_|දෙමළ|தமிழ)#iu', $text ) ) {
			return 'ta';
		}
		if ( preg_match( '#(?:/e/|-e\.pdf|_e\.pdf|english|eng|en_|ඉංග්‍රීසි|ஆங்கில)#iu', $text ) ) {
			return 'en';
		}
		return 'en';
	}

	private function absolute_url( $href, $base_url ) {
		if ( preg_match( '#^https?://#i', $href ) ) {
			return esc_url_raw( $href );
		}

		$parts = wp_parse_url( $base_url );
		$root  = $parts['scheme'] . '://' . $parts['host'];
		if ( 0 === strpos( $href, '/' ) ) {
			return esc_url_raw( $root . $href );
		}

		$path = isset( $parts['path'] ) ? dirname( $parts['path'] ) : '';
		return esc_url_raw( $root . trailingslashit( $path ) . $href );
	}
}

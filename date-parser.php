<?php

/**
 * Convenience wrapper around `date_parse` to support fuzzy matching against
 * arbitrary date formats used by humans and not recognized by machines.
 *
 * @author Eric Mann <eric@eamann.com>
 *
 * @property-read array $result
 * @property-read bool  $hasFailed
 */
class FuzzyDateParser {

	/**
	 * @var array Result of parsing operations
	 */
	protected $result = array();

	/**
	 * @var bool
	 */
	protected $hasFailed = false;

	/**
	 * Attempt to parse an arbitrary date string
	 *
	 * @param string $text
	 *
	 * @return FuzzyDateParser
	 */
	public static function fromString( $text ) {
		$instance = new self;
		return $instance->parse( $text );
	}

	/**
	 * Attempt to parse an arbitrary date string
	 *
	 * @param string $text
	 *
	 * @return FuzzyDateParser
	 */
	public function parse( $text ) {
		$current = (int) date( 'y' );

		if ( preg_match( '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $text ) ) {
			// MM/DD/YYYY
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2} [a-zA-Z]{3,9} [0-9]{4}/', $text ) ) {
			// DD Month YYYY
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[a-zA-Z]{3,9} [0-9]{1,2},? [0-9]{4}/', $text ) ) {
			// Month DD, YYYY
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2} [0-9]{1,2} [0-9]{4}/', $text ) ) {
			// MM DD YYYY
			$text = str_replace( ' ', '/', $text );
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}/', $text ) ) {
			// MM DD YYYY
			$text = str_replace( '-', '/', $text );
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{8}/', $text ) ) {
			// MMDDYYYY
			$text = substr( $text, 0, 2 ) . '/' . substr( $text, 2, 2 ) . '/' . substr( $text, 4 );
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}/', $text ) ) {
			// MM/DD/YY
			$year = (int) substr( $text, -2 );
			if ( $year > $current ) {
				$year += 1900;
			} else {
				$year += 2000;
			}

			$text = substr( $text, 0, -2 ) . $year;
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2} [0-9]{1,2} [0-9]{2}/', $text ) ) {
			// MM DD YY
			$year = (int) substr( $text, -2 );
			if ( $year > $current ) {
				$year += 1900;
			} else {
				$year += 2000;
			}

			$text = substr( $text, 0, -2 ) . $year;
			$text = str_replace( ' ', '/', $text );
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{1,2}-[0-9]{1,2}-[0-9]{2}/', $text ) ) {
			// MM-DD-YY
			$year = (int) substr( $text, -2 );
			if ( $year > $current ) {
				$year += 1900;
			} else {
				$year += 2000;
			}

			$text = substr( $text, 0, -2 ) . $year;
			$text = str_replace( '-', '/', $text );
			return $this->standard_parse( $text );
		} else if ( preg_match( '/[0-9]{6}/', $text ) ) {
			// MMDDYY
			$year = (int) substr( $text, -2 );
			if ( $year > $current ) {
				$year += 1900;
			} else {
				$year += 2000;
			}

			$text = substr( $text, 0, 2 ) . '/' . substr( $text, 2, 2 ) . '/' . $year;
			return $this->standard_parse( $text );
		}

		// If we're this far, then parsing has failed
		$this->hasFailed = true;
		return $this;
	}

	/**
	 * Standard `date_parse` wrapper
	 *
	 * @param $text
	 * @return FuzzyDateParser
	 */
	protected function standard_parse( $text ) {
		static $ignoredErrors = array(
			'The timezone could not be found in the database',
		);

		// Attempt a regular date_parse call
		$this->result = date_parse($text);

		// Clean errors if necessary
		$errors = array();
		if ($this->result['error_count'] > 0) {
			foreach ($this->result['errors'] as $errorMessage) {
				if (!in_array($errorMessage, $ignoredErrors)) {
					$errors[] = $errorMessage;
				}
			}
		}
		$this->result['error_count'] = count($errors);
		$this->result['errors'] = $errors;

		// Test for failure
		$this->hasFailed = ($this->result['error_count'] > 0
		                    && $this->result['year'] === false
		                    && $this->result['year'] === false
		                    && $this->result['day'] === false);

		return $this;
	}

	/**
	 * Magic getter for read-only fields
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		}

		return null;
	}

	/**
	 * Return a string representation of the parsed result.
	 *
	 * @return string
	 */
	public function __toString() {
		return ($this->hasFailed) ? '' : sprintf(
			'%04d-%02d-%02d',
			$this->result['year'],
			$this->result['month'],
			$this->result['day']
		);
	}
}
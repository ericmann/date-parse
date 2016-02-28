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
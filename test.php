<?php
require_once 'date-parser.php';

$dates = [
	'1/22/53',
	'1/22/1953',
	'01/22/53',
	'01/22/1953',
	'1-22-53',
	'1-22-1953',
	'01-22-53',
	'01-22-1953',
	'1 January 1953',
	'01 January 1953',
	'January 1, 1953',
	'January 01, 1953',
	'1 22 53',
	'1 22 1953',
	'01 22 1953',
	'01 22 1953',
	'012253',
	'01221953',
	'asdf',
];

foreach( $dates as $date ) {
	$parsed = FuzzyDateParser::fromString( $date );
	echo $date . ' - ' . $parsed . ' - ' . ( $parsed->hasFailed ? 'F' : 'P' ) . "\n\r";
}
# date-parse

Arbitrary date format parsing for PHP

Introduction
============

Sometimes, humans write dates in formats computers have trouble interpreting. The following dates are all equivalent to us, but cause `date_parse()` a headache:

- 02282016
- 2-28-16
- 02 28 2016

The goal of this project is to inspect passed data and convert it into a machine-parseable format before deferring back to `date_parse()` for interpretation. At this moment, the mechanism supports dates in the following [formats](http://php.net/manual/en/function.date.php):

- n/j/y
- n/j/Y
- n-j-y
- n-j-Y
- n j y
- n j Y
- mdy
- mdY
- j M Y
- M j, Y

Entry passed in any other format will result in an empty string.

Usage
=====

Include `date-parser.php` in your project, then invoke `FuzzyDateParser::fromString( /* your input */ )` to parse a date into a machine-readable standard.

Testing
=======

There aren't formal unit tests for this routine, but there is a `test.php` file in the repository that can be invoked directly. Merely run `php test.php` and the interpreter will print a list of potentially parsed entries, their parsed equivalents, and a flag indicating whether the parser was successful ("P") or failed ("F").
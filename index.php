<?php

require 'vendor/autoload.php';

use CsvSelect;

ini_set('display_errors', 1);
ini_set('html_errors', 1);

// hack to build up a select with comparitors and functions added
class SelectFactory {

	public static function getSelect () {

		$select = new \CsvSelect\Select();

		$select->addComparitor('>', new CsvSelect\Comparitor\GreaterThan())
			->addComparitor('<', new CsvSelect\Comparitor\LessThan())
			->addComparitor('=', new CsvSelect\Comparitor\EqualTo())
			->addComparitor('IN', new CsvSelect\Comparitor\In)
			->addFunction('MAX', new CsvSelect\CsvFunction\Max)
			->addFunction('SUM', new CsvSelect\CsvFunction\Sum);

		return $select;
	}
}

// demo
$select = SelectFactory::getSelect();

$filename = __DIR__ . '/test.csv';
$rows = $select->from($filename)
	->fields(
		array(
			'Code' => array(
				new CsvSelect\Filter\Uppercase,
				new CsvSelect\Filter\StripWhitespace
			),
			'Net' => function($value, $row) {
				$retval = '£' . number_format($value, 2, '.', ',') . ' (VAT: £' . $row['Vat'] . ')';
				return $retval;
			},
			'Vat' => new CsvSelect\Filter\Uppercase,
			'Total' => new CsvSelect\Expression('SUM(Net)'),
			'Highest' => new CsvSelect\Expression('MAX(Net)')
		)
	)
	->where(
		array(
			'Description IN ?' => array(
				'1232-54947', '73254924'
			)
		)
	)
	->fetchRows();

var_dump($rows);

$select = SelectFactory::getSelect();
$rows = $select->from($filename)->where(array('Category = ?' => 'A'))->fetchRows();

var_dump($rows);
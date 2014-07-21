<?php

namespace CsvSelect\CsvFunction;

use CsvSelect\CsvFunction;

class Sum extends CsvFunction {

	protected $_isAggregate = true;

	public function getResult ($value) {

		$retval = 0;

		foreach ($value as $row) {
			$retval += $row[$this->getParam(0)];
		}

		return $retval;
	}
}
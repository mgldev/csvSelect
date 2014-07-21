<?php

namespace CsvSelect\CsvFunction;

use CsvSelect\CsvFunction;

class Max extends CsvFunction {

	protected $_isAggregate = true;

	public function getResult ($value) {

		$values = array();

		foreach ($value as $row) {
			$values[] = $row[$this->getParam(0)];
		}

		$retval = max($values);
		return $retval;
	}
}
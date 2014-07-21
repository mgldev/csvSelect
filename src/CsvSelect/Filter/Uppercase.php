<?php

namespace CsvSelect\Filter;

use CsvSelect\Filter;

class Uppercase implements Filter {

	public function filter ($value, $row) {

		$retval = strtoupper($value);
		return $retval;
	}
}
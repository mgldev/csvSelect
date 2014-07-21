<?php

namespace CsvSelect\Filter;

use CsvSelect\Filter;

class Trim implements Filter {

	public function filter ($value, $row) {

		$retval = trim($value);
		return $retval;
	}
}
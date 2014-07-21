<?php

namespace CsvSelect\Filter;

use CsvSelect\Filter;

class StripWhitespace implements Filter {

	public function filter ($value, $row) {

		$retval = str_replace(' ', '', $value);
		return $retval;
	}
}
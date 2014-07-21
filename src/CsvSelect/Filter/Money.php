<?php

namespace CsvSelect\Filter;

use CsvSelect\Filter;

class Money implements Filter {

	public function filter ($value, $row) {

		$retval = '£' . number_format($value, 2, '.', ',');
		return $retval;
	}
}
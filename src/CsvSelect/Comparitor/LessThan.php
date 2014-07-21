<?php

namespace CsvSelect\Comparitor;

use CsvSelect\Comparitor;

class LessThan implements Comparitor {

	public function match($a, $b) {

		$retval = $a < $b;
		return $retval;
	}
}
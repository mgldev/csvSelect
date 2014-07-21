<?php

namespace CsvSelect\Comparitor;

use CsvSelect\Comparitor;

class In implements Comparitor {

	public function match($a, $b) {

		$retval = in_array($a, $b);
		return $retval;
	}
}
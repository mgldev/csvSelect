<?php

namespace CsvSelect\Comparitor;

use CsvSelect\Comparitor;

class EqualTo implements Comparitor {

	public function match($a, $b) {

		$retval = $a == $b;
		return $retval;
	}
}
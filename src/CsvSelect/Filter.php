<?php

namespace CsvSelect;

interface Filter {

	public function filter ($value, $row);
}
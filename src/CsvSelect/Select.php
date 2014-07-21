<?php

namespace CsvSelect;

use CsvSelect\CsvFunction;
use CsvSelect\Comparitor;
use CsvSelect\Expression;
use CsvSelect\Filter;

class Select {

	protected $_filename = null;
	protected $_fields = array();
	protected $_selectFields = array();
	protected $_whereClauses = array();
	protected $_comparitors = array();
	protected $_functions = array();

	public function addComparitor ($operand, Comparitor $comparitor) {

		$this->_comparitors[$operand] = $comparitor;
		return $this;
	}

	public function getComparitor ($operand) {

		if (!isset($this->_comparitors[$operand])) {
			throw new \Exception('Comparitor for operand ' . $operand . ' not registered');
		}

		$retval = $this->_comparitors[$operand];
		return $retval;
	}

	public function addFunction ($name, CsvFunction $function) {

		$this->_functions[$name] = $function;
		return $this;
	}

	public function getFunction ($name) {

		if (!isset($this->_functions[$name])) {
			throw new \Exception('Function ' . $name . ' not registered');
		}

		$retval = $this->_functions[$name];
		return $retval;
	}

	public function from ($filename, array $fields = array()) {

		$this->_filename = $filename;
		$this->fields($fields);
		return $this;
	}

	public function fields (array $fields = array()) {

		$this->_selectFields = $fields;
		return $this;
	}

	public function getFields () {

		return $this->_selectFields;
	}

	public function where ($clause) {

		$this->_whereClauses['and'][] = $clause;
		return $this;
	}

	public function orWhere ($clause) {

		$this->_whereClauses['or'][] = $clause;
		return $this;
	}

	public function fetchRows () {

		$handle = fopen($this->_filename, 'r');

		if (!$handle) {
			throw new Exception('Unable to open CSV file');
		}

		$first = true;

		$rows = array();

		while (($row = fgetcsv($handle, 1024, ',')) !== false) {

			if ($first) {
				$this->_fields = $row;
				$first = false;
				continue;
			}
			
			$rows[] = $this->_shapeRow($row);
		}

		$retval = $this->_filter($rows);
		return $retval;
	}

	protected function _applyWhereClauses (array $rows) {

		$retval = $rows;

		foreach ($this->_whereClauses['and'] as $clause) {

			$clauseText = $clause;
			$clauseParam = null;

			if (is_array($clause)) {
				$clauseText = key($clause);
				$clauseParam = reset($clause);
			}

			$parts = explode(' ', $clauseText);
			$field = array_shift($parts);
			$operand = array_shift($parts);
			$matchValue = implode(' ', $parts);

			if ($matchValue == '?') {
				$matchValue = $clauseParam;
			}

			$comparitor = $this->getComparitor($operand);

			foreach ($rows as $index => $row) {

				if (!isset($row[$field])) {
					throw new \Exception('Field "' . $field . '" does not exist');
				}

				$value = $row[$field];

				if (!$comparitor->match($value, $matchValue)) {
					unset($retval[$index]);
				}
			}
		}

		return $retval;
	}

	protected function _applySelects (array $rows) {

		$retval = array();

		foreach ($rows as $row) {

			$entry = $row;

			foreach ($this->_selectFields as $key => $value) {

				if (is_numeric($key)) {
					$entry[$value] = $row[$value];
				} else {

					switch (true) {

						case is_string($value):
							$entry[$key] = $row[$value];
							break;

						case $value instanceof Expression:
							$functionName = $value->getFunctionName();
							$params = $value->getParams();
							$function = $this->getFunction($functionName);
							$function->setParams($params);
							if ($function->isAggregate()) {
								$entry[$key] = $function->getResult($rows);
							} else {
								$entry[$key] = $function->getResult($row);
							}
							break;

						case $value instanceof Filter:

							if (!isset($row[$key])) {
								throw new \LogicException('Field "' . $key . '" not found in source row');
							}

							$entry[$key] = $value->filter($row[$key], $row);
							break;

						case $value instanceof Closure:

							if (!isset($row[$key])) {
								throw new \LogicException('Field "' . $key . '" not found in source row');
							}

							$entry[$key] = $value($row[$key], $row);
							break;

						// allow filter chaining
						case is_array($value):
							foreach ($value as $filter) {
								if ($filter instanceof Filter) {

									if (!isset($row[$key])) {
										throw new \LogicException('Field "' . $key . '" not found in source row');
									}

									$entry[$key] = $filter->filter($row[$key], $row);
								} else if ($filter instanceof Closure) {

									if (!isset($row[$key])) {
										throw new \LogicException('Field "' . $key . '" not found in source row');
									}

									$entry[$key] = $filter($row[$key], $row);
								}
							}
							break;
					}
				}
			}

			$retval[] = $entry;
		}

		return $retval;
	}

	protected function _filter (array $rows) {

		$winnowed = $this->_applyWhereClauses($rows);
		$retval = $this->_applySelects($winnowed);
		return $retval;
	}

	protected function _shapeRow ($row) {

		$retval = array();

		foreach ($this->_fields as $index => $field) {
			$retval[$field] = $row[$index];
		}

		return $retval;
	}
}
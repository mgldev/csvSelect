<?php

namespace CsvSelect;

abstract class CsvFunction {

	protected $_isAggregate = false;
	protected $_params = array();

	public function isAggregate() {

		return $this->_isAggregate;
	}

	public function setParams(array $params) {

		$this->_params = $params;
		return $this;
	}

	public function getParam($index) {

		$retval = null;

		if (isset($this->_params[$index])) {
			$retval = $this->_params[$index];
		}

		return $retval;
	}

	public function getParams() {

		return $this->_params;
	}

	public abstract function getResult($value);
}
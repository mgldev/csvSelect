<?php

namespace CsvSelect;

class Expression {

	protected $_expression = null;
	protected $_functionName = null;
	protected $_params = null;

	public function __construct($expression) {

		$this->_parse($expression);
	}

	public function _parse($expression) {

		$paramStart = strpos($expression, '(');
		$functionName = substr($expression, 0, $paramStart);
		$params = explode(',', substr($expression, $paramStart + 1, (strlen($expression) - strlen($functionName)) - 2));
		$this->_functionName = $functionName;
		$this->_params = $params;
		return $this;
	}


	public function getFunctionName() {

		return $this->_functionName;
	}

	public function getParams() {

		return $this->_params;
	}
}
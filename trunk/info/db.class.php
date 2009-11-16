<?php

class Db_Mysql {
	/*
	 * Stores all config values for adapter
	 *
	 * @var 	array
	 */
	private $_config = array();

	/**
	 * Current query resource
	 *
	 * @var 	resource
	 */
	private $_query_result;



	/**
	 * * Make query to database
	 * *
	 * * @param	string	$query
	 *
	 */
	public function query($query)
	{
		$args = func_get_args();
		array_splice($args, 0, 1);
	
		if (is_array($args[0])) $args = $args[0];
	
		$query = $this->makeSql($query, $args);
		$this->_query_result = @mysql_query($query);

		if (!$this->_query_result) {
			throw new Exception('Cannot perform query because: "' . mysql_error() . '"');
		}
	}


	/**
	 * * Fetches each row of query
	 * *
	 * * @param	string	$query
	 * * @return	mixed
	 * */
	public function fetchRow($query = null)
	{
		if (!is_null($query)) {
			$args = func_get_args();
			call_user_func_array(array($this, 'query'), $args);
		}
		if (!$this->_query_result) {
			throw new Exception('Cannot perform fetching, because query was not executed!');
		}

		return mysql_fetch_array($this->_query_result);
	}


	/**
	 * * * Fetches all rows and returns array of rows
	 * * *
	 * * * @param	string	$query
	 * * * @return	array
	 * * */
	public function fetchRowset($query = null)
	{
		if (!is_null($query)) {
			$args = func_get_args();
			call_user_func_array(array($this, 'query'), $args);
		}

		$result = array();
		while ($row = $this->fetchRow()) {
			$result[] = $row;
		}
		return $result;
	}

	/**
	 * * * Fetches only first one element of the first row
	 * * *
	 * * * @param	string	$query
	 * * * @return	mixed
	 * * */
	public function fetchOne($query = null)
	{
		if (!is_null($query)) {
			$args = func_get_args();
			call_user_func_array(array($this, 'query'), $args);
		}
		if (!$this->_query_result) {
			throw new Exception('Cannot perform fetching, because query was not executed!');
		}
		$query_result = mysql_fetch_array($this->_query_result);
		if (array($query_result)){
			return $query_result[0];
		} else {
			return false;
		}
	}

	/**
	 * * * Fetches result with 2 queried columns as array where 1st column - index, 2nd column - value
	 * * *
	 * * * @param	string	$query
	 * * * @return	array
	 * * */
	public function fetchPairs($query = null) 
	{   
		if (!is_null($query)) {
			$args = func_get_args();
			call_user_func_array(array($this, 'query'), $args);
		}
		if (!$this->_query_result) {
			throw new Exception('Cannot perform fetching, because query was not executed!');
		}
		
		$query_result = array();
		while (list($index, $value) = mysql_fetch_array($this->_query_result)) {
			$query_result[$index] = $value;
		}

		return $query_result;
	}


	/**
	 * * * Inserts values to specified table
	 * * *
	 * * * @param	string	$table
	 * * * @param	array	$values
	 * * */
	function insert($table, $values)
	{
		$query = 'INSERT INTO ' . $table;

		$arr_keys = array();
		$arr_vals = array();
		$arr_alternates = array();
		foreach ($values as $key => $val) {
			$arr_keys[] = '`' . $key . '`';
			$arr_vals[] = $val;
			$arr_alternates[] = '?';   
		}

		$query .= '(' . implode(', ', $arr_keys) . ')';
		$query .= ' VALUES (' . implode(', ', $arr_alternates) . ')';

		$this->query($query, $arr_vals);

		return $this->insertId();
    	}


	/**
	 * * * Updates table records with provided values
	 * * *
	 * * * @param	string	$table
	 * * * @param	array	$values
	 * * * @param	array	$conditions
	 * * */
	function update($table, $values, $conditions) {   
		$query = 'UPDATE ' . $table;

		if (!is_array($conditions))
			$conditions = array($conditions);

		$arr_vals = array();
		$arr_pairs = array();
		foreach ($values as $key => $val) {
			$arr_vals[] = $val;
			$arr_pairs[] = $key . '=?';
		}

		$query .= ' SET ' . implode(', ', $arr_pairs);
		$query .= ' WHERE ' . implode(' AND ', $conditions);

		$this->query($query, $arr_vals);
	}


	/**
	 * * * Deletes table records
	 * * *
	 * * * @param	string	$table
	 * * * @param	array	$values
	 * * * @param	array	$conditions
	 * * */
	function delete($table, $conditions) {
		$query = 'DELETE FROM ' . $table;

		if (!is_array($conditions))
			$conditions = array($conditions);

		$query .= ' WHERE ' . implode(' AND ', $conditions);
		$this->query($query);
	}


	/**
	 * * * Return count of rows returnd by select query
	 * * */
	function numRows() 
	{  
		return mysql_num_rows($this->_query_result);
	}

	/*
	 * Return last id for insert query
	 */
	function insertId() {
		return mysql_insert_id();
	}

	/**
	 * * * Makes sql-code from code with placeholders
	 * * */
	function makeSql($query)
	{	  
		$args = func_get_args();

		array_splice($args, 0, 1);
		if (is_array($args[0]))
			$args = $args[0];

		if (strpos($query, '?')!==false) {
			$query_parts = explode('?', $query);

			if ((count($query_parts)-1)!=count($args)) {
				throw new Exception('Wrong parameter count for query!');
			}

			$query = '';
			$num_parts = count($query_parts);

			for ($i = 0; $i < $num_parts; $i++) {
				$arg = self::_safeValue($args[$i]);
				$query .= $query_parts[$i] . (($i < $num_parts-1) ? $arg : '');
			}

		}

		return $query;
    	}


	private function _safeValue($value)
	{
		if(is_int($value))
			return $value;
		if(is_float($value))
		       	return $value;
		if(is_null($value))
		       	return 'NULL';
		return "'".mysql_escape_string($value)."'";  
	}
}

?>


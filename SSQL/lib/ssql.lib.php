<?php
mysqli_report(MYSQLI_REPORT_OFF);
class SSQL{
	protected $server;
	protected $name;
	protected $psw;
	protected $conn;
	
	function __construct(){
		$this->server='';
		$this->name='';
		$this->psw='';
		$this->conn='';
	}
	function setCredential(string $s, string $u, string $p) : bool{
		$this->server = filter_var($s,FILTER_SANITIZE_STRING);
		$this->name = filter_var($u, FILTER_SANITIZE_STRING);
		$this->psw = filter_var($p, FILTER_SANITIZE_STRING);
		$this->conn = new mysqli($this->server, $this->name, $this->psw);
		if($this->conn->connect_error){
			die('Failed to connect to SQL DB');
			return false;
		}else{
			return true;
		}
	}
	function close() : bool{
		return $this->conn->close();
	}
	function checkDB(string $dbname) : bool{
		if($this->conn->select_db(strtolower($dbname))){
			return true;
		}else{
			return false;
		}
	}
	function dropDB(string $dbname) : bool{
		$sql = 'DROP DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function makeDB(string $dbname) : bool{
		$sql = 'CREATE DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}	
	}
	function resetDB(string $dbname) : bool{
		if($this->checkDB(strtolower($dbname))){
			$this->dropDB(strtolower($dbname));
			$this->makeDB(strtolower($dbname));
			return true;
		}else{
			return false;
		}
	}
	function selectDB(string $dbname){
		$this->conn->select_db(strtolower($dbname));
		return $this;
	}
	function makeTable(string $tbname, array $items, array $types ,array $values, array $options) : bool{
		if(count($items)!=count($types)||count($items)!=count($values)||count($items)!=count($options)){
			die('All array must match up with items amout(Physical Count: '.count($items).'/Array Count:'.(count($items)-1).')');
			return false;
		}else{
			$sql = 'CREATE TABLE '.strtolower($tbname). '(';
		for($i=0;$i<count($items);$i++){
			$sql.=$items[$i].' '.($types[$i]!=='' ? $types[$i] : 'VARCHAR(30)').' '.($values[$i]!=='' ? (stripos($values[$i],'UNSIGNED')!==FALSE ? $values[$i] : 'DEFAULT '.$values[$i]) : '').' '.$options[$i].($i<(count($items)-1) ? ', 
			' : '');
		}
		$sql.=')';
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
		}
	}
	function dropTable(string $tbname) : bool{
		$sql = 'DROP TABLE '.strtolower($tbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}	
	}
	function checkTable(string $tbname) : bool{
		$sql = 'SELECT * FROM '.strtolower($tbname);
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			return false;
		}
	}
	function addData(string $tbname, array $data, array $values) : bool{
		$setValues='';
		for($i=0;$i<count($values);$i++){
			$setValues.='('.implode(',',array_map(function($i){return "'".$i."'";}, $values[$i])).')'.($i<(count($values)-1) ? ',' : '');
		}
		$sql = 'INSERT INTO '.strtolower($tbname).' ('.implode(',',$data).') VALUES '.$setValues;
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function selectData(string $tbname, array $sel, string $condition='') : array{
		$row=[];
		$sql = 'SELECT '.implode(',',$sel).' FROM '.strtolower($tbname).($condition!=='' ? ' '.$condition : '');
		$results = $this->conn->query($sql);
		if($results->num_rows > 0){
				while($rows = $results->fetch_assoc()){
					$row[] = $rows;
				}
			return $row;
		}else{
			die('Error: '.$this->conn->error);
			return $row;
		}
	}
	function dropData(string $tbname, string $condition) : bool{
		$sql = 'DELETE FROM '.strtolower($tbname).' WHERE '.$condition;
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function updateData(string $tbname, string $replacement, string $condition) : bool{
		$sql = 'UPDATE '.strtolower($tbname).' SET '.$replacement.' WHERE '.$condition;
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
}
?>
<?php
mysqli_report(MYSQLI_REPORT_OFF);
class SSQL{
	protected $server;
	protected $name;
	protected $psw;
	protected $conn;
	protected $db;
	protected $dm;
	function __construct(){
		$this->server='';
		$this->name='';
		$this->psw='';
		$this->conn='';
		$this->db='';
		$this->dm = 'dark';
	}
	function style($darkmode=true){
		$this->dm = ($darkmode ? 'dark' : 'light');
		$out= '<style>
		.ssql-table{
			border-collapse: collapse;
			border-spacing: 0;
			width: 100%;
			display: table;
			border: 1px solid #ccc;
			margin: 20px 0;
		}
		.ssql-table th:first-child, .ssql-table td:first-child {
			padding-left: 16px;
		}
		.ssql-table td, .ssql-table th {
			padding: 8px 8px;
			display: table-cell;
			text-align: left;
			vertical-align: top;
		}
		';
		if($darkmode){
			$out.='.ssql-table.dark tr:nth-child(odd) {
			background-color: #1d2a35;
			color: #ddd;
		}
		.ssql-table.dark tr:nth-child(even) {
			background-color: #38444d;
			color: #ddd;
		}
		.ssql-table.dark tr {
			border-bottom: 1px solid #38444d;
		}';
		}else{
			$out.='.ssql-table.light tr:nth-child(odd) {
			background-color: #ddd;
			color: #1d2a35;
			}
			.ssql-table.light tr {
				border-bottom: 1px solid #dddddd;
			}';
		}
		$out.='</style>';
		return $out;
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
	# database
	function checkDB(string $dbname) : bool{
		if($this->conn->select_db(strtolower($dbname))){
			$this->db = strtolower($dbname);
			return true;
		}else{
			return false;
		}
	}
	function dropDB(string $dbname) : bool{
		$sql = 'DROP DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			$this->db = '';
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function makeDB(string $dbname) : bool{
		$sql = 'CREATE DATABASE '.strtolower($dbname);
		if($this->conn->query($sql)===TRUE){
			$this->db = strtolower($dbname);
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
	# tables
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
	function dropData(string $tbname, string $condition='') : bool{
		$sql = 'DELETE FROM '.strtolower($tbname).($condition!=='' ? ' WHERE '.$condition : '');
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function updateData(string $tbname, string $replacement, string $condition='') : bool{
		$sql = 'UPDATE '.strtolower($tbname).' SET '.$replacement.($condition!=='' ? ' WHERE '.$condition : '');
		if($this->conn->query($sql)===TRUE){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	# permissions
	function givePerm(array $perm, string $tbname, array $username) : bool{
		$sql = 'GRANT '.implode(',',$perm).' ON '.$this->db.'.'.strtolower($tbname).' TO '.implode(',',array_map(function($i){return $i.'@'.$this->server;},$username));
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
			return false;
		}
	}
	function dropPerm(array $perm,string $tbname, array $username) : bool{
		$sql = 'REVOKE '.implode(',',$perm).' ON '.$this->db.'.'.strtolower($tbname).' FROM '.implode(',',array_map(function($i){return $i.'@'.$this->server;},$username));
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}	
	}
	# accounts
	function makeUser(string $username, string $psw='', bool $checkExists=true ,array $options=[]) : bool{
		$sql = 'CREATE USER '.($checkExists ? 'IF NOT EXISTS' : '').' "'.$username.'"@"'.$this->server.'" IDENTIFIED BY '.($psw!==''||$psw!==null ? '"'.$psw.'"' : '').' '.implode(' ',$options);
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	function dropUser(string $username){
		$sql = 'DROP USER "'.$username.'"@"'.$this->server.'"';
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	# views
	function makeView(string $viewName, string $tbname, array $data, string $condition='', array $options=[]) : string{
		$sql = 'CREATE OR REPLACE VIEW '.strtolower($viewName).' AS SELECT '.implode(',',$data).' FROM '.$this->db.'.'.strtolower($tbname).($condition!=='' ? 'WHERE '.$condition : '').' '.implode(' ',$options);
		if($this->conn->query($sql)){
			$out='<table class="ssql-table '.$this->dm.'">
			<tbody>';
			$getView = 'SELECT * FROM '.$viewName;
			$r = $this->conn->query($getView);
			$out.='<tr>';
			foreach($data as $d){
				$out.='<th>'.$d.'</th>';
			}
			$out.='</tr>';
			while($row = $r->fetch_assoc()){
				$out.='<tr>';
				foreach($data as $d){
					$out.='<td>'.$row[$d].'</td>';
				}
				$out.='</tr>';
			}
			$out.='</tbody>
			</table>';
			return $out;
		}else{
			die('Error: '.$this->conn->error);
				return '';
		}
	}
	function dropView(string $viewName) : bool{
		$sql = 'DROP VIEW '.strtolower($viewName);
		if($this->conn->query($sql)){
			return true;
		}else{
			die('Error: '.$this->conn->error);
				return false;
		}
	}
	
	# others
	function genPsw($salt='') {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-_+=';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return $salt.implode($pass); //turn the array into a string
	}
}
?>
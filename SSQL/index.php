<?php
require_once('lib/ssql.lib.php');
?>
<html>
<head>
<title>SSQL</title>
<?php
$ssql = new SSQL();
echo $ssql->style();
?>
</head>
<body>
<?php
if($ssql->setCredential('localhost', 'root', '')){
	if(!$ssql->resetDB('StoreData'))
		$ssql->makeDB('StoreData');
	$db = $ssql->selectDB('StoreData');
	$db->makeTable('customers', ['id', 'first_name','last_name', 'email', 'phone'],['int(6)', '', '', 'varchar(50)','bigint'],['UNSIGNED','','','',''],['AUTO_INCREMENT PRIMARY KEY','NOT NULL','NOT NULL', '','']);
	$ssql->close();	
}
?>
</body>
</html>
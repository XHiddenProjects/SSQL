<?php
require_once('lib/ssql.lib.php');
?>
<html>
<head>
<title>SSQL</title>
</head>
<body>
<?php
$ssql = new SSQL();
if($ssql->setCredential('localhost', 'root', '')){
	if(!$ssql->resetDB('myDB'))
		$ssql->makeDB('myDB');
$db = $ssql->selectDB('myDB');
($db->checkTable('myTable') ? $db->dropTable('myTable') : '');
$db->makeTable('myTable', ['id', 'firstname', 'lastname', 'email', 'reg_date'], ['int(6)', '', '', 'VARCHAR(50)', 'TIMESTAMP'], ['UNSIGNED', '', '', '', 'CURRENT_TIMESTAMP'], ['AUTO_INCREMENT PRIMARY KEY', 'NOT NULL', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP']);
$db->addData('myTable', ['firstname','lastname','email'],[['John','Doe','john@example.com'],['Fred','Bang','fred@example.com'],['Greg','barns','greg@example.com']]);
$db->selectData('myTable',['*']);
$db->dropData('myTable', 'id=3');
$db->updateData('myTable', 'lastname="Doe"', 'id=2');
$ssql->close();	
}
?>
</body>
</html>
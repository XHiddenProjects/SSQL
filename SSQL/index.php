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
	if(!$ssql->resetDB('myDB'))
		$ssql->makeDB('myDB');
	$db = $ssql->selectDB('myDB');
	($db->checkTable('myTable') ? $db->dropTable('myTable') : '');
	$db->makeTable('myTable', ['id', 'firstname', 'lastname', 'email', 'reg_date'], ['int(6)', '', '', 'VARCHAR(50)', 'TIMESTAMP'], ['UNSIGNED', '', '', '', 'CURRENT_TIMESTAMP'], ['AUTO_INCREMENT PRIMARY KEY', 'NOT NULL', 'NOT NULL', '', 'ON UPDATE CURRENT_TIMESTAMP']);
	$db->addData('myTable', ['firstname','lastname','email'],[['John','Doe','john@example.com'],['Fred','Bang','fred@example.com'],['Greg','barns','greg@example.com']]);
	$db->selectData('myTable',['*']);
	$db->dropData('myTable', 'id=2');
	$db->updateData('myTable', 'lastname="Doe"', 'id=3');
	echo $db->makeView('myView','myTable',['firstname','lastname','email']);
	$db->dropView('myView');
	$ssql->makeUser('test', $ssql->genPsw('psw'));
	$ssql->givePerm(['ALL'], '*', ['test']);
	$ssql->remPerm(['ALL'], '*', ['test']);
	$ssql->dropUser('test');
	$ssql->close();	
}
?>
</body>
</html>
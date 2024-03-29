# SSQL
A easy way to control your SQL with a very minimal of work. SSQL stands for _Simple structure query language_. Which makes this your SQL and storage more easy to create/remove/update/filter your SQL data.

### Loading the library:
First you have to load up the lib, by writting this:
```php
<?php 
require_once('{basepath}/ssql.lib.php');
$ssql = new SSQL();
$ssql->style($darkmode=true); //loads stylesheet(place in head tag)
?>
```

### loading credential
this is the most imporant thing in order for this to work, your credentals must be active, use:
```php
$ssql->setCredential('{servername}', '{username}', '{password}'); //return boolean
```

### closing connection
to close the connections:
```php
$ssql->close();
```

***

# Database

### checking database
to make sure your database exists use:
```php
$ssql->checkDB(string $dbname); // returns boolean
```

### removing database
To remove the database use:
```php
$ssql->dropDB(string $dbname); //returns boolean
```

### creating database
To create a database use:
```php
$ssql->makeDB(string $dbname); //return boolean
```

### reseting database
to reset a database(remove and recreate the database) use:
```php
$ssql->resetDB(string $dbname); //return boolean
```

### selecting a database
to select a database, use:
```php
$ssql->selectDB(string $dbname);//return $this
//or
$db = $ssql->selectDB(string $dbname); //return $this
```
***
**Note: you must have `$ssql->selectDB(string $dbname)` in a variable or use that to proceed on making the tables happen!, I used `$db=` for this example**

# Import files
```php
# path to SQL file(.sql) and will automatically import all data to the data
$db->import(string $filepath);
```

*** 
# Tables


### creating a table
To create a table use:
```php
$db->makeTable(string $tbname, array $items, array $types, array $values, array $options); //returns bool
```

### removing a table
To remove a table use:
```php
$db->dropTable(string $tbname); //returns boolean
```

### check for a table
To check for an existing table use:
```php
$db->checkTable(string $tbname); //returns boolean
```

### adding data
To add more data use:
```php
$db->addData(string $tbname, array $data, array $values); //return boolean

/*
to add multiple values do:
[[array1],[array2],[array3]]
*/
```

### selecting data
To select an array of data use:
```php
$db->selectData(string $tbname, array $sel, string $condition=''); //returns array
```

### deleting data
To delete an data use:
```php
$db->dropData(string $tbname, string $condition=''); //returns boolean
```

### updating data
To update a data use:
```php
$db->updateData(string $tbname, string $replacement, string $condition=''); //returns boolean
```
***

### permissions
To create/replace permissions use:
```php
$ssql->givePerm(array $perm, string $tbname, array $username); //returns boolean
```

To remove permissions use:
```php
$ssql->dropPerm(array $perm,string $tbname, array $username); //returns boolean
```
***

### accounts
To create an account use:
```php
$ssql->makeUser(string $username, string $psw='', bool $checkExists=true ,array $options=[]); //returns boolean
```

To remove an account use:
```php
$ssql->dropUser(string $username); //returns boolean
```

***

### Views
to load a view use:
```php
echo $db->makeView(string $viewName, string $tbname, array $data, string $condition='', array $options=[]); //return string:table
```

to remove view use:
```php
$db->dropView(string $viewName); //returns boolean
```

***

### Others
Generate Password, use:
```php
$ssql->genPsw($salt=''); //returns string
```

***

# Conditions & options

- Conditions: `lastname="fred"`, `id=2`, `email="example@example.com"`,etc... They only accept **`WHERE`** conditions
- Options: `AUTO_INCREMENT`,`PRIMARY`,`KEY`, etc... or any extended version of conditions: **`LIKE`**, **`IF`**, **`AND`**, etc...

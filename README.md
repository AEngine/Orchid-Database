Orchid Database
====
It allows you to connect the database to a project using a wrapper around the PDO.

#### Requirements
* Orchid Framework
* PHP >= 7.0

#### Installation
Run the following command in the root directory of your web project:
  
> `composer require aengine/orchid-database`

#### Usage
Connect to the server
```php
Db::setup([
    [
        'dsn'           => 'mysql:host=HOST;dbname=DB-NAME',
        'username'      => 'USERNAME',
        'password'      => 'PASSWORD',
        // additional can be passed options, server-role and pool name:
        // 'option'     => [
        //     PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // ],
        // 'role'       => 'master', // or slave
        // 'pool_name'  => 'default', // pool list of connections
    ],
    // possible another connection config
    // for the implementation of master-slave
]);
```

Query execution
```php
$stm = Db::query('SELECT * FROM `user` WHERE `age` > 23');

while ($a = $stm->fetch(PDO::FETCH_ASSOC)) {
    // some action
    pre($a);
}
```

#### Aliases

Select rows
```php 
$list = Db::select('SELECT * FROM `products` WHERE `price` >= 150');
```

Select first element of array from `Db::select`
```php 
$first = Db::selectOne('SELECT * FROM `products` WHERE `price` >= 150');
```

Affect row and return count of affected
```php 
$affected = Db::affect('INSERT INTO `products` SET `name` = "Socks with owls", `price` = 200');
```

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
        'dsn'      => 'mysql:host=HOST;dbname=DB-NAME',
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        // additional can be passed options and the server-role:
        // 'option'   => [
        //     PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        // ],
        // 'role'  => 'master', // or slave
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

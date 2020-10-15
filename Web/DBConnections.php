<?php

use Framework\Database\Database;
use Databases\MySQL;

/*
|--------------------------------------------------------------------------
| Database connections
|--------------------------------------------------------------------------
|
| Here you can register new database connections for your app. A default
| connection to a MySQL database is already included (in dir /Databases).
| For more info see the docs.
|
| (Database connections need to implement the DatabaseInterface to work)
*/

$mysqlDatabase = new MySQL();
Database::connect($mysqlDatabase);
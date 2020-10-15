<?php

namespace Framework\Database;

use Framework\Database\DatabaseInterface;

class Database 
{
    /**
     * Contains all database connections
     * 
     * @var array
     */
    public static $Connections = [];

    /**
     * Triggers connect function of specific database class
     * 
     * @parameter DatabaseInterface $database
     * @return void
     */
    public static function connect(DatabaseInterface $databaseClass) : void
    {
        $name = $databaseClass->getConnectionName();
        self::$Connections[$name] = $databaseClass->makeDatabaseConnection();
    }
}
<?php

namespace Framework\Migrations;

use Framework\Database\Database;

class Migrations
{
    /**
     * Contains database connection
     *
     * @var object $database
     */
    public $database;

    /**
     * Contains query to be executed
     *
     * @var string $query
     */
    public $query;

    /**
     * Migrations constructor.
     */
    public function __construct()
    {
        $this->database = reset(Database::$Connections);
    }

    /**
     * Add sql query to this objects properties
     *
     * @param string $query
     */
    public function addSql(string $query)
    {
        $this->query = $query;
        $this->runScript();
    }

    /**
     * Runs sql query
     */
    public function runScript()
    {
        $this->database->query($this->query);
    }
}
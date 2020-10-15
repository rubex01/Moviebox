<?php

namespace Databases;

use Framework\Database\DatabaseInterface;
use Framework\Exceptions\HttpExceptions;
use mysqli;

class MySQL implements DatabaseInterface
{
    /**
     * Key of connection in database connections array
     *
     * @var string
     */
    public $name = 'MySQL';

    /**
     * Contains database connection
     *
     * @var object
     */
    public $Conn;

    /**
     * Contains servername
     *
     * @var string
     */
    private $serverName;

    /**
     * Contains username
     *
     * @var string
     */
    private $username;

    /**
     * Contains password
     *
     * @var string
     */
    private $password;

    /**
     * Contains database name
     *
     * @var string
     */
    private $dbName;

    /**
     * Make database connection
     *
     * @return object
     */
    public function makeDatabaseConnection() : object
    {
        $this->serverName = getenv('DB_HOST');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->dbName = getenv('DB_NAME');

        $this->Conn = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);

        if ($this->Conn->connect_error) {
            $this->connectionError($this->Conn->connect_error);
        }

        return $this->Conn;
    }

    /**
     * Returns name of the db connection
     *
     * @return string
     */
    public function getConnectionName() : string
    {
        return $this->name;
    }

    /**
     * Error handling in case of failed connection
     *
     * @return void
     */
    public function connectionError(string $causeOfException) : void
    {
        try {
            throw new HttpExceptions($causeOfException, 500);
        } catch (HttpExceptions $th) {
            exit();
        }
    }
}
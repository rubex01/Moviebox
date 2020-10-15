<?php

namespace Framework\Commands\RunMigrations;

use Framework\Database\Database;

class RunMigrations
{
    use \Framework\Commands\DefaultTraits;

    /**
     * Contains selected migration method
     *
     * @var
     */
    public $migrationMethod;

    /**
     * Contains migration directory
     *
     * @var string
     */
    public $migrationDir = __DIR__ . '/../../../Migrations';

    /**
     * Contains all the migration files
     *
     * @var array
     */
    public $migrationFiles = [];

    /**
     * Start function
     *
     * @return void
     */
    public function start() : void
    {
        $this->setRightDatabase();
        $this->getMigrationMethod();
        $this->getMigrationFiles();
        $this->runMigrations();
    }

    /**
     * Set right database
     *
     * @return void
     */
    public function setRightDatabase() : void
    {
        $scanDir = scandir(__DIR__ . '/../../../Databases', SCANDIR_SORT_DESCENDING);
        $databases = array_diff($scanDir, array('.', '..'));

        foreach ($databases as $key => $database) {
            $databases[$key] = basename($database, '.php');
        }

        if (count($databases) > 1) {
            $chosenDatabase = null;
            while (!in_array($chosenDatabase, $databases)) {
                $chosenDatabase = self::requestInput('There are multiple databases setup, please choose one.' ,$databases);
                if (!in_array($chosenDatabase, $databases)) {
                    self::reportError('This option is not valid');
                }
            }
            $dbClass =  'Databases\\'.$chosenDatabase;
            $database = new $dbClass;
        }
        else {
            $dbClass =  'Databases\\'.$databases[0];
            $database = new $dbClass;
        }

        Database::connect($database);
    }

    /**
     * Get migration method from user
     *
     * @return void
     */
    public function getMigrationMethod() : void
    {
        $migrationMethods = ['up', 'down'];
        $chosenMethod = null;
        while (!in_array($chosenMethod, $migrationMethods)) {
            $chosenMethod = self::requestInput('Choose a migration method.' ,$migrationMethods);
            if (!in_array($chosenMethod, $migrationMethods)) {
                self::reportError('This option is not valid');
            }
        }
        $this->migrationMethod = $chosenMethod;
    }

    /**
     * Get all the migration files
     *
     * @return void
     */
    public function getMigrationFiles() : void
    {
        $getFiles = array_slice(scandir($this->migrationDir), 2);
        $sortedArray = [];

        foreach ($getFiles as $key => $file) {
            $sortedArray[] = ['time' => substr($file, -18, 14), 'file' => $file];
        }

        usort($sortedArray, function($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        foreach ($sortedArray as $key => $sortedArrayItem) {
            $sortedArray[$key] = $sortedArrayItem['file'];
        }

        $this->migrationFiles = ($this->migrationMethod === 'up') ? $sortedArray : array_reverse($sortedArray);
    }

    /**
     * Run migrations
     *
     * @return void
     */
    public function runMigrations() : void
    {
        foreach ($this->migrationFiles as $file) {
            require $this->migrationDir.'/'.$file;

            $className = 'Migrations\\'.substr($file, 0, -4);
            $run = new $className;
            $method = $this->migrationMethod;
            $run->$method();
        }
        echo "Migrations finished successfully";
    }
}
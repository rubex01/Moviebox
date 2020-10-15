<?php

namespace Framework\Commands\Start;

use Framework\TemplateEngine\TemplateEngine;

class Start
{
    use \Framework\Commands\DefaultTraits;

    /**
     * Contains app name
     *
     * @var string
     */
    public $appName;

    /**
     * Contains basepath of app
     *
     * @var string
     */
    public $basePath;

    /**
     * Contains autocompile enabled or disabled bool
     *
     * @var bool
     */
    public $autoCompile;

    /**
     * Contains database name
     *
     * @var string
     */
    public $databaseName;

    /**
     * Contains database username
     *
     * @var string
     */
    public $databaseUsername;

    /**
     * Contains database password
     *
     * @var string
     */
    public $databasePassword;

    /**
     * Start function
     *
     */
    public function start() : void
    {
        echo "=====================================\n";
        $this->createEnv();
        echo "=====================================\n";
        $this->getContinue();
        echo "=====================================\n";
        $this->getName();
        $this->getBasePath();
        echo "=====================================\n";
        $this->getAutoCompile();
        echo "=====================================\n";
        echo "Almost done.. We only need some database information from you..\n";
        echo "=====================================\n";
        $this->getDBName();
        $this->getDBUsername();
        $this->getDBpassword();
        echo "=====================================\n";
        $this->makeFile();
        echo "You're all setup, go to the browser and find your app on: http://localhost" . $this->basePath;
    }

    /**
     * Create env file from env example
     *
     * @return void
     */
    public function createEnv() : void
    {
        echo (copy(__DIR__ . '/../../../.env.example.php',__DIR__ . '/../../../.env.php')) ? "ENV file generated\n" : "Something went wrong..";
    }

    /**
     * Create env file from env example
     *
     * @return void
     */
    public function getContinue() : void
    {
        $continue = self::requestInput('Do you want to continue, or setup the .ENV file yourself?', ['(Y)es', '(N)o']);
        if ($continue != 'yes' && $continue != 'y') {
            echo "Alright, you can find the .ENV file in the root of your project.";
            exit();
        }
    }

    /**
     * Get name of application
     *
     * @return void
     */
    public function getName() : void
    {
        $this->appName = self::requestInput('What is the name of your app going to be?');
    }

    /**
     * Get base path of application
     *
     * @return void
     */
    public function getBasePath() : void
    {
        $this->basePath = self::requestInput('What is the basepath for this app in your browser? (default is: "/")');
    }

    /**
     * Get autocompile boolean
     *
     * @return void
     */
    public function getAutoCompile() : void
    {
        $autoCompile = self::requestInput('Do you want the framework to autocompile template files?', ['(Y)es', '(N)o']);
        if ($autoCompile != 'yes' && $autoCompile != 'y') {
            $this->autoCompile = false;
        }
        else {
            $this->autoCompile = true;
        }
    }

    /**
     * Get database name
     *
     * @return void
     */
    public function getDBName() : void
    {
        $this->databaseName = self::requestInput('What is the name of your database?');
    }

    /**
     * Get database username
     *
     * @return void
     */
    public function getDBUsername() : void
    {
        $this->databaseUsername = self::requestInput('What is the database username?');
    }

    /**
     * Get database password
     *
     * @return void
     */
    public function getDBpassword() : void
    {
        $this->databasePassword = self::requestInput('What is the database password?');
    }

    /**
     * Overwrite env file with new information
     *
     * @return void
     */
    public function makeFile() : void
    {
        $newEnv = file_get_contents(__DIR__ . '/../../../.env.example.php');
        $newEnv = str_replace([
            "'APP_NAME' => 'Framework'",
            "'BASEPATH' => '/'",
            "'AUTOCOMPILE' => true",
            "'DB_NAME' => 'example'",
            "'DB_USERNAME' => 'root'",
            "'DB_PASSWORD' => ''"
                              ],
                              [
            "'APP_NAME' => '".$this->appName."'",
            "'BASEPATH' => '".$this->basePath."'",
            "'AUTOCOMPILE' => ". ($this->autoCompile === true ? 'true' : 'false'),
            "'DB_NAME' => '". $this->databaseName ."'",
            "'DB_USERNAME' => '". $this->databaseUsername ."'",
            "'DB_PASSWORD' => '". $this->databasePassword ."'",
                              ], $newEnv);

        $env = fopen(__DIR__ . '/../../../.env.php', "w");
        fwrite($env, $newEnv);
        fclose($env);
        if ($this->autoCompile === false) {
            $firstCompile = new TemplateEngine();
        }
    }
}
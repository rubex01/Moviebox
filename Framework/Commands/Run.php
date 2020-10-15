<?php

namespace Framework\Commands;

include __DIR__ . '/../autoload.php';
include __DIR__ . '/../bootstrap.php';

error_reporting(0);

class Run
{
    use \Framework\Commands\RunTraits;
    use \Framework\Commands\DefaultTraits;

    /**
     * Contains all viable options
     *
     * @var string[]
     */
    public static $options = [
        'Create',
        'RunMigrations',
        'EnableRoles',
        'RunTemplateRender'
    ];

    /**
     * Contains action user wants to perform
     *
     * @var null
     */
    public static $action = null;

    /**
     * Start command
     *
     */
    public static function init()
    {
        self::welcomeMessage();
        self::requestAction();
    }

    /**
     * Request action that user wants to perform
     *
     * @return void
     */
    public static function requestAction() : void
    {
        $action = null;
        while (!in_array($action, self::$options)) {
            $action = self::requestInput('Choose what you want to do:');
            if (!in_array($action, self::$options)) {
                self::reportError('This option is not valid');
            }
        }
        self::$action = $action;
        self::runAction();
    }

    /**
     * Runs action and calls right command class
     *
     * @return void
     */
    public static function runAction() : void
    {
        echo "=====================================\n";
        $class = '\Framework\Commands\\' . self::$action . '\\' . self::$action;
        $startFunction = new $class();
        $startFunction->start();
    }
}

Run::init();
<?php

namespace Framework\Commands;

trait RunTraits {

    /**
     * Run welcome message (in case no env file start)
     *
     * @return void
     */
    public static function welcomeMessage() : void
    {
        if (!file_exists(__DIR__ . '/../../.env.php')) {
            echo "Heya, we think you are new. Let us spin up the frame work for you!..";
            $request = self::requestInput('Do you want to continue?', ['(Y)es', '(N)o']);
            if ($request == 'yes' || $request == 'y') {
                $start = new \Framework\Commands\Start\Start;
                $start->start();
                exit();
            }
            echo "=====================================\n";
        }
        echo "Welcome to the command environment. Choose what you want to do, you have the following options: \n";
        foreach (self::$options as $option) {
            echo "- $option \n";
        }
    }
}
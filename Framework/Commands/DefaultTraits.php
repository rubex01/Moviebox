<?php

namespace Framework\Commands;

trait DefaultTraits
{
    /**
     * Request a certain input
     *
     * @param string $requestMessage
     * @param array|null $options
     * @return string
     */
    public static function requestInput(string $requestMessage, array $options = null) : string
    {
        echo $requestMessage.' ';
        if ($options !== null) {
            echo "\nYou have the following options:\n";
            foreach ($options as $option) {
                echo "- $option \n";
            }
            echo "Your choice: ";
        }
        $handle = fopen ("php://stdin","r");
        $input = trim(fgets($handle));
        fclose($handle);
        if ($input == 'exit') {
            exit();
        }
        return $input;
    }

    /**
     * Report error message in the console
     *
     * @param string $errorMessage
     * @return void
     */
    public static function reportError(string $errorMessage) : void
    {
        echo "!! ".$errorMessage."\n";
    }
}
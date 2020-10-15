<?php

$fileContent = '<?php

namespace '.$namespace.';

use Framework\Database\DatabaseInterface;

class '.$name.' implements DatabaseInterface
{
    public function makeDatabaseConnection() : object
    {
        // some code
    }

    public function getConnectionName() : string
    {
        // some code
    }

    public function connectionError(string $causeOfException) : void
    {
        // some code
    }
}';
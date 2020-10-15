<?php

$fileContent = '<?php

namespace '.$namespace.';

class '.$name.'
{
    public function handle() : bool
    {
        // some code (do not forget to declare the middleware in the config)
    }

    public function onFailure()
    {
        // some code
    }
}';
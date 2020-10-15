<?php

namespace Framework\Commands\RunTemplateRender;

use Framework\TemplateEngine\TemplateEngine;

class RunTemplateRender
{
    public function start()
    {
        $runCompiler = new TemplateEngine();
        echo "Templates have been compiled!";
    }
}
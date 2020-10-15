<?php

namespace Framework\Pages;

class Pages
{
    /**
     * view function that returns page in layout
     * 
     * @param string $viewLayout
     * @param string $viewPage
     * @param array $dataToPassOn
     * @return void
     */
    public static function view(string $viewLayout, string $viewPage, array $dataToPassOn, bool $cacheEnabled = false) : void
    {
        extract($dataToPassOn);
        $page = __DIR__ . '/../../Views/Rendered/Pages/' . $viewPage . '.page.php';

        if (getenv('CACHING') == true && $cacheEnabled === true && file_exists(__DIR__ . '/../../Storage/Cache/'.$viewLayout.$viewPage.'.html') === true) {
            $time = filectime(__DIR__ . '/../../Storage/Cache/' . $viewLayout . $viewPage . '.html');
            
            if (date("Y-m-d H:i:s.", $time) > date('Y-m-d H:i:s', strtotime("-1 day", strtotime(date("Y/m/d"))))) {
                include __DIR__ . '/../../Storage/Cache/' . $viewLayout . $viewPage . '.html';
                return;
            }
        }
        

        ob_start();
        include(__DIR__ . '/../../Views/Rendered/Layouts/' . $viewLayout . '.layout.php');
        $content = ob_get_clean();

        if ($cacheEnabled !== false) {
            $cacheFile = fopen(__DIR__ . '/../../Storage/Cache/' . $viewLayout . $viewPage . '.html', "w");
            fwrite($cacheFile, $content);
            fclose($cacheFile);
        }

        echo $content;
    }
}
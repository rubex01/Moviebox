<?php

namespace Framework\TemplateEngine;

use \Framework\TemplateEngine\ConvertingTraits;

class TemplateEngine
{
    use ConvertingTraits;

    /**
     * Contains all the possible template items
     *
     * @var string[]
     */
    public $templateItems = ['component', 'layout', 'page'];

    /**
     * Contains template item tree
     *
     * @var array
     */
    public $templatesTree = [];

    /**
     * TemplateEngine constructor.
     */
    public function __construct()
    {
        $this->createTree('Components');
        $this->createTree('Layouts');
        $this->createTree('Pages');
        $this->compileStart();
    }

    /**
     * Creates tree of all template files
     *
     * @param string $directory
     * @return void
     */
    public function createTree(string $directory) : void
    {
        $templateItems = array_slice(scandir(__DIR__ . '/../../Views/Templating/'.$directory), 2);
        foreach ($templateItems as $templateItem) {
            $templateItemParts = explode('.', $templateItem);
            $extension = end($templateItemParts);
            if (!in_array($extension, $this->templateItems)) {
                $this->createTree($directory.'/'.$templateItem);
            }
            else {
                $this->templatesTree[$directory][] = $templateItem;
            }
        }
    }

    /**
     * Starts convertion
     *
     * @return void
     */
    public function compileStart() : void
    {
        foreach ($this->templatesTree as $dir => $files) {
            foreach ($files as $file) {
                $fileContent = file_get_contents(__DIR__ . '/../../Views/Templating/' . $dir.'/'.$file);

                $templatingStrings = $this->getReplacementStrings($fileContent, '{{ ', ' }}');

                $toReplace = [];

                foreach ($templatingStrings as $key => $templatingString) {
                    $toReplace[$templatingString] = $this->compileToPHP($templatingString);
                }

                $replacedFileContents = $this->replaceItems($fileContent, $toReplace);

                if (!file_exists(__DIR__ . '/../../Views/Rendered/' . $dir)) {
                    mkdir(__DIR__ . '/../../Views/Rendered/' . $dir, 0777, true);
                }

                $templateRender = fopen(__DIR__ . '/../../Views/Rendered/' . $dir.'/'.$file.'.php', "w");
                fwrite($templateRender, $replacedFileContents);
                fclose($templateRender);
            }
        }
    }

    /**
     * Convert to php
     *
     * @param string $templateString
     * @return string
     */
    public function compileToPHP(string $templateString) : string
    {
        switch ($templateString) {
            case substr($templateString, 0, 4) === '@if(':
                $convertedString = $this->ifConvert($templateString);
                break;
            case substr($templateString, 0, 8) === '@elseif(':
                $convertedString = $this->elseIfConvert($templateString);
                break;
            case $templateString === '@else':
                $convertedString = $this->elseConvert($templateString);
                break;
            case $templateString === '@endif':
                $convertedString = $this->endIfConvert($templateString);
                break;
            case substr($templateString, 0, 3) === '$!!' && substr($templateString, -2) == '!!':
                $convertedString = $this->variableConvertUnsafe($templateString);
                break;
            case $templateString[0] === '$':
                $convertedString = $this->variableConvert($templateString);
                break;
            case substr($templateString, 0, 8) === '@include':
                $convertedString = $this->includeConvert($templateString);
                break;
            case $templateString === '@content':
                $convertedString = $this->contentConvert($templateString);
                break;
            case substr($templateString, 0, 6) === 'array(':
                $convertedString = $this->arrayConvert($templateString);
                break;
            case substr($templateString, 0, 5) === '@for(':
                $convertedString = $this->forConvert($templateString);
                break;
            case substr($templateString, 0, 7) === '@endfor':
                $convertedString = $this->endForConvert($templateString);
                break;
        }
        return $convertedString;
    }

    /**
     * Find all contents of instaces between characters
     *
     * @param $str
     * @param $startDelimiter
     * @param $endDelimiter
     * @return array
     */
    public function getReplacementStrings($str, $startDelimiter, $endDelimiter) : array
    {
        $contents = [];
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($str, $endDelimiter, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }
        return $contents;
    }

    /**
     * Replace items
     *
     * @param string $fileContent
     * @param array $toReplace
     * @return string
     */
    public function replaceItems(string $fileContent, array $toReplace) : string
    {
        $fileReplacedContent = $fileContent;
        foreach ($toReplace as $original => $replacement) {
            $fileReplacedContent = str_replace('{{ '.$original.' }}', '<?php '.$replacement.' ?>', $fileReplacedContent);
        }
        return $fileReplacedContent;
    }
}
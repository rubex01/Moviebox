<?php

namespace Framework\TemplateEngine;

trait ConvertingTraits
{
    /**
     * Convert variable and echo it
     *
     * @param string $templatingString
     * @return string
     */
    public function variableConvert(string $templatingString) : string
    {
        return 'echo htmlspecialchars('.$templatingString.');';
    }

    /**
     * Convert variable and echo it in unsafe way
     *
     * @param string $templatingString
     * @return string
     */
    public function variableConvertUnsafe(string $templatingString) : string
    {
        return 'echo $'.substr($templatingString, 3, -2).';';
    }

    /**
     * Convert to include a component
     *
     * @param string $templateString
     * @return string
     */
    public function includeConvert(string $templateString) : string
    {
        return 'include "'.str_replace('\\', '/', __DIR__).'/../../Views/Rendered/Components/'.substr($templateString, 9, -1).'.component.php";';
    }

    /**
     * Convert into contain the page
     *
     * @return string
     */
    public function contentConvert() : string
    {
        return 'include $page;';
    }

    /**
     * Convert printing array
     *
     * @param string $templateString
     * @return string
     */
    public function arrayConvert(string $templateString) : string
    {
        return 'print_r('. substr($templateString, 6, -1) .')';
    }

    /**
     * Convert if
     *
     * @param string $templatingString
     * @return string
     */
    public function ifConvert(string $templatingString) : string
    {
        return 'if('.substr($templatingString, 4, -1).') {';
    }

    /**
     * Convert else if
     *
     * @param string $templatingString
     * @return string
     */
    public function elseIfConvert(string $templatingString) : string
    {
        return '} else if('.substr($templatingString, 8, -1).') {';
    }

    /**
     * Convert else
     *
     * @param string $templatingString
     * @return string
     */
    public function elseConvert(string $templatingString) : string
    {
        return '} else {';
    }

    /**
     * Convert end if
     *
     * @param string $templatingString
     * @return string
     */
    public function endIfConvert(string $templatingString) : string
    {
        return '}';
    }

    /**
     * convert for loop
     *
     * @param string $templatingString
     * @return string
     */
    public function forConvert(string $templatingString) : string
    {
        return 'foreach('.substr($templatingString, 5, -1).') {';
    }

    /**
     * Convert end for loop
     *
     * @param string $templatingString
     * @return string
     */
    public function endForConvert(string $templatingString) : string
    {
        return '}';
    }
}
